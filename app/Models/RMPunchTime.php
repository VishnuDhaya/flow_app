<?php

namespace App\Models;

use App\Models\Model;

class RMPunchTime extends Model
{

    const TABLE = "rm_punch_time";

    const INSERTABLE = ['country_code', 'rel_mgr_id', 'date', 'punch_in_time', 'punch_out_time', 'created_at', 'created_by'];
   
    const UPDATABLE = ['punch_out_time', 'updated_at', 'updated_by'];

    public function model(){
        return self::class;
    }
}