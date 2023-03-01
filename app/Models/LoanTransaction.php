<?php

namespace App\Models;
use App\Models\Model;
//use Illuminate\Database\Eloquent\Model;

class LoanTransaction extends Model
{

     const INSERTABLE = ["photo_transaction_proof","loan_doc_id","from_ac_id","to_ac_id","amount","txn_id","remarks","txn_mode","txn_date","txn_exec_by","txn_type", "country_code", "reason_for_skip", "principal", "fee", "penalty", "excess"];
     const UPDATABLE = ["remarks", 'write_off_id', 'txn_id', 'recon_amount', 'amount'];
     const TABLE = "loan_txns";
      public static function rules($json_key)
    
    {
         $required = parent::is_required($json_key);

        $default_rules = [
             
                'amount' => "$required|numeric",
                'penalty_collected' => "numeric|nullable",
                // 'txn_id' => "$required|regex:/^[a-zA-Z0-9]*$/u",
                'txn_id' => "$required",
                'txn_mode' => "$required",
                //'txn_date' => "$required|date_format:Y-m-d\TH:i|before_or_equal:today",
                'txn_date' => "$required|date_format:Y-m-d|before_or_equal:today",
                'to_ac_id' => "$required"
            ];
            
        
        if ($json_key == "instant_disbursal_txn_instant_disburse" ){
             return ['amount' => "$required|numeric",
             'to_ac_id' => "$required",
             "to_acc_num" => "$required",
             "acc_prvdr_code" => "$required",
             "lender_code" => "$required",
             "loan_doc_id" => "$required",
             "cust_id" => "$required",
                 
            ];
        }            
        else if ($json_key == "disbursal_txn_disburse" ){

            $default_rules['from_ac_id'] = "$required";
            
            return $default_rules;
        
        }else if($json_key == "repayment_txn_capture_repayment"){

            return $default_rules;

           return $rules;
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
   
    
    
    'amount.required' => 'Amount is a required field',
    'txn_id.required' => 'Transaction id is a required field',
    'txn_mode.required' => 'Transaction mode is a required field',
    'remarks.required' => 'Remarks is a required field',
    'txn_date.required' => 'Transaction date is a required field',
  

        ];
    }

}
