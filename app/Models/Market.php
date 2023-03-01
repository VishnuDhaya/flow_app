<?php

namespace App\Models;

use App\Models\Model;


class Market extends Model
{
   const TABLE = "markets";
   const UPDATABLE = ['country_code','currency_code','isd_code'];
   const INSERTABLE = ['country_code','currency_code','org_id', 'head_person_id', 'time_zone','isd_code'];	
   const CODE_NAME = "country_code";
   
   public static function rules($json_key)
	
    {
        /*$default_rules = [
              
                'name' => 'required|max:100',
            ];*/

    	if ($json_key =="market_update"){
          
          $default_rules = ['id' => 'required'];
          return $default_rules;
    	
        }elseif($json_key == "market_create"){
    	    
          $default_rules['country_code'] = 'required|max:3';
          $default_rules['currency_code'] = 'required|max:3';
          $default_rules['isd_code'] = 'required|numeric';
          $default_rules['org_id'] = 'max:10';
          $default_rules['head_person_id'] = 'max:10';
        	return $default_rules;
    	}else{
        
          $default_rules['country_code'] = 'required|max:3';
          $default_rules['currency_code'] = 'required|max:3';
          $default_rules['isd_code'] = 'required|numeric';
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

          /* 'name.required' => 'Name is a required field',
           'name.max' => 'Name should contain maximum of 100 characters',
           'country_code.required' => 'Country code is a required field',
           'currency_code.required' => 'Currency code is a required field' */
            
        ];
    }
}
