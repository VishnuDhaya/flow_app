<?php 

namespace App\Services\Vendors\File;

use Exception;
use Illuminate\Support\Facades\Log;


class GoogleService{

    public function google_drive_file_upload($file_path, $share_folder_id, $prvdr_folder_name, $accno_folder_name){

        try{

                $sub_folder_id = $exception_msg = $trace = $file_id = null;
                $flow_keys_path = env("FLOW_KEYS_PATH");
                $drive_cred_json = env("APP_SUPPORT_DRIVE_CRED_JSON");
                
                $scopes = ['https://www.googleapis.com/auth/drive'];

                // $cred_path = "/home/oem/Downloads/file-upload-demo-365812-030cc4b2fe6c.json";
                $cred_path = $flow_keys_path."/".$drive_cred_json.".json";
                putenv("GOOGLE_APPLICATION_CREDENTIALS=$cred_path");
                
                $client = new \Google\Client();
                $client->setAuthConfig($cred_path);
                $client->addScope($scopes);
                
                $service = new \Google\Service\Drive($client);
                
                // Now lets try and send the metadata as well using multipart!
                $file = explode("/", $file_path);
                $upload_file_name = explode(".", $file[sizeof($file)-1]);
                $sub_folder_id = $this->create_folder($service, $share_folder_id, $prvdr_folder_name, $accno_folder_name);
                $upload_file = new \Google\Service\Drive\DriveFile();
                $upload_file->setName($upload_file_name[0]);
                $upload_file->setParents([$sub_folder_id]);
                $file_ext_arr = ['xls', 'xlsx'];
                $mime_type = (in_array($upload_file_name[1], $file_ext_arr )) ? ('application/vnd.ms-excel') : ('text/csv');            
                $res = $service->files->create($upload_file, [
                                                            'data' => file_get_contents($file_path),
                                                            'mimeType' => $mime_type,
                                                            'uploadType' => 'multipart'
                                                            ]);
                $file_id = $res->id;
        }catch(Exception $e) {
                
            $exception_msg = $e->getMessage();
            $trace = $e->getTraceAsString();
        } 
        
        return ["folder_id" => $sub_folder_id, "file_id" => $file_id, "exception" => $exception_msg, "trace" => $trace];
        
    }
        
    private function create_folder($service, $share_folder_id, $prvdr_folder_name, $accno_folder_name){
    
        $drive_new_folder = new \Google\Service\Drive\DriveFile();
        $prvdr_folder_id = $this->check_folder_exists($service, $prvdr_folder_name);
        if(isset($prvdr_folder_id) and !is_object($prvdr_folder_id)){
            $accno_folder_id = $this->check_folder_exists($service, $accno_folder_name);
            if(isset($accno_folder_id) and !is_object($accno_folder_id)){
                return $accno_folder_id;
            }else{
                return $this->create_non_exists_folder($service, $drive_new_folder, $prvdr_folder_id, $accno_folder_name);
            }
        }
        else{
            $prvdr_folder_id = $this->create_non_exists_folder($service, $drive_new_folder, $share_folder_id, $prvdr_folder_name);
            $accno_folder_id = $this->create_non_exists_folder($service, $drive_new_folder, $prvdr_folder_id, $accno_folder_name);
            return $accno_folder_id;
        }
    }
        
    private function check_folder_exists($service, $child_folder_name){

        // if folder exist
        $folder_list = null;
        $folder_list = $service->files->listFiles(["q" => "name='{$child_folder_name}' and trashed=false"]);
        if(isset($folder_list[0]['id'])){
            $folder_rights = $service->files->get($folder_list[0]['id'], array("fields" => "*"));
            if($folder_rights['permissions'][0]['role'] == 'writer'){
                return $folder_list[0]['id'];
            }  
        }else{
            return $folder_list;
        }
    }

    private function create_non_exists_folder($service, $drive_new_folder, $parent_folder_id, $child_folder_name){

        // if folder doesn't exist
        $drive_new_folder->setName($child_folder_name);
        $drive_new_folder->setMimeType('application/vnd.google-apps.folder');
        if(!empty($parent_folder_id)){
            $drive_new_folder->setParents([$parent_folder_id]); 
        }
        $folder_id = $service->files->create($drive_new_folder);
        return $folder_id['id'];
    }
}