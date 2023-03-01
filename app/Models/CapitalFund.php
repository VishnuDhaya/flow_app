<?php

namespace App\Models;
use App\Models\Model;
use Illuminate\Support\Facades\Log;


class CapitalFund extends Model
{
    const TABLE = "capital_funds";
    const CODE_NAME = "fund_code";
    
    const INSERTABLE = ["country_code","fund_code","fund_name","lender_code", "fund_type","is_lender_default","alloc_date","alloc_amount_usd","status",
"alloc_amount","os_amount","earned_fee","total_alloc_cust","current_alloc_cust","status","created_by","updated_by","created_at","updated_at"];

    const UPDATABLE = ["os_amount","current_alloc_cust","earned_fee","fund_code"];


 
   
     /**
     * Custom message for validation
     *
     * @return array
     */
    public static function messages($json_key)
    {

        

        

    }
}
