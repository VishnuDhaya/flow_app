<?php

namespace App\Models;
use App\Models\Model;
use Log;

class FundUtilizationReport extends Model
{

    const INSERTABLE = ["fund_code", "date", "initial_amount", "current_amount", "util_perc", "country_code"];
    const UPDATABLE = ["current_amount", "util_perc"];
    const TABLE = "fund_utilization_reports";

    public function model(){
    	    return self::class;
	}

}
