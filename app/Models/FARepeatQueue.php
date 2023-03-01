<?php
namespace App\Models;
use App\Models\Model;
use Illuminate\Support\Facades\Log;


class FARepeatQueue extends Model
{
    const TABLE = "fa_repeat_queue";

    const INSERTABLE = ["cust_id","country_code","loan_doc_id","status", "mobile_num", "created_at", "created_by"];

    const UPDATABLE = ['status', "updated_at", "updated_by"];

    const CODE_NAME = "loan_doc_id";


    public function model(){
        return FARepeatQueue::class;
    }
}
