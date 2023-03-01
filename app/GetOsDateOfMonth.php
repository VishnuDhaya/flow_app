<?php
namespace App;

use Illuminate\Database\QueryException;
use App\Exceptions\FlowCustomException;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Carbon\CarbonPeriod;
class GetOsDateOfMonth
{

	public function __construct(){
		$month_data = $this->get_data();
		
		$this->get_outstanding_amount($month_data);
        
    }
    public function get_data(){
		#DB::statement("SET @runtot:=0; SELECT q1.disbursal_date, q1.loan_principal, (@runtot := @runtot + q1.loan_principal) AS rt FROM (select loan_doc_id, loan_principal, disbursal_date, paid_date from loans where paid_date > '2019-12-31' or (disbursal_date <=  '2020-01-31' and paid_date is NULL) group by disbursal_date order by loan_doc_id) as q1");

    	$month_data = DB::select("select loan_doc_id, loan_principal, disbursal_date, paid_date from loans where paid_date > '2019-12-31' or (disbursal_date <=  '2020-01-31' and paid_date is NULL) order by loan_doc_id");
    	return $month_data;


    }
    public function get_outstanding_amount($month_data){
    	$ranges =  CarbonPeriod::create('2020-01-01', '2020-01-31');
    	$outstanding_array = [];
    	foreach ($ranges as $date) {
    		$single_info = array();
    		$date =  $date->format('Y-m-d');
    		$amount = 0;
    		
    		foreach ($month_data  as $data) {
    			$disbursal_date = date('Y-m-d', strtotime($data->disbursal_date));
    			if($data->paid_date != NULL){
					$paid_date = date('Y-m-d', strtotime($data->paid_date));
					if (($date >= $disbursal_date) && ($date < $paid_date)){
	    				$amount = $amount + $data->loan_principal;
	    			}	
				}else{
					if ($date >= $disbursal_date){
						$amount = $amount + $data->loan_principal;
					} 
				}
    		}

    		$outstanding_array [$date] = [$amount];
    		#Log::warning($outstanding_array);	
    		#die;
		}

		Log::warning('outstanding_array');
		Log::warning($outstanding_array);	
    	
    }
        
    
}
