<?php

namespace App\Services;
use Log;
use DB;
use stdClass;
use Carbon\Carbon;
class Factors {

	public function __construct($cust_id, $perf_eff_date, $true_eff_date = false)
    {
		
		if($true_eff_date){
			$this->perf_eff_date = $perf_eff_date;
		}else{
			$this->perf_eff_date = calc_perf_eff_date($perf_eff_date);
		}
		
		$this->cust_id = $cust_id;
		$this->calc_perf_data($cust_id, $this->perf_eff_date);
		
	}

	private function calc_perf_data($cust_id, $perf_eff_date){
		
		$perf_data = DB::select("select count(id) as perf_tot_loans, 
				count(if(datediff(paid_date,due_date) > 0, 1, null)) as perf_late_loans, 
				count(if(datediff(paid_date,due_date) = 1, 1, null)) as perf_late_1_day_loans 
				from loans where cust_id = ? and date(disbursal_date) >= date(?)",[$cust_id, $perf_eff_date]);
	
		$this->perf_tot_loans = $perf_data[0]->perf_tot_loans;
		$this->perf_late_loans = $perf_data[0]->perf_late_loans - $perf_data[0]->perf_late_1_day_loans;
		
    }


	public function _ontime_loans_pc(){
		
		return  100 * (1 - div($this->perf_late_loans, $this->perf_tot_loans));
	}
	
	public function _repaid_after_N_days_pc($N){
		
		//$format_eff_date = date("Y-m-d", strtotime($perf_data['perf_eff_date']));
		
		$tot_delayed_loans = DB::table('loans')
								->where("cust_id", $this->cust_id)
								->whereRaw("DATEDIFF(paid_date , due_date) > ? ",[$N])
								->whereRaw("date(disbursal_date) >= date(?)", [$this->perf_eff_date])
								->count();

		return 100 * (div($tot_delayed_loans, $this->perf_tot_loans));
	}

	public function _number_of_advances_till_now(){
		
		return $this->perf_tot_loans;
	}

	public function _number_of_advances_per_quarter(){
		//$perf_eff_date = (new static)->calc_perf_eff_date($borrower_info->perf_eff_date);
		
		$client_since = Carbon::now()->diffInMonths($this->perf_eff_date);
		
		if($client_since <= 2 || $this->perf_tot_loans == null){
			return 0;
		}
		return $this->perf_tot_loans / ($client_since/3);
	}

	public function _avg_days_delayed_per_FA(){
		$loans = DB::table('loans')
					->select('due_date', 'paid_date', 'status')
					->where('cust_id',$this->cust_id)
					->whereRaw("date(disbursal_date) >= date(?)", [$this->perf_eff_date])->get();
					
		$sum_of_loans_delay = 0;
		foreach($loans as $loan){
			
			$overdue_days = get_od_days($loan->due_date, $loan->paid_date, $loan->status);
			$sum_of_loans_delay = $sum_of_loans_delay + $overdue_days;
		}
	
		$avg_days = $this->perf_tot_loans != 0 ? $sum_of_loans_delay / $this->perf_tot_loans : 0;
		return $avg_days;
	}
	public static function normalize($acc_number, $country_code, $perf_factors){

		$normalized_arr = array();		
		Log::warning($perf_factors);
		$normal_val_10_for_0 = ['repaid_after_3_days_pc', 'repaid_after_10_days_pc', 'repaid_after_30_days_pc','delay_days_per_fa'];

		foreach($perf_factors as $csf_type => $value){
				
				if($value == 0){
					if(in_array($csf_type, $normal_val_10_for_0)){
						$normal_value = 10;						
					}else{
						$normal_value = 0;
					}
				}else{
					$normal_value = DB::table('cs_factor_values')
												///->where('country_code', $country_code)
												->where('csf_type', $csf_type)
												->where('value_from', '<' , $value)
												->where('value_to', '>=' , $value)
												->where('country_code', session('country_code'))
												->where('status', 'enabled')
												->pluck('normal_value')->first();
					if($normal_value === null){
						thrw("Normalization range not configured for the factor : '$csf_type'");
					}
				}
				
				##Log::warning($normal_value);
				$csf_obj = new stdClass();
				$csf_obj->csf_type = $csf_type;
				$csf_obj->n_val = $normal_value;
				$csf_obj->g_val = $value; // added
				$normalized_arr[] = $csf_obj;
		}
		

		return $normalized_arr;
	}

	
	

	
}