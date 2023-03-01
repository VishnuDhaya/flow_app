<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Services\FileService;
use App\Services\Mobile\RMService;
use App\Repositories\SQL\CommonRepositorySQL;
use Log;
use File;


class FileController extends ApiController
{
	public function upload_file(Request $req){
		
	    try{
	        $data = $req->data;

	        #$file_name = $data['file_name'];

	        $file_serv = new FileService();
	        $resp = $file_serv->create_file_from_data_url($data);
	        
	        #$file_url = $host.$file_folder.DIRECTORY_SEPARATOR.$file_name;
	        /*$thumbnail_data = null;
	        if($file_det['file_type'] == "image"){
	           $thumbnail_data = $this->get_thumbnail_data($file_path.DIRECTORY_SEPARATOR.$file_name);
	        }*/

	        ##$response =  ["file_rel_path" => file_rel_path('images') ,  "file_name" => $resp['file_name'], "folder_path" => $resp['file_folder'], "message" => $resp['resp_msg'], 'file_type' => $resp['file_type'] ];

	    
	        /*if($file_type == "text"){
	            $response =  ["file_url" => $file_url, "file_name" => $file_name, "folder_path" => $file_folder];
	        }*/
	        return $this->respondData($resp);
	    }
	    catch (FlowCustomException $e) {
	            throw new FlowCustomException($e->getMessage());
	    }
   
  	}

  	 public function remove_file(Request $req){
        $data = $req->data;

        $file_serv = new FileService();
        $resp = $file_serv->remove_file($data);

        
        if($resp['is_in_folder' ]== 1 || $resp['is_in_table'] == 1){
            return $this->respondWithMessage("File removed successfully");
        
        }else{
            return $this->respondWithError("No such file exists.");
        }

        
        
    }

    public function get_file_rel_path(Request $req){
    	$data = $req->data;	
       
    	$resp['file_rel_path'] = separate(['files', get_file_rel_path($data['entity_code'], $data['file_of'])]);
    	return $this->respondData($resp);
    }

  	private function get_thumbnail_data($file_path){
    	return (string) Image::make($file_path)->resize(450 , 600)->encode("data-url");
  	}

	  public function extract_text(Request $request){
        $data = $request->data;
        $data["file_data_type"] = "data-url";
        $rm_serv = new RMService();

        $result = $rm_serv->extract_text_details_from_card($data);
        
        if(isset($result) && array_key_exists("err_msg",$result)){
            return $this->respondWithError($result['err_msg']);
        }
        else{
            return $this->respondData($result);
        }
            

    }
}