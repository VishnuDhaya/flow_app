<?php
namespace App;
use Image;
use Log;
use DB;
use File;
class ProcessImage {
	public function __invoke(){
		$this->base_path = "/usr/share/nginx/html/flow_api/public/";
		$this->middle_path = "images/UGA/";
		$this->src_path = $this->base_path . $this->middle_path;
		#$this->dest_path = '/usr/share/flow_storage/files/UGA/';
		$this->dest_path = '/var/miru_share/PROJECTS/storage/files/UGA/';
        $this->process_image(); 

    }
	public function process_image(){
		
		$borrower_folder_arr = ['photo_biz_lic', 'photo_shop'];
		$person_folder_arr = ['photo_national_id', 'photo_pps', 'photo_selfie'];

		foreach ($borrower_folder_arr as $folder) {
			Log::warning("Processing" . $folder);
			$photos = DB::select("select cust_id as id, $folder from borrowers where $folder IS NOT NULL");
			$this->process_photos("borrowers", $photos, $folder);

		}

		foreach ($person_folder_arr as $folder) {
			Log::warning("Processing" . $folder);
			$photos = DB::select("select id, $folder from persons where $folder IS NOT NULL");
			$this->process_photos("persons", $photos, $folder);

		}
		
	}

	private function process_photos($type, $photos, $folder){
		foreach ($photos as $photo) {
			$src_file = $this->src_path.$folder. '/'. $photo->{$folder};
			if(File::exists($src_file)){
				$this->move_file($src_file, $type, $photo->id, $folder,  $photo->{$folder});
			}else{
				if($type == 'borrowers'){
					DB::update("update borrowers set $folder = null where cust_id = '{$photo->id}'");
				}else if($type == 'persons'){
					DB::update("update persons set $folder = null where id = {$photo->id}");
				}
			}
			
		}

	}

	private function move_file($src_file, $type, $id, $folder, $file_name){
		$dest_path = $this->dest_path.'/'. $type . '/'. $id. '/'. $folder.'/';
		create_dir($dest_path);	   	       
		$dest_file_path = $dest_path.DIRECTORY_SEPARATOR.$file_name;
	    File::copy($src_file, $dest_file_path);

	    	
	    
	    Log::warning("src_file : " . $src_file);
	    Log::warning("dest_file_path : " . $dest_file_path);
		$image_obj = Image::make($dest_file_path);
		resize('l', $image_obj, $dest_path, $file_name);
		resize('m', $image_obj, $dest_path, $file_name);
		resize('s', $image_obj, $dest_path, $file_name);

		
	}
}

