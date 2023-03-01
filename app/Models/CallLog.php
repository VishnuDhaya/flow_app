<?php

namespace App\Models;
use App\Models\Model;
use Illuminate\Support\Facades\Log;


class CallLog extends Model
{


	 const TABLE = "call_logs";

    
    const INSERTABLE = ["cust_id","cust_name","country_code", "call_logger_id","call_logger_name","call_start_time","call_end_time",
"remarks","call_purpose","time_spent","call_type","timestamp",'loan_doc_id'];

    const UPDATABLE = ['call_end_time','call_purpose','remarks'];
}