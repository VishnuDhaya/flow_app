<?php

namespace App\Models;

use App\Models\Model;

class RMLocation extends Model
{

    const TABLE = "rm_locations";
   
    const JSON_FIELDS = ['locations'];
   
    const INSERTABLE = ['country_code', 'rel_mgr_id', 'locations', 'date', 'created_at', 'updated_at', 'created_by', 'updated_by'];
   
    const UPDATABLE = ['locations', 'date', 'created_at', 'updated_at', 'created_by', 'updated_by'];

    public function model(){
        return self::class;
    }
}