<?php

namespace App\Models;

use App\Models\Model;


class MasterAgreement extends Model
{
   const TABLE = "master_agreements";
   const INSERTABLE = ['country_code', 'aggr_doc_id', 'product_id_csv', 'lender_code', 'acc_prvdr_code', 'status', 'valid_from', 'valid_upto', 'name','aggr_duration','aggr_type','duration_type', 'acc_purpose'];
   //const UPDATABLE = ['status'];


    public static function rules($json_key)
    {
        $required = Model::is_required($json_key);

        $default_rules = [
                'name' => "unique:master_agreements",
                'lender_code' => "$required",
                //'product_ids' => "$required",
                  ];
                


    	if ($json_key == "master_agreement"){ 
  	        
            return $default_rules;     	
        }else{
            
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
            
        ];
    }

}
