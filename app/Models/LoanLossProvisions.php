<?php

namespace App\Models;
use App\Models\Model;
//use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Rules\CCACustIDRule;
use App\Rules\UEZMCustIDRule;

class LoanLossProvisions extends Model
{
    const TABLE = "loan_loss_provisions";

    const INSERTABLE = ['country_code', 'data_prvdr_code', 'prov_amount', 'balance'];

    const UPDATABLE = ['requested_amount', 'balance'];
}