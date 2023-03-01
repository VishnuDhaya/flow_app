<?php
namespace App\Services;
use Log;
use Image;
use File;
use Illuminate\Support\Str;
use App\Repositories\SQL\CommonRepositorySQL;

class FileService{

	public function __construct()
	{
		$this->country_code = session('country_code');
    $this->allowed_file_types = ["png" , "jpg" , "jpeg", 'pdf', 'xls', 'xlsx', 'xlsm', 'csv'];
	}

	public function create_file_from_data_url($data){

        $file_det = $this->get_file_details($data);
        
        $file_det['file_data'] = $data['file_data'];
        $result = $this->save_file($file_det);
        
        if(array_key_exists('sign_of', $data)){
          $file_name = $data['sign_of'].'_file_name';
        }
        else{
          $file_name = 'file_name';
        }

        return ['resp_msg' => $result['resp_msg'] ,$file_name => $result['file_name'],'file_rel_path' => $file_det['file_rel_path'], 'original_file_name' => $file_det['original_file_name']];
        //return ['resp_msg' => $resp[0],'file_name' => $resp[1],'file_rel_path' => $file_det['file_rel_path']];
	}

    public function remove_file($data){
        

        $file_of = $data['file_of'];
        $file_name = $data['file_name'];
        $file_rel_path = $data['file_rel_path'];
        $ctry_code = $this->country_code;
        $is_in_table = 0;
        $is_in_folder = 0;

        $common_repo = new CommonRepositorySQL();
        if(isset($data['entity_id']) && isset($data['entity_type'])){
            $table_file_name = $common_repo->check_if_file_exists($data['entity_id'],$data['entity_type'],$file_of);  
            
            if($table_file_name != null && $table_file_name == $file_name){
                
                $is_in_table = 1;
                $common_repo->remove_file_frm_table($data['entity_id'],$data['entity_type'],$file_of);    
            }
        
        }

       
        //$destination_path = public_path().$file_rel_path.DIRECTORY_SEPARATOR.$file_name;
        //$destination_path = flow_file_path().$file_rel_path.DIRECTORY_SEPARATOR.$file_name;
        $destination_path = flow_storage_path($file_rel_path);
       
        if(File::exists(flow_storage_path($file_rel_path.DIRECTORY_SEPARATOR.$file_name))){
            $is_in_folder = 1;// TO DO
        }
            
        if($is_in_folder){
            #File::cleanDirectory($destination_path); 
            File::delete(File::glob($destination_path.DIRECTORY_SEPARATOR.'*'.$file_name));
        }   

        return  ["is_in_folder" =>$is_in_folder,"is_in_table" => $is_in_table];
       

    }

	private function get_file_details($data){
        
        $original_file_name = explode('.' , $data['file_name']);
        $file_of = $data['file_of'];  
        $req_file_type = $data['file_type'];
        $type = explode('/' , $req_file_type);
        $parent_folder = null;//$type[0];
        $filename_ext = get_ext($data['file_name']);
        $file_ext = $type[1];
        $ctry_code = $this->country_code;
        
        $entity_code = isset($data['entity_code'])? $data['entity_code'] : null;
    
        //$allowed_file_types = ["png" , "jpg" , "jpeg", 'pdf', 'xls', 'xlsx'];
        

        if($data['file_data_type'] != "data-url"){
           thrw("Invalid file data : {$data['file_data_type']}"); 
        } 
       
        if($file_of == "cust_txn_file")
        {
          $valid_exts = ['xls', 'xlsx'];
        }else{
          $valid_exts = $this->allowed_file_types;
        }


        if(!(in_array($file_ext, $valid_exts) 
                || in_array($filename_ext, $valid_exts)) ) {
         thrw("Invalid file type : {$file_ext}");
        }    

        if($file_ext == "pdf"){
            $parent_folder = $file_ext;
        }
       
        
        if($file_of == "agreement"){
          $aggr_doc_id = $data['aggr_doc_id'];  
          $file_name = $aggr_doc_id.'.'.$file_ext;
          
        }
        else if($file_of == "agreement_signature"){
           
            $aggr_doc_id =$data['aggr_doc_id'];  
            $file_name = $data['sign_of']."_".$aggr_doc_id.'.'.$file_ext;
            
        } 
        else if($file_of == "consent_signature"){
            $file_name = $data['lead_id'].'_'.time().'.'.$file_ext;
        }else if($file_of == "cust_txn_file"){
          $file_name = time().'.'.$filename_ext;
        }else{
            $file_name = time().'.'.$file_ext;
        }
        
        $file_rel_path = get_file_rel_path($entity_code, $file_of, $parent_folder);
        
        $file_path = flow_file_path().$file_rel_path;
        
        return ['file_name' => $file_name, 'file_path' => $file_path, 'file_rel_path' => separate(["files", $file_rel_path]) , 'file_of' => $file_of, 'type' => $type, 'original_file_name' => $original_file_name[0]];
  	}

  	private function save_file($file_det){
      $file_type = $file_det['type'][0];
	    $file_name = $file_det['file_name'];
	    $file_path = $file_det['file_path'];  
	    $file_data = $file_det['file_data'];
      $file_of = $file_det['file_of'];	  
	    $file_ext = $file_det['type'][1]; 
	   	//$file_name = time()."_o.".$file_ext;
	    create_dir($file_path);
	    
      
       if($file_ext != 'png' && Str::contains($file_of, "logo")){
        $file_name = get_filename($file_name).'.png';
        $file_det['file_name'] = $file_name;
       }
       $resp_msg =  $this->save_data_as_file($file_path , $file_name, $file_data);
       if($file_type == "image"){
         $this->resize_image($file_det);
       }
	   
	    return ['resp_msg' => $resp_msg, 'file_name' => $file_name];
  	}


    private function resize_image($file_det){
      $file_name = $file_det['file_name'];
      $file_of = $file_det['file_of'];
      $file_ext = $file_det['type'][1];
      $file_path = $file_det['file_path'];  
      $file = $file_path.DIRECTORY_SEPARATOR.$file_name;

      $image_obj = Image::make($file);

      /*if(Str::contains($file_of, "logo") && $file_ext != "png"){
          $image_obj =  $image_obj->encode('png',100);
      }*/
       

       if($file_of == "agreement_signature" || $file_of == "consent_signature" ){
      
          $image_obj->resize(100,50);
          $image_obj->save($file);
       }

       else if($file_of == "agreement" && $image_obj->width() > $image_obj->height()){
          $image_obj->rotate(90);
          $image_obj->save($file);
       }
       else if(Str::contains($file_of, "logo")){
          convert_to_logo('m', $image_obj, $file_path, $file_name);
          convert_to_logo('s', $image_obj, $file_path, $file_name);
          convert_to_logo('t', $image_obj, $file_path, $file_name);
       }
       else{
            resize('l', $image_obj, $file_path, $file_name);
            resize('m', $image_obj, $file_path, $file_name);
           
            resize('s', $image_obj, $file_path, $file_name);
       }
    }

  	private function save_data_as_file($file_path , $file_name , $file_data){
       
        $file_data = explode(',',$file_data);
        $file_data = str_replace(' ', '+', $file_data[1]);
        $file_data = base64_decode($file_data);

        $result = file_put_contents($file_path.DIRECTORY_SEPARATOR.$file_name, $file_data);
        if(!$result){
            thrw("Unable to save during upload");
        }else{
            return "File attached successfully.";
        }
        /*else{
            thrw("File uploaded successfully.Please submit the application.");
        }*/
  	}
}
