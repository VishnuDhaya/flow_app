<?php

namespace App\Scripts\php;

use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Services\Support\SMSNotificationService;
use App\Services\DisbursalService;
use App\Services\AccountService;
use DB;
use Log;
use Consts;
use Illuminate\Support\Str;

class CashBackScript{

    public function send_cashback_and_sms(){

        $disb_serv = new DisbursalService();
        $pers_repo = new PersonRepositorySQL();
        $acc_repo = new AccountRepositorySQL();
        $acc_serv = new AccountService();
        $sms_notif = new SMSNotificationService();

        $cashbacks = DB::select("select cust_id, biz_name, mobile_num, dp_cust_id, cashback,dp_code from cash_backs where status in ('Called', 'Failed')");
        $err_log = array();
        $success_log = array();
        
        foreach($cashbacks as $cashback){
            $dp_code = $cashback->dp_code;
            
            $disb_acc = $acc_serv->get_lender_disbursal_account('UFLW', $dp_code);
            $account = $acc_repo->get_records_by_many(['cust_id', 'is_primary_acc'],[$cashback->cust_id, true],['id']);
            if(sizeof($account) == 0){
                $err_log[$cashback->cust_id] =  "No primary account";
                continue;
            }
            $loan_txn = ['to_ac_id' => $account[0]->id, 'amount' => $cashback->cashback];
            
            #$disb_resp = $disb_serv->make_instant_disbursal($loan_txn, (object)$disb_acc);
            $disb_resp = $this->test_make_instant_disbursal();

            if($disb_resp['status'] == 'success'){
               
                $success_log[] = $cashback->cust_id;
                $status = 'Transfered';

                $person = $pers_repo->get_person_by_cust_id($cashback->cust_id);
                

                $reward = [
                    'cust_name' => $person->first_name, 
                    'cashback' => $cashback->cashback,
                    'mobile_num' => $person->mobile_num,
                    'cust_id' => $cashback->cust_id,
                    'country_code' => session('country_code')
                ];
                

                $sms_notif->notify_reward($reward);
            }else{
                $status = 'Failed';
                $err_log[$cashback->cust_id] =  $disb_resp;

            }
        
            DB::update("update cash_backs set txn_id = ?, status = ? where cust_id = ?", [$disb_resp['txn_ids'] , $status, $cashback->cust_id]);
        }
        Log::warning($success_log);
        Log::warning($err_log);
    }
    private function test_make_instant_disbursal(){
        $disb_resp['status'] = 'success';
        $disb_resp['txn_ids'][0] = 'TXN-1';
        return $disb_resp;
    }

}
