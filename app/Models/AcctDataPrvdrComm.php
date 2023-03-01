<?php

namespace App\Models;
use App\Models\Model;
use \Illuminate\Database\Eloquent\Model as EloqModel;
//use Illuminate\Database\Eloquent\Model;

class AcctDataPrvdrComm extends EloqModel
{
    const TOUCH = true;
    const TABLE = "acct_data_prvdr_comm";
    const INSERTABLE = ["acc_prvdr_code" , "cust_id",  "loan_doc_id" ,"amount" ,"credit", "debit", "comm_date"];
    const UPDATABLE = ["acc_prvdr_code" , "cust_id",  "loan_doc_id" ,"amount" ,"credit", "debit", "comm_date" ];


     public static function rules($json_key)
	
    {
        
        $required = Model::is_required($json_key);

        $default_rules = [
                
                'acc_prvdr_code' => "$required|max:3",
                'amount' => '$required|numeric'
            ];      

    	if($json_key =="acct_data_prvdr_comm_create"){
    	
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
