<?php

namespace App\Models;
use App\Models\Model;

//use Illuminate\Database\Eloquent\Model;

class AccountTxn extends Model
{
     //$updatable = ["name","lender_type","status"];
    const TABLE = "account_txns";
    
    const INSERTABLE = ["country_code", "acc_id", "txn_date", "acc_txn_type", "credit", "debit", "balance", "ref_acc_id", "txn_id", "txn_mode","txn_exec_by", "remarks", 'created_at', 'created_by'];

      public function model(){        
            return AccountTxn::class;
        }
    
    const UPDATABLE =  ["txn_date", "credit", "debit", "balance", "ref_acc_id"];

    public static function rules($json_key)
    {
        $default_rules = [
                //'country_code' => 'required',
                //'acc_id' => 'required',
                'txn_date' => 'required',
                'amount' => 'required',
                'acc_txn_type' => 'required',
                'txn_exec_by' => 'required',
                'txn_mode' => 'required'
                    ];

        if ($json_key == "acc_txn_create"){        
            return $default_rules;         
        }else{      
            return $default_rules;        
        }
    }
     /**
     * Custom message for validation
     *
     * @return array
     */
    public static function messages($json_key)
    {
        return [];
    }
}