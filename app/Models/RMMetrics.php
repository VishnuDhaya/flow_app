<?php

namespace App\Models;
use App\Models\Model;
use Log;

class RMMetrics extends Model
{

    const INSERTABLE = ['30_days_appr_count',"rm_id", "appr_count", "max_time", "avg_time",'country_code','30_days_avg_time','30_days_max_time'];
    const UPDATABLE = ['30_days_appr_count',"rm_id", "appr_count", "max_time", "avg_time",'30_days_avg_time','30_days_max_time'];
    const TABLE = "rm_metrics";

    const CODE_NAME = "rm_id";

    public function model(){
    	    return self::class;
	}

}
