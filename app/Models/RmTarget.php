<?php

namespace App\Models;
use App\Models\Model;



class RmTarget extends Model
{
    const TABLE = "rm_targets";

    const JSON_FIELDS = ['targets'];

    const CODE_NAME = "rel_mgr_id";
    
    const INSERTABLE = ['country_code', 'rel_mgr_id', 'rm_name', 'targets', 'year', 'created_at', 'updated_at', 'created_by', 'updated_by'];

    const UPDATABLE  = ['rel_mgr_id', 'rm_name', 'year','targets', 'created_at', 'updated_at', 'created_by', 'updated_by'];

    public function model(){
        return self::class;
    }

}