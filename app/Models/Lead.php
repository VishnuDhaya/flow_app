<?php

namespace App\Models;
use App\Models\Model;
use Illuminate\Support\Facades\Log;


class Lead extends Model
{
    const TABLE = "leads";


    const INSERTABLE = ['first_name','last_name','visit_ids','cust_reg_json','mobile_num','account_num','location','territory','acc_prvdr_code','country_code','acc_purpose','flow_rel_mgr_id','lead_json','status','lead_date','eval_date','rm_kyc_start_date','rm_kyc_end_date','audit_kyc_start_date','audit_kyc_end_date','rm_eval_id','biz_name', 'type', 'cust_id', 'kyc_reason','product','created_by','tf_status','file_json','score_status','channel'];

    const UPDATABLE = ['audited_by','consent_signed_date','run_id','score_status','first_name','last_name','consent_json','rm_kyc_start_date','onboarded_date','visit_ids','profile_status','close_reason','reassign_reason','remarks','cust_reg_json','cust_eval_json','status','mobile_num','account_num','location','territory','acc_prvdr_code','country_code','acc_purpose','flow_rel_mgr_id','lead_json','biz_name','eval_date','rm_kyc_end_date','audit_kyc_end_date', 'type', 'cust_id', 'kyc_reason','update_data_json','product','tf_status','file_json', 'remarks'];

    const JSON_FIELDS = ['acc_purpose', 'remarks'];

    public static function rules($json_key){
        $required = Model::is_required($json_key);

        $default_rules = [
                            'biz_name' => "$required|min:3|max:80|regex:/^[\'\pL\s\-]+$/u",
                            'mobile_num' => "regex:/^[^0][0-9]{8}$/|digits:9",
                            'latitude' => 'numeric|between:-90,90',
                            'longitude' => 'numeric|between:-180,180',
                            'first_name'=> "min:3|max:20|regex:/^[\'\pL\s\-]+$/u",
                            'last_name'=> "min:3|max:20|regex:/^[\pL\s\-]+$/u",


                        ];

        if($json_key == "lead_create_lead"){
            $default_rules['account_num'] = "$required";
            $default_rules['acc_prvdr_code'] = "$required";
        }
        if(session('country_code') == 'UGA') {
            $default_rules['national_id'] = "regex:/^[a-zA-Z0-9]+$/|starts_with:C,P";
        }
        elseif(session('country_code') == 'RWA') {
            $default_rules['national_id'] = "regex:/^[0-9]+$/|size:16";
        }
        if($json_key == "lead_cust_lead" || $json_key == "lead_refer_agent" ){
            $default_rules['biz_name'] = "min:3|max:80|regex:/^[\pL\s\-]+$/u";
        }
        return $default_rules;
        

    }
     /**
     * Custom message for validation
     *
     * @return array
     */
    public static function messages($json_key)
    {
        return
        [
            'biz_name.required' => 'Business name is a required field',
            'account_num.required' => 'A/C number is a required field',
            'acc_prvdr_code.required' => 'A/C provider code is a required field',
            'biz_name.max' => 'Business name should contain maximum of 20 characters',
            'biz_name.regex' => 'Business name should not contain special characters',
            'mobile_num.max' => 'Mobile number should contain maximum of 10 characters',
            'mobile_num.min' => 'Mobile number should contain minimum of 10 characters', 
            'national_id.starts_with' => 'National ID must start with C or P',
            'latitude.between' => 'The latitude must be in range between -90 and 90',
            'longitude.between' => 'The longitude mus be in range between -180 and 180'
        ];
    }
}
