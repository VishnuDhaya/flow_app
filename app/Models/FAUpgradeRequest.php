<?php

namespace App\Models;
use App\Models\Model;
use Log;

class FAUpgradeRequest extends Model
{

    const INSERTABLE = ['cust_id',"available_amounts", "requested_amount", "crnt_fa_limit", "approval_json",'country_code', 'type', 'status', 'acc_prvdr_code'];
    const UPDATABLE = ['upgrade_amount',"approval_json", 'status'];
    const TABLE = "fa_upgrade_requests";

    const CODE_NAME = "cust_id";

    public function model(){
        return self::class;
    }

}