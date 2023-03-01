<?php

namespace App\Models;
use App\Models\Model;

// use Illuminate\Database\Eloquent\Model;

class CustFeedback extends Model
{
    const TABLE = "cust_feedbacks";

    // const CODE_NAME = "cust_id";
    const INSERTABLE = ['country_code', 'ratings', 'rm_id', 'cust_id', 'total_score', 'created_at', 'created_by'];

    const UPDATABLE = ['ratings', 'total_score', 'updated_at', 'updated_by'];

    const JSON_FIELDS = ['ratings'];

    public function model(){ 
        return CustFeedback::class;
    }
}