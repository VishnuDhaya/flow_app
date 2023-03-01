<?php

namespace App\Models;
use App\Models\Model;
//use Illuminate\Database\Eloquent\Model;

class LoanRecovery extends Model
{
     const INSERTABLE = ["country_code","loan_doc_id","amount",'cust_id','rs_id','status','biz_name'];

     const UPDATABLE = ['status'];

     const TABLE = "loan_recoveries";

     const CODE_NAME = "id";


     public function model(){
         return self::class;
     }

      public static function rules($json_key)

    {
         $required = parent::is_required($json_key);

        $default_rules = [

                'amount' => "$required|numeric",

            ];


            return $default_rules;


    }



     /**
     * Custom message for validation
     *
     * @return array
     */
    public static function messages($json_key)
    {
        return [
                'amount.required' => 'Amount is a required field',
               ];
    }

}
