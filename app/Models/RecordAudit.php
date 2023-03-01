<?php

namespace App\Models;
use App\Models\Model;
use Illuminate\Support\Facades\Log;


class RecordAudit extends Model
{
    const TABLE = "record_audits";

    
    const INSERTABLE = ["remarks","data_after","data_before","country_code","record_type","record_id",
    "record_code","audit_type"];

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
