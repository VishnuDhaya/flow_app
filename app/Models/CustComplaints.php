<?php

namespace App\Models;
use App\Models\Model;
use Illuminate\Support\Facades\Log;


class CustComplaints extends Model
{
    const TABLE = "cust_complaints";

    const JSON_FIELDS = ['resolution'];
    
    const INSERTABLE = ["country_code", "raised_date", "complaint_type", "cust_id", "status", "remarks", "created_by",  "created_at"];

    const UPDATABLE =  ["resolved_date", "resolution", "status","updated_at", "updated_by"];

    public function model(){        
        return CustComplaints::class;
    
    }
}