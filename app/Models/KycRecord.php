<?php

namespace App\Models;
use App\Models\Model;
//use Illuminate\Database\Eloquent\Model;

class KycRecord extends Model
{
     const INSERTABLE = ["lead_id","cust_json_before","cust_json_now",'cust_id'];


     const TABLE = "kyc_records";

     const CODE_NAME = "cust_id";


     public function model(){
         return self::class;
     }

}
