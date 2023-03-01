<?php
namespace App\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Exceptions\FlowValidationException;
use Illuminate\Support\Str;

class FlowValidator {

	public static function validate($data, $model_array=[], $action = "")
	{
        $main_array = array();
		$data_array = self::convertArray($data);
		$validation_success = true;

		//Log::warning("$model_array : " .$model_array);
		foreach($model_array as $json_key){
			Log::warning("VALIDATING $json_key");
			if(!array_key_exists($json_key,$data_array)){
				Log::warning("$json_key not available in the request");
				//Log::warning("Skipping model $json_key for validation");
				//continue;
			}

			if(!array_key_exists($json_key, \App\Consts::MODEL_ALIAS)){
				Log::warning("$json_key not available in Const::MODEL_ALIAS mapping");
				//Log::warning("Skipping model $json_key for validation");
				//continue;
			}
			$obj_arr = $data_array[$json_key];
			
			$validator = self::validate_model($json_key, $obj_arr, $action, $data['country_code']);
			if ($validator->fails()) {
                //Log::info($validator->messages()->all());
				//return $validator->messages()->all();
				throw new FlowValidationException(implode("|" , $validator->messages()->all()));
			}
			
			/*if(array_keys($obj_arr) !== range(0, count($obj_arr) - 1)){
				$obj_arr = [$obj_arr];
			}
			
			 foreach($obj_arr as $obj){

				$validator = self::validate_model($json_key, $obj, $action, $data['country_code']);
				if ($validator->fails()) {
	                //Log::info($validator->messages()->all());
					//return $validator->messages()->all();
					throw new FlowValidationException(implode("|" , $validator->messages()->all()));
					

				}
			}*/
		}
	    return $validation_success;
	}

	private static function validate_model($json_key, $data, $action, $country_code){
		

		$model_obj = self::get_model($json_key);
		if(Str::contains($json_key, "address")){
			$rules = $model_obj->rules($json_key."_".$action, $country_code);	
		}else{
			$rules = $model_obj->rules($json_key."_".$action);
		}
		

		$messages = $model_obj->messages($json_key);
		
		$validator = Validator::make($data ,$rules, $messages);
		
		return $validator;

	}

	 private static function get_model($json_key){
	 	

	 	$get_class_name = \App\Consts::MODEL_ALIAS[$json_key];	

		$class_name = "App\\Models\\".$get_class_name;
		
		return new $class_name();


		//ucfirst(end(explode(",",$model)));
    }

    private  static function convertArray($arr, $narr = array(), $nkey = '')
	{
		global $main_array;
		

		foreach ($arr as $key => $value)
		{
		
			if(is_assoc_arr($value) && !empty($value))
			{
				$narr = array_merge($narr, self::convertArray($value, $narr, $key));
				foreach ($value as $value_item) {
					if (is_array($value_item) && !empty($value_item)){
						$narr = array_merge($narr, self::convertArray($value_item, $narr, $key));
					}
				}

			}else if (is_array($value) && !empty($value)){
				foreach ($value as $value_item) {
                    if (is_array($value_item) && !empty($value_item)){
						$narr = array_merge($narr, self::convertArray($value_item, $narr, $key));
					}
                    else{
                        $narr[$nkey . $key] = isset($narr[$nkey . $key]) ? $narr[$nkey . $key].','.$value_item : $value_item;
                        $array_name = $nkey;
                        $main_array[$array_name][$key] = isset($main_array[$array_name][$key]) ? $main_array[$array_name][$key].','.$value_item : $value_item;
                    }
				}
			}
			else
			{
				$narr[$nkey . $key] = $value;
				$array_name = $nkey;
				$main_array[$array_name][$key] = $value;
			}
		}

		return $main_array;
	}



	//public validate($data_array, ["borrower","owner_person","org","reg_address"])
/*
public function array_md_to_2d($value, $key) 
	{

		    global $outer_array;
	        global $global_value;
	        if(is_array($value)){
	      
	            
	                $global_value = $value;
	                //dd($value);
	                array_walk($value,array($this,"remove_inner_array"), $value);
	                //$value_obj = new ArrayObject($global_value);
	                //$outer_array[$key]=  $value_obj->getArrayCopy();
	                
                   $outer_array[$key] =  $global_value; 

	                //dd($global_value);
	                	//print_r("fawef");
	                  //outer_array[$key]= $value;

	                array_walk($value,array($this,"array_md_to_2d"));
	        } 
	 
	}

public function remove_inner_array($inner_value, $inner_key, $value)
	{ 

	global $global_value;

		dd($inner_key);
	        if(is_array($inner_value))
	        { 
	        unset($global_value[$inner_key]);  

	        }
	}
*/

/*
    private  function get_class_name($model){

    	
    	ucfirst(end(explode(",",$model)));
    }
*/

/*
	public function flatten_array($data){    
		$outer_array  = array();
		$global_value;
		$org_data = $data;
		
		//$org_data = $data['borrower'];
		//dd($org_data );
	    array_walk($org_data,array($this,'array_md_to_2d'));
	    return $outer_array;
}
*/

	

}

