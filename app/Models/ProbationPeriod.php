<?php

namespace App\Models;
use App\Models\Model;

class ProbationPeriod extends Model
{
     const TABLE = "probation_period";
     
     const UPDATABLE = ["end_date", "updated_at","status","start_date"];
     const INSERTABLE = ["cust_id","start_date", "type", "fa_count", "status","country_code","created_at"];
}
