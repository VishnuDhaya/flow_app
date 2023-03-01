<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Support\Facades\Log;

class InboundVoiceCallLogs extends Model
{
    const TABLE = "inbound_voice_call_logs";

    const INSERTABLE = ["vendor_ref_id", "country_code", "direction", "csm_number","cs_id","cust_id" , "date", "cust_number", "created_by", "updated_by", "created_at", "updated_at"];

    const UPDATABLE  = ["cust_id", "cust_number","call_duration", "direction", "status", "cs_id","hangup_causes", "cost_of_call", "recording_url"];

    public function model(){        
        return InboundVoiceCallLogs::class;
    }

}
