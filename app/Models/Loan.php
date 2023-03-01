<?php

namespace App\Models;
use App\Models\Model;
//use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
	const CODE_NAME = "loan_doc_id";


	const INSERTABLE = ['pre_appr_id','penalty_waived','loan_purpose','disbursal_status',"due_date","disbursal_date","loan_appl_id","loan_appl_date","fund_code",'paid_principal' ,'paid_fee','paid_excess',"loan_approver_name","loan_approver_id","dp_rel_mgr_id","flow_rel_mgr_id","lender_code","cust_name","cust_id","cust_mobile_num","cust_acc_id","product_id","product_name","loan_principal","duration","flow_fee","due_amount","current_os_amount", "paid_amount" ,"waiver_amount","cs_model_id","credit_score","cust_addr_text" , "loan_doc_id", 'flow_fee_type', 'flow_fee_duration', 'currency_code', 'country_code', 'lender_code', 'loan_applied_by','loan_approved_date','status','master_loan_doc_id','biz_name','provisional_penalty','gs_comments','paid_by','cs_result_code','approver_role','customer_consent_rcvd', 'acc_prvdr_code', 'acc_number','payment_status','review_reason','loan_event_time', 'applied_location'];


	 const UPDATABLE = ['loan_approver_id','flow_rel_mgr_id','penalty_waived','due_date', 'status','disbursal_date','fund_code','paid_principal' ,'paid_fee','paid_excess', 'due_amount', 'current_os_amount','paid_amount','provisional_penalty', 'penalty_collected', 'paid_date', 'ref_row_id','gs_comments','paid_by','loan_approver_name',"customer_consent_rcvd","allow_pp",'disbursal_status','acc_number', 'acc_prvdr_code', 'partner_loan_id','payment_status','review_reason', 'write_off_id', 'write_off_status', 'cust_conf_channel', 'conf_otp_id', 'loan_appl_date', 'loan_approved_date', 'penalty_days', 'overdue_days', 'manual_disb_user_id','loan_event_time','cust_gps'];

     const JSON_FIELDS = ['loan_event_time'];

	 const TABLE = "loans";

     public function model(){
        return self::class;
     }
	  public static function rules($json_key)
    
    {
         $required = parent::is_required($json_key);

        $default_rules = [
        	   'country_code' => "$required",
        	   //'credit_score' => "$required|numeric|max:1000"
                             
            ];
        
        
            if ($json_key =="loan_request"){

            $default_rules['loan_principal'] = "$required|numeric";
            $default_rules['duration'] = "$required|numeric";
            $default_rules['flow_fee'] = "$required|numeric";
            $default_rules['due_amount'] = "$required|numeric";
            $default_rules['current_os_amount'] = "$required|numeric";
            $default_rules['paid_amount'] = "$required|numeric";
            $default_rules['waiver_amount'] = "$required|numeric";
            	
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
