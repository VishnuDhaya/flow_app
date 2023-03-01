<?php

namespace App\Models;
use App\Models\Model;
use Illuminate\Support\Facades\Log;


class CsDevices extends Model
{
    const TABLE = "cs_devices";
    
    const INSERTABLE = ["country_code", "date", "type", "number", "person_id", "call_status", "status", "created_by",  "created_at"];

    const UPDATABLE =  ["date", "call_status", "call_duration", "status","updated_at", "updated_by"];

    public function model(){        
        return CsDevices::class;
    
    }
}
