<?php

namespace App\Models;
use App\Models\Model;

class MasterData extends Model
{
	const INSERTABLE = ["country_code", "data_type", "data_key","parent_data_code","data_value","data_code"];
	const TABLE = "master_data";
	const CODE_NAME = "country_code";

   public static function rules($json_key)
    {
         $required = parent::is_required($json_key);

        $default_rules = [
                'data_code' => "$required",
                'data_value' => "$required",
                'data_type' => "$required"           
            ];
    	
    	if ($json_key =="master_data"){
	        
            return $default_rules;
    	
        }else{

    		return $default_rules;
    	}
    }
}