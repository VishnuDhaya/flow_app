<?php

namespace App\Scripts\php;

use App\Repositories\SQL\PersonRepositorySQL;
use App\Services\InvApp\InvAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvestmentReport
{
    public function generateReport($start,$end){
        session()->put('country_code','UGA');
        $start_date = carbon::parse($start)->subMonth();
        $end_month = carbon::parse($end)->format('Ym');
        $persons = DB::table('investment_txns')->distinct('person_id')->pluck('person_id');
        $investment_report = [];
        $report =[];
        $personsRepo = new PersonRepositorySQL();
        $invServ = new InvAppService();
        foreach ($persons as $person_id){
            $report['Name'] = $personsRepo->full_name($person_id);
            $currency = DB::table('investment_txns')->where(['person_id' => $person_id, 'txn_type' => 'investment'])->pluck('currency_code');
            session()->put('currency_code',$currency[0]);
            $currency_code = get_currency_sign($currency[0]);
            $funds = $invServ->get_invested_bonds($person_id,carbon::parse($end)->endOfMonth());
            if($funds == null){
                continue;
            }
            $report['Funds'] = implode(',',$funds);
            $payout = $investment = $disbursed = $customer = $return_profit = $last_payout =  $month_diff =$earn = $tot_rtn = $alloc_date = 0;
            foreach ($funds as $fund){
                $alloc_date = carbon::parse($invServ->get_alloc_date($fund));
                $start_date_diff = $alloc_date->diffInDays($start_date, false);
                $end_date_diff = $alloc_date->diffInDays(carbon::parse($end)->endOfMonth(),false);
                if($end_date_diff < 0){
                    continue;
                }
                if($start_date_diff < 0){
                    $start_month  = carbon::parse($alloc_date)->subMonth()->format('Ym');
                }
                else{
                    $start_month  = carbon::parse($start)->subMonth()->format('Ym');
                }
                session()->put('report_date',carbon::parse($start)->subMonth()->endOfMonth());
                $bond_detail_start = $invServ->get_bond_summary($start_month,$fund,$person_id);
                session()->put('report_date',carbon::parse($end)->endOfMonth());
                $bond_detail_end = $invServ->get_bond_summary($end_month,$fund,$person_id);
                $last_payout += $bond_detail_start->earnings;
                $payout += ($bond_detail_end->earnings - $bond_detail_start->earnings)    ;
                $earn += $bond_detail_end->earnings;
                $investment += $bond_detail_end->invested_amount;
                $disbursed += $bond_detail_end->total_disbursed -$bond_detail_start->total_disbursed;
                $customer += $bond_detail_end->current_alloc_cust;
                $diff_days = (carbon::createFromFormat('Ym',$start_month)->addMonthNoOverflow(1)->startOfMonth())->diffInDays(carbon::parse($end)->endOfMonth());
                $month_diff = $diff_days/30.42;
                $diff_tot = (carbon::parse($alloc_date))->diffInDays(carbon::parse($end)->endOfMonth());
                $diff_tot = $diff_tot/30.42;
                $return_profit += ((($payout / $month_diff) * 12)/$investment) * 100;
                $tot_rtn += ((($earn/ $diff_tot) * 12)/$investment) * 100;
            }
            $report['Total Invested Amount'] = $currency_code." ". $investment;
            $report['Payout So Far'] = $currency_code ." ". round($last_payout);
            $report['Current Payout'] = $currency_code ." ". round($payout);
            $report['Total Value'] = $currency_code." ". round($investment + $payout + $last_payout);
            $report['Customers'] = number_format($customer,0);
            $report['current ARR'] = is_nan($return_profit) ? 0.0 : round($return_profit,1);
            $report['current ARR Period'] = carbon::parse($start)->format('M y') . " - " . carbon::parse($end)->format('M y');
            $report['Total ARR'] = number_format($tot_rtn,2);
            $report['Total ARR Period'] = carbon::parse($alloc_date)->format('M y') ." _ ".carbon::parse($end)->format('M y');
            array_push($investment_report,$report);
        }
        $argv['data'] = $investment_report;
        $argv['file_name'] = carbon::parse($start)->format('M y') . " - " . carbon::parse($end)->format('M y');
        $argv = json_encode($argv);
        $resp = run_python_script('array_to_csv',$argv);
    }
}