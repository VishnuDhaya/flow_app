<?php

namespace App\Models;
use App\Models\Model;

//use Illuminate\Database\Eloquent\Model;

class LoansView extends Model
{
    const TABLE = "loans_view";

    const CODE_NAME = "loan_doc_id";

    public function model(){
        return self::class;
    }
}