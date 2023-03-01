<?php

namespace App\Models;
use App\Models\Model;

class CustCSFValues extends Model
{
  
	const INSERTABLE = ["country_code","acc_prvdr_code","acc_number","cust_score_factors","score","result","conditions","run_id"];
	const UPDATABLE = ["score","result","conditions","run_id"];

	const TABLE = "cust_csf_values";

	const JSON_FIELDS = ['cust_score_factors', 'conditions'];

    public function model() {
    	    return self::class;
	}
}
