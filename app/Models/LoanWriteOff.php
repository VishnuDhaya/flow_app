<?php

namespace App\Models;
use App\Models\Model;
//use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Rules\CCACustIDRule;
use App\Rules\UEZMCustIDRule;

class LoanWriteOff extends Model
{
    const TABLE = "loan_write_off";
    const CODE_NAME = "loan_doc_id";


    const INSERTABLE = ['loan_doc_id', 'country_code', 'acc_prvdr_code', 'write_off_status', 'write_off_amount', 'remarks', 'req_by', 'req_date', 'loan_prov_id', 'year', 'recovery_amount'];

    const UPDATABLE = ['recovery_amount', 'appr_by', 'appr_date', 'write_off_status'];
}