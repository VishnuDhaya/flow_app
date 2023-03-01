<?php
namespace App\Services\Support;

use Illuminate\Database\QueryException;
use App\Exceptions\FlowCustomException;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Carbon\CarbonPeriod;

class ReportMetrics {

    public static  function _get_max_os($start_date_obj, $end_date_obj){

		$start_date = $start_date_obj->format('Y-m-d');
		$end_date = $end_date_obj->format('Y-m-d');

    	$loans = DB::select("select loan_doc_id, loan_principal, disbursal_date, paid_date from loans 
    	where disbursal_date <=  ? and (paid_date > ? or paid_date is NULL)", 
    	[$start_date, $end_date]);

        $ranges =  CarbonPeriod::create($start_date, $end_date, CarbonPeriod::EXCLUDE_START_DATE);
        Log::warning($ranges);
        $outstanding_array = [];
        foreach ($ranges as $date) {
            $date =  $date->format('Y-m-d');
            $amount = 0;
           
            foreach ($loans as $loan) {
                $disbursal_date = date('Y-m-d', strtotime($loan->disbursal_date));
                if($loan->paid_date != NULL){
                    $paid_date = date('Y-m-d', strtotime($loan->paid_date));
                    if (($date >= $disbursal_date) && ($date < $paid_date)){
                        $amount = $amount + $loan->loan_principal;
                        }
                }else{
                    if ($date >= $disbursal_date){
                    $amount = $amount + $loan->loan_principal;
                    }
                }
            }

            $outstanding_array [$date] = $amount;
        }
        Log::warning('outstanding_array');
        Log::warning($outstanding_array);
        $max_os_amount = 0;
        $max_os = array();
        foreach ($outstanding_array as $key => $value){
            $value = intval($value);
            if ($max_os_amount < $value){
                
                $date = $key;
                $max_os_amount = $value;
                
            } 
        }
        // $max_os['max_os_amt'] = $max_os_amount;
        // $max_os['date'] = $date;
        return $max_os_amount;
    }
}           