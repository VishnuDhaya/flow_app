<?php
namespace App\Services;
use App\Repositories\SQL\LenderRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\AgreementRepositorySQL;
use App\Repositories\SQL\CustAgreementRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Models\FlowKpiReports;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Services\Support\ReportMetrics;
use App\Consts;
use Log;
use DB;
use Carbon\Carbon;

class KPIReportGenService{
	public function __construct($country_code, $data_prvdr_code){
		$this->start_date = null;
		$this->country_code = $country_code;
		$this->data_prvdr_code = $data_prvdr_code;
		$this->amt_codes = ['cumm_core_tot_disbursed_fa_a22', 'cumm_core_settled_flow_fee_a36', 'avg_margin_per_fa_a45', 'margin_earned_a52', 'cumm_margin_earned_a52', 'core_flow_invest_a19', 'fee_per_cust_a40', 'fee_per_settled_fa_a42',  'return_on_tot_invest', 'core_tot_disbursed_fa_a22', 'cur_os_fa', 'cur_os_flow_fee', 'core_settled_flow_fee_a36', 'avg_fa_amt_a13','max_os_amt'];
		$this->cust_codes = ['core_reg_cust_a5', 'core_active_cust_a6',  'active_cust_pc_a7','avg_no_of_fa_per_cust_a15'];
		$this->no_of_fa_codes = ['core_disbursed_no_of_fa_a8','core_settled_no_of_fa_a9',];

		$this->currency = (new CommonRepositorySQL())->get_currency()->currency_code;

		$this->forex = config("app.1_usd_in_$this->currency");
		$this->cumm_also_items = ['core_tot_disbursed_fa_a22', 'core_settled_flow_fee_a36', 'margin_earned_a52'];
		$this->keep_for_calc = ['core_active_cust_a6', 'core_flow_invest_a19'];
		

		$this->all_sections = [
			[
				'title' => "Customers & Float Advances",	
				'items' => ['core_reg_cust_a5', 
				'core_active_cust_a6', 'active_cust_pc_a7',  'core_disbursed_no_of_fa_a8','core_settled_no_of_fa_a9',]
			],
			[
				'title' => "Performance Metrics",
	  		 	'items' => ['ontime_fa_pc_a12' , "avg_fa_amt_a13", 'avg_no_of_fa_per_cust_a15', 'avg_fa_duration_a16', 'max_os_amt']
	  		 ],
	  		 [
	  		 	'title' => 'Float injected by FLOW into DP Ecosystem',
	  		 	'items' => ['core_flow_invest_a19', 'core_tot_disbursed_fa_a22', 'cumm_core_tot_disbursed_fa_a22']
	  		],
	  		 [
	  		 	'title' => 'Fees/Commissions paid to EzeeMoney (Expense)',
	  		 	'items' => ['core_new_cust_comm', 'core_repay_comm', 'dp_commission_a31']
	  		],
	  		
	  		[
	  		 	'title' => 'Revenue earned by FLOW',
	  		 	'items' => ['core_settled_flow_fee_a36', 'cumm_core_settled_flow_fee_a36']
	  		],		
	  		[
	  		 	'title' => 'Financial Metrics / Unit Economics',
	  		 	'items' => ['fee_per_cust_a40', 'fee_per_settled_fa_a42', 'avg_return_per_amt_per_day_a44', 'avg_margin_per_fa_a45', 'avg_margin_per_amt_per_day_a47']
	  		],		
	  		[
	  		 	'title' => 'Financial Metrics / Overall',
	  		 	'items' => ['margin_earned_a52', 'cumm_margin_earned_a52', 'avg_return_per_amt_per_month_a54', 'avg_margin_per_amt_per_month_a55', 'tot_roi_rev_based_a57', 'tot_roi_margin_based_a59', 'capt_mul_factor_a61']
	  		],		
	  	];	
  		

  		$this->cumm_sections = [
			[
				'title' => "Customers & Float Advances",	
				'items' => ['core_reg_cust_a5', 'core_active_cust_a6', 'active_cust_pc_a7', 'core_disbursed_no_of_fa_a8','core_settled_no_of_fa_a9',]
			],
			[
				'title' => "Performance Metrics",
	  		 	'items' => ['ontime_fa_pc_a12' ,  "avg_fa_amt_a13", 'avg_no_of_fa_per_cust_a15', 'avg_fa_duration_a16']
	  		 ],
	  		 [
	  		 	'title' => 'Float injected by FLOW into DP Ecosystem',
	  		 	'items' => ['core_flow_invest_a19', 'core_tot_disbursed_fa_a22', ]
	  		],
	  		[
	  		 	'title' => 'Revenue earned by FLOW',
	  		 	'items' => ['core_settled_flow_fee_a36']
	  		],		
	  		[
	  		 	'title' => 'Financial Metrics / Unit Economics',
	  		 	'items' => ['fee_per_cust_a40', 'fee_per_settled_fa_a42', 'avg_return_per_amt_per_day_a44', 'avg_margin_per_fa_a45', 'avg_margin_per_amt_per_day_a47']
	  		],		
	  		[
	  		 	'title' => 'Financial Metrics / Overall',
	  		 	'items' => ['margin_earned_a52', 'avg_return_per_amt_per_month_a54', 'avg_margin_per_amt_per_month_a55', 'tot_roi_rev_based_a57', 'tot_roi_margin_based_a59', 'capt_mul_factor_a61']
	  		],
	  		[
  				'title' => 'Current Portfolio Snapshot',
  				'items' => ["cur_os_no_of_fa", "cur_os_fa", "cur_os_flow_fee"]
  			]
	  	];	

  		$this->cur_os_section  = 
  		[
  			[
  				'title' => 'Current Portfolio Snapshot',
  				'items' => ["cur_os_no_of_fa","cur_os_fa","cur_os_flow_fee"]
  			]
  		];
	}


	public function generate_past_dates($start_date, $end_date){

		$date = Carbon::createFromFormat('Y-m-d', $start_date);
		$end_date = Carbon::createFromFormat('Y-m-d', $end_date);

		while($date->lessThanOrEqualTo($end_date)){
			
			$this->get_kpi_report($date, true, true);
			$date->addDays(1);
		}

	}


	public function get_kpi_reports_trend($no_of_months, $real_time){
	
		$i = 1;
		$all_reports = array();
		$start_date = Carbon::today()->subMonthsNoOverflow(1)->endOfMonth();
		$end_date = Carbon::today();
		
		while($i <= 5){

			$report = $this->get_monthly_kpi_report($start_date, $end_date, $real_time);
			if(!empty($report)){
				$all_reports[] = $report;
			}
			$start_date->subMonthsNoOverflow(1)->endOfMonth();
			$end_date = $end_date->subMonthsNoOverflow(1)->endOfMonth();
			//$end_date = $end_date->endOfMonth();
			
			$i++;
		}
		return [	'all_sections' => $this->all_sections ,
						'reports' => $all_reports, 
						'forex' => $this->forex, 
						'currency_code' => $this->currency];
		//return $all_reports;
	}

	public function get_monthly_kpi_report($start_date, $end_date, $real_time){
		
		
		if($real_time){
			$this->date_obj = $start_date->copy();
			$this->date = $start_date->format('Y-m-d');
			$start_metrics = $this->take_core_metrics(false);
			$this->date_obj = $end_date->copy();
			$this->date = $end_date->format('Y-m-d');
			$this->start_date = $start_date->format('Y-m-d');
			$end_metrics = $this->take_core_metrics(false);
			$this->start_date = null;
			
		}else{
			$start_metrics = $this->take_saved_metrics($start_date->format('Y-m-d'));
			$end_metrics = $this->take_saved_metrics($end_date->format('Y-m-d'));
		}
		if(empty($start_metrics) && empty($end_metrics) ){
			return array();
		}

		#$this->calc_derived_metrics($start_metrics);
		

		if(empty($start_metrics) && !empty($end_metrics)){
			$final_metrics = 	$end_metrics;
		}else{
			$final_metrics = $this->get_metrics_diff($start_metrics, $end_metrics);
		}

		
		$this->calc_derived_metrics($final_metrics, $end_metrics);

		
		$this->fill_cummul_metrics($final_metrics, $end_metrics);
		Log::warning('Checking time');
		$mytime = Carbon::now();
		Log::warning($mytime->toDateTimeString());
		$this->fill_independent_metrics($final_metrics, $start_date, $end_date);
		
		Log::warning('checking time');
		$mytime = Carbon::now();
		 Log::warning($mytime->toDateTimeString());
		Log::warning(time());
		$report = $this->present_report($final_metrics);	
		
		$date_format = $start_date->copy();
		$report['title'] = $date_format->addDay(1)->format("M Y");

		return $report;
	}
	private function fill_cummul_metrics(&$final_metrics, $end_metrics){
		Log::warning($end_metrics);
		Log::warning($final_metrics);
		foreach ($this->cumm_also_items as $metric) {
			Log::warning($metric);
			$final_metrics['cumm_'.$metric] = $end_metrics[$metric];
		}
	}
	
	private function get_metrics_diff($start_metrics, $end_metrics){
		$final_metrics = array();
		
		foreach ($end_metrics as $metric => $value) {
			if(in_array($metric, $this->keep_for_calc)){
				$final_metrics["calc_".$metric]  = $value;
			}
			if(array_key_exists($metric, $start_metrics)){
				$start_value = 	$start_metrics[$metric];
			}else{
				$start_value = 0;	
			}

			
				$final_metrics[$metric]  = $value - $start_value;
			
			
		}
		return $final_metrics;
			
	}


	public function get_kpi_report($date , $generate = true, $save = false){
		$metrics = array();
		$report_data = array();
		
		$this->date = $date->endOfDay();
		$this->date_obj = $date->endOfDay()->copy();
		if($generate){
			$metrics = $this->take_core_metrics($save);
		}
		else{
			if($save){
				thrw("You can not save metrics. You have requested metrics that are already saved");
			}

			// $metrics = $this->take_saved_metrics($this->date->format('Y-m-d'));
		}
		if(empty($metrics)){
			thrw("Unable to retrieve metrics");	
		}else{
			$this->calc_derived_metrics($metrics);
			$report_data = $this->present_report($metrics);
		}
		$this->forex = config("app.1_usd_in_$this->currency");
		
		$report_data['title'] = $date->format('Y-m-d'); 

		return [	'all_sections' => $this->cumm_sections ,
						'reports' =>[$report_data],
						'forex' => $this->forex, 
						'currency_code' => $this->currency];
	}

	private function present_report($metrics){
		
		
		$report_data = array();
		foreach ($metrics as $code => $value) {
			$unit = $this->get_unit($code);
			$report_data[$code] = [
							'kpi_metric' => $code, 
							'kpi_unit' => $unit, 
							'kpi_value' => $value, 
							'country_code' => $this->country_code, 
							'report_date' => date($this->date),
							'usd' => $this->get_usd($value, $unit)
						];
		}
		
		return $report_data;
	}

	private function fill_independent_metrics(&$final_metrics, $start_date, $end_date){
			$max_os = ReportMetrics::_get_max_os($start_date, $end_date);
			$final_metrics['max_os_amt'] = $max_os;
	}

	private function calc_derived_metrics(&$metrics, &$end_metrics = null){
		if(empty($metrics)){
			return ;
		}
		
		$metrics['dp_commission_a31'] = $metrics['core_new_cust_comm'] + $metrics['core_repay_comm'];
		#$metrics['core_tot_default_loans'] = 0;

		$metrics['avg_fa_duration_a16'] = div($metrics['core_tot_duration'] , $metrics['core_tot_lns_loans_tbl']);

		if($metrics['core_tot_lns_loans_tbl'] == 0 ){
			$metrics['ontime_fa_pc_a12'] = 0;

		}else{
			
			$metrics['ontime_fa_pc_a12'] = 100 * (1- div($metrics['core_tot_default_loans'],$metrics['core_tot_lns_loans_tbl']));	
		}
		
		
		if(array_key_exists('calc_core_flow_invest_a19', $metrics)){
			$core_flow_invest_a19 = $metrics['calc_core_flow_invest_a19'];
		}else{
			$core_flow_invest_a19 = $metrics['core_flow_invest_a19'];
		}


		if(array_key_exists('calc_core_active_cust_a6', $metrics)){
			$core_active_cust_a6 = $metrics['calc_core_active_cust_a6'];
		}else{
			$core_active_cust_a6 = $metrics['core_active_cust_a6'];
		}
		


#todo
	
		$metrics['avg_fa_amt_a13'] = div($metrics['core_tot_disbursed_fa_a22'] , $metrics['core_disbursed_no_of_fa_a8']);

		$metrics['fee_per_settled_fa_a42'] = div($metrics['core_settled_flow_fee_a36'] , $metrics['core_settled_no_of_fa_a9']);

		$metrics['capt_mul_factor_a61'] = div($metrics['core_tot_disbursed_fa_a22'] , $core_flow_invest_a19);

		/////////////////////////
		if($end_metrics){
			$metrics['avg_no_of_fa_per_cust_a15'] = div($metrics['core_disbursed_no_of_fa_a8'] , $core_active_cust_a6);

			$metrics['fee_per_cust_a40'] = div($metrics['core_settled_flow_fee_a36'] , $core_active_cust_a6);
			$metrics['active_cust_pc_a7'] = 100 * div($end_metrics['core_active_cust_a6'] ,  $metrics['core_reg_cust_a5']);

			$end_metrics['dp_commission_a31'] = $end_metrics['core_new_cust_comm'] + $end_metrics['core_repay_comm'];

			$end_metrics['margin_earned_a52'] = $end_metrics['core_settled_flow_fee_a36'] - $end_metrics['dp_commission_a31'];		

			#$end_metrics['active_cust_pc_a7'] = 100 * div($end_metrics['core_active_cust_a6'] ,  $end_metrics['core_active_cust_a6']);

		}else{
			$metrics['avg_no_of_fa_per_cust_a15'] = div($metrics['core_disbursed_no_of_fa_a8'] , $metrics['core_active_cust_a6']);
			$metrics['fee_per_cust_a40'] = div($metrics['core_settled_flow_fee_a36'] , $metrics['core_active_cust_a6']);

		}
		$metrics['avg_return_per_amt_per_day_a44'] = 100 * div($metrics['fee_per_settled_fa_a42'] , ($metrics['avg_fa_amt_a13'] * $metrics['avg_fa_duration_a16']));

		$metrics['margin_earned_a52'] = $metrics['core_settled_flow_fee_a36'] - $metrics['dp_commission_a31'];		

		$metrics['tot_roi_rev_based_a57'] = 100 * div($metrics['core_settled_flow_fee_a36'] , $core_flow_invest_a19);		

		$metrics['tot_roi_margin_based_a59'] = 100 * div($metrics['margin_earned_a52'] , $core_flow_invest_a19);		
		////////////

		$metrics['avg_margin_per_fa_a45'] = div($metrics['margin_earned_a52'] , $metrics['core_settled_no_of_fa_a9']);

		$metrics['avg_margin_per_amt_per_day_a47'] = 100 * div($metrics['avg_margin_per_fa_a45'], ($metrics['avg_fa_amt_a13'] *  $metrics['avg_fa_duration_a16']));

		$metrics['avg_return_per_amt_per_month_a54'] = $metrics['avg_return_per_amt_per_day_a44'] * 26;

		$metrics['avg_margin_per_amt_per_month_a55'] = $metrics['avg_margin_per_amt_per_day_a47'] * 26;
     
		
	}

	private function take_saved_metrics($date){

		$metrics_obj = (new FlowKpiReports())->get_records_by('report_date',  $date, ["kpi_metric", "kpi_value"]);
		$metrics_arr = array();

		foreach ($metrics_obj as $metric) {
			$metrics_arr[$metric->kpi_metric] = $metric->kpi_value;
		}
		if(empty($metrics_arr)){
			//thrw("Unable to retrive metrics for date : " . $date);
		}
		return $metrics_arr;
	}

	private function take_core_metrics($save){
		
		$disb_fas = $this->get_total_disbursal();
		
		$current_os_fas = $this->get_current_outstanding();
		
		$settled_fas = $this->get_total_settled();

		$tot_loan_details =  $this->get_tot_loan_details_1();

		#$repeat_cust = $this->get_repeat_cust();
		
		$duration_dets = $this->get_duration_dets();

		#$uniq_cust = $this->get_uniq_cust();

		$active_cust = $this->get_active_cust();
		
		$reg_cust = $this->get_registered_cust();

		

		// $dp_comm = $this->get_dp_comm();

		$metrics = array_merge(
			(array) $reg_cust,
		
			(array) $disb_fas, 
			(array) $current_os_fas, 
			(array) $settled_fas, 
			(array) $tot_loan_details,
			//  $dp_comm,
			//  (array) $this->get_investment(),
			#(array) $repeat_cust, 
			(array) $duration_dets,
			#(array) $uniq_cust,
			(array) $active_cust
		);
		if($save){
			$this->save_core_metrics($metrics);
		}

		return $metrics;
		
	}

	

	private function  save_core_metrics($metrics){

		$already_exists = DB::table('flow_kpi_reports')
															->where('country_code', $this->country_code)
															->where('data_prvdr_code', $this->data_prvdr_code)
															->where('report_date', $this->date->format('Y-m-d'))
															->exists();
		if($already_exists){
			thrw("Unable to save for the same date : ". $this->date->format('Y-m-d'));
		}

		foreach ($metrics as $code => $value) {
			$report_data[] = ['kpi_metric' => $code, 'kpi_value' => $value, 'country_code' => $this->country_code, 'data_prvdr_code' => $this->data_prvdr_code ,'report_date' => $this->date->format('Y-m-d'),
			];
		}

		FlowKpiReports::insert(array_values($report_data));
		
	}
	private function get_usd($value, $unit){
		if($unit == $this->currency){
			return $usd = $value / $this->forex;
			
		}else{
			return "";
		}
	}

	private function  get_unit($code){
		if(in_array($code, $this->amt_codes)){
			return $this->currency;
		}else if (in_array($code, ['repeat_cust_pc_a15C', 'ontime_fa_pc_a12', 'active_cust_pc_a7','avg_return_per_amt_per_day_a44', 'avg_margin_per_amt_per_day_a47',  'avg_return_per_amt_per_month_a54', 'avg_margin_per_amt_per_month_a55', 'tot_roi_rev_based_a57', 'tot_roi_margin_based_a59'])){
			return "%";
		}	else if (in_array($code, ['avg_fa_duration_a16'])){
			return "days";
		}
		else{
			return "";
		}
	}

	private function get_total_disbursal(){
		return DB::selectOne("select count(loan_doc_id) as core_disbursed_no_of_fa_a8, sum(loan_principal) core_tot_disbursed_fa_a22 from loans where disbursal_date < ? and country_code = ? and data_prvdr_code = ? and status != ?", [$this->date, $this->country_code, $this->data_prvdr_code, Consts::LOAN_PNDNG_DSBRSL]);
	}

	private function get_current_outstanding(){
		return DB::selectOne("select count(loan_doc_id) as cur_os_no_of_fa, sum(loan_principal) cur_os_fa, sum(flow_fee) cur_os_flow_fee from loans where disbursal_date < ? and country_code = ? and data_prvdr_code = ? and status in(?, ?, ?)", [$this->date, $this->country_code, $this->data_prvdr_code, Consts::LOAN_ONGOING, Consts::LOAN_DUE, Consts::LOAN_OVERDUE]);
	}

	private function get_total_settled(){
		return DB::selectOne("select count(loan_doc_id) as core_settled_no_of_fa_a9,  sum(flow_fee + penalty_collected) core_settled_flow_fee_a36 from loans where disbursal_date < ? and country_code = ? and status = ?", [$this->date, $this->country_code, Consts::LOAN_SETTLED]);
	}

	private function get_tot_loan_details(){ # ONLY GROSS
		return DB::selectOne("select sum(tot_default_loans) as core_tot_default_loans, sum(tot_loans)  as core_tot_loans from borrowers where country_code = ? and data_prvdr_code = ?", [$this->country_code, $this->data_prvdr_code]);
	}

	private function get_tot_loan_details_1(){ 
		#return DB::selectOne("select sum(tot_default_loans) as core_tot_default_loans, sum(tot_loans)  as core_tot_loans from borrowers where country_code = ? and data_prvdr_code = ?", [$this->date, $this->country_code, $this->data_prvdr_code]);
		return DB::selectOne("select sum(late_loans) as core_tot_default_loans, sum(tot_loans)  as core_tot_loans from borrowers where country_code = ? and data_prvdr_code = ?", [$this->country_code, $this->data_prvdr_code]);
	}

	/*private function get_uniq_cust(){ 
		return DB::selectOne("select count(cust_id) as core_uniq_cust_a6 from borrowers where first_loan_date is NOT NULL and first_loan_date < ? and country_code = ? and data_prvdr_code = ?", [$this->date, $this->country_code, $this->data_prvdr_code]);
	}*/

/*	private function get_active_cust(){ 

		if($this->start_date  != null){
			$st_date = $this->start_date;
		}else{
			$st_date = $this->date_obj->copy()->subDays(30);
		}

		return DB::selectOne("select count(distinct cust_id) as core_active_cust_a6 from loans where disbursal_date >= ? and disbursal_date < ? and country_code = ? and data_prvdr_code = ?", [$st_date, $this->date, $this->country_code, $this->data_prvdr_code]);
		
	}*/
	
	private function get_active_cust(){ 
		if($this->start_date  != null){
			$st_date = $this->start_date;
		}else{
			$st_date = $this->date_obj->copy()->subDays(30);
		}
		return DB::selectOne("select count(distinct cust_id) as core_active_cust_a6 from loans where disbursal_date > ? and country_code = ? and data_prvdr_code = ?", [$st_date, $this->country_code, $this->data_prvdr_code]);
		
	}

	private function get_registered_cust(){
		return DB::selectOne("select count(cust_id) as core_reg_cust_a5 from borrowers where data_prvdr_code = ? and country_code = ? and  reg_date < ?", [$this->data_prvdr_code, $this->country_code, $this->date]);
	}
/*
	private function get_active_cust(){
		return DB::selectOne("select count(cust_id) as core_active_cust_a6 from borrowers where data_prvdr_code = ? and country_code = ? and  last_loan_date < ?", [$this->data_prvdr_code, $this->country_code, $this->date]);
	}
*/
	/*private function get_repeat_cust(){
		return DB::selectOne("select count(cnt) as core_repeat_cust from (select count(distinct cust_id) cnt from loans  where disbursal_date < ? and country_code = ?  and data_prvdr_code = ? group by cust_id having count(cust_id)> 1) p", [$this->date, $this->country_code, $this->data_prvdr_code]);
	}*/

	private function get_duration_dets(){
		return DB::selectOne("select sum(duration) as core_tot_duration, count(1) as  core_tot_lns_loans_tbl from loans where disbursal_date < ? and country_code = ? and data_prvdr_code = ?", [$this->date, $this->country_code, $this->data_prvdr_code]);
	}

	// private function get_dp_comm(){
	// 	$resp =  DB::select("select acc_txn_type, sum(credit) as amount from account_txns where acc_id in (select id from accounts where country_code = ? and  data_prvdr_code = ? and JSON_CONTAINS(acc_purpose, JSON_ARRAY(?))) and txn_date < ? and acc_txn_type in (?, ?) group by acc_txn_type", [$this->country_code, $this->data_prvdr_code, 'commission', $this->date, 'new_cust_comm', 'repay_comm']);
	// 	$result = ['core_new_cust_comm' => 0,
	// 					   'core_repay_comm' => 0];

	// 	foreach ($resp as $record) {
	// 		$result['core_'.$record->acc_txn_type] = $record->amount;
	// 	}
	// 	return $result;
	// }

	// private function get_investment(){
	// 	return DB::selectOne("select sum(credit - debit) as core_flow_invest_a19 from account_txns where acc_id in (select id from accounts where country_code = ? and  network_prvdr_code = ? and JSON_CONTAINS(acc_purpose, JSON_ARRAY(?))) and txn_date < ? and acc_txn_type = ? group by acc_txn_type", [$this->country_code, $this->data_prvdr_code, 'disbursement', $this->date, 'capital_investment']);
	// }

	


/*	private function get_avg_fa_duration_a16($duration_dets){
		$avg_fa_duration_a16 = $duration_dets->core_tot_duration/$duration_dets->core_tot_lns_loans_tbl;
		return ['avg_fa_duration_a16' => $avg_fa_duration_a16];
	}
	*/
}