<?php

namespace App\Scripts\php;
use App\Services\Schedule\ScheduleService;
use Illuminate\Support\Facades\Log;

class UGARecon{

    public static function run(array $accounts){

        set_app_session('UGA');

        $serv = new ScheduleService();

        // $accounts = [["acc_prvdr_code" => 'CCA', 'id' => 1783]];

        foreach($accounts as $account){

            $acc_prvdr_code = $account['acc_prvdr_code'];
            $account_id = $account['id'];
            $recon_id = rand(100, 999);

            $serv->recon_debit_txns($account_id, $acc_prvdr_code, $recon_id);
            $serv->recon_credit_txns($account_id, $acc_prvdr_code, $recon_id);
        }
    }
}


?>