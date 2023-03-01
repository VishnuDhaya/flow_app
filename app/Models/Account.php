<?php

namespace App\Models;
use App\Models\Model;
use Illuminate\Support\Facades\Log;

//use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
     //$updatable = ["name","lender_type","status"];
    const TABLE = "accounts";

    const CODE_NAME = 'acc_number';


    const INSERTABLE = ['tp_acc_owner_id','photo_consent_letter','alt_acc_num',"photo_new_acc_letter","country_code", "cust_id", "lender_code", "acc_prvdr_name", "type", "holder_name", "acc_number", "branch", "is_primary_acc","acc_prvdr_code", 'balance','acc_purpose', 'network_prvdr_code', 'assessment_type', 'status', 'acc_elig_reason', 'to_import', 'holder_name_mismatch_reason'];

    const UPDATABLE =  ['tp_acc_owner_id','photo_consent_letter','alt_acc_num', "photo_new_acc_letter","holder_name", "branch", "is_primary_acc", 'balance','acc_purpose', 'assessment_type', 'acc_number', 'status', 'mobile_cred', 'to_import', 'acc_elig_reason', 'holder_name_mismatch_reason'];

    const JSON_FIELDS = ['acc_purpose', 'mobile_cred', 'api_cred'];

    public function model(){        
            return Account::class;
    }

     public static function rules($json_key)
    {
        $required = Model::is_required($json_key);


        $default_rules = [
                'country_code' => 'required',
                'acc_number' => 'required|same:reconfirm_acc_number',
                'reconfirm_acc_number' => 'required',
                'is_primary_acc' => 'required'
             
                    ];


        if ($json_key == "account"){
            
            return $default_rules; 
        
        }else if($json_key == "cr_account_reg_cust" || $json_key == "cr_account_submit_kyc"){
           unset($default_rules);
           $default_rules['acc_number'] = "$required";
           $default_rules['acc_prvdr_code'] = "$required";
           return $default_rules;
        }
        else{
        
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
            'country_code.required' => 'country_code is a required field',
            'type.required' => 'type is a required field',
            'acc_number.required' => 'acc_number is a required field',
            'is_primary_acc.required' => 'is_primary_acc is a required field',

        ];
    }
}
