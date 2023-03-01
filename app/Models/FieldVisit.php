<?php

namespace App\Models;
use App\Models\Model;
use Illuminate\Support\Facades\Log;


class FieldVisit extends Model
{
    const TABLE = "field_visits";

    const INSERTABLE = ["after_biz_hours", "force_checkin","force_checkin_reason","lead_id","cust_id","cust_name","loan_doc_id","country_code", "visitor_id","visitor_name","visit_start_time","visit_end_time",
"remarks","visit_purpose","time_spent","location","sch_date","sch_status","sch_purpose","sch_slot","sch_remarks","data_prvdr_cust_id",'cust_mobile_num','biz_name','owner_person_id','gps','shop_status','photo_visit_selfie','force_checkout','checkout_distance','cust_gps','cust_kyc_data','sch_status', 'sch_from'];

    const UPDATABLE = ["after_biz_hours", 'early_checkout',"lead_id","force_checkin","force_checkin_reason","visit_start_time",'visit_end_time','visit_purpose','remarks','sch_date','resch_id','sch_status','force_checkout','checkout_distance','gps','cust_id','cust_gps','force_checkout_reason','biz_name','checkin_distance', 'sch_from'];



   
     /**
     * Custom message for validation
     *
     * @return array
     */
    public static function messages($json_key)
    {

        

        

    }
}
