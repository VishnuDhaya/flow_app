<?php

namespace App\Models;
use App\Models\Model;
use Illuminate\Database\Eloquent\Model as EloqModel;

class Lender extends EloqModel
{
     const TOUCH = true;
     const CODE_NAME = "lender_code";
     const INSERTABLE = ["name","lender_type", "lender_code", "lender_logo", "org_id", "country_code","contact_person_id"];
     const UPDATABLE = ["name","lender_type","lender_code","lender_logo"];
     const TABLE = "lenders";
     
     public static function rules($json_key)
    {

      $required = Model::is_required($json_key);
      $default_rules = [
                'name' => "$required|regex:/^[\pL\s\-]+$/u|max:40",
                'lender_type' => "$required|max:40|regex:/^[\s\w-]*$/",
                'lender_code' => "unique:lenders|$required|max:4"
                  ];


    	if ($json_key == "lender_update"){ 
        	        
            $default_rules['id'] = "$required";
            return $default_rules; 
    	
        }elseif($json_key =="lender_create"){
        
            $default_rules['country_code'] = "$required|max:3";
            return $default_rules;

        }
        else{
            
            $default_rules['country_code'] = "$required|max:3";
    		return $default_rules;
    		
    	}
    }
     /**
     * Custom message for validation
     *
     * @return array
     */
    public static function messages($json_key)
    {
        return [
            /*'country_code.required' => 'Country code is a required field',
            'name.required' => 'Name is a required field',
            'name.regex' => 'Name should not contain special characters',
            'lender_type.required' => 'Lender type is a required field',*/
           // 'org_id.required' => 'Org id is a required field'
        ];
    }
}
