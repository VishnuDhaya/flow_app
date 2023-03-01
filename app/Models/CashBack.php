<?php

namespace App\Models;
use App\Models\Model;
use Illuminate\Support\Facades\Log;


class CashBack extends Model
{
    const TABLE = "cash_back";
    const CODE_NAME = "cust_id";
    
    const UPDATABLE = ["status","transfer_status",'txn_id'];

}
