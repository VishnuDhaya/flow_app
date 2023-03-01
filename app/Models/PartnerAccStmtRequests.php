<?php

namespace App\Models;
use App\Models\Model;
use Log;

class PartnerAccStmtRequests extends Model
{
    const INSERTABLE = ["acc_prvdr_code", "flow_req_id", "orig_flow_req_id", "req_time", "acc_number", "country_code", "object_key", "presigned_url", "start_date", "end_date", "lead_id"];
    const UPDATABLE = ["ap_req_id", "resp_time", "status", "object_key", "error_message", "lambda_status"];
    const TABLE = "partner_acc_stmt_requests";

    public function model(){
    	    return self::class;
	}

    public static function rules($json_key){
        $required = Model::is_required($json_key);
    
        $default_rules = ['acc_number' => "$required"];
    
        if ($json_key == 'partner_data_notify_repayment') {
            $rules = [
                        'repayment_acc_number' => "$required",
                        'payment_amount' => "$required",
                        'repayment_acc_number' => "$required",
                        'payment_datetime' => "$required|date_format:Y-m-d H:i:s",
                        'payment_txn_id' => "$required",
                    ];
            return array_merge($default_rules, $rules);
        }

        if ($json_key == 'partner_data_notify_cust_interest') {
            $rules = [
                        'mobile_num' => "$required|regex:/^[^0][0-9]{8}$/|digits:9",
                        'first_name'=> "$required|min:3|max:20|regex:/^[\pL\s\-]+$/u",
                        'last_name'=> "$required|min:3|max:20|regex:/^[\pL\s\-]+$/u",
                        'latitude' => 'numeric|between:-90,90',
                        'longitude' => 'numeric|between:-180,180',
                    ];
            return array_merge($default_rules, $rules);
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
            // partner_data_notify_repayment
            'acc_number.required' =>  "Please provide the agent's account number",
            'repayment_acc_number.required' =>  "Please provide the repayment account number",
            'payment_amount.required' => "Payment amount is a required field",
            'payment_datetime.required' => "Payment datetime is a required field",
            'payment_txn_id.required' => "Payment txn id is a required field",
            // partner_data_notify_cust_interest
            'mobile_num.required' => "Mobile number is a required field",
            'mobile_num.max' => 'Mobile number should contain maximum of 10 characters',
            'mobile_num.min' => 'Mobile number should contain minimum of 10 characters', 
            'first_name.required' => "Please provide the agent's First Name",
            'last_name.required' => "Please provide the agent's Last Name",
            'landmark.required' => "Landmark is a required field",
        ];
    }
}
