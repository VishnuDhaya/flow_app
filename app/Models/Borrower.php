<?php

namespace App\Models;
use App\Models\Model;
//use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Rules\CCACustIDRule;
use App\Rules\UEZMCustIDRule;

class Borrower extends Model
{
    const TABLE = "borrowers";
    const CODE_NAME = "cust_id";


    const INSERTABLE = ["guarantor1_name","guarantor2_name","lc_name","guarantor1_doc","guarantor2_doc","lc_doc","cust_id","lead_id","territory","aggr_valid_upto","current_aggr_doc_id","biz_type","country_code", "lender_code", "acc_prvdr_code","owner_person_id","owner_address_id","prob_fas","flow_rel_mgr_id","dp_rel_mgr_id","org_id","biz_address_id","biz_addr_prop_type","number_of_tills","tot_loan_appls","tot_loans","tot_default_loans","first_loan_date", 'biz_name','fund_code','remarks', "photo_shop","master_cust_id","photo_biz_lic","reg_date", 'category','csf_run_id','kyc_status','status','last_visit_date','ownership','business_distance','gps','cust_eval_id','visit_id', 'location', "reg_flow_rel_mgr_id", "cust_acc_id", "acc_number", "last_kyc_date", "file_data_consent"];


    const UPDATABLE = ['allow_tp_ac_owner_manual_id_capture',"pre_appr_count","pre_appr_exp_date","cust_id", 'fund_code',"dp_rel_mgr_id", "prob_fas","flow_rel_mgr_id", "lender_code","biz_addr_prop_type","number_of_tills","tot_loan_appls","tot_loans","tot_default_loans","first_loan_date",'biz_name', 'remarks', "photo_shop", "master_cust_id", "photo_biz_lic", "current_aggr_doc_id", "aggr_valid_upto", 'has_ongoing_loan','acc_prvdr_code','pending_loan_appl_doc_id','ongoing_loan_doc_id','is_og_loan_overdue','csf_run_id','reg_date', 'late_2_day_plus_loans', 'late_2_day_loans','late_3_day_loans', 'late_1_day_loans', 'late_loans', 'ontime_loans', 'category','last_loan_date','late_3_day_plus_loans','last_loan_doc_id','kyc_status','last_visit_date', 'perf_eff_date', 'cond_count','ownership','aggr_status','business_distance', 'profile_status', "activity_status", "is_otp_verified","gps","location", "reg_flow_rel_mgr_id", 'territory', 'risk_category', "cust_acc_id", "acc_number", 'owner_person_id', 'biz_address_id', 'owner_address_id', 'lead_id', 'last_kyc_date','next_visit_date','temp_first_conf_code_sent','allow_force_checkin_on', 'file_data_consent', 'fa_upgrade_id', 'rm_feedback_due'];

    #TODO remove temp_first_conf_code_sent



    public static function rules($json_key)
    {
        $required = parent::is_required($json_key);
        
        $default_rules = [
                'country_code' => "$required|max:3",
                'dp_rel_mgr_id' => "$required",
                'lender_code' => "$required",
                'flow_rel_mgr_id' => "$required",
                'biz_name' => "$required",
                'biz_addr_prop_type' => "$required",
                'ownership' => "$required",
                'business_distance' => "$required",
                'reg_date' => "$required",
        
            ];

        if (in_array(request()->get('acc_prvdr_code'),config('mobile.prm_optional_for_dps'))){
            unset($default_rules['dp_rel_mgr_id']);
        }
    

        /*if ($this->attributes->get('data_prvdr_code') == 'CCA') {
            $default_rules['data_prvdr_cust_id'] = [$required, new CCACustIDRule];
            
        }else if ($this->attributes->get('data_prvdr_code') == 'UEZM') {
           $default_rules['data_prvdr_cust_id'] = [$required, new UEZMCustIDRule];
            
        }*/

        if ($json_key == "borrower"){

            return $default_rules;       
        }else if($json_key == "borrower_update"){
          
            return $default_rules;
        }else if($json_key == "reference_borrower_create"){

                $rules = ['cust_id' => '$required',
                        'reference_borrower_id' => '$required'
                    ];

                    return $rules;
        }elseif($json_key == "add_lender_create"){

                $rules = ['cust_id' => '$required',
                        'lender_code' => '$required'
                    ];

                    return $rules;
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

        return[];
        if($json_key == "borrower")
        {
        return [
            'country_code.$required' => 'Market id $required!',
            'acc_prvdr_code.$required' => 'Account provider code $required!',
            'biz_type.$required' => 'Business type $required!',
            'ownership' => 'Ownership is $required!',
            
        ];
        }
        elseif($json_key == "reference_borrower"){

            return [
            'borrower_id.$required' => 'Borrower id $required!',
            'reference_borrower_id.$required' => 'Reference borrower id $required!',
                       
        ];

        }
        elseif($json_key == "add_lender"){

            return [
            'borrower_id.$required' => 'Borrower id $required!',
            'lender_code.$required' => 'Lender id $required!',
                       
        ];

        }

    }
}
