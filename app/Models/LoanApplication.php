<?php

namespace App\Models;


//use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class LoanApplication extends Model
{
    
    
     const TABLE = "loan_applications";

     const CODE_NAME = "loan_appl_doc_id";

     const UPDATABLE = ['loan_approver_id','flow_rel_mgr_id','loan_approver_name','pre_appr_ignr_reason','pre_appr_id','loan_appl_doc_id', 'status','fund_code',  'action_reason_code',  'remarks',  'loan_approved_date','credit_score','loan_doc_id','appr_reason','customer_consent_rcvd', 'acc_number','acc_prvdr_code'];


     const INSERTABLE = ['loan_appl_date', 'loan_applied_by', 'fund_code','loan_approver_name',  'loan_approver_id', 'product_id', 'dp_rel_mgr_id', 'flow_rel_mgr_id', 'lender_code', 'acc_prvdr_code', 'cust_name', 'cust_id', 'cust_mobile_num', 'cust_acc_id', 'product_name', 'loan_principal', 'duration', 'flow_fee', 'due_amount',  'currency_code','cs_model_id', 'credit_score', 'cust_addr_text',  'flow_fee_type',  'flow_fee_duration', 'country_code','loan_approved_date','min_credit_score', 'status', 'master_loan_doc_id','biz_name','loan_appl_doc_id','cs_result_code','approver_role', 'acc_number', 'loan_purpose', 'channel', 'applied_location'];


    const SEARCHABLE = [];

    public static function rules($json_key)
    
    {
        //LOG::warning($json_key);

         $required = parent::is_required($json_key);

        $default_rules = [
               
                'country_code' => "$required"

                
            ];
        
        if ($json_key =="loan_application_"){
            
            $default_rules['dp_rel_mgr_id'] = "$required";
            $default_rules['flow_rel_mgr_id'] = "$required";
            $default_rules['lender_code'] = "$required";
            $default_rules['acc_prvdr_code'] = "$required";
            $default_rules['cust_name'] = "$required";
            $default_rules['cust_id'] = "$required";
            $default_rules['cust_mobile_num'] = "$required";
            $default_rules['product_id'] = "$required";
            $default_rules['product_name'] = "$required";
            $default_rules['loan_principal'] = "$required|numeric";
            $default_rules['duration'] = "$required|numeric";
            $default_rules['flow_fee'] = "$required|numeric";
            $default_rules['due_amount'] = "$required|numeric";
            $default_rules['currency_code'] = "$required";
            $default_rules['appr_reason'] = "max:126";
            #$default_rules['customer_consent_rcvd'] = "$required";
            #$default_rules['photo_first_appl'] = "required_if:require_agreement,true";

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
