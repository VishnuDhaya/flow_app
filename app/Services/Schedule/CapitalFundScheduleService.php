<?php
namespace App\Services\Schedule;

use App\Mail\FundUtilizationMail;
use App\Models\FundUtilizationReport;
use App\Repositories\SQL\CapitalFundRepositorySQL;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CapitalFundScheduleService{
    public function update_underused_funds(){
        $country_code = session('country_code');
        $funds = DB::select("select fund_name, alloc_amount, alloc_amount_fc, os_amount, fund_code from capital_funds where fund_type not in ('internal') and country_code = '$country_code'");

        foreach($funds as $fund){
            $fund->utilization_perc = $fund->os_amount / $fund->alloc_amount;
            $date = Carbon::now();
            $available_amount = $fund->alloc_amount - $fund->os_amount;
            if($available_amount > 0) {
                $record = ['fund_code' => $fund->fund_code,
                            'date' => $date,
                            'initial_amount' => $available_amount,
                            'current_amount' => $available_amount,
                            'util_perc' => $fund->utilization_perc,
                            'country_code' => $country_code];
                $repo = new FundUtilizationReport();
                $repo->insert_model($record);
            }
        }
        Mail::to(config('app.founder_emails'))->send(new FundUtilizationMail($funds, $country_code));
    }
}
