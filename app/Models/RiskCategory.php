<?php

namespace App\Models;
use App\Models\Model;
//use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Rules\CCACustIDRule;
use App\Rules\UEZMCustIDRule;

class RiskCategory extends Model
{
    const TABLE = "risk_category_rules";

}

