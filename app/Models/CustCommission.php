<?php

namespace App\Models;

use App\Models\Model;

class CustCommission extends Model
{
    const INSERTABLE = ["country_code","acc_prvdr_code","acc_number","alt_acc_num","year","commissions"];
    
    const UPDATABLE = ["acc_number", "commissions"];

    const JSON_FIELDS = ["commissions"];

	const TABLE = "cust_commissions";

    public function model(){
        return self::class;
	}
}
