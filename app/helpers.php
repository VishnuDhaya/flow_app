<?php
use App\Consts;
use App\Mail\FlowCustomMail;
use App\Models\AddressConfig;
use App\Models\ApiRequest;use App\Models\ApiResponse;use App\Models\FlowApp\AppUser;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\FieldVisitRepositorySQL;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use \PhpOffice\PhpSpreadsheet\Shared;
use Illuminate\Support\Facades\Cache;
use App\Services\Support\CacheService;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use Carbon\CarbonInterface;
use Symfony\Component\Process\Process;
use App\Models\Vendor;
use App\Services\Vendors\Whatsapp\WhatsappWebService;

	function get_header($http_origin){ 
        /*$split_http_origin = explode("/", $http_origin);
         $http_origin_ip = explode(":", $split_http_origin[2])[0];
        $local_ip_subnet = substr($http_origin_ip, 0 , 7);
        $headers["Content-type"] = "application/json";
   
        if($local_ip_subnet == "192.168" || $http_origin_ip == 'localhost' || $http_origin_ip = ''){
            $headers["Access-Control-Allow-Origin"] = $http_origin;
       }*/
         $headers["Access-Control-Allow-Origin"] = $http_origin;
       //Log::warning($headers);
       return $headers;

    }

   	function get_usd($value, $forex = null, $currency_code = null){	
   		if($forex == null){
   			$forex = config("app.1_usd_in_$currency_code");
   		}
		return $value / $forex;
	}
    function setPHPTimeZone($timezone){
              
        //Log::warning($user);
       	$php_time_zone = data_value($timezone , "*", "time_zone");
        date_default_timezone_set($php_time_zone);
        
        return $php_time_zone;
              
     }

     function data_value($data_code, $country_code = '*', $data_key = null){
     	$criteria["data_code"] = $data_code;

     	$data_key ? $criteria["data_key"] = $data_key : null;
     	$criteria["country_code"] = $country_code;
     	
     	$data = (new CommonRepositorySQL())->get_master_data($criteria, true);
    
    	if(!empty($data)){
    		 return $data[0]->data_value;	
    	}else{
    		return $data_code;
    	}
        
     }

    function increment_json_attr($json_col, $json_field){
    	$add_json = "{\"$json_field\" : 1}" ;
    	$update_json_attr = "IF(JSON_EXTRACT($json_col, '$.{$json_field}') IS NULL, 1 , JSON_EXTRACT($json_col, '$.{$json_field}') + 1)";
    	$update_json = "JSON_SET($json_col, '$.{$json_field}' , $update_json_attr)";
    	
    	$str =  "IF($json_col IS NULL, '$add_json' , $update_json)";
    	return DB::raw($str);
    }

    function update_json_attr($json_col, $json_field, $new_value){
    	 $add_json = "{\"$json_field\" : $$new_value}" ;
    	 $update_json = "JSON_SET($json_col, '$.{$json_field}' , $new_value)";
    	
    	$str =  "IF($json_col IS NULL, '$add_json' , $update_json)";
    	return DB::raw($str);
    }

    function percent($value, $divisor, $pc_sym = true, $precision = 2){
    	$pc = 100 * div($value, $divisor);
    	$pc = round($pc, $precision);
    	return $pc_sym? $pc . " %" : $pc;
    }
	function div($value, $divisor){
		return $divisor == 0 ?  0 : $value / $divisor;
	}
	function move_entity_file($from_entity_code,$to_entity_code, $entity, $to_entity_id, $entity_photo_key,$from_entity_id = 'tmp'){
		if($entity[$entity_photo_key] == null || is_string($entity[$entity_photo_key])){
			$file_name = $entity[$entity_photo_key];
			$entity[$entity_photo_key] = ['value' => $file_name];
		}
		if(array_key_exists($entity_photo_key, $entity) && $entity[$entity_photo_key]['value'] != null){
			$source_path = separate([flow_file_path(),session('country_code'),$from_entity_code,$from_entity_id,$entity_photo_key]);
			$dest_path = separate([flow_file_path(),session('country_code'), $to_entity_code, $to_entity_id, $entity_photo_key]);
			mount_file($source_path, $dest_path, $entity[$entity_photo_key]['value']);
			$file_path = separate(["files",session('country_code'), $to_entity_code, $to_entity_id, $entity_photo_key]);
			$path = $file_path.DIRECTORY_SEPARATOR.$entity[$entity_photo_key]['value'];
		}else{
			$path = null;
		}
		return $path;

			
	}

    function mount_entity_file($entity_code, $entity, $entity_id, $entity_photo_key){
	    if(array_key_exists($entity_photo_key, $entity) && $entity[$entity_photo_key] != null){
	    	//if($entity_photo_key == "logo"){
	    	if(Str::contains($entity_photo_key, "logo")){
	    		$source_path = separate([flow_file_path(),$entity['country_code'], $entity_code,"tmp" , $entity_photo_key]);	    		
	    		//$source_path = separate([flow_file_path(),$entity['country_code'],"tmp",$entity_photo_key]);
	    		//$file_ext = explode(".", $entity[$entity_photo_key]);
	    		//$entity[$entity_photo_key] = $entity_id.".".$file_ext[1];
	    		
	    	}else{
				//$source_file = separate([flow_file_path(),$entity['country_code'],$entity_code,"tmp",$entity_photo_key,$entity[$entity_photo_key]]);
				$source_path = separate([flow_file_path(),$entity['country_code'],$entity_code,"tmp",$entity_photo_key]);
			}

			$dest_path = separate([flow_file_path(),$entity['country_code'], $entity_code, $entity_id, $entity_photo_key]);
		
			mount_file($source_path, $dest_path, $entity[$entity_photo_key]);
	    }

  }

    function mount_file($source_path, $dest_path, $dest_file_name){

    	if(File::exists($source_path)){
			
			create_dir($dest_path);
               $files = File::allFiles($source_path);
               foreach($files as $file){
					$file_name = $file->getFilename();
					if(Str::contains($file_name,$dest_file_name)){
						File::copy($source_path.DIRECTORY_SEPARATOR.$file_name, $dest_path.DIRECTORY_SEPARATOR.$file_name);
						if(File::exists($dest_path.DIRECTORY_SEPARATOR.$file_name)){
							if(Str::contains($source_path, 'tmp')){
								File::delete(File::glob($source_path.DIRECTORY_SEPARATOR.$file_name));
							}
						}else{
							thrw("Unable to move/copy files. Please try again");
						}
					}
               }
               #File::copyDirectory($source_path, $dest_path);
               #File::delete(File::glob($source_path.DIRECTORY_SEPARATOR.'*'.$dest_file_name));

	        #File::cleanDirectory($source_path);
	        //File::delete($source_path);
	        //File::copyDirectory($source_file, $dest_path.DIRECTORY_SEPARATOR.$dest_file_name);
      }
    }


  function get_arr_val($arr, $key){
  	return array_key_exists($key, $arr)? $arr[$key] : null;
  }

  function meta($key){
  		return starts_with($key, "meta_");
  }
    function record_name($key){
    	
    	   $record_names = [
    	   			'ontime_loans' =>  'Ontime FAs',
    				'late_1_day_loans' => "1 Day Late",
    				'late_2_day_loans' => "2 Days Late",
    				'late_3_day_loans' => "3 Days Late",
    				'late_3_day_plus_loans' => ">3 Days Late",
    				'default' => "Default",
    				'late_loans' => "Total Late FAs",
    				'tot_loans' => "Total Completed FAs",
					];
    	
    	if(array_key_exists($key, $record_names)){
    		return $record_names[$key];	
    	}else{
    		return $key;
    	}
    	
    }

    function sum_of_values($obj){
    	$sum = 0;
    	foreach ($obj as $key => $value) {
    		$sum += $value;
    	}
    	return $sum;
    }

    function get_ext($file_name){
    	$split_file_name = explode('.' , $file_name); 
    	$arr_size = sizeof($split_file_name);  
    	if($arr_size >= 2){
     	   return $split_file_name[$arr_size - 1];
    	}
    }


    function get_filename($file_name){
    	$split_file_name = explode('.' , $file_name); 
    	$arr_size = sizeof($split_file_name);  
    	if($arr_size >= 1){
     	   return $split_file_name[0];
    	}
    }
   
	function compile_sms($template, $data) {
		foreach(array_keys($data) as $key){
			if(is_array($data[$key]) || is_object($data[$key])){
				unset($data[$key]);
			}
		}
		
		return __("sms.$template", $data, config('app.sms_lang')[session('country_code')]);
	}

    function get_country_code($data){

	    $country_code = null;
	  	if(isset($data['country_code'])){
	  		$country_code =  $data['country_code'];
	  	}else{
	  		foreach (array_values($data) as  $item) {
	  			if(isset($item['country_code'])){
	  				$country_code =  $item['country_code'];
	  				break;
	  			}	
	  		}
	  	}

	  	if($country_code == null){
	  		$country_code = "*";
	  	}
	  	return $country_code;
    }

   function format_date($date, $format = "d M Y")
   {
    	$original_date = strtotime($date);
        return date($format,$original_date);
   }
	function datetime_db($date = null){
		return date(Consts::DB_DATETIME_FORMAT);
	}

	function date_db(){
		return date(Consts::DB_DATE_FORMAT);
	}

	function date_ui(){
		return date(Consts::UI_DATE_FORMAT);
		
	}

	function datetime_ui(){
		return date(Consts::UI_DATETIME_FORMAT);
		
	}
	
	function float_date(){
		return date(Consts::FLOAT_ID_DATE_FORMAT);
	}

	function aggr_date(){
		return date(Consts::AGGR_DATE_TIME_FORMAT);
	}

	function loan_doc_id($loan_appl_doc_id){
		//var_dump($loan_appl_id);
		//$loan_id = explode("-", $loan_appl_id);
		//return end($loan_id);
		return Str::replaceFirst('APPL-', '', $loan_appl_doc_id);
	}

	function addDays($days, $date = null, $format = Consts::DATETIME_FORMAT){
		if($date == null)
		{ 
			$date = Carbon::now();
		}else{
			$date = parse_date($date,$format);	
		}
		return $date->addDays($days);
	}

	function parse_date($date_str, $format = Consts::DB_DATE_FORMAT){
	
		if(strlen($date_str) == 19){
			$format = Consts::DB_DATETIME_FORMAT;
		}
	
		return Carbon::CreateFromFormat($format, $date_str);	
		
	}
	
	
	function getPenaltyDate($due_date, $now = null){

		if($now == null){
			$now = Carbon::now();
		}
		

		$due_date = Carbon::parse($due_date);
		$due_date = $due_date->startOfDay();
		$due_date_duration = Carbon::parse($due_date)->diffInDays(Carbon::parse($now),false);
		$holidays = CarbonPeriod::create($due_date,$now)->countHolidays();
		$penalty_days = $due_date_duration - $holidays;
		if($penalty_days > 0){
			return $penalty_days;	
		}
			
		return 0;
	}


	function get_visit_selfie_path($visit_ids){
		$visit_ids = json_decode($visit_ids,true);
		$visit_id = null;
		if(is_array($visit_ids) && sizeof($visit_ids) > 0){
			$visit_id = max($visit_ids);
		}else if(is_int($visit_ids)){
			$visit_id = $visit_ids;
		}
		$visit_data = (new FieldVisitRepositorySQL)->find($visit_id,['photo_visit_selfie']);
		if($visit_data && $visit_data->photo_visit_selfie){
			return get_file_path("cust_reg_checkin",$visit_id,"photo_visit_selfie")."/".$visit_data->photo_visit_selfie;
		}else{
			return null;
		}
	}
	


	function getDueDate($loan_duration, $disburse_date = null, $format = Consts::DATETIME_FORMAT){
		if($disburse_date == null){
			$start_date = Carbon::now();
		}else{
			$start_date = parse_date($disburse_date,  $format );		
		}
		
		$end_date = addDays($loan_duration, $disburse_date);
		$holidays = null;
		while($holidays !== 0){
			$holidays = CarbonPeriod::create($start_date, $end_date)->countHolidays();
			if($holidays !=0){
				$start_date = $end_date->copy()->addDay();
				$end_date->addDays($holidays);
			}
		}
		return $end_date->endOfDay(); 
	}

	function isHoliday($date){

		$date_str = $date->format(Consts::DB_DATE_FORMAT);
		if($date->isSunday() 
			|| in_array($date_str,Consts::HOLIDAYS[session('country_code')]) ){
			return true;
			
		}
	}

	CarbonPeriod::macro('countHolidays', function(){
		return $this->filter('isHoliday')->count();
	} );

	CarbonPeriod::macro('countSundays', function(){
		return $this->filter('isSunday')->count();
	} );

	function isWorkingDay($date){
		return !isHoliday($date);
	}
	
	function m_array_filter(&$array){
		foreach($array as $key => $value) {
		   if($value == "" || $value == null || $value === -1) {
               unset($array[$key]);
		   }
        }
	}

	function has_any($array1,$array2){
		foreach ($array2 as $arr){
			if(in_array($arr , $array1)){
				return true ;
			}
		}
		return false;
		
	}
	function time_diff_since($start_time){
			
			$call_start_time = carbon::parse($start_time);
			$time_now = carbon::now();
			$sec_diff = $time_now->diffInSeconds($call_start_time);
			$min_diff = $time_now->diffInMinutes($call_start_time);
			$sec_diff = $sec_diff - $min_diff * 60;
			$result = ["min_diff" => $min_diff, "sec_diff" => $sec_diff ];

			return $result;
			
	}
	function time_diff_between($start_time,$end_time){

				$start_time=Carbon::parse($start_time);
				$end_time=Carbon::parse($end_time);
				$dur_secs = $start_time->diffInSeconds($end_time);
				$dur_human = CarbonInterval::seconds($dur_secs)->cascade()->forHumans(
				);
				$result = ["dur_secs" => $dur_secs,"dur_human" => $dur_human];

				return $result;
	}

	function full_name($person, $separator = " "){
		$full_name = "";
		if($person->first_name != null || $person->first_name != ""){
			$full_name .= $person->first_name.$separator;
		}
		if(isset($person->middle_name) && ($person->middle_name != null || $person->middle_name != "")){
			$full_name .= $person->middle_name.$separator;
		}
		if($person->last_name != null || $person->last_name != ""){
			$full_name .= $person->last_name.$separator;
		}	
		return rtrim($full_name,  $separator);
	}
	
	function status_ctn($data_status, &$field_values = null){
		if(is_array($data_status)) {
			if(isset($data_status['status'])){
				$status = $data_status['status'];
			}else{
				$status = null;
			}
		}else{
			$status = $data_status;
		}

		if($status){
			if($field_values){
				$field_values[] = $status;
				if(sizeof($field_values) == 1){
					return Consts::STATUS_CTN;
				}else if(sizeof($field_values) > 1){
					return " and " . Consts::STATUS_CTN;
				}
			}
		}
			return "";
		
	}


	function country_ctn($country_code_data, &$field_values = null){
		$country_code = null;
		if(is_array($country_code_data)) {
			if(isset($country_code_data['country_code'])){
				$country_code = $country_code_data['country_code'];
			}else{
				$country_code = null;
			}
		}else if($country_code_data){
			$country_code = $country_code_data;
		}else if(session('country_code')){
			$country_code = session('country_code');
		}

		if($country_code && $country_code != "*"){
			if($field_values){
				$field_values[] = $country_code;
				if(sizeof($field_values) == 1){
					return Consts::COUNTRY_CTN;
				}else if(sizeof($field_values) > 1){
					return " and " . Consts::COUNTRY_CTN;
				}
			}
		}
			return "";
		
	}

	function full_addr($addr, $separator = ", "){

		$text = "";
		foreach($addr as $key => $val) {
			$value = ucwords($val);
			
			if($value == null || $value == ""){
				continue;
			}
			$text .= $value.$separator;
		}
		return rtrim($text,  $separator);
	};

	function short_addr($addr){
		unset($addr['id']);
		unset($addr['country_code']);
		unset($addr['gps']);
		$reversed_arr = array_reverse($addr);
		$short_addr = [];

		foreach ($reversed_arr as $key => $val) {
			if($val){
				$short_addr[] = $val;
			}
			if(sizeof($short_addr) == 3){
				break;
			}			
		}

		$short_addr = implode(", ", $short_addr);
		
		return $short_addr;

	}


	function merge_obj(&$final , $temp){
		foreach($temp as $key => $value) {
			$final->{$key} =  $value;
		}
	}

	function pluck($obj_arr, $field){
		$value_arr = array();
		
		foreach($obj_arr as $obj){
			$value_arr[] = $obj->{$field};
			//$final->{$key} =  $value;
		}
		return $value_arr;
	}	

	function loan_appl_status(){
		return code_arr_to_dd_arr(Consts::LOAN_APPL_STATUS);		
	}

	function loan_status(){
		return code_arr_to_dd_arr(Consts::LOAN_STATUS);		
	}

	function code_arr_to_dd_arr(){
		$final_arr = array();
		foreach(Consts::LOAN_APPL_STATUS as $status){
			$arr["id"] = $status;
			$arr["name"] = Str::title($status);
			$final_arr[] = $arr;
		}
		return $final_arr;
	}

	 function thrw($msg, $response_code = 9999 ,$code = null){
	 	throw new App\Exceptions\FlowCustomException($msg, $response_code, $code);
	 }
	function err($msg, $http_code = 500){
		throw new App\Exceptions\FlowSystemException($msg, $http_code);
	}

	 function skip_record($msg){
	 	throw new App\Exceptions\SkipRecordException($msg);
	 }

	 function thrw_borrower_duplicate($borrowers, $dup_field, $cust_id = 'null'){
		if(sizeof($borrowers)  > 0  ){
			if($cust_id && sizeof($borrowers) == 1 && $borrowers[0]->cust_id == $cust_id){
				return;
			}
            $cust_ids = pluck($borrowers, "cust_id");
            $cust_ids = implode(', ',$cust_ids);
            throw new App\Exceptions\FlowCustomException("Other profile [$cust_ids] already exists with the entered ".dd_value($dup_field));
        }
	 }
	 

	function cachee($country_code, $key){
		$cache_key = "{$country_code}_{$key}";
		if(!Cache::has($cache_key)){
			CacheService::load();
			CacheService::load_key($country_code, $key);
		}
		return Cache::get($cache_key);
		/*return Cache::rememberForever("{$country_code}_{$key}",  
					function () {
									CacheService::get( $country_code, $key);
										}
									);
									*/
	}

	function user(){
		//return auth()->user();
		return app('Illuminate\Contracts\Auth\Guard')->user();
	}

	function extract_array($source, $fields){

		$new_arr = array();
		foreach($fields as $field){
			$new_arr[$field] = $source[$field];
		}		
		return $new_arr;
	}


	function compile_sms_old($message, $data, $flow_prefix = true){
		//dd($data);
		
		foreach(array_keys($data) as $key){
			//dd($data[$key]);
			if(!is_array($data[$key])){
				$message = str_replace(':'.$key,$data[$key],$message);
			}

		}

		if($flow_prefix){
			$message = "[FLOW] ".$message;
		}
	       
		return $message;

}

	function csv($array, $en = "'"){
		$string = '';
		Log::warning($array);
		foreach ($array as $arr){
			Log::warning($arr);
			if (is_array($arr)){
				foreach ($arr as $key =>$val){
					$string = "$string$en$val$en";
					
					
				}
			}else{
				$string = "$string$en$arr$en";
			}
		}
		$string = str_replace("''","','",$string);
		return $string;
		// return implode(",", array_map(function($item) use($en){ return "$en$item$en";}, $array) );
		// $items = array();
	}

function resize($size, $image_obj, $file_path, $file_name){
	$w = $image_obj->width();
	$h = $image_obj->height();
	/*if($w > $h){
		$orientation = "landscape";
	}else{
		$orientaion = "portrait";
	}*/

	$new_size = get_new_size($size, $w, $h);
	if($new_size){
		$image_obj->resize($new_size[0], $new_size[1]);
	}
	//$image_obj->save($new_size."_".$file);
	$new_file_name = $size."_".$file_name;
	$image_obj->save($file_path.DIRECTORY_SEPARATOR.$new_file_name);
	//return $orientation;
	
}

function get_new_size($new_size, $width, $height){
	$aspect_ratio = $width / $height;
	$longer_side_size = ["l" => 768, "m" => 512, "s" => 256];
	if($width > $longer_side_size[$new_size] || $height > $longer_side_size[$new_size]){
		if($width > $height){
			$new_w = $longer_side_size[$new_size];
			$new_h = $new_w / $aspect_ratio;
		}else{
			$new_h = $longer_side_size[$new_size];
			$new_w = $new_h * $aspect_ratio;
		}
		return [$new_w, $new_h];
	}
	
}



function convert_to_logo($new_size, $image_obj, $file_path, $file_name){
	
	//$image_obj = Image::make($file);
	/*$w = $image_obj->width();
	$h = $image_obj->height();
	*/
	//$image_obj->save($file);    //save img in original size

	/*$shape = check_image($image_obj); # rec / sq
	if($shape == "port"){
		thrw("Portrait logo : Unsupported shape");
	}*/
	if($new_size == 'm'){
		$size = 90;		// Agreement PDF
	}else if($new_size == 's'){
		$size = 48;     // Menu Bar
	}else if($new_size == 't'){
		$size = 32;		// Select DP List
	}
	

	resize_to_logo($new_size, $image_obj, $size, $file_path, $file_name);
	
	//$image_obj->save($file);
	
}

function check_image($image_obj){
	$w = $image_obj->width();
	$h = $image_obj->height();

	$img_ratio = $w / $h;
	if($img_ratio > 1 && $img_ratio > Consts::LOGO_RATIO){
		$shape = "land";
	}else if($img_ratio < 0.75){
		$shape = "port";
		//thrw("Portrait image : Unsupported shape");
	}else{
		$shape = "sq";
	}
	

	return $shape;

}

function resize_to_logo($new_size, &$image_obj, $size, $file_path, $file_name){
	
	$w = $image_obj->width();
	

	if($w > $size){
		$image_obj->resize($size, null, function($con){$con->aspectRatio();});

	}	

	$h = $image_obj->height();
	
	if($h > $size){
		$image_obj->resize(null, $size, function($con){$con->aspectRatio();});

	}	

	/*if($w > $width || $h > $height){
		if($shape == "land"){
			$width_ratio = $w / $width; 
		}else{
			$width_ratio = $w / $height;
		}


		$height_ratio = $h / $height; // 30
		if($width_ratio > $height_ratio){
			$resize_ratio = $width_ratio;	
			
		}else{
			$resize_ratio = $height_ratio;
		}

	
		$new_w = $w / $resize_ratio;
		$new_h = $h / $resize_ratio;
		$image_obj->resize($new_w, $new_h);
	}*/
	
	

	$image_obj->resizeCanvas($size, $size, 'center', false,  array(0,0,0,0));
	$new_file_name = $new_size."_".$file_name;
	$image_obj->save($file_path.DIRECTORY_SEPARATOR.$new_file_name);
	
	//return $new_file_name;
}


/*function resize_to_logo($new_size, &$image_obj, $shape, $width, $height, $file_path, $file_name){
	
	$w = $image_obj->width();
	$h = $image_obj->height();
	if($w > $width || $h > $height){
		if($shape == "land"){
			$width_ratio = $w / $width; 
		}else{
			$width_ratio = $w / $height;
		}


		$height_ratio = $h / $height; // 30
		if($width_ratio > $height_ratio){
			$resize_ratio = $width_ratio;	
			
		}else{
			$resize_ratio = $height_ratio;
		}

	
		$new_w = $w / $resize_ratio;
		$new_h = $h / $resize_ratio;
		$image_obj->resize($new_w, $new_h);
	}
	

	$image_obj->resizeCanvas($width, $height, 'center', false,  array(0,0,0,0));
	$new_file_name = $new_size."_".$file_name;
	$image_obj->save($file_path.DIRECTORY_SEPARATOR.$new_file_name);
	
	//return $new_file_name;
}*/



function trim_comma($string){

	 return rtrim($string, ", ");
	 //Log::warning($trim);
}

 function separate($folders){
    $path = "";
    foreach ($folders as $folder) {
        $path .= DIRECTORY_SEPARATOR.$folder;
    }
    return $path;
  }


	function file_rel_path($root){
		
		return DIRECTORY_SEPARATOR.$root.DIRECTORY_SEPARATOR.$req->data['country_code'].DIRECTORY_SEPARATOR;
	}

	function get_entity_type($file_of){
		if(in_array($file_of, ['photo_consent_letter','guarantor1_doc','guarantor2_doc','lc_doc','photo_witness_national_id','photo_witness_national_id_back','agreement', 'photo_shop', 'photo_biz_lic','photo_new_acc_letter'])){
			return 'borrowers';
		}else if(in_array($file_of, ['photo_national_id', 'photo_pps', 'photo_selfie','photo_national_id_back'])){
			return 'persons';
		}else if($file_of == 'lender_logo'){
			return 'lenders';
		}else if($file_of == 'data_prvdr_logo'){
			return 'data_providers';
		}else if($file_of == 'acc_provider_logo'){
			return 'acc_providers';
		}else if($file_of == "photo_visit_selfie"){
			return 'cust_reg_checkin';
		}else if ($file_of == 'photo_payment_proof' || $file_of == 'photo_disbursal_proof' ||  $file_of == 'photo_reversal_proof' ){
			return 'loan_txns';
		}else if ($file_of == 'photo_statement_proof'){
			return 'acc_stmts';
		}else{
			null;
		}
			
	}
	
function run_py_script($file_name, $args = null){
        $file_name = app_path()."/Scripts/python/$file_name";
    	
    	if($args){
    	    $args = implode(' ', $args);    
    	}else{
    	    $args = '';
    	}
    	ob_start();
        #$file_name = separate([base_path(),$file_name]);
    	Log::warning("COMMAND : python $file_name $args");
    	$resp = passthru("python $file_name $args");
    	$output = ob_get_clean();
    	Log::warning("RESPONSE : " . $output);
    	
        return $output;
    

    }

	function get_all_lead_file_types(){
		$file_upload_tmplts = Consts::LEAD_FILE_UPLOAD_TMPLT;
		$file_of_arr = [];
		foreach ($file_upload_tmplts as $file_upload_tmplt){
			$files_arr = $file_upload_tmplt['files'];
			foreach($files_arr as $file_arr) {
				array_push($file_of_arr, $file_arr['file_of']);
			}
		}
		return array_unique($file_of_arr);
	}
    
  function get_file_rel_path($entity_code, $file_of, $parent_folder = null){
  	$folder_path =  null;
	  $all_lead_file_types = get_all_lead_file_types();
  	/*$req = request();
  	$ctry_code = $req['country_code'];*/
  	$ctry_code = session('country_code');
  	if($file_of == "agreement" || $file_of == "agreement_signature" ){
		$folder_arr = [$ctry_code, "borrowers", $entity_code, $file_of, $parent_folder];
	}else if(in_array($file_of,$all_lead_file_types)){
		$folder_arr = [$ctry_code, "leads", $entity_code,"statements", $file_of, $parent_folder];
	}else if($file_of == "unsigned_consent" || $file_of == "signed_consent" || $file_of == "consent_signature" ){
		$folder_arr = [$ctry_code, "leads", $entity_code, $file_of, $parent_folder];
	}else if($file_of == "master_agreements" || 
			$file_of == "cust_txn_file"){
		$folder_arr = [$ctry_code, $file_of];
	}else if($file_of == "account_holder_name_proof"){
		$folder_arr = [$ctry_code, "leads", $entity_code, $file_of];	
	}else if($entity_code != null){
    	$entity_type = get_entity_type($file_of);
		$folder_arr = [$ctry_code, $entity_type, $entity_code, $file_of];
    }else{
    	$entity_type = get_entity_type($file_of);
        $folder_arr = [$ctry_code, $entity_type, "tmp", $file_of];
    }
    
    //array_unshift($folder_arr, "files");
    $folder_path = separate($folder_arr);
	
    return $folder_path;

  }

  function create_dir($file_path){
    $folder_status = false;
    if(!File::exists($file_path)){
        $folder_status = File::makeDirectory($file_path, 0777, true);
        if(!$folder_status){
            thrw("Unable to create folder $file_path");
        }
    }
  }

  function flow_storage_path($sub_folder){
	$storage_path = env('FLOW_STORAGE_PATH');
  	//$storage_path = env('FLOW_STORAGE_PATH', '/home/ubu/PROJECTS/flow_storage');
  	$storage_path = $storage_path.DIRECTORY_SEPARATOR.$sub_folder;
  	/*if(!File::exists($storage_path)){
  		create_dir($storage_path);
  	}*/
  	//Log::warning($storage_path);
  	return $storage_path;
  
  }


  function flow_file_path(){
  	return flow_storage_path("files");
  }

  function flow_report_path(){
  	return flow_storage_path("reports");
  }

  function get_select_obj_arr($obj_arr, $name_field, $name = null){
  	
  	$new_obj_arr = array();
  	foreach ($obj_arr as $obj) {
  		if($obj->{$name} == null){
  			$new_obj_arr[] = ["obj" => $obj, "name" => $obj->{$name_field}];
  		}else{
  			$new_obj_arr[] = ["obj" => $obj, "name" => $obj->{$name} ."(". $obj->{$name_field} .")" ];
  		}
  		
  	}
 
  	return $new_obj_arr;
  }
  
  function date_formats($date)
  {
	 $txn_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date[1]);
	 $txn_date = $txn_date->format('Y-m-d h:m:s');
	 return $txn_date;
  }
  function is_aggr_valid($valid_from,$valid_upto)
  {
  	$current_date = Carbon::now();
  	$current_date = parse_date($current_date, Consts::DB_DATETIME_FORMAT);
	$valid_from = parse_date($valid_from, Consts::DB_DATETIME_FORMAT);
	if($valid_upto != null){
		$valid_upto = parse_date($valid_upto, Consts::DB_DATETIME_FORMAT);	
	}
	
  	if($current_date >= $valid_from 
  		&& ($current_date <= $valid_upto || $valid_upto == null))
     {
     	return true;
     }
     else
     {
     	return false;
     }
  }

  function get_od_days($due_date, $paid_date, $status){


	    $due_date = Carbon::parse($due_date)->startOfDay();
	    
	    if($due_date && ($status == Consts::LOAN_OVERDUE || $due_date < $paid_date)){
	    	
	    	return Carbon::parse($due_date)->diffInDays($paid_date);
	    }else{
	    	
	        return 0;
	      }

		}

	function calc_perf_eff_date($perf_eff_date){
	
		$perf_eff_date = new Carbon($perf_eff_date);
		$total_days = $perf_eff_date->diffInDays();

		$perf_eff_cutoff_days = config('app.perf_eff_cutoff_days');
		
		$perf_eff_date = $total_days > $perf_eff_cutoff_days ? Carbon::now()->subDays($perf_eff_cutoff_days) : $perf_eff_date;
		return $perf_eff_date;
	}

	
	   function auth_vendor($vendor_code, $service, $cred){
	   		$vendor_cred = config('app.vendor_credentials')[$vendor_code][$service];
	   		$checked = 0;
	   		foreach($cred as $key => $value){
	   			if(array_key_exists($key,$vendor_cred) && $cred[$key] == $vendor_cred[$key]){
	   				$checked++;
	   			}
	   		  
	   		}
	   		if(sizeof($cred) >0 && $checked == sizeof($cred)){
	   			return true;
	   		}else{
	   			return false;
	   		}
	   }
	   
	    
	    #TODO  move this to cache
		function get_country_code_by_isd($isd_code){
		   if($isd_code == '256'){
		   	return 'UGA';
		   }
		   elseif($isd_code == '250'){
			return 'RWA';
		   }
		}


		function confirm_code_alert($country_code){
			return compile_sms_old(Consts::CONFIRM_CODE_ALERT,['shortcode' => config('app.sms_reply_to')[$country_code]], false);
		}

		function get_recovery_otp_info($country_code){
			return compile_sms_old(Consts::RECOVERY_OTP_INFO,['shortcode' => config('app.sms_reply_to')[$country_code]], false);
		}

		function trim_abs_path($file_path){
			$abs_path = env('FLOW_STORAGE_PATH');
			return str_replace($abs_path,'',$file_path);
		}

	
		function get_column_list($table){
			Log::warning($table);
			$record = DB::selectOne("select * from {$table}");
			return array_keys((array)$record);
		}

		function set_country_n_timezone_by_isd($isd_code){
			$country_code = get_country_code_by_isd($isd_code);
			set_app_session($country_code);
		}

		function match_ussd_disb_response_status($ussd_disb_responses, $data, $status){
			foreach ($ussd_disb_responses as $resp){
                if(preg_match("/$resp/", $data['message'])){
                    $data['status'] = $status;
					return $data;
                }
            }
			return $data;
		}

		function set_app_session($country_code){
			session()->put('country_code', $country_code);
			$time_zone = (new CommonRepositorySQL())->get_time_zone($country_code);
			(isset($time_zone)) ? setPHPTimeZone($time_zone) : thrw("Country Undefined");
		}

		function get_user_email($app_user_id){
			$user = AppUser::where('id',$app_user_id)->get(['email']);
			return $user[0]->email;
		}

		
		function get_cust_from_mobile_num($mobile_num, $fields = ['*']){
			$brwr_repo = new BorrowerRepositorySQL;
			$cust_id = $brwr_repo->get_cust_id_from_mobile_num($mobile_num);
            $cust = $brwr_repo->get_record_by('cust_id', $cust_id, $fields);
            return $cust;
    }

		function dd_value($str){
			return preg_replace_callback("/^[a-z]|_[a-z]?/", function ($matches) {
				return strtoupper(str_replace("_"," ",$matches[0]));
			},$str);
		}
		

		function array_remove_values(array $haystack, $needle = null, $only_first = false)
		{
		    if (!is_bool($only_first)) { throw new Exception("The parameter 'only_first' must have type boolean."); }
		    if (empty($haystack)) { return $haystack; }
		
		    if ($only_first) { // remove the first found value
		        if (($pos = array_search($needle, $haystack)) !== false) {
		            unset($haystack[$pos]);
		        }
		    } else { // remove all occurences of 'needle'
		        $haystack = array_diff($haystack, array($needle));
		    }
		
		    return $haystack;
		}

	function check_val_exists($line_data,$key) {
		foreach($line_data as $data){
			$is_valid = Str::contains($data,$key);
			if($is_valid == true){
			  return $is_valid;
			}
		}
    }

    function trim_text_after_token($str,$token){
    	$str = explode($token, $str);
    	return trim($str[1]);
    }

    function text_explode($str,$index){
    	$str = explode(' ', $str);
    	return $str[$index];
    }


    function split_names($names){
    	$first_names = explode(' ', $names);
    	if(count($first_names) > 1) {
          $first_name = array_shift($first_names);
          $middle_name = implode(' ',$first_names);
          return ['first_name' => $first_name,"middle_name" => $middle_name];
      }else if (count($first_names) == 1){
          $first_name = $first_names[0];
          return ['first_name' => $first_name];

      }
    }

	function get_addl_sql_like_name($key,$name){
		$name_arr = explode(' ',$name);
		if(sizeof($name_arr)>1){
			$name = $name_arr[0];
		}
		return "($key like('$name%') or $key like('%$name'))";
	}

    function get_file_path($entity_code,$entity_id,$file_of){

    	$file_rel_path = get_file_rel_path($entity_code,$entity_id);
    	$file_path = separate(["files", $file_rel_path, $file_of]);
    	return $file_path;
    }

    function get_annualized_returns($invested_amount, $cumulative_earning, $alloc_date) {
        $end = new Carbon('last day of last month');
        $date_diff = $end->diffInDays(Carbon::parse($alloc_date));
        $month_diff = $date_diff / 30.42;
        $result = ($cumulative_earning * (12/$month_diff) ) / $invested_amount;
        return number_format($result * 100,2);
    }

    function get_currency_sign($currency_code){
        $sign = null;
        if($currency_code == 'USD'){
            $sign = '$';
        }
        elseif($currency_code == 'EUR'){
            $sign = '€';
        }


        return $sign;
    }

    function get_current_forex($base, $quote){
			$record = DB::selectOne("select forex_rate from forex_rates where base='{$base}' and quote='{$quote}' order by id desc limit 1;");
			return $record->forex_rate;
		}

	function get_forex_by_date($base, $quote, $date_str){
		if($base == $quote){
			return 1;
		}
		$record = DB::selectOne("select forex_rate from forex_rates where base='{$base}' and quote='{$quote}' and date(forex_date) = '$date_str'");
		return $record->forex_rate;
	}

     function get_days_ago($date){
        $date= Carbon::parse($date);
        $current_date = Carbon::now();
        $days_ago = $date->diffInDays($current_date); 
        return $days_ago ;
   }
   function is_assoc_arr($data_arr){
       if(!is_array($data_arr)){
               return false;
       }
       return array_keys($data_arr) !== range(0, count($data_arr) - 1);
    } 

    function is_flow_email($email){
    	$domain = explode("@",$email);
	    if($domain[1] == "flowglobal.net"){
	      return true;
	    }else{
	    	return false;
	    }
    }

	function to_years($days){
        $month_diff = $days / 30.42;
        return ($month_diff/12);

	}

	function send_guzzle_req($url, $req_body) {
		$client = new \GuzzleHttp\Client(); 
		$promise = $client->postAsync($url, [ \GuzzleHttp\RequestOptions::JSON => $req_body, 'http_errors' => false])->then(
			function ($response){
				return $response;
			}
		);
		$response = $promise->wait();
		return $response;
	}

	function send_get_req($url) {
		$client = new \GuzzleHttp\Client(); 
		$promise = $client->getAsync($url)->then(
			function ($response){
				return $response;
			}
		);
		$response = $promise->wait();
		return $response;
	}

	function get_country_from_timezone_str($timezone_str){
	    $timezone = DB::selectOne("select data_code from master_data where data_key = 'time_zone' and data_value = '{$timezone_str}'");
		if ($timezone == null) {
			thrw("Timezone {$timezone_str} not found");
		}

		$country = DB::selectOne("select country_code, isd_code from markets where time_zone = '{$timezone->data_code}'");
		if ($country == null) {
			thrw("Market for the timezone {$timezone->data_code} has not been configured");
		}
		return $country;
  }
   	function gen_cust_id(){
		$lender_code = config('app.lender_code')[session('country_code')];
		$cust_id = (new CommonRepositorySQL())->get_new_flow_id(session('country_code'), 'customer');
		return "{$lender_code}-{$cust_id}";
	}
	function gen_cust_id_frm_mob_num($mobile_num){
		$lender_code = config('app.lender_code')[session('country_code')];
        return "{$lender_code}-{$mobile_num}";
	}
	function get_file_rel_path_w_file_name($aggr_doc_id){
		$file_path = separate(['files', get_file_rel_path(null, "master_agreements")]);
		return $file_path.'/'.$aggr_doc_id.'.pdf';
	}
	function flatten_borrower(&$borrower){
		foreach($borrower as $key => $value){
            if(is_array($value)){

                $borrower[$key]['country_code'] = session('country_code');
				foreach($value as $inner_key=>$inner_value){
					if(is_array($inner_value) && array_key_exists('value' ,$inner_value)){
						
						$borrower[$key][$inner_key] = $inner_value['value'];
					}
				}
            }
            if($key == "contact_persons" || $key == "references" || $key == "addl_num"){
                foreach($value as $i=>$v){
                    if(is_array($v)){
                        $borrower[$key][$i]['country_code'] = session('country_code');
						foreach($v as $cp_inner_key=>$cp_inner_value){
							if(is_array($cp_inner_value) && array_key_exists('value' ,$cp_inner_value)){
								$borrower[$key][$i][$cp_inner_key] = $cp_inner_value['value'];
							}
							if(is_array($cp_inner_value) && $cp_inner_key == "contact_address"){
								$borrower[$key][$i][$cp_inner_key]['country_code'] = session('country_code');
								foreach($cp_inner_value as $addr_key => $addr_val){
									if(is_array($addr_val) && array_key_exists('value' ,$addr_val)){					 
										$borrower[$key][$i][$cp_inner_key][$addr_key] = $addr_val['value'];
									}
							}	}
						}
					}
                }
            }
			
        }
	}
	function get_flow_fee($data){
		return number_format($data->max_loan_amount*($data->flow_fee/100)*$data->duration/12);
	}


	function get_sql_condition(&$criteria_array, $conditions, $addl_sql_condition_arr = []){
		foreach ($conditions as $criteria_key => $criteria) {
           if(array_key_exists($criteria_key, $criteria_array)){
              if($criteria_array[$criteria_key] == 'true'){
                $addl_sql_condition_arr[] = $criteria;
              }
              unset($criteria_array[$criteria_key]);
            }
        }

        $addl_sql_condition = "";
        if(sizeof($addl_sql_condition_arr) > 0){
            $addl_sql_condition = implode(" and ", $addl_sql_condition_arr);
            if(sizeof($criteria_array) > 0 ){
               $addl_sql_condition = " and " .$addl_sql_condition;
            }
        }
		return $addl_sql_condition;
	}


	function run_python_script($file_path, $data, $timeout_sec = 500)
    {
        $process = new Process(["python", app_path()."/Scripts/python/{$file_path}.py", $data]);
		$process->setTimeout($timeout_sec);
		$process->run();
        $output = $process->getOutput();
		Log::warning("COMMAND : python $file_path $data");
		Log::warning($process->getErrorOutput());
        $response = json_decode($output,true);
        return $response;
    }



	function get_mobile_config(){
		$config_arr = Arr::only(config('app'), config('app.mobile_config_keys'));
		$country_code = session('country_code');
		$config_arr['otp_msg'] = compile_sms_old($config_arr['otp_msg'], ['shortcode' => config("app.sms_reply_to.$country_code")]);
		return $config_arr;
	}

	function get_web_ui_config(){
		return Arr::only(config('app'), config('app.web_ui_config_keys'));

	}function calc_tf_fee($amount, $fee_percent, $months){
		return ($amount * ($fee_percent/100) * $months)/12;

	}

	function get_cust_reg_arr() {
		$base_cust_reg = Consts::CUST_REG_ARR; #Take the Base Cust Reg Structure without address
		$addr_configs = (new AddressConfig)->get_records_by_country_code(['field_code' , 'field_type']);

		$addr_structure = []; #Construct address structure from addr_config
		foreach($addr_configs as $addr_config) {
			$type = ($addr_config->field_type == 'select') ? 'dropdown' : $addr_config->field_type;	
		
			$addr_structure[$addr_config->field_code] = [	'value' => NULL,
															'type' => $type,
															'cmts' => []
														];
		}
		unset($addr_structure['gps']);

		#Set address structure 
		$base_cust_reg['biz_address'] = $addr_structure;
		$base_cust_reg['owner_address'] = $addr_structure;
		
		foreach($base_cust_reg['contact_persons'] as $index => $val) {
			$base_cust_reg['contact_persons'][$index]['contact_address'] = $addr_structure;
		}

		return $base_cust_reg;
	}


	function to_string($arr){
		return is_array($arr) ? str_replace("'","",csv($arr)) : $arr;
	}

	function is_tf_acc($acc_purpose){
		return in_array('terminal_financing', $acc_purpose);
	}
	function is_fa_acc($acc_purpose){
		return in_array('float_advance', $acc_purpose);
	}

	function is_tf_only_acc($acc_purpose){
		return is_tf_acc($acc_purpose) && !is_fa_acc($acc_purpose);
	}

	function get_tf_status($key){
        if($key){
            $status = ['tf_01A_pending_prod_sel' => 'Pending Product Selection','tf_01_pending_dp' => 'Pending Downpayment', "tf_01_pending_dp_ver" => "Downpayment Verification", 'tf_02A_pending_rm_alloc' => 'Pending RM Allocation', 'tf_02_pending_flow_kyc' => 'Pending KYC', 'tf_10_pending_sc_gen' => 'Pending SC Code Generation', 'tf_10_pending_transfer_dp' => 'Pending Transfer of Downpayment', 'tf_20_pending_terminal_act' => 'Pending Terminal ID Activation', 'tf_30_pending_flow_disb' => 'Pending Flow Loan Disbursal', 'tf_40_pending_pos_to_rm' => 'Pending POS to Flow RM', 'tf_50_pending_pos_to_cust' => 'Pending POS to Customer', 'tf_50_pending_repay_cycle' => 'Pending Repayment Cycle'];
            $tf_status = $status[$key];
            return $tf_status;
        }
        return $key;
    }

	function get_ops_admin_email($country_code = null)
    {
		$country_code = ($country_code == null) ? session('country_code') : $country_code;
        return AppUser::where('country_code', $country_code)->where('role_codes', 'ops_admin')->where('status', 'enabled')->get(['email'])[0]->email;
    }

	function get_csm_email($country_code = null)
    {
		$country_code = ($country_code == null) ? session('country_code') : $country_code;
        return AppUser::where('country_code', $country_code)->where('role_codes', 'customer_success')->where('status', 'enabled')->get(['email'])[0]->email;
    }

	function get_market_admin_email($country_code = null)
	{
		$country_code = ($country_code == null) ? session('country_code') : $country_code;
		return AppUser::where('country_code', $country_code)->where('role_codes', 'market_admin')->where('status', 'enabled')->get(['email'])[0]->email;
	}

	function get_ops_admin_csm_email($country_code = null){
		$ops_admin_email = get_ops_admin_email($country_code);
		$csm_email = get_csm_email($country_code);
		return [$ops_admin_email, $csm_email];
	}

	function get_l3_email($country_code = null){
		$country_code = ($country_code == null) ? session('country_code') : $country_code;
        return AppUser::where('role_codes', 'it_admin')->get(['email'])[0]->email;	
	}

	function get_super_admin_email($country_code = null){
        $country_code = ($country_code == null) ? session('country_code') : $country_code;
        return AppUser::where('role_codes', 'super_admin')->get(['email'])[0]->email;   
    }

	function get_last_fa_applier_email($cust_id)
    {
		$email = null;
		$last_cs_applied_fa = DB::selectOne("select loan_applied_by from loans where cust_id = ? and loan_applied_by is not null order by id desc limit 1", [$cust_id]);
		if($last_cs_applied_fa && isset($last_cs_applied_fa->loan_applied_by) && $last_cs_applied_fa->loan_applied_by !=0){
			$email = AppUser::find($last_cs_applied_fa->loan_applied_by)->email;
		}
		return $email;
    }

	function get_env(){
		return env('APP_ENV') == "production" ? "" : "|".env('APP_ENV');
  }

	function is_single_session_ap($acc_prvdr_code){
		return in_array($acc_prvdr_code, config('app.single_session_acc_prvdrs'));
	}

	function create_zip_file($zip_file_path, $files_to_include) {
		$zip = new ZipArchive();
		$zip_creation_resp = $zip->open($zip_file_path, ZipArchive::CREATE);
		if ( $zip_creation_resp === TRUE) {
			foreach ($files_to_include as $file_path){
				$abs_file_path = flow_storage_path($file_path);
				if(!File::exists($abs_file_path)){
					thrw("Can not create zip.\nGiven file: '{$file_path}' does not exist.");
				}
				$zip->addFile($abs_file_path, basename($abs_file_path));
			}
			$zip_close_resp = $zip->close();
			if (!$zip_close_resp) {
				thrw("Zip was not closed properly.");
			}
		}
		else {
			thrw("Unable to create zip file.\nError code: '$zip_creation_resp'.");			
		}
	}
	

	function is_disb_capture_frm_stmt_reqrd($acc_prvdr_code){
		return in_array($acc_prvdr_code, config('app.aps_reqrd_disb_capture_from_stmt'));
	}


	function map_logo_to_account($data,$include_all = false){
		$acc_pvdr_logos = config('app.acc_prvdr_logo')[session('country_code')];
		$excess_accs = array();
		foreach ($acc_pvdr_logos as $prvdr => $logo){
			$set = false;
			if($logo){
				foreach ($data as $key => $cust){
					if($cust['acc_prvdr_code'] == $prvdr){
						$data[$key]['acc_prvdr_logo'] = $logo;
						$set = true;
					}
				}
				if($include_all == true && $set == false and Str::contains($logo,session('country_code'))){
					array_push($excess_accs,["acc_prvdr_code" => $prvdr,"acc_prvdr_logo" => $logo]);
				}
			}
		}
		$data = array_merge($data,$excess_accs);
		return $data;
	}

	function get_acc_num_by_district($cust_id){
		$district = (new BorrowerRepositorySQL)->get_cust_district($cust_id);
        $acc_number = config("app.RMTN_district_accounts.$district");
		return $acc_number;
	}

	function get_amt_field($mode){
		return $mode == 'credit' ? 'cr_amt' : 'dr_amt';
	}

    function log_req_resp($request,$response, $should_db_log = true)
    {
        $duration = $request->end - $request->start;
        $url = $request->fullUrl();
        $method = $request->getMethod();
        $ip = $request->getClientIp();

        $log = "{$ip}: {$method}@{$url} - {$duration}ms \n".
            "******************************** Request ********************************\n : $request \n\n\n".
            "---------------------------------------------------------------------------------------\n\n\n".
            log_response($url, $request->path(), $response, $should_db_log);
        Log::info($log);
    }

    function log_response($url, $path, $response, $should_db_log){

        if($should_db_log){
            return "\n$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ Response $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$\n : $response ---------------------------------------------------------------------------------------\n\n\n";
        }else{
            return "\n$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ Response $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$\n$url THIS URL IS EXEMPTED FROM LOGGING. HOWEVER THE API REQUEST WAS SUCCESSFULLY PROCESSED.\n\n\n";
        }
    }

    function log_api_req_in_db($request, $req_json, $country_code,$should_log_json = true){

        $log = array();
        $log['country_code'] = $country_code;
        $log['h_url'] = $request->fullUrl();
        $log['h_page'] = $request->header('Referer');
        $log['h_user_agent'] = $request->header('User-Agent');
        $path = $request->path();
		$route_array = explode('/',$path);
		$route = end($route_array);

        if(strpos($log['h_url'],"upload")){
            unset($req_json['file_data']);
        }
		if($should_log_json){
			$log['request_json'] = json_encode($req_json);
		}
        $log['request_time'] = Carbon::now();

        $log_repo = new CommonRepositorySQL(ApiRequest::class);
        $api_req_id = $log_repo->insert_model($log);
		$path = str_replace("api","",$path);
        $path = str_replace("/cust_mob","",$path);
        $log_prefix = $api_req_id . " | ".$path ." | ".session('username');
        session([
            'api_req_id' => $api_req_id
//            'api_req_id_n_path' => $api_req_id_n_path,

        ]);

        session()->put('log_prefix',$log_prefix);

        return $log['request_time'];

    }

    function log_api_resp_in_db($response, $req_time, $path, $should_log_resp = true){

        $log = array();
        Log::warning("SESSION_USER_ID");
        Log::warning(session('user_id'));

        if(in_array($path,['api/app/user/logout', 'api/app/user/master_data', 'app/common/country', 'app/common/currency', 'app/common/approvers', 'app/common/disbursers','app/common/dropdown', 'admin/data_provider/name_list', 'admin/rel_mgr/name_list', 'admin/lender/name_list', 'admin/org/name_list', 'admin/org/org_details', 'app/loan_txns/disbursal_accounts','api/admin/data_provider/list'])){
            $user_id = 0;
        }else{
            $user_id = session('user_id');
            //$user_id = 0;
        }

        $log['api_req_id'] = session('api_req_id') ?? 0;
        if(isset($resp->message)){
            $msg = $resp->message;
        }else{
            $msg = null;
        }
        $resp = $response->getData();
        $log['req_user_id'] = $user_id ?? 0;
        $log['response_code'] = $resp->status_code;
        $log['response_msg'] = $msg;
        $log['response_time'] = Carbon::now();
        $log['response_status'] = $resp->status;

        if($should_log_resp){
            $resp_json = json_decode($response->getContent());
            //if(property_exists($resp_json, "thumbnail_data")){
            if(strpos($response->getContent(),"thumbnail_data")){
                unset($resp_json->data->thumbnail_data);
            }
            $log['response_json'] = substr(json_encode($resp_json), 0, 10000);
        }
        $log['ms'] = $log['response_time']->diffInSeconds(Carbon::parse($req_time));

        $log_repo = new CommonRepositorySQL(ApiResponse::class);
        $log_repo->insert_model($log);
    }

	function get_route($path, $slice_prefix = 1){
		$route = implode('/',array_slice(explode('/',$path), -1 * $slice_prefix));

		return $route;
	}

	function get_username_from_email($email){
		$email_arr = explode('@',$email);
        $username = $email_arr[0];

		return $username;
	}

	function send_event_notification($event_info){
		$notify_events = DB::select("select id from events where type = ? and entity = ? and country_code = ? and event_time > ? and entity_id = ?", [$event_info['type'], $event_info['entity'], session('country_code'), now()->subMinutes($event_info['interval']), $event_info['entity_id']]);
		if(sizeof($notify_events) == 0){
			if($event_info['channel'] == 'whatsapp'){
				$whatsapp_serv = new WhatsappWebService();
				$whatsapp_serv->send_message(["body" => $event_info['notification'], "to" => $event_info['group_id']['biz_ops'][session('country_code')], "isd_code"=> "", "session" => config('app.whatsapp_notification_number')]);
				DB::table("events")->insert(["type" => $event_info['type'], "entity" => $event_info['entity'], "entity_id" => $event_info['entity_id'], "event_time" => now(), "country_code" => session('country_code')]);
			}
		}
	}
	function send_notification_failed_mail($e, $recipient_id, $notify_type) {

		$exp_msg = $e->getMessage();
		if($e instanceof \GuzzleHttp\Exception\RequestException){
			$trace = $e->getResponse()->getBody(true);
			$trace = json_encode($trace, JSON_PRETTY_PRINT);
		}
		else{
			$trace = $e->getTraceAsString();
		}
		$mail_data = ['country_code' => session('country_code'), 'exp_msg' => $exp_msg, 'exp_trace' => $trace, 'notify_type' => $notify_type];
		$mail_data['recipient_name'] = (new PersonRepositorySQL())->full_name_by_sql($recipient_id);
		Mail::to(config('app.app_support_email'))->queue((new FlowCustomMail('notification_failed', $mail_data))->onQueue('emails'));
	}

	function get_export_lambda($acc_prvdr_code) {

        return ($acc_prvdr_code == Consts::MTN_AP_CODE && config('app.env') != 'production') ? 'mtn_export3' : strtolower($acc_prvdr_code)."_export";
	}


	function send_email(string $view, array $recipients, array $data, bool $queued = true){
		$connection = $queued ? config('queue.default') : 'sync';
		Mail::to($recipients)->queue((new FlowCustomMail($view, $data))->onConnection($connection)->onQueue('emails'));
	}

/**
 *
 * @param String $latest_version
 * @param String $current_version
 * @return int Integer value from 0 to 3 ( 0 = no update, 1 = tweak update, 2 = minor update, 3 = major update )
 */

	function check_for_update_level(String $latest_version, String $current_version){
		$latest_split = array_reverse(explode(".", $latest_version));
		$crnt_split = array_reverse(explode(".", $current_version));
		$level = 0;
		foreach ($latest_split as $key => $val){
			if($val > $crnt_split[$key]){
				$level=$key+1;
			}
		}
		return $level;
	}

	function get_last_five_days($format='d'){

		$cur_date = Carbon::now();		
		$end_date_month = $cur_date->copy()->lastOfMonth()->endOfDay();
        $start_date = $end_date_month->copy()->subDays('4')->format('Y-m-d');
        $period = CarbonPeriod::create($start_date, $end_date_month->format('Y-m-d'));
        foreach ($period as $date) {
           $date_range[] = $date->format($format);
        }
		return $date_range;
    }

	function get_sms_vendors($fields, $status = Consts::ENABLED){

		return (new Vendor)->get_records_by_many(['vendor_type','status'], ['sms', $status], $fields);

	}

	function insert_flow_loan_appl_ids($country_code, $last_id_value = null){
	    if(!$last_id_value){
            $last_id_value = DB::selectOne("select max(id_value) max_value from flow_ids where country_code = '{$country_code}' and id_type = 'loan_appl'")->max_value;
		}
		if(!$last_id_value){
		   thrw("no last id value found");
	    }
		echo $last_id_value;
		$insert_data = [];
		$max_insert = $last_id_value + 10000;
		while($last_id_value <= $max_insert){
		    $data = ['country_code' => $country_code, 'id_type' => 'loan_appl', 'id_value' => ++$last_id_value];
		    $insert_data[] = $data;
	    }
		 
		$insert_data = collect($insert_data);
		$chunks = $insert_data->chunk(500);
		foreach ($chunks as $chunk){
		    DB::table('flow_ids')->insert($chunk->toArray());
		}
		echo "\nlast id now : ";
		echo $last_id_value;
	}
		
    function array_to_csv(array $arr) {
        $fp = fopen('php://temp', 'w');
        fputcsv($fp, $arr['columns']); // Header
        foreach ($arr['data'] as $fields) {
            fputcsv($fp, $fields); // Rows
        }
		rewind($fp);
		$data = stream_get_contents($fp);
        fclose($fp);
        return $data;
    }

    function array_to_txt(array $arr) {

		$fp = fopen('php://temp', 'w');
        foreach ($arr as $line) {
            fwrite($fp, $line.PHP_EOL);
        }
		rewind($fp);
		$data = stream_get_contents($fp);
        fclose($fp);
        return $data;
    }

	/**
     * Slice the array into chunks of size (chunk_size)
     *
     * @param  array  $arr
     * @param  int $chunk_size
     */
	function chunkit(array $arr, int $chunk_size) {
		
		$arr_size = count($arr);
		$num_of_chunks = floor($arr_size / $chunk_size);
		if (($arr_size % $chunk_size) != 0) 
			$num_of_chunks += 1;

		for( $iters=0; $iters < $num_of_chunks; $iters++ ) {
			yield array_splice($arr, 0, $chunk_size);
		}
	}
	
	function invoke_step_function($data, $sfn_name) {

		$aws_region = env('AWS_REGION');
		$aws_account_id = env('AWS_ACCOUNT_ID');
		$sfn_arn = "arn:aws:states:$aws_region:$aws_account_id:stateMachine:$sfn_name";

        $sfn_client = AWS::createClient('sfn');
		return $sfn_client->startExecution([
			'input' => json_encode($data),
			'name' => uniqid(),
			'stateMachineArn' => $sfn_arn, // REQUIRED
		]);
    }

	/**
     * Returns an array of the last N months from the given month and year
     *
     * @param  string  $year Initial Year
     * @param  string  $month Initial Month
     * @param  int  $N_months Last N months to consider
     * @param  bool  $include_curr_month To include current month or not
     * @return  array $months
     */
	function get_last_N_months(string $year, string $month, int $N_months, bool $include_curr_month=false) {
		
		$date = Carbon::createFromDate($year, $month);
		$months = array();

		for ($i = 0; $i < $N_months; $i++) {
			
			$date_obj = ($include_curr_month && $i == 0) ? $date: $date->subMonth(1);
			$date_string = $date_obj->format('Y-m');
			$date_array = explode('-', $date_string);
			$year = $date_array[0];
			$month = $date_array[1];

			if(array_key_exists($year, $months)) {
				$months[$year][] = $month;
			}
			else {
				$months[$year] = [$month];
			}
		}

		return $months;
	}

	/**
     * Return the queue to process Statement Import
     *
     */
	function get_stmt_import_queue($account) {
		$queue_prefix = "";
		if ($account->disb_int_type == 'web' && in_array('disbursement', $account->acc_purpose)) {
			$queue_prefix = "DISB_";
		}
		return "{$queue_prefix}STMT_{$account->acc_prvdr_code}_{$account->acc_number}_{$account->stmt_int_type}";
	}

?>
