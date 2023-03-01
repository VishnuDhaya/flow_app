<?php

namespace App\Models;
use App\Models\Model;

class SmsLog extends Model
{

    const UPDATABLE = ['status','callback_json','purpose'];
    const INSERTABLE = ['vendor_ref_id', 'vendor_code', 'otp_id', 'status', 'direction', 'purpose', 'content', 'mobile_num','country_code','loan_doc_id','cust_id'];
    const TABLE = "sms_logs";

    	public function model(){
			#return get_class($cls_name);
    	    return SmsLog::class;
	}
}
