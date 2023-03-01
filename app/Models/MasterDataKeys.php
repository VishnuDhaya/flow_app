<?php

namespace App\Models;
use App\Models\Model;

class MasterDataKeys extends Model
{
	const INSERTABLE = ["country_code","data_key", "data_type", "parent_data_key","key_desc", "data_group"];
	const TABLE = "master_data_keys";

	public static function rules($json_key)
	
    {
         $required = parent::is_required($json_key);

        $default_rules = [

				'data_key' => "$required",
				'data_key' => "$required|unique:master_data_keys",
                'data_type' => "$required"
				//'parent_data_key' => "$required",
				//'key_desc' => "$required",
				//'key_group' => "$required"
            ];
    	
    	if ($json_key =="master_data_keys"){
	        
            return $default_rules;
    	
        }else{

    		return $default_rules;
    	}
    }
}