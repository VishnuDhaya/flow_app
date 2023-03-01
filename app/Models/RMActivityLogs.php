<?php

namespace App\Models;

use App\Models\Model;

class RMActivityLogs extends Model
{

    const TABLE = "rm_activity_log";

    const JSON_FIELDS = ['activities'];

    const INSERTABLE = ['country_code', 'rel_mgr_id', 'activities', 'date', 'created_at', 'created_by'];
   
    const UPDATABLE = ['id', 'activities', 'updated_at', 'updated_by'];

    public function model(){
        return self::class;
    }
}