<?php

namespace App\Services\Support;

use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\LoanTransactionRepositorySQL;
use App\Repositories\SQL\MarketRepositorySQL;
use App\Services\Support\SMSService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use App\SMSTemplate;
use Carbon\Carbon;



class SMSNotificationService {

	public function __construct(){
		
		$this->loan_repo = new LoanRepositorySQL();
		$this->loan_txn_repo = new LoanTransactionRepositorySQL();
		$this->person_repo = new PersonRepositorySQL();
	}


	public function notify_loan($loan, $message){
		$msg = $this->get_sms($loan, $message);
		$market_repo = new MarketRepositorySQL();
		$isd_code = $market_repo->get_isd_code($loan->country_code);
		//Log::warning($isd_code->isd_code);
		$sms_serv = new SMSService();
		$sms_serv($loan->cust_mobile_num, $msg, $isd_code->isd_code);
		return $msg;

	}
	// public function send_call_feedback($log_data){
	// 	$message = compile_sms('CALL_LOG_FEEDBACK_MSG', $log_data);
	// 	$market_repo = new MarketRepositorySQL();
	// 	$isd_code = $market_repo->get_isd_code($log_data['country_code']);

	// 	$sms_serv = new SMSService();
	// 	$sms_serv($log_data['cust_mobile_num'], $message, $isd_code->isd_code);
	// } 
	
	public function notify_welcome_customer($data){
		
		$message = compile_sms('WELCOME_MSG', $data);
		$market_repo = new MarketRepositorySQL();
		$isd_code = $market_repo->get_isd_code($data['country_code']);
		$log_data = ['purpose' => 'otp/verify_mobile', 'otp_id' => $data['otp_id'], 'cust_id' => $data['cust_id']];
		$sms_serv = new SMSService();
		$sms_serv($data['cust_mobile_num'], $message, $isd_code->isd_code, false, $log_data);
		
	}
	public function notify_welcome_enabled_customer($data){
		$message = compile_sms('WELCOME_ENABLED_MSG', $data);
		$market_repo = new MarketRepositorySQL();
		$isd_code = $market_repo->get_isd_code($data['country_code']);
		$sms_serv = new SMSService();
		$sms_serv($data['cust_mobile_num'], $message, $isd_code->isd_code);
		
	}

	public function send_kyc_otp($data){
		$message = compile_sms('OTP_MSG', $data);
		$market_repo = new MarketRepositorySQL();
		$isd_code = $market_repo->get_isd_code($data['country_code']);
		$sms_serv = new SMSService();
		$sms_serv($data['cust_mobile_num'], $message, $isd_code->isd_code);
		
	}
	public function send_appl_confirmation_details($data){

	    $market_repo = new MarketRepositorySQL();
	    $isd_code = $market_repo->get_isd_code($data['country_code']);
        $data['isd_code'] = $isd_code->isd_code;
    	// $message = compile_sms(SMSTemplate::APPL_CONFIRMATION_MSG, $data);

		$message = compile_sms('APPL_CONFIRMATION_MSG', $data);

	    $sms_serv = new SMSService();
	    $log_data = ['purpose' => 'otp/confirm_fa', 'otp_id' => $data['otp_id'], 'loan_doc_id' => $data['loan_doc_id']];
	    $sms_serv($data['cust_mobile_num'], $message, $isd_code->isd_code, true, $log_data);
	}

    public function send_cust_app_otp($data){
        $market_repo = new MarketRepositorySQL();
        $isd_code = $market_repo->get_isd_code($data['country_code']);
        $data['isd_code'] = $isd_code->isd_code;
        $message = compile_sms('CUST_APP_MOBILE_CONFIRM', $data);
        $sms_serv = new SMSService();
        $log_data = ['purpose' => 'otp/cust_app_verification', 'otp_id' => $data['otp_id']];
        $sms_serv($data['cust_mobile_num'], $message, $isd_code->isd_code, $log_data);
    }


	public function get_sms($loan, $message){
		$this->add_fields($loan);
		return compile_sms($message , (array)$loan);
	}

	public function send_cust_visit_feedback($field_visits){
		
		$message = compile_sms('FIELD_VISIT_FEEDBACK_MSG', $field_visits);
		$market_repo = new MarketRepositorySQL();
		$isd_code = $market_repo->get_isd_code($field_visits['country_code']);

		$sms_serv = new SMSService();
		$sms_serv($field_visits['cust_mobile_num'], $message, $isd_code->isd_code);


	}


	private function add_fields(&$loan){
		$split_cust_name = explode(" ", $loan->cust_name);
		$loan->cust_name = $split_cust_name[0];
		//if(isset($loan->flow_rel_mgr_id)){
		//	$mobile_num = $this->person_repo->get_mobile_num($loan->flow_rel_mgr_id);
		//	$loan->mobile_num = $mobile_num->mobile_num;
		//}

	}

    public function send_confirmation_message($data, $template)
    {
        $market_repo = new MarketRepositorySQL();
	    $isd_code = $market_repo->get_isd_code($data['country_code']);
        $data['isd_code'] = $isd_code->isd_code;
        $message = compile_sms($template, $data );
	    $sms_serv = new SMSService();
	    $sms_serv($data['cust_mobile_num'], $message, $isd_code->isd_code, false, $data);

    }

    public function send_notification_message( $data,$template)
    {
		$market_repo = new MarketRepositorySQL();
		$isd_code = $market_repo->get_isd_code($data['country_code']);
		$message = compile_sms($template,$data);
		$sms_serv = new SMSService();
		$sms_serv($data['cust_mobile_num'], $message, $isd_code->isd_code);

    }

    private function bifurcate_loans($loans, &$regular_overdue_loans, &$penalty_only_pending_loans){
		
		if(!empty($loans)){
			foreach($loans as $loan){
				if($loan->due_amount == $loan->paid_amount && $loan->current_os_amount > 0 && $loan->penalty_amount > 0){
					
					$penalty_only_pending_loans[] = $loan;
				}elseif($loan->paid_amount < $loan->due_amount + $loan->penalty_amount){
					
					$regular_overdue_loans[] = $loan;
				}
			}
		}
	}

	
}
