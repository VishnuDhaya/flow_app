<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ScoreModelsExport;
use App\Exports\MerchantValidation;
use App\Exports\CustomerStatus;
use Excel;

class ScriptController extends Controller
{
    public function scoreModelsReport(){

        return Excel::download(new ScoreModelsExport, 'score_models.xlsx');
    }

    public function custValidationReport(){

        return Excel::download(new MerchantValidation, 'customer_validation.xlsx');
    }

    public function custStatusReport(){

        return Excel::download(new CustomerStatus, 'customer_status.xlsx');

    }
}
