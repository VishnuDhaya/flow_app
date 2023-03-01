<?php

namespace App\Models;
use App\Models\Model;
use Illuminate\Support\Facades\Log;


class CustEvalChecklist extends Model
{
    const TABLE = "cust_eval_checklists";
    
    const INSERTABLE = ["country_code","acc_prvdr_code","acc_number","biz_name","checklist_json","rm_recommendation","created_by","updated_by","created_at","updated_at",'cust_kyc_data','visit_id'];

    const UPDATABLE = [];


 
   
     /**
     * Custom message for validation
     *
     * @return array
     */
    public static function messages($json_key)
    {

        

        

    }
}
