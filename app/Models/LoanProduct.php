<?php

namespace App\Models;
use Log;

//use Illuminate\Database\Eloquent\Model;

class LoanProduct extends Model
{
     const TABLE = "loan_products";
     const UPDATABLE = ["product_name", "flow_fee","flow_fee_type" , "flow_fee_duration", "duration", "max_loan_amount","product_template_id","acc_prvdr_code","lender_code","cs_model_code","product_type","penalty_amount","loan_purpose"];
     
     const INSERTABLE = ["product_name", "flow_fee","flow_fee_type" , "flow_fee_duration", "duration", "max_loan_amount","product_template_id","acc_prvdr_code","lender_code","cs_model_code","country_code","product_type","penalty_amount","product_code","loan_purpose"];
	//$alias = ["owner_person" => Person,  "contact_person"]
     public static function rules($json_key)
	
    {
         $required = parent::is_required($json_key);

        $default_rules = [
               
                'product_name' => "$required",
                'product_code' => "$required|unique:loan_products",
                'product_type' => "$required",
                'flow_fee' => "$required|numeric",
                'flow_fee_type' => "$required",
                'flow_fee_duration' => "$required|regex:/^[\s\w-]*$/",
                'duration' => "$required|numeric",
                'max_loan_amount' => "$required|numeric",
                //'product_template_id' => "$required",
                'acc_prvdr_code' => "$required",
                'lender_code' => "$required",
                'cs_model_code' => "$required",
                "country_code" => "$required"
                
            ];
    	
    	if ($json_key =="LoanProduct"){
	        
            return $default_rules;
    	
        }else if($json_key =="loan_product_update"){
            
            return ['product_name' => "$required",
                    'cs_model_code' => "$required",
                    'penalty_amount' => "$required"];
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
            // 'product_name.required' => 'Product name is a required field',
            // 'interest_percent_days.required' => 'Interest precent in days is a required field',
            // 'flow_fee.required' => 'Flat Fee is a required field',
            // 'tenure.required' => 'Tenure is a required field',
            // 'max_loan_amount.required' => 'Max FA amount is a required field',
            // 'base_product_id.required' => 'Base Product ID is a required field',
            // 'data_provider_code.required' => "Data Provider ID is a required field",
            // 'lender_code.required' => "Lender ID is a required field",
            // 'credit_score_model_id.required' => "Credit Score Model ID is a required field",
            // 'product_code.regex' => 'The product code has already taken'
        ];
    }
}
