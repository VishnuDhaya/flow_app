<?php

namespace App\Models;
use App\Models\Model;

//use Illuminate\Database\Eloquent\Model;

class VoiceCallLog extends Model
{
    const TABLE = "voice_call_logs";

    const JSON_FIELDS = ['details'];

    const CODE_NAME = 'vendor_ref_id';

    const INSERTABLE = ['country_code','direction','vendor_ref_id','vendor_code','mobile_num','purpose','details','person_id','status','created_at','updated_at', 'loan_appl_doc_ids'];

    const UPDATABLE = ['details','status','hang_up_cause','cost_of_call','created_at','updated_at'];

    public function model(){
        return self::class;
    }
}