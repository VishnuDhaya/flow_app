<?php
namespace App\Scripts\php;
use App\Services\Schedule\ScheduleService;


class RWARecon{
    public static function run(){
        set_app_session('RWA');
        $s = new ScheduleService;
        $s->recon_debit_txns(4182, 'RBOK', 123);
        $s->recon_credit_txns(4182, 'RBOK', 123);
        
        $s->recon_debit_txns(4183, 'RMTN', 123);
        $s->recon_credit_txns(4183, 'RMTN', 123);
        
        $s->recon_debit_txns(4184, 'RMTN', 123);
        $s->recon_credit_txns(4184, 'RMTN', 123);
        
        $s->recon_debit_txns(4185, 'RMTN', 123);
        $s->recon_credit_txns(4185, 'RMTN', 123);

    }
}

