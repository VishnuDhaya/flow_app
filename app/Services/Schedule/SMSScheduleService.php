<?php

namespace App\Services\Schedule;

use App\Consts;
use App\Repositories\SQL\CustAgreementRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\LoanTransactionRepositorySQL;
use App\Repositories\SQL\MarketRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use App\SMSTemplate;
use App\Services\Support\SMSService;
use App\Services\LoanService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class SMSScheduleService {
	
	public function __construct($market){
		$this->sms_serv = new SMSService();
		$this->loan_repo = new LoanRepositorySQL();
		$this->loan_txn_repo = new LoanTransactionRepositorySQL();
		$this->loan_serv = new LoanService();
		$this->market = $market;
		
	}



	public function notify_today_due_loans(){
		$today = Carbon::now()->endOfDay();
		$loans = $this->loan_repo->get_due_loans($today);
		$message = 'DUE_DATE_REMIND_MSG';
		return $this->process_loans($loans, $message);
		
	}

	public function notify_overdue_loans(){
		//$loans = $this->loan_repo->get_overdue_loans(); already //
		// $loan_doc_ids = $this->loan_txn_repo->get_penalty_loans();
		// $loans = $this->loan_repo->get_loans($loan_doc_ids);
		// $message = SMSTemplate::overdue_msg;
		// return $this->process_loans($loans, $message);

		$loans = $this->loan_repo->get_records_by("status",'overdue', ['loan_doc_id', 'cust_id', 'cust_name',
		 'currency_code', 'cust_mobile_num', 'due_date', 'provisional_penalty', 'due_amount', 'paid_amount', 
		 'current_os_amount', 'acc_prvdr_code','country_code']);
		 
		
		
		$sms_serv = new SMSService();
		foreach($loans as $loan){
			$loan = $this->loan_serv->add_overdue_details($loan);
			
			if($loan->days_overdue < 5){
				$sms_serv($loan->cust_mobile_num, $loan->message, $this->market->isd_code);
				
			}else if($loan->days_overdue > 5 && $loan->days_overdue <=45){
				if($loan->days_overdue%2 != 0 ){
					$sms_serv($loan->cust_mobile_num, $loan->message, $this->market->isd_code);
				}
				
			}
			
		}
	}

	public function notify_tomorrow_due_loans() {
		$due_date = Carbon::tomorrow()->endOfDay();
		$loans = $this->loan_repo->get_due_loans($due_date);
		$message = 'DUE_TOMORROW_REMIND_MSG';
		return $this->process_loans($loans, $message);
	}

	private function process_loans($loans , $message){
		if(!empty($loans)){
			foreach($loans as $loan){
				
				$split_cust_name = explode(" ", $loan->cust_name);
				$loan->cust_name = $split_cust_name[0];
				
				$msg = compile_sms($message , (array)$loan);
				$sms_serv = new SMSService();
				$sms_serv($loan->cust_mobile_num, $msg, $this->market->isd_code);
			}
		}
	}
	
	
	public function reminder_rm(){
		
		$approvers = DB::select("select loan_approver_id, count(1) as count, min(loan_appl_date) as min_time from loan_applications where status = 'pending_approval' and loan_approver_id is not null group by loan_approver_id");

		foreach($approvers as $approver){
			
			$person_repo = new PersonRepositorySQL();
			$person = $person_repo->get_person_name($approver->loan_approver_id);

			$approver_name = full_name($person);
			$mobile_num = $person->mobile_num;

			$min_time = $approver->min_time;
			$current_time = Carbon::now()->toDateTimeString();
			
			$startTime = Carbon::parse($min_time);
			// $endTime = Carbon::parse($current_time);
			$time =  $startTime->diffInHours($current_time);
			
			$time = $time." hours";
		
		
			$this->notify_pending_loan_appls([	'approver_name' => $approver_name,
													'mobile_num' => $mobile_num,
													'no_of_fas' => $approver->count,
													'country_code' => session('country_code'),
													'last_time' => $time
												]);

		}

	}
	
	public function notify_pending_loan_appls($loan_appl){

		$message = compile_sms('APPROVAL_PENDING_MSG', $loan_appl);
		$market_repo = new MarketRepositorySQL();
		$isd_code = $market_repo->get_isd_code($loan_appl['country_code']);
		$sms_serv = new SMSService();
		$sms_serv($loan_appl['mobile_num'], $message, $isd_code->isd_code);
	}

	public function notify_today_due_loans_evening(){
		$today = Carbon::now()->endOfDay();
		$loans = $this->loan_repo->get_due_loans($today);
		$message = 'EVENING_DUE_DATE_REMIND_MSG';
		return $this->process_loans($loans, $message);
		
	}

	public function expiring_agreement_sms($market){

		$sms_serv = new SMSService();
		$market_repo = new MarketRepositorySQL();
		$isd_code = $market_repo->get_isd_code(session('country_code'));
		$date=Carbon::now()->addDays(3)->format(Consts::DB_DATE_FORMAT);
		$sql = "(date (aggr_valid_upto) = '{$date}' or (aggr_valid_upto is null and prob_fas <= 1 and date(DATE_ADD(last_loan_date, INTERVAL 1 DAY))= date (NOW()) and status = 'enabled')) ";
        $expiring_aggrs = DB::select("select cust_id, tot_loans, category, owner_person_id, cust_id, aggr_valid_upto, current_aggr_doc_id from borrowers where {$sql} and country_code = ? ",[session('country_code')]);
		foreach($expiring_aggrs as $expiring_aggr){
			log::warning(array($expiring_aggr));
			log::warning('expiring_aggr');
			$message = null;
			$cust_mobile_num = (new PersonRepositorySQL)->get_record_by('id', $expiring_aggr->owner_person_id, ['mobile_num'])->mobile_num;
           
			if($expiring_aggr->category == 'Probation'){
				$cust_aggr = (new CustAgreementRepositorySQL)->get_record_by('aggr_doc_id', $expiring_aggr->current_aggr_doc_id, ['status', 'aggr_type']);
			
				if($cust_aggr && $cust_aggr->status == 'active' && $cust_aggr->aggr_type == 'onboarded'){
					$curr_aggr_active = true;
				}
				else{
					$data = ['prob_fas_count' => $expiring_aggr->tot_loans, 'total_prob_fas' => config('app.default_prob_fas')];
					$message = compile_sms('PROB_CUST_AGGR_RENEWAL_MSG', $data);
				}
			}
			else{
			
				$data = ['aggr_valid_upto' => $expiring_aggr->aggr_valid_upto];
				$message = compile_sms('CUST_AGGR_RENEWAL_MSG', $data);
			}
			if($message){
				$sms_serv($cust_mobile_num, $message ,$isd_code);
			}
			
		}
        
	
	}

}