<?php

namespace App\Services;

use App\Consts;
use Illuminate\Support\Facades\Log;
use App\Repositories\SQL\AccountStmtRepositorySQL;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\FlowCustomMail;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Exceptions\FlowCustomException;

class AutoCapturePaymentService{

	public function auto_capture($account_id, $matching_recon_status = Consts::MATCHING_RECON_STATUS, $acc_stmt_id = null){
		$stmt_repo = new AccountStmtRepositorySQL;
		
		// $acc_stmts_capture = $stmt_repo->get_records_by('recon_status','10_capture_payment_pending',['id','country_code','loan_doc_id','cr_amt','stmt_txn_id','stmt_txn_date']);

		$addl_sql = "";
		if($acc_stmt_id){
			$addl_sql = "and id = $acc_stmt_id";
		}

		$recon_start_date = config('app.recon_scr_strt_date');
		$addl_condn = "and date(stmt_txn_date) >= '$recon_start_date' $addl_sql order by stmt_txn_date";


		$payments_pending_capture = $stmt_repo->get_records_by_many(['recon_status','account_id'],['10_capture_payment_pending',$account_id],['id','country_code','loan_doc_id','cr_amt','stmt_txn_id','stmt_txn_date'], "and", $addl_condn);

		if($payments_pending_capture){

			foreach($payments_pending_capture as $pending_capture){

				$pay_smry_req['mode'] = 'capture';
				$pay_smry_req['amount'] = $pending_capture->cr_amt;
				$pay_smry_req['loan_doc_id'] =  $pending_capture->loan_doc_id;

				$this->initiate_auto_capture($pay_smry_req, $pending_capture, $matching_recon_status);
			}	
		}
		
		$dup_disb_rvrsl_pend_capture = $stmt_repo->get_records_by_many(['recon_status','account_id'],['11_capture_disb_rvrsl_pending',$account_id],['id','loan_doc_id','cr_amt','stmt_txn_id','stmt_txn_date'], "and", $addl_condn);
		if($dup_disb_rvrsl_pend_capture){
			foreach($dup_disb_rvrsl_pend_capture as $stmt_txn){
				$loan_txn = ['amount' => $stmt_txn->cr_amt, 'txn_id' => $stmt_txn->stmt_txn_id, 'txn_date' => $stmt_txn->stmt_txn_date,
							 'txn_mode' => 'wallet_transfer', 'to_ac_id' => $account_id, 'loan_doc_id' => $stmt_txn->loan_doc_id];
				(new LoanService)->capture_dup_disb_n_reversal($loan_txn, 'credit');
			}
		}
	}

	private function initiate_auto_capture($pay_smry_req, $pending_capture, $matching_recon_status){

		$this->repay_serv = new RepaymentService();

		try{
			$loan = $this->repay_serv->get_payment_summary($pay_smry_req);
			$loan->mode = 'auto_capture';
			$loan->acc_stmt_id = $pending_capture->id;
	
			if($loan->payment_status == 'settled' || $loan->payment_status == 'partially_paid'){
				$this->repay_serv->review_n_sync((array)$loan, $loan->payment_status, $loan->review_reason, $matching_recon_status);
			}else{
				(new AccountStmtRepositorySQL)->update_model(["review_reason" => $loan->review_reason,"id" => $pending_capture->id]);
				$this->send_payment_txn_review_email(array_merge((array)$loan, (array)$pending_capture));
			}
		}
		catch(\Exception $e){
	
			$this->send_exception_email($e->getMessage(), (array)$pending_capture);
			if($matching_recon_status == Consts::PENDING_STMT_IMPORT) thrw($e->getMessage());

		}	
		#$loan->acc_stmt_txn_id = $acc_stmt->stmt_txn_id;
	
	}

	private function send_payment_txn_review_email($data){

		$csm_email = get_csm_email();

		if($data['review_reason'] == 'penalty_pending'){

			Mail::to($csm_email)->send(new FlowCustomMail('auto_capture_review_pending', $data)); 	

		}else if($data['review_reason'] == 'excess_paid'){

			Mail::to([config('app.app_support_email'), $csm_email])->send(new FlowCustomMail('auto_capture_review_pending', $data)); 			
		}

	}

	private function send_exception_email($exception, $data){

		$currency_code = (new CommonRepositorySQL())->get_currency_code($data['country_code']);

		$data['failed_at'] = Carbon::now();
		$data['exception'] = $exception;
		$data['currency_code'] = $currency_code->currency_code;

		$csm_email = get_csm_email();
		Mail::to([config('app.app_support_email'), $csm_email])->send(new FlowCustomMail('auto_capture_failure', $data)); 		

	}
}