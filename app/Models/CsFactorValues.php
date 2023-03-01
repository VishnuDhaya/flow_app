<?php

namespace App\Models;
use App\Models\Model;

class CsFactorValues extends Model
{
    const INSERTABLE = ["country_code", "csf_group", "csf_type", "value_from", "value_to", "normal_value", "status"];
    const UPDATABLE = ["value_from", "value_to"];
    const TABLE = "cs_factor_values";

    public function model(){
    	    return self::class;
	}
}
