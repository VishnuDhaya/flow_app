<?php

namespace App\Models;
use App\Models\Model;

class PaymentAttempt extends Model
{

    const UPDATABLE = ['status','updated_at','updated_by'];
    const INSERTABLE = ['loan_doc_id', 'status', 'flow_request', 'country_code', 'cust_id', 'created_by', 'updated_by', 'created_at', 'updated_at'];
    const TABLE = "payment_attempts";

    public function model(){
        return self::class;
    }
}
