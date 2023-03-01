<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Model;

class LoanApprover extends Model
{
     const TABLE = "loan_approvers";

     // const UPDATABLE = ["product_name","interest_percent_per_day","flow_fee_type" , "flat_fee", "tenure_in_days", "max_loan_amount","base_product_id","data_provider_id","lender_id","credit_score_model_id","status"];
     
     const INSERTABLE = ["market_id","person_id","email_id","mobile_num"];

}
