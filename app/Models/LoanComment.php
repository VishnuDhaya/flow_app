<?php

namespace App\Models;
use App\Models\Model;
//use Illuminate\Database\Eloquent\Model;

class LoanComment extends Model
{
	
	const INSERTABLE = ["loan_doc_id","cmt_type","comment","cmt_to","cmt_from","cmt_to_name","cmt_from_name","cmt_to_info","cmt_from_info","country_code"];


	 const TABLE = "loan_comments";



   public static function rules($json_key)
	
    {
         $required = parent::is_required($json_key);

        $default_rules = [
               
                'cmt_type' => "$required",
                'comment' => "$required"
               
                      
            ];
    	
    	if ($json_key =="loan_comments"){
	        
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


  
}
