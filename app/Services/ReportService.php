<?php
namespace App\Services;

use App\Repositories\SQL\LenderRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\AgreementRepositorySQL;
use App\Repositories\SQL\CustAgreementRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Models\FlowKpiReports;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Consts;
use Log;
use DB;
use Mail;
use App\Mail\FlowCustomMail;
use Carbon\Carbon;
use App\Models\FlowApp\AppUser;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Services\Mobile\RMService;
use function Livewire\str;

class ReportService{

	private $AMOUNT_METRICS = ['tot_disb_val', 'gross_txn_val', 'os_val_eom', 'os_fee_eom', 'max_os_val', 'new_overdue_val', 'revenue', 'retail_txn_val', 'revenue_by_small_biz', 'fee_per_cust', 'fee_per_fa'];
	private $PERCENTAGE_METRICS = ['cust_churn_perc', 'ontime_repayment_rate', 'due_perc', 'female_perc', 'youth_perc'];
	public function __construct($country_code = null, $acc_prvdr_code = null){
		$this->country_code = $country_code;
		$this->acc_prvdr_code = $acc_prvdr_code;
	}

	public function get_portfolio_quality_rpt(){
		$pf_q = $this->get_portfolio_quality();
		$records = array();
		$header = ["Metric", 'FAs', '% FAs / Completed', "% FAs / Late"];
		foreach($pf_q as $key => $value){
		/*	if(in_array($key, ['late_loans', 'tot_loans'])){
				continue;
			}*/
			if(in_array($key, ['ontime_loans', 'tot_loans'])){
				$records[] = [record_name($key), $value, percent($value, $pf_q->tot_loans), 'NA'];	
			}else{
				$records[] = [record_name($key), $value, percent($value, $pf_q->tot_loans), percent($value, $pf_q->late_loans)];	
			}
			
		}

		return ['title' => "Portfolio Quality", 'header' => $header, 'records' => $records,"page_length" => 10];
	}

	private function get_portfolio_quality(){
		$sql = 'select sum(tot_loans) as tot_loans ,sum(tot_loans) - sum(late_loans) ontime_loans , sum(late_loans) as late_loans, sum(late_1_day_loans) as late_1_day_loans, sum(late_2_day_loans) as late_2_day_loans, sum(late_3_day_loans) as late_3_day_loans, sum(late_3_day_plus_loans) as late_3_day_plus_loans from borrowers';

		if($this->acc_prvdr_code){
			return DB::selectOne($sql." where country_code =? and acc_prvdr_code = ?", [ $this->country_code, $this->acc_prvdr_code]);
		}else{
			return DB::selectOne($sql." where country_code =?", [ $this->country_code]);
		}
	}

	public function get_growth_chart($cust_id){
		//DATE_FORMAT(disbursal_date, '%d-%b-%y') as disbursal_date
		$sql = "select disbursal_date, loan_principal,loan_doc_id from  loans where status != 'pending_disbursal'  and country_code = ? and cust_id = ? order by disbursal_date limit 100"; 
		
		$loans = DB::select($sql, [$this->country_code, $cust_id]);
		$result_arr = [];
		$item = [];
		
		foreach ($loans as $loan) {
			$item['loan_principal'] = $loan->loan_principal;
		    $item['loan_doc_id'] = $loan->loan_doc_id;
		    
			$result_arr[$loan->disbursal_date] = $item;
		}
		$final_result = [];
		$all_dates = array_keys($result_arr);
		foreach ($all_dates as $index => $date) {
			if($index < sizeof($result_arr) -1 ){
				$next_fa_date = $all_dates[$index+1];
				$next_fa_date = parse_date($next_fa_date, Consts::DB_DATETIME_FORMAT); 
				$date_obj = parse_date($date, Consts::DB_DATETIME_FORMAT); 
				$interval = $date_obj->diffInDays($next_fa_date);
				while($interval > 1){
					$interval--;
					$next_date = format_date($date_obj->addDay(1), Consts::DB_DATETIME_FORMAT);
					$final_result[$next_date] = 0;
				}	
				if(array_key_exists($date, $result_arr)){
					$final_result[$date] = $result_arr[$date];
				}
			}else{

				$final_result[$date] = $result_arr[$date];
			}
		}
		ksort($final_result);
		if (!$final_result){
			thrw('Please enter valid Customer ID');
			
		}
		$count_date = DB::selectOne("select first_loan_date, tot_loans from borrowers where cust_id = ?",[$cust_id]);
		$loan_amt = DB::selectOne("select sum(loan_principal) as loan_value from loans where cust_id=?",[$cust_id]);
		//$sql = "select disbursal_date, loan_principal  from  loans where status != 'pending_disbursal'  and country_code = ? and cust_id = ? and disbursal_date < date_sub(NOW(), INTERVAL 180 DAY) order by disbursal_date"; 
		
		$cust_data = ["days" => Carbon::parse($count_date->first_loan_date)->diffInDays() + 1,
				"first_loan_date" => $count_date->first_loan_date,
				'tot_loans' => $count_date->tot_loans, 'tot_loan_amt' => $loan_amt->loan_value];

		return ["chart_data" => $final_result, "cust_data" => $cust_data,"page_length" => 10];

	}


	public function get_product_takeup_rpt($acc_prvdr_code){
		$products = DB::select("select id, product_name, flow_fee, max_loan_amount, duration, status from loan_products where acc_prvdr_code = ? and country_code = ? ", [$acc_prvdr_code, $this->country_code]);

		$this->currency_code = (new CommonRepositorySQL())->get_currency()->currency_code;

		$records = array();

		$forex = config("app.1_usd_in_$this->currency_code");
		
		$total_takeups = 0;
		$product_takeups = DB::select('select count(id) as count,product_id from loans where acc_prvdr_code = ? and country_code = ? group by product_id order by count(id) desc', [$acc_prvdr_code, $this->country_code]);

		$takeups_arr = array();
		foreach($product_takeups as $index => $product_takeup){
			$takeups_arr["{$product_takeup->product_id}"] = [$product_takeup->count, $index+1];
			$total_takeups += $product_takeup->count;
		}
		

		$records = array();
		foreach($products as $index => $product){
			$record = array();
			$record[] = $product->product_name;
			$record[] = $product->status;
			$record[] = $product->max_loan_amount;
			$record[] = get_usd($product->max_loan_amount, $forex);
			
			$record[] = $product->flow_fee;
			
			$record[] = ($product->flow_fee / $product->max_loan_amount) * 100;
			$record[] = $product->duration;
			if(array_key_exists($product->id, $takeups_arr)){
				$take_ups = $takeups_arr[$product->id];
				$record[] = $take_ups[0];
				$record[] = ($take_ups[0] / $total_takeups) * 100;
				$record[] = $take_ups[1];
			}else{
				$record[] = 0;
				$record[] = 0;
				$record[] = 'NA';
			}
			
			$records[] = $record;
		}

		$header = ["Product", "Status", "Amount / $this->currency_code", "Amount / USD" , "Fee" , "Fee %" , "Nights" , "Product Take up" , "Percentage" , "Rank"];

		$units = [null, 'status', 'UGX', 'USD' , 'amt' , "%" , "days" , null , "%" , null];

		return ['title' => "Product Take up", 'header' => $header, 'records' => $records, 'units' => $units,"page_length" => 10];
	}


	public function get_daily_activity_rpt($data,$acc_prvdr_code = null){

		$this->currency_code = (new CommonRepositorySQL())->get_currency()->currency_code;
		$date_range = $data['date_range'];
		
		$records = array();

		$check_ac_id = "";

		if($data['account_id'] != null){			
			$check_ac_id = "and (t.from_ac_id = ? or t.to_ac_id = ?)";
		}

		$sql = "select * from (select date(txn_date) as date, sum(IF(txn_type = 'disbursal', amount, 0)) as disbursed_amt, sum(IF(txn_type = 'payment',amount, 0)) as repay_amt, 
				sum(IF(txn_type = 'payment', penalty, 0)) as penalty_amt, sum(IF(txn_type = 'excess_reversal', amount, 0)) as excess_reversal_amt, sum(IF(txn_type = 'payment', fee, 0)) fee,
				count(distinct(IF(txn_type = 'disbursal',l.loan_doc_id, null))) as disb_count, count(distinct(IF(txn_type = 'payment', l.loan_doc_id, null))) as repay_count, 
				count(distinct(IF(txn_type = 'payment' and penalty > 0, l.loan_doc_id, null))) as penalty_count, count(distinct(IF(txn_type = 'excess_reversal', l.loan_doc_id, null))) as 
				excess_reversal_count from loans l, loan_txns t where t.loan_doc_id = l.loan_doc_id and acc_prvdr_code = ? $check_ac_id
				and l.country_code = '{$this->country_code}' and date(txn_date) >= ? and date(txn_date) <= ?  group by date(txn_date)) as disb_repay_details
				";
				
		if($data['account_id'] == null){
			$disb_repay_details = DB::select($sql, [$acc_prvdr_code, $date_range['start_date'], $date_range['end_date']]);
		}else{
			$disb_repay_details = DB::select($sql, [$acc_prvdr_code, $data['account_id'], $data['account_id'], $date_range['start_date'], $date_range['end_date']]);
		}

		foreach ($disb_repay_details as $disb_repay_detail){
			
			if($disb_repay_detail && $data['account_id'] == null){
				$disb_repay_detail->acc_number = "All";
			}elseif($disb_repay_detail && $data['account_id'] != null){
				$acc_number = ((new AccountRepositorySQL)->get_accounts_by(['id'],[$data['account_id']],['acc_number']))[0]->acc_number;
				$disb_repay_detail->acc_number = $acc_number;
			}

			$disb_repay_detail->disbursed_count = $disb_repay_detail->disb_count;
			$disb_repay_detail->repaid_count = $disb_repay_detail->repay_count;
			$disb_repay_detail->excess_count = $disb_repay_detail->excess_reversal_count;

			$disb_repay_detail->disb_amt = $disb_repay_detail->disbursed_amt;
			$disb_repay_detail->excess_amt = $disb_repay_detail->excess_reversal_amt;

			$records[] = [$disb_repay_detail->acc_number, $disb_repay_detail->date, $disb_repay_detail->disb_amt, $disb_repay_detail->disbursed_count, 
			$disb_repay_detail->repay_amt, $disb_repay_detail->repaid_count, $disb_repay_detail->penalty_amt, $disb_repay_detail->penalty_count, 
			$disb_repay_detail->excess_amt, $disb_repay_detail->excess_count, $disb_repay_detail->fee];
			
		}

		$header = ["Account", "Activity on", "Disbursed" , "Disbursed #", "Repaid", "Repaid #", "Penalty Received", "Penalty Received #", "Excess Reversed", "Excess Reversed #", "Fee Received"];
		$units = [null, "date", $this->currency_code, null, $this->currency_code , null, $this->currency_code, null , $this->currency_code, null, $this->currency_code];	

		return ['title' => "Daily Activity", 'header' => $header, 'records' => $records, 'units' => $units,"page_length" => 15];
	}

	public function get_overdue_fa_repayments_rpt($data){

		$this->currency_code = (new CommonRepositorySQL())->get_currency()->currency_code;
		
		$date_range = $data['date_range'];

		$records = array();

		$sql = "select t3.*, v.visit_purpose from (select * from
			(select loan_doc_id , l.cust_id, l.acc_number p_cust_id, b.biz_name, concat(first_name,' ',last_name) as cust_name, paid_amount , date(due_date) due_dt, date(paid_date) paid_dt, DATEDIFF(paid_date, due_date) od_days,  date(last_visit_date) lst_visit_dt, l.status from loans l, borrowers b, persons p where p.id = b.owner_person_id and l.cust_id = b.cust_id and date(paid_date) >= ? and date(paid_date) <= ?  and paid_date > due_date )as loan

		UNION
		select * from(
		select l.loan_doc_id , l.cust_id, l.acc_number p_cust_id, b.biz_name, concat(first_name,' ',last_name) as cust_name, amount paid_amount , date(due_date) due_dt, date(txn_date) paid_dt, DATEDIFF(txn_date, due_date) od_days,  date(last_visit_date) lst_visit_dt, l.status from loans l, borrowers b, persons p, loan_txns t where t.loan_doc_id = l.loan_doc_id and p.id = b.owner_person_id and l.cust_id = b.cust_id and date(txn_date) >= ? and date(txn_date) <= ? and txn_date > due_date and txn_type = 'payment' and amount < due_amount ) t2 

			
		) t3 LEFT JOIN field_visits v on v.cust_id = t3.cust_id and date(v.visit_start_time) = t3.lst_visit_dt where country_code='{$this->country_code}' order by paid_dt desc, lst_visit_dt desc";

		$overdue_repay_details = DB::select($sql,[
								$date_range['start_date'], $date_range['end_date'],
								$date_range['start_date'], $date_range['end_date']
								]);

		
		
		foreach ($overdue_repay_details as $overdue_repay_detail ) {
			//Log::warning((array)$overdue_repay_details);
			$records[] = [$overdue_repay_detail->loan_doc_id,
				$overdue_repay_detail->cust_id,		
				$overdue_repay_detail->biz_name,
				$overdue_repay_detail->paid_amount,
				$overdue_repay_detail->due_dt,
				$overdue_repay_detail->paid_dt,
				$overdue_repay_detail->od_days,
				$overdue_repay_detail->visit_purpose,
				$overdue_repay_detail->lst_visit_dt,
				$overdue_repay_detail->status];
		}

		$header = ["Loan Doc ID", "Cust ID", "Biz Name","Paid Amount","Due Date","Paid Date","Overdue Days","Visit Purpose","Last Visit Date","Status"];

		$units = [null, null, null,$this->currency_code,"date","date",null,null,"date",null];

		return ['title' => "Overdue FA Repayments", 'header' => $header, 'records' => $records, 'units' => $units,"page_length" => 25];

	}


	public function get_daily_visits($data){

		$date_range = $data['date_range'];

		$records = array();

		$daily_visits = DB::select ("select date(visit_start_time) visit_start_time, visitor_name, count(1) as visits from field_visits where date(visit_start_time) >= ? and date(visit_start_time) <= ? and country_code = ? group by date(visit_start_time), visitor_name order by visit_start_time desc",[$date_range['start_date'], $date_range['end_date'], $this->country_code ])  ;


		foreach ($daily_visits as $daily_visit) {
			$records[] = [$daily_visit->visit_start_time,$daily_visit->visitor_name,$daily_visit->visits];
		}
		$header = ["Visit Date", "Vistor Name" , "Visits"];

		$units = ["date",null,null];

		return ['title' => "Daily Visits", 'header' => $header, 'records' => $records,'units' => $units,"page_length" => 10];

	}

	public function get_daily_agreements($data){
		$date_range = $data['date_range'];
		$records = array();
		$sql = "select date(c.created_at) date, p.email, count(1) as agreements from cust_agreements c, app_users p  where p.id = c.created_by and date(c.created_at) >= ? and date(c.created_at) <= ? and country_code = ? group by date(c.created_at),  p.email order by date desc";
		 $agreements = DB::select($sql,[$date_range['start_date'], $date_range['end_date'], $this->country_code]);


		 foreach ($agreements as $agreement) {
		 	$records[] = [$agreement->date,$agreement->email,$agreement->agreements];
		 }

		 $header = ["Date","Email","Agreements"];

		 $units = ["date",null,null];

		 return ['title' => "Daily Agreements", 'header' => $header ,'records'=>$records, 'units' => $units,"page_length" => 10];
	}

	public function get_capital_funds($data){
		$this->currency_code = (new CommonRepositorySQL())->get_currency()->currency_code;

		$records = array();

		$capital_funds = DB::select ("select fund_name,fund_type,alloc_date,alloc_amount_usd,alloc_amount_eur,alloc_amount,total_alloc_cust,tot_disb_amount,os_amount,earned_fee from capital_funds where fund_type != ? and country_code = ?  order by alloc_date desc",['internal',$this->country_code ]);

		foreach ($capital_funds as $capital_fund) {
		 	$records[] = [	$capital_fund->fund_name,
		 					$capital_fund->fund_type,
		 					$capital_fund->alloc_date,
		 					$capital_fund->alloc_amount_usd,
		 					$capital_fund->alloc_amount_eur,
		 					$capital_fund->alloc_amount,
		 					$capital_fund->total_alloc_cust,
		 					$capital_fund->tot_disb_amount,
		 					$capital_fund->os_amount,
		 					$capital_fund->earned_fee
		 				 ];
		 }

		 $header = ["Fund Name","Fund Type","Allocation Date","Allocation (USD)","Allocation (EUR)", "Allocation", "Allocated Customers", "Total Disbursed", "Current OS", "Fee Earned to Date"  ];

		 $units = [null,'status','date',$this->currency_code,$this->currency_code,$this->currency_code,null,$this->currency_code,$this->currency_code,$this->currency_code];

		 return ['title' => "Capital Funds", 'header' => $header ,'records'=>$records, 'units' => $units,"page_length" => 10];
	}



	public function get_risk_category_report(){

		
		$risk_category_records = DB::select("select first_name, risk_category, count(if(activity_status = 'active', 1, null)) active, count(if(activity_status != 'active', 1, null)) non_active from borrowers b join persons p on p.id = b.flow_rel_mgr_id where associated_with = 'FLOW' and associated_entity_code is null and p.status ='enabled' and risk_category is not NULL and b.country_code = ? group by first_name, risk_category 
		UNION 
		select first_name, 'total' as risk_category, count(if(activity_status = 'active', 1, null)) active, count(if(activity_status != 'active', 1, null)) non_active from borrowers b join persons p on p.id = b.flow_rel_mgr_id where associated_with = 'FLOW' and associated_entity_code is null and  p.status ='enabled' and risk_category is not NULL and b.country_code = ? group by first_name",[$this->country_code, $this->country_code]);

		
		
		$reports = [];
		$reports[0] = ['title' => 'Active'];
		$reports[1] = ['title' => 'Non-Active'];
		$all_sections = array();
		$section_records = array();
		$cust_types = ['active', 'non_active'];

		$sections = [];
		foreach ($risk_category_records as $section_records){
		
			if(!in_array($section_records->first_name, $sections)){
				$all_sections[] = 
				[
					"title" => $section_records->first_name,
					"ref" => $section_records->first_name,
					"items" => ["0_low_risk", "1_medium_risk", "2_high_risk", "3_very_high_risk","total"],
					
				
				];
				$sections[] = $section_records->first_name;
			}
			
				foreach($cust_types as $cust_type){
					$index = $cust_type == 'active' ? 0 : 1;
					if(isset($section_records->risk_category)){

					
					$reports[$index][$section_records->first_name][$section_records->risk_category] =  
											["metric" => $section_records->risk_category,
											"unit" => "", 
											"value" => $section_records->$cust_type,
										
										];
				}}
				
				
			}
			return['all_sections' => $all_sections,'section_header'=>"Flow RM", "reports" => $reports, "page_length" => 18];
			
	}

	public function get_lead_report($data){

		$date_range = $data['date_range'];
		
		$lead_conver_records = DB::select("select count(id) as total ,status  from leads where country_code = ? and created_at between  ? and  ? group by status order by status",[session('country_code'), $date_range ['start_date'], $date_range ['end_date'] ]);
		
		$lead_record = array();
		$reports = array();

		foreach($lead_conver_records as $lead_conver_record){

			$reports[]= [
				$lead_conver_record->status,
				$lead_conver_record->total
			];
		}

		$header = ["Current Lead Status", "Lead Counts"]; 
		$units =["status", null];

		return ['title' => "Lead Conversion Report", 'header' => $header,'records'=>$reports, 'units' => $units, "page_length" => 10 ];
	}

	public function get_mgmt_dashboard_live_report($data)
	{

		$last_report_date = (DB::connection('report')->selectOne("SELECT max(report_date) last_date from live_reports"))->last_date;
		$live_report_records = DB::connection('report')->select("SELECT * FROM live_reports where report_date = ?", [$last_report_date]);

		$records = [];

		foreach($live_report_records as $live_report_record){
			$records[] = $live_report_record;
		}
		
		$currency_data = $this->get_report_currency_info($last_report_date);
		$data = ['records' => $records, 'currency_data' => $currency_data];
		return $data;
		
	}

	public function get_report_currency_info($forex_date = null){
		$currency_data = [];

        if(!$forex_date){
            $forex_date = Carbon::now()->subDays(26)->format('Y-m-d');
        }
        else{
            $now = Carbon::now()->format('Y-m-d');
            if($forex_date > $now){
                $forex_date = Carbon::now()->subDays()->format('Y-m-d');
            }
        }

		$markets = DB::select("select * from markets where status = 'enabled'");
		foreach($markets as $market){
			$currency_data[$market->country_code] = ['currency_code' => $market->currency_code];
			$forex_rates = [];
			$rates = DB::select("select quote, forex_rate from forex_rates where base = ? and quote in ('USD','EUR','UGX','RWF') and date(forex_date) = DATE_ADD(?, INTERVAL 1 DAY) order by id desc ", [$market->currency_code, $forex_date]);
			foreach($rates as $rate){
				$forex_rates[$rate->quote] = $rate->forex_rate;
			}
			$currency_data[$market->country_code]['forex_rates'] = $forex_rates;
		}

        $currency_data['date'] = $forex_date;

		return $currency_data;
	}


	public function get_mgmt_dashboard_monthly_report($data){
		[$report_month, $compare_month] = $this->get_months_for_report($data);
		$fields = ["country_code","acc_prvdr_code","cust_reg_count","cust_active_count",
					"cust_churn_perc","tot_disb_val","tot_disb_count","tot_fa_settled_count",
					"gross_txn_val","os_val_eom","os_fee_eom","os_count_eom","od_count","od_amount",
					"people_benefited","max_os_val","ontime_repayment_rate",
					"new_overdue_count","due_perc","new_overdue_val","revenue","biz_supported_count",
					"female_perc","youth_perc","retail_txn_count","retail_txn_val",
					"revenue_by_small_biz","fee_per_cust","fee_per_fa","run_at","rev_per_cust","rev_per_rm","rm_count_for_rev_calc","cust_count_for_rev_calc"];

		$report_records = DB::connection('report')->table('monthly_mgmt_reports')->where('month', $report_month)->get($fields)->toArray();
		$comparison_records = DB::connection('report')->table('monthly_mgmt_reports')->where('month', $compare_month)->get($fields)->toArray();
		
		if(empty($report_records) || empty($comparison_records)){
			thrw("Data missing for the selected month");
		}

		$records = [];
		foreach($report_records as $record){
			$filtered_compare_records = array_filter($comparison_records, function($value) use ($record){
				return ($value->country_code == $record->country_code && $value->acc_prvdr_code == $record->acc_prvdr_code);
			});
			$compare_record = empty($filtered_compare_records) ? null : array_values($filtered_compare_records)[0];
			$formatted_data = $this->get_monthly_report_formatted_data($record, $compare_record);
			$records[] = $formatted_data;
		}

		$end_of_report_month = Carbon::createFromFormat("Ymd", $report_month.'01')->endOfMonth()->format("Y-m-d");
		$currency_data = $this->get_report_currency_info($end_of_report_month);
		return ['report_month' => $report_month, 'vs_month' => $compare_month, 'records' => $records, 'currency_data' => $currency_data];		
	}
	
	
	private function get_months_for_report($data){
		if(array_key_exists('report_month', $data) && array_key_exists('vs_month', $data)){
			if($data['report_month'] < $data['vs_month']){
				thrw("Please compare with a month before the report month");
			}
			$report_month = $data['report_month'];
			$compare_month = $data['vs_month'];
		}else{
			$report_month = (DB::connection('report')->selectOne("SELECT max(month) last_month from monthly_mgmt_reports"))->last_month;
			$compare_month = (DB::connection('report')->selectOne("SELECT max(month) vs_month FROM monthly_mgmt_reports where month < ?", [$report_month]))->vs_month;
		}

		return [$report_month, $compare_month];


	}

	private function get_monthly_report_formatted_data($record, $compare_record){
		$result = [];
		foreach($record as $metric => $value){
			if(in_array($metric, ['id','country_code', 'month', 'acc_prvdr_code', 'run_at'])){
				$result[$metric] = $value;
				continue;
			}
			$data = [];
			$data['value'] = $value;
			$data['unit'] = $this->get_unit($metric);
			if(isset($compare_record)){
				$data['vs_value'] = $compare_record->$metric; 
			}
			$result[$metric] = $data;
		}
		return $result;
	}

	private function get_unit($metric){
		$unit = null;
		if(in_array($metric, $this->AMOUNT_METRICS)){
			$unit = "currency";
		}
		elseif(in_array($metric, $this->PERCENTAGE_METRICS)){
			$unit = "%";
		}
		
		return $unit;
	}

	public function get_rm_distant_checkin_checkout_report($data = [],$send_email = false){
		
		$country_code = session('country_code');

		if(array_key_exists('date_range',$data)){
			$date_range = $data['date_range'];
			$end_date = $date_range ['end_date'];
			$start_date =$date_range ['start_date'];

		}else{
			$last_sunday = new Carbon("last sunday");
			$end_date = $last_sunday->format(Consts::DB_DATE_FORMAT);
			$start_date = $last_sunday->subDays(7)->format(Consts::DB_DATE_FORMAT);
		}
		
		$app_users = AppUser::where('country_code', $country_code)->where('role_codes', 'relationship_manager')->where('status', 'enabled')->get(['person_id','email']);
		
		foreach($app_users as $app_user){			
			$person_id = $app_user->person_id;
			$email = $app_user->email;
			$person_repo = new PersonRepositorySQL;
			$rm_name = $person_repo->full_name($person_id);
			
			$select_fields = "count(*) as total_visits, count(IF(after_biz_hours = true, 1, null)) as after_biz_hrs_chkin_count, count(IF(force_checkin = true, 1, null)) as force_checkin_count, count(IF(force_checkout = true, 1, null)) as force_checkout_count,count(IF(early_checkout = true, 1, null)) as early_checkout_count, 
								GROUP_CONCAT( IF(force_checkin = true, checkin_distance, null)/1000,' KM - ', force_checkin_reason) as force_checkin_reason,
								GROUP_CONCAT( IF(force_checkout = true, checkout_distance, null)/1000,' KM - ', force_checkout_reason) as force_checkout_reason,
								GROUP_CONCAT( IF(early_checkout = true, MINUTE(TIMEDIFF(visit_end_time, visit_start_time)), null),' Mins - ', force_checkout_reason) as early_checkout_reason,
								GROUP_CONCAT( IF(after_biz_hours = true, force_checkin_reason, null)) as after_biz_hrs_chkin_reason";

			
			$sql = "select $select_fields from field_visits where visitor_id = ? and date(visit_end_time) >= ? and date(visit_end_time) <= ?  and sch_status =  ? and country_code = ?";
			$fields_arr = [$person_id, $start_date, $end_date,'checked_out',$country_code];
			
			$field_visits = DB::selectOne($sql, $fields_arr);

			$total_count = $field_visits->force_checkin_count + $field_visits->force_checkout_count + $field_visits->early_checkout_count;
			
			if($field_visits->total_visits > 0 && $total_count > 0 ){
				$percentage = ($total_count/$field_visits->total_visits)*100;
			}
			
			$force_checkout_reason = explode(',', $field_visits->force_checkout_reason);
			$force_checkin_reason = explode(',', $field_visits->force_checkin_reason);
			$early_checkout_reason = explode(',', $field_visits->early_checkout_reason);
			$after_biz_hrs_chkin_reason = explode(',', $field_visits->after_biz_hrs_chkin_reason);


			$total_visits = isset($field_visits->total_visits) ? $field_visits->total_visits : 'NA';
			$force_checkin_count = isset($field_visits->force_checkin_count) ? $field_visits->force_checkin_count :"NA";
			$force_checkout_count = isset($field_visits->force_checkout_count) ? $field_visits->force_checkout_count :"NA";
			$force_checkin_reason = isset($field_visits->force_checkin_reason) ? $force_checkin_reason :"NA";
			$force_checkout_reason = isset($field_visits->force_checkout_reason) ? $force_checkout_reason :"NA";
			$early_checkout_count = isset($field_visits->early_checkout_count) ? $field_visits->early_checkout_count :"NA";
			$early_checkout_reason = isset($field_visits->early_checkout_reason) ? $early_checkout_reason :"NA";
			$after_biz_hrs_chkin_count = isset($field_visits->after_biz_hrs_chkin_count) ? $field_visits->after_biz_hrs_chkin_count :"NA";
			$after_biz_hrs_chkin_reason = isset($field_visits->after_biz_hrs_chkin_reason) ? $field_visits->after_biz_hrs_chkin_reason :"NA";


			
			$records[] = [	$rm_name,
							$total_visits,
							$force_checkin_count,
							$force_checkin_reason,
							$force_checkout_count,
							$force_checkout_reason,
							$early_checkout_count,
							$early_checkout_reason,
							$after_biz_hrs_chkin_count,
							$after_biz_hrs_chkin_reason
							];
			
			
			if($send_email && $percentage >= 10 && $field_visits){
				$week_start_date = (new Carbon("last sunday"))->subDays(7)->format(Consts::UI_DATE_FORMAT);
				$week_end_date = (new Carbon("last sunday"))->format(Consts::UI_DATE_FORMAT);
			   
				$mail_data = ['start_date' => $week_start_date, 'end_date' => $week_end_date, 'country_code' => session('country_code'), 'rm_name' => $rm_name, 'total_visits' => $total_visits, 'force_checkin_count' => $force_checkin_count ,
				"force_checkout_count" => $force_checkout_count, 'checkin_reason' => $force_checkin_reason, 'checkout_reason' => $force_checkout_reason,'early_checkout_count' => $early_checkout_count, 'early_checkout_reason' => $early_checkout_reason,
				'after_biz_hrs_chkin_count' => $after_biz_hrs_chkin_count, 'after_biz_hrs_chkin_reason' => $after_biz_hrs_chkin_reason, ];
				Mail::to([get_ops_admin_email()])->cc($email)->queue((new FlowCustomMail('rm_distant_checkin_checkout_report', $mail_data))->onQueue('emails'));
			}
		}
		
		$header = ["RM Name", "Total Visits", "Distant Checkin Count", "Distant Checkin Reason", "Distant Checkout Count", "Distant Checkout Reason", "Early Checkout Count", "Early Checkout Reason", "After Biz Hrs Checkin Count", "After Biz Hrs Checkin Reason"]; 
		$units =["status", null, null, null, null, null,null,null,null,null];
		
		return [ 'title' => "Daily Visits" , 'header' => $header, 'records'=>$records, 'units' => $units, "page_length" => 10 ];
	}



	public function get_rm_wise_report(){
		
		$country_code = session('country_code');
		
		$app_users = AppUser::where('country_code', $country_code)->where('role_codes', 'relationship_manager')->where('status', 'enabled')->get(['person_id']);
		
		foreach($app_users as $app_user){	
			
			$person_id = $app_user->person_id;
			$email = $app_user->email;
			$person_repo = new PersonRepositorySQL;
			$rm_name = $person_repo->full_name($person_id);
			$date = Carbon::now()->addDays(7);

			$borrowers = DB::selectOne("select count(*) as count from borrowers where ( flow_rel_mgr_id = ?) and country_code = ?  and ((aggr_valid_upto between '" .Carbon::now()->subDays(7). " 'and' " .Carbon::now()->addDays(7)."'  and prob_fas = 0) or (aggr_status = 'inactive' and status ='enabled')) order by next_visit_date, last_visit_date" ,[$person_id, $country_code, $date]);
			
			$agrmt_renewal_due = $borrowers ? $borrowers->count : null;

			$month_start_last_30 = carbon::now()->subDays(30)->format(Consts::DB_DATE_FORMAT);
			$month_start_last_60 = carbon::now()->subDays(60)->format(Consts::DB_DATE_FORMAT);

			$month_end = date_db();

			$leads = DB::selectOne("select count(*) as lead_count from leads where profile_status = 'open' and country_code = ? and flow_rel_mgr_id = ?",[$country_code, $person_id]);

			$aquisition = $leads ? $leads->lead_count : 0 ;
			
			$overdues = DB::selectOne("select count(if(date(due_date) <= '{$month_start_last_30}' and paid_date is null, 1, null)) as last_30_days, 
											  count(if(date(due_date) <= '{$month_start_last_60}' and paid_date is null, 1, null)) as last_60_days 
											  from loans where flow_rel_mgr_id = ? and country_code = ?", [$person_id, $country_code]);

			$loans = DB::selectOne ("select ((count(if(date(paid_date) <= date(due_date),1,null))/count(if(disbursal_status = 'disbursed',1,null)))*100) as repayment_rate from loans where flow_rel_mgr_id = {$person_id} and date(due_date) >= '{$month_start_last_30}' and date(due_date) <= '{$month_end}' and country_code = '{$country_code}'");
			
			$repayment_rate = $loans ? round($loans->repayment_rate, 2)."%" : 0;

			$last_30_days_overdue = $overdues->last_30_days;
			$last_60_days_overdue = $overdues->last_60_days;

            $records[] = compact('rm_name', 'agrmt_renewal_due', 'last_30_days_overdue',  'last_60_days_overdue', 'repayment_rate', 'aquisition');
			
        }

	   
	   return  $records;
	}

	public function get_rm_wise_repayment_rate_rpt($data = [],$send_email = false){
		
		$country_code = session('country_code');

		$date_range = $data['date_range'];
		$end_date = $date_range ['end_date'];
		$start_date =$date_range ['start_date'];


		
		$app_users = AppUser::where('country_code', $country_code)->where('role_codes', 'relationship_manager')->where('status', 'enabled')->get(['person_id']);
		
		foreach($app_users as $app_user){			
			$person_id = $app_user->person_id;
			$person_repo = new PersonRepositorySQL;
			$rm_name = $person_repo->full_name($person_id);

			$loans = DB::selectOne("select count(*) as overdue_fas from loans where date(due_date) >= '{$start_date}' and date(due_date) <= '{$end_date}' and (paid_date is null or paid_date > due_date) and flow_rel_mgr_id = ? and country_code = ?", [$person_id, $country_code]);

			$overdue_fas = $loans->overdue_fas;

			$loans = DB::selectOne ("select ((count(if(date(paid_date) <= date(due_date),1,null))/count(if(disbursal_status = 'disbursed',1,null)))*100) as repayment_rate from loans where flow_rel_mgr_id = {$person_id} and date(due_date) >= '{$start_date}' and date(due_date) <= '{$end_date}' and country_code = '{$country_code}'");

			$repayment_rate = $loans ? round($loans->repayment_rate, 2)."%" : "NA";
			
						
			$records[] = [	$rm_name,
							$repayment_rate,
							$overdue_fas,
							];
			
			
			
		}
		
		$header = ["RM Name", "FA Ontime Rate", "Overdue FAs"]; 
		$units =[null, null, null];
		
		return [ 'title' => "RM Wise Repayment Rate" , 'header' => $header, 'records'=>$records, 'units' => $units, "page_length" => 50 ];
	}

    public function get_monthly_new_cust_report($country_code)
    {
        $results = DB::connection("report")->select("select acc_prvdr_code, month, cust_reg_count from monthly_mgmt_reports where acc_prvdr_code is not null and country_code = '{$country_code}' order by month");
        $resp = array();
        $acc = array_values(array_unique(array_column($results,'acc_prvdr_code')));
        $prev = [];
        foreach ($acc as $ac){
            $prev[$ac] = 0;
        }
        foreach ($results as $result ) {
                $date = carbon::createFromFormat('Ymd', ($result->month . '01'))->format('Y-m-d');
                $resp[$result->acc_prvdr_code][] = ['x' => $date, 'y' => ($result->cust_reg_count - $prev[$result->acc_prvdr_code])];
                $prev[$result->acc_prvdr_code] = $result->cust_reg_count;
            }

        return $resp;
    }

    public function get_monthly_new_cust_report_of_country($country_code)
    {
        $results = DB::connection("report")->select("select country_code, month, cust_reg_count from monthly_mgmt_reports where acc_prvdr_code is null and country_code = '{$country_code}' order by month");
        $resp = array();
        $now = 0;
        foreach ($results as $result ) {
            $date = carbon::createFromFormat('Ymd', ($result->month . '01'))->format('Y-m-d');
            $resp[$result->country_code][] = ['x' => $date, 'y' => $result->cust_reg_count - $now];
            $now =  $result->cust_reg_count;
        }

        return $resp;
    }

    function searchForId($id, $array) {
        foreach ($array as $key => $val) {
            if ($val['uid'] === $id) {
                return $key;
            }
        }
        return null;
    }

    public function get_reg_and_active_customer($country_code)
    {
        $results = DB::connection("report")->select("select month, cust_active_count as active_cust, cust_reg_count from monthly_mgmt_reports where acc_prvdr_code is null and country_code = '{$country_code}' order by month");
        $resp = array();
        foreach ($results as $result ){
            $date = carbon::createFromFormat('Ymd',($result->month.'01'))->format('Y-m-d');
            $resp['Active Customer'][] = ['x' => $date, 'y' => [$result->active_cust]];
            $resp['Registered Customer'][] = ['x' => $date, 'y' => [$result->cust_reg_count]];
        }

        return $resp;
    }

    public function get_revenue_per_cust($country_code)
    {
        $results = DB::connection("report")->select("select acc_prvdr_code, month, fee_per_cust from monthly_mgmt_reports where acc_prvdr_code is not null and country_code = '{$country_code}' order by month");
        $resp = array();
        foreach ($results as $result ){
            $date = carbon::createFromFormat('Ymd',($result->month.'01'))->format('Y-m-d');
            $resp[$result->acc_prvdr_code][] = ['x' => $date, 'y' => round($result->fee_per_cust,3)];
        }

        return $resp;
    }

    public function get_revenue_per_cust_of_country($country_code)
    {
        $results = DB::connection("report")->select("select country_code, month, fee_per_cust from monthly_mgmt_reports where acc_prvdr_code is null and country_code = '{$country_code}' order by month");
        $resp = array();
        foreach ($results as $result ){
            $date = carbon::createFromFormat('Ymd',($result->month.'01'))->format('Y-m-d');
            $resp[$result->country_code][] = ['x' => $date, 'y' => round($result->fee_per_cust,3)];
        }

        return $resp;
    }

    public function get_revenue_per_fa($country_code)
    {
        $results = DB::connection("report")->select("select acc_prvdr_code, month, fee_per_fa from monthly_mgmt_reports where acc_prvdr_code is not null and country_code = '{$country_code}' order by month");
        $resp = array();
        foreach ($results as $result ){
            $date = carbon::createFromFormat('Ymd',($result->month.'01'))->format('Y-m-d');
            $resp[$result->acc_prvdr_code][] = ['x' => $date, 'y' => round($result->fee_per_fa,3)];
        }

        return $resp;
    }

    public function get_revenue_per_fa_of_country($country_code)
    {
        $results = DB::connection("report")->select("select country_code, month, fee_per_fa from monthly_mgmt_reports where acc_prvdr_code is null and country_code = '{$country_code}' order by month");
        $resp = array();
        foreach ($results as $result ){
            $date = carbon::createFromFormat('Ymd',($result->month.'01'))->format('Y-m-d');
            $resp[$result->country_code][] = ['x' => $date, 'y' => round($result->fee_per_fa,3)];
        }

        return $resp;
    }

    public function get_tot_disb_report($country_code){
        $results = DB::connection("report")->select("select acc_prvdr_code, month, tot_disb_val from monthly_mgmt_reports where acc_prvdr_code is not null and country_code = '{$country_code}' order by month");
        $resp = array();
        foreach ($results as $result ){
            $date = carbon::createFromFormat('Ymd',($result->month.'01'))->format('Y-m-d');
            $resp[$result->acc_prvdr_code][] = ['x' => $date, 'y' => round($result->tot_disb_val,3)];
        }

        return $resp;

    }

    public function get_tot_disb_report_of_country($country_code){
        $results = DB::connection("report")->select("select country_code, month, tot_disb_val from monthly_mgmt_reports where acc_prvdr_code is null and country_code = '{$country_code}' order by month");
        $resp = array();
        foreach ($results as $result ){
            $date = carbon::createFromFormat('Ymd',($result->month.'01'))->format('Y-m-d');
            $resp[$result->country_code][] = ['x' => $date, 'y' => round($result->tot_disb_val,3)];
        }

        return $resp;

    }

    public function get_tot_disb_count($country_code)
    {
        $results = DB::connection("report")->select("select acc_prvdr_code, month, tot_disb_count from monthly_mgmt_reports where acc_prvdr_code is not null and country_code = '{$country_code}' order by month");
        $resp = array();
        foreach ($results as $result ){
            $date = carbon::createFromFormat('Ymd',($result->month.'01'))->format('Y-m-d');
            $resp[$result->acc_prvdr_code][] = ['x' => $date, 'y' => round($result->tot_disb_count,3)];
        }

        return $resp;
    }

    public function get_tot_disb_count_of_country($country_code)
    {
        $results = DB::connection("report")->select("select country_code, month, tot_disb_count from monthly_mgmt_reports where acc_prvdr_code is null and country_code = '{$country_code}' order by month");
        $resp = array();
        foreach ($results as $result ){
            $date = carbon::createFromFormat('Ymd',($result->month.'01'))->format('Y-m-d');
            $resp[$result->country_code][] = ['x' => $date, 'y' => round($result->tot_disb_count,3)];
        }

        return $resp;
    }

    public function get_tot_settled_disb_count($country_code)
    {
		
        $results = DB::connection("report")->select("select acc_prvdr_code, month, tot_fa_settled_count from monthly_mgmt_reports where acc_prvdr_code is not null and country_code = '{$country_code}' order by month");
        $resp = array();
        foreach ($results as $result ){
            $date = carbon::createFromFormat('Ymd',($result->month.'01'))->format('Y-m-d');
            $resp[$result->acc_prvdr_code][] = ['x' => $date, 'y' => round($result->tot_fa_settled_count,3)];
        }

        return $resp;
    }

    public function get_tot_settled_disb_count_of_country($country_code)
    {
        $results = DB::connection("report")->select("select country_code, month, tot_fa_settled_count from monthly_mgmt_reports where acc_prvdr_code is null and country_code = '{$country_code}' order by month");
        $resp = array();
        foreach ($results as $result ){
            $date = carbon::createFromFormat('Ymd',($result->month.'01'))->format('Y-m-d');
            $resp[$result->country_code][] = ['x' => $date, 'y' => round($result->tot_fa_settled_count,3)];
        }

        return $resp;
    }

    public function get_ontime_payments_for_country($country_code)
    {
        $results = DB::connection("report")->select("select country_code, month, (ontime_repayment_rate * 100) on_time_repayment_percent from monthly_mgmt_reports where acc_prvdr_code is null and country_code = '{$country_code}' order by month");
        $resp = array();
        foreach ($results as $result ){
            $date = carbon::createFromFormat('Ymd',($result->month.'01'))->format('Y-m-d');
            $resp[$result->country_code][] = ['x' => $date, 'y' => round($result->on_time_repayment_percent,3)];
        }

        return $resp;
    }

    public function get_ontime_payments($country_code)
    {
        $results = DB::connection("report")->select("select acc_prvdr_code, month, (ontime_repayment_rate * 100) on_time_repayment_percent from monthly_mgmt_reports where acc_prvdr_code is not null and country_code = '{$country_code}' order by month");
        $resp = array();
        foreach ($results as $result) {
            $date = carbon::createFromFormat('Ymd', ($result->month . '01'))->format('Y-m-d');
            $resp[$result->acc_prvdr_code][] = ['x' => $date, 'y' => round($result->on_time_repayment_percent,3)];
        }

        return $resp;
    }

	public function get_outstanding_fa_for_country($country_code)
    {
        $results = DB::connection("report")->select("select country_code, month, os_val_eom from monthly_mgmt_reports where acc_prvdr_code is null and country_code = '{$country_code}' order by month");
        $resp = array();
        foreach ($results as $result ){
            $date = carbon::createFromFormat('Ymd',($result->month.'01'))->format('Y-m-d');
            $resp[$result->country_code][] = ['x' => $date, 'y' => round($result->os_val_eom,3)];
        }

        return $resp;
    }

    public function get_outstanding_fa($country_code)
    {
        $results = DB::connection("report")->select("select acc_prvdr_code, month, os_val_eom  from monthly_mgmt_reports where acc_prvdr_code is not null and country_code = '{$country_code}' order by month");
        $resp = array();
        foreach ($results as $result) {
            $date = carbon::createFromFormat('Ymd', ($result->month . '01'))->format('Y-m-d');
            $resp[$result->acc_prvdr_code][] = ['x' => $date, 'y' => round($result->os_val_eom,3)];
        }

        return $resp;
    }

    public function get_revenue_for_country($country_code)
    {
        $results = DB::connection("report")->select("select country_code, month, revenue from monthly_mgmt_reports where acc_prvdr_code is null and country_code = '{$country_code}' order by month");
        $resp = array();
        foreach ($results as $result) {
            $date = carbon::createFromFormat('Ymd', ($result->month . '01'))->format('Y-m-d');
            $resp[$result->country_code][] = ['x' => $date, 'y' => round($result->revenue,3)];
        }

        return $resp;
    }

    public function get_portfoloio_risk($country_code = null)
    {
        $date = Carbon::now()->subMonthNoOverflow()->endOfMonth();
        if($country_code){
            $result = db::connection('report')->select("select par_days, country_code, par_loan_principal, acc_prvdr_code, percentage from portfolio_risks where date(date) = date('{$date}') and country_code = '{$country_code}'");
        }
        else{
            $result = db::connection('report')->select("select par_days, sum(par_loan_principal) as par_loan_principal, sum(par_loan_principal)/sum(total_os_principal) as percentage from portfolio_risks  where date(date) = date('{$date}') group by par_days");
        }

        $date = $date->format('d-m-Y');

        return ["report" => $result, "report_date" => $date];
    }

    public function get_outstanding($country_code = null)
    {
        $addl = "";
        if($country_code){
            $addl = " and country_code = '{$country_code}' ";
        }

        $result = db::select("select  DATEDIFF(CURDATE(), due_date) overdue_days, data_prvdr_cust_id partner_id ,data_prvdr_code, dp_rel_mgr_id, cust_id, cust_mobile_num, product_name, duration, loan_principal, flow_fee, paid_amount,
                    disbursal_date,due_date ,paid_date ,biz_name from loans where status in ('ongoing','due','overdue') and  DATEDIFF(CURDATE(), due_date) > 0 {$addl}");

        $report_date = Carbon::now()->format("d-m-Y");

        return ["report" => $result, "report_date" => $report_date];
    }

    public function get_cust_performance($country_code = null)
    {
        $addl = "";
        if($country_code){
            $addl = " where country_code = '{$country_code}' ";
            $addl2 = " and country_code = '{$country_code}' ";
        }

        $full_perf_list = db::connection('report')->select("select  * from client_performance  {$addl}");
        $perf_list = db::connection('report')->select("select gender, sum(total_FA) total_FA, sum(total_amt) total_Amt, sum(total_Fee) total_Fee, sum(total_Fee_USD) total_Fee_USD, avg(assume_income) assume_income, avg(fee_per_income) fee_per_income, sum(per_adv_revenue) per_adv_revenue, avg(avg_fa_size) avg_fa_size, avg(total_Late_FA_perc) total_Late_FA_perc  from client_performance {$addl} group by gender");
        $report_date = Carbon::now()->subDay(1)->format("d-m-Y");



        return ['full_perf_list' => $full_perf_list, 'perf_list' => $perf_list, 'report_date' => $report_date];
    }
    
	public function get_sms_report($data){

		$criteria = $data['sms_report_criteria'];
		
		if(array_key_exists('otp_type',$criteria)){
			unset($criteria['otp_type']);
		}

		$addl_sql = $this->get_add_sql($criteria);

		$sms_report = DB::select("select vendor_code,direction, status, purpose, count(*) as sms_counts from sms_logs where $addl_sql and country_code = ? group by status, vendor_code, purpose,direction",[$data['country_code']]);

		return $sms_report;

	}

	private function get_add_sql($criteria){

		m_array_filter($criteria);

		$add_sql = "";

        foreach( $criteria as $key => $value){
            if($add_sql != ""){
                $add_sql .= " and ";
            }
            if($key == 'start_date'){  
                $add_sql .= "date(created_at) >= '{$criteria[$key]}'";
            }
            else if($key == 'end_date'){
                $add_sql .= " date(created_at) <= '{$criteria[$key]}'";
            }
			else{
                $add_sql .="$key = '{$criteria[$key]}'";
            }

        }

        return $add_sql;

	}
    public function get_revenue($country_code)
    {
        $results = DB::connection("report")->select("select acc_prvdr_code, month, revenue from monthly_mgmt_reports where acc_prvdr_code is not null and country_code = '{$country_code}' order by month");
        $resp = array();
        foreach ($results as $result) {
            $date = carbon::createFromFormat('Ymd', ($result->month . '01'))->format('Y-m-d');
            $resp[$result->acc_prvdr_code][] = ['x' => $date, 'y' => round($result->revenue,3)];
        }

        return $resp;
    }

    public function get_total_and_overdue_fa($country_code)
    {
        $results = DB::connection("report")->select("select tot_disb_count, acc_prvdr_code, new_overdue_count, month from monthly_mgmt_reports where acc_prvdr_code is null and country_code = '{$country_code}' order by month");
        $resp = array();
        foreach ($results as $result ){
            $date = carbon::createFromFormat('Ymd',($result->month.'01'))->format('Y-m-d');
            $resp['Total Disbursed'][] = ['x' => $date, 'y' => $result->tot_disb_count];
            $resp['New Overdues'][] = ['x' => $date, 'y' => $result->new_overdue_count];
        }

        return $resp;
    }

	public function get_advances_disbursed_and_completed($country_code)
    {

        $results = DB::connection("report")->select("select tot_disb_count,tot_fa_settled_count, acc_prvdr_code, od_count, month from monthly_mgmt_reports where acc_prvdr_code is null and country_code = '{$country_code}' order by month");
        $resp = array();
        foreach ($results as $result ){
            $date = carbon::createFromFormat('Ymd',($result->month.'01'))->format('Y-m-d');
            $resp['Advances Disbursed'][] = ['x' => $date, 'y' => $result->tot_disb_count];
            $resp['Advances Completed'][] = ['x' => $date, 'y' => $result->tot_fa_settled_count];
        }

        return $resp;
    }

    public function get_monthly_report_date()
    {
        $date = DB::connection("report")->select("select max(run_at) max from monthly_mgmt_reports");
        $report_date = Carbon::parse($date[0]->max)->subDay()->format(Consts::DB_DATETIME_FORMAT);
        return $report_date;
    }

	public function get_rm_productivity_report($data)
    {
		$country_code = session('country_code');
		$start_date= $data['date_range']['start_date'] ?? Carbon::now()->startOfMonth()->format("Y-m-d");
		$end_date= $data['date_range']['end_date'] ?? Carbon::now()->format("Y-m-d");
        $this->currency_code = (new CommonRepositorySQL())->get_currency()->currency_code;

        $datas= DB::select("select concat(p.first_name,' ',p.last_name) as rm_names, sum(l.paid_fee+l.penalty_collected)/count(distinct  l.cust_id) as revenue_per_customer, sum(l.loan_principal)/count(l.loan_doc_id) as fa_size, count(distinct  l.cust_id) as cust_count  from persons p ,loans l where p.id= l.flow_rel_mgr_id and l.disbursal_status= 'disbursed' 
		and l.country_code = '{$country_code}' and disbursal_date >= '{$start_date}' and  disbursal_date<= '{$end_date}'  group by flow_rel_mgr_id");

		$record_arr=[];
        $records=json_decode(json_encode($datas), true);
		foreach($records as $key =>$value ){

				$record_arr[] = array_values($value);
		}
		$units =[null, $this->currency_code, $this->currency_code,null];
		$header = ["RM Name", "Revenue Per Customer", "FA size" ,"Total Customers"]; 
		return ['title' => "RM Productivity Report" ,'header' => $header, 'units' => $units, 'records'=>$record_arr, "page_length" => 50 ];
		
    }
	
}

