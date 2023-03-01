<?php

namespace App\Services;
use App\Repositories\SQL\AccountStmtRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\LoanTransactionRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\ProbationPeriodRepositorySQL;
use App\Repositories\SQL\CapitalFundRepositorySQL;
use App\Repositories\SQL\WriteOffRepositorySQL;
use App\Services\Support\FireBaseService;
use App\Services\Support\SMSNotificationService;
use App\Services\Vendors\SMS\AitSMSService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\FARepeatQueue;
use App\Consts;
use Carbon\Carbon;
use App\SMSTemplate;
use Mail;
use App\Mail\FlowCustomMail;
use Illuminate\Support\Facades\Storage;



class RepaymentService {
	public function get_payment_summary($data){

        $account_repo = new AccountStmtRepositorySQL();

		if($data['mode'] == 'recon'){

            $acc_stmt = $account_repo->find($data['acc_stmt_id'],['loan_doc_id','cr_amt', 'descr','stmt_txn_id','stmt_txn_date']);
            if($acc_stmt && $acc_stmt->loan_doc_id == $data['loan_doc_id']){
                if(array_key_exists('waived_penalty',$data) || array_key_exists('waived_fee', $data)){
                    $amount = $data['amount'];
                }else{
                    $amount = $acc_stmt->cr_amt;
                }
                    

            }else{
                thrw("No valid A/C statement record found for {$data['acc_stmt_id']}");
            }

        }else if ($data['mode'] == 'capture'){
            $amount = $data['amount'];
            $loan_txn_repo = new LoanTransactionRepositorySQL;

            $stmt_txn = $account_repo->get_record_by_many(['loan_doc_id','recon_status'], [$data['loan_doc_id'], '31_paid_to_different_acc'], ['id']);

            if($stmt_txn ){
                thrw("You cannot capture the repayment here. \n Already the repayment against this FA has paid to the different account.\n Please verify that payment and capture manually using 'Manual Recon' in Unknown Txns Screen from the Flow's Float Accounts Menu.");
            }

            if(isset($data['txn_date'])){
                if($data['txn_date'] > carbon::now()->format('Y-m-d')){
                    thrw("You can not capture a transaction for future date, Please enter a valid date");
                }elseif($data['txn_date']< Carbon::now()->format('Y-m-d') && session('role_codes') != "app_support"){
                    thrw('You cannot capture a transaction for past date. Report to App support to capture the transaction ');
                }
            }
           

            if(array_key_exists('txn_id' ,$data)){
                $dup_loans = $loan_txn_repo->get_loan_doc_id($data['txn_id']);
                if(sizeof($dup_loans) > 0 && $dup_loans[0]->reversed_date == null){
                    thrw("Unable to capture this payment because this transaction ID ({$data['txn_id']}) has already been captured for another FA {$dup_loans[0]->loan_doc_id}", 5003);
                }
    
            }
            
        }
        $loan = $this->get_loan_w_capture_fields($data['loan_doc_id'], $amount);

        
        $loan->request_waiver = $loan->penalty_days > 1 ? true :false;        

        if($data['mode'] == 'recon'){
            $loan->acc_stmt_id = $data['acc_stmt_id'];
			foreach($acc_stmt AS $var=>$value){
                $loan->$var = $value;
            }
        }
        $payment_status = $this->get_payment_status($loan);

        $loan->payment_status = $payment_status['payment_status']; 
        $loan->review_reason = $payment_status['review_reason'];
        $loan->mode = $data['mode'];
        return $loan;

	}

	private function get_loan_w_capture_fields($loan_doc_id, $amount){
        $loan_repo = new LoanRepositorySQL();
        $loan = $loan_repo->find_by_code($loan_doc_id, ['cust_id','cust_name', 'current_os_amount', 'loan_doc_id', 'status','provisional_penalty','due_date','penalty_collected','due_amount','reversed_excess','paid_amount','paid_principal','loan_principal','paid_fee','flow_fee','penalty_waived','cust_mobile_num','biz_name',"country_code","flow_rel_mgr_id",'currency_code','lender_code','acc_prvdr_code','acc_number','product_id','fund_code','paid_excess', 'write_off_id', 'write_off_status', 'fee_waived', 'disbursal_date', 'penalty_days']);
        if($loan){
            if(in_array($loan->status, Consts::DISBURSED_LOAN_STATUS)){

                $to_capture = $this->get_amounts_to_capture($loan, $amount);
                /*$loan->os_principal = $loan->loan_principal - $loan->paid_principal;
                $loan->os_fee = $loan->flow_fee - $loan->paid_fee;    
                    
                $loan->os_penalty = $to_capture['tot_prov_penalty'] 
                                        - $loan->penalty_waived 
                                        - $loan->penalty_collected;*/

                $loan->new_status = $to_capture['new_status'];
                $loan->to_capture = $to_capture;
                $loan->amount = $amount;


                return $loan;
            }else{
                thrw("FA {$loan->loan_doc_id} is in {$loan->status} status. Can not capture payment");
            }

        }else{
            thrw("No valid FA record found for {$loan->loan_doc_id}");
        }
    }

	public function test_all(){
    	/*$this->test(9000,0,0,0,10000,1000,100);
    	$this->test(10000,0,0,0,10000,1000,100);
    	$this->test(10500,0,0,0,10000,1000,100);
    	$this->test(11000,0,0,0,10000,1000,100);
    	$this->test(11050,0,0,0,10000,1000,100);
    	$this->test(11100,0,0,0,10000,1000,100);
    	$this->test(11200,0,0,0,10000,1000,100);
    	$this->test(4000,5000,0,0,10000,1000,100);
    	$this->test(5000,5000,0,0,10000,1000,100);
    	$this->test(5500,5000,0,0,10000,1000,100);
    	$this->test(6000,5000,0,0,10000,1000,100);
    	$this->test(6050,5000,0,0,10000,1000,100);
    	$this->test(6100,5000,0,0,10000,1000,100);
    	$this->test(6200,5000,0,0,10000,1000,100);
    	$this->test(500,10000,0,0,10000,1000,100);
    	$this->test(1000,10000,0,0,10000,1000,100);
    	$this->test(1050,10000,0,0,10000,1000,100);
    	$this->test(1100,10000,0,0,10000,1000,100);
    	$this->test(1500,10000,0,0,10000,1000,100);*/

    	$this->test(400,10000,500,0,10000,1000,100);
    	$this->test(500,10000,500,0,10000,1000,100);
    	$this->test(550,10000,500,0,10000,1000,100);
    	$this->test(600,10000,500,0,10000,1000,100);
    	$this->test(1000,10000,500,0,10000,1000,100);

    	$this->test(50,10000,1000,0,10000,1000,100);
    	$this->test(100,10000,1000,0,10000,1000,100);
    	$this->test(200,10000,1000,0,10000,1000,100);

    	$this->test(40,10000,1000,50,10000,1000,100);
    	$this->test(50,10000,1000,50,10000,1000,100);
    	$this->test(100,10000,1000,50,10000,1000,100);

    	$this->test(100,10000,1000,100,10000,1000,100);



    }
   private function test($txn_amount, $paid_principal, $paid_fee, $penalty_collected,
   				$loan_principal, $flow_fee, $provisional_penalty){
   	$loan = ['txn_amount' => $txn_amount,'paid_principal' => $paid_principal, 				'paid_fee' => $paid_fee,'penalty_collected' => $penalty_collected,
   						'loan_principal' => $loan_principal,'due_date' =>"2021-05-19 23:59:59", 'flow_fee' => $flow_fee, 
   						'provisional_penalty' => $provisional_penalty];
   	Log::warning("START");
   	Log::warning($loan);	
   	$loan = (object) $loan;
	$result = $this->get_amounts_to_capture($loan,$txn_amount);	
	Log::warning($result);
	Log::warning("END");
   }

    public function capture($amount, $paid_already, $tot_to_pay){
        if($paid_already == $tot_to_pay){
            return 0;
        }else {
        	$pending = $tot_to_pay - $paid_already ;

        	Log::warning("amount : $amount | pending : $pending");
        	if($pending >= $amount){
            	return $amount;
	        }else if($pending < $amount){
	            return $pending ;
	        }
        }
    }
	public function get_amounts_to_capture(&$loan, $txn_amount){

		$principal = $fee = $penalty = $excess = $waived_penalty_paid = 0;

	    $principal = $this->capture($txn_amount, $loan->paid_principal, $loan->loan_principal);
	 $remaining = $txn_amount - $principal;


	 $fee = $this->capture($remaining, $loan->paid_fee + $loan->fee_waived, $loan->flow_fee);
	 $remaining = $remaining - $fee;


	 $penalty_days = getPenaltyDate($loan->due_date);
	 $loan->penalty_collected = (int) $loan->penalty_collected;
	 $provisional_penalty = (int) $loan->provisional_penalty;

	 $tot_prov_penalty = $provisional_penalty * $penalty_days;
     $penalty_after_waiver = $tot_prov_penalty - $loan->penalty_waived;


	 $penalty = $this->capture($remaining, $loan->penalty_collected + $loan->penalty_waived, $tot_prov_penalty);

     $remaining = $remaining - $penalty;

    if($remaining > 0 && $loan->penalty_waived > 0){
      $paid_waiver = ($loan->penalty_collected + $penalty) - $penalty_after_waiver;
      $waived_penalty_paid = $this->capture($remaining, $paid_waiver, $loan->penalty_waived);
      $penalty += $waived_penalty_paid;
    }
     $excess = $remaining - $waived_penalty_paid;

	 $loan->os_principal = $loan->loan_principal - $loan->paid_principal;
	 $loan->os_fee = $loan->flow_fee - $loan->fee_waived - $loan->paid_fee;

	 $loan->os_penalty = $tot_prov_penalty
     - $loan->penalty_waived
     - $loan->penalty_collected;

	 $loan->os_total =  $loan->os_principal + $loan->os_fee + $loan->os_penalty ;



     
	 if($penalty >= $loan->os_penalty &&
         $penalty <= $loan->os_penalty + $loan->penalty_waived &&
		 $principal == $loan->os_principal  &&
		 $fee == $loan->os_fee && 
		 $excess + $loan->paid_excess == $loan->reversed_excess ){

		 $new_status = Consts::LOAN_SETTLED;
	 }else{
		 $new_status = $loan->status;
	 }
	return [
			 'principal' => $principal,
			 'fee' => $fee,
			 'penalty' => $penalty,
             'waived_penalty_paid' => $waived_penalty_paid,
			 'excess' => $excess,
			 'penalty_days' => $penalty_days,
			 'tot_prov_penalty' => $tot_prov_penalty,
			 'new_status' => $new_status,

		 ]; 

}



public function capture_excess_reversal($data){
    
    try{
        DB::beginTransaction();

        $loan_txn_repo  = new LoanTransactionRepositorySQL;
        $loan_repo      = new LoanRepositorySQL;
        $borr_repo = new BorrowerRepositorySQL;

        $data['txn_type'] = 'excess_reversal';
        $data['country_code'] = session("country_code");
        $data['photo_reversal_proof'] = array_key_exists('photo_reversal_proof', $data)  ? $data['photo_reversal_proof'] : null;

        if(isset($data['photo_reversal_proof'] )){

            mount_entity_file("loan_txns", $data, $data['txn_id'], 'photo_reversal_proof');
            $data['photo_transaction_proof']['photo_reversal_proof'] = $data['photo_reversal_proof'];
            $data['photo_transaction_proof'] = json_encode($data['photo_transaction_proof']);
        }
       
        $loan_txn_repo->create($data);

        $result = $loan_repo->increment_by_code('reversed_excess',$data['loan_doc_id'] , $data['amount']);
        $loan = $this->get_loan_w_capture_fields($data['loan_doc_id'], 0);

        
        if($loan->new_status == Consts::LOAN_SETTLED){
            
            $loan_txn = DB::selectOne("select txn_date from loan_txns where loan_doc_id = ? order by id desc limit 1" ,[$data['loan_doc_id']]);
            $result = $loan_repo->update_model_by_code(['loan_doc_id' => $data['loan_doc_id'],'paid_date' => $loan_txn->txn_date, 'status' => Consts::LOAN_SETTLED]);
            $borr_repo->update_model_by_code(['cust_id' => $data['cust_id'], 'ongoing_loan_doc_id' => null,'last_loan_doc_id' => $data['loan_doc_id']]);
           
            $acc_stmt_repo = new AccountStmtRepositorySQL();

		    if(array_key_exists('acc_stmt_id', $data )){
                $stmt_txn = $acc_stmt_repo->find($data['acc_stmt_id'] , ['loan_doc_id', 'cr_amt', 'stmt_txn_date', 'descr', 'stmt_txn_id', 'account_id', 'dr_amt']);
            }else{
                $stmt_txn = $acc_stmt_repo->get_record_by_many(['stmt_txn_id','account_id'], [$data['txn_id'], $data['from_ac_id']], ['loan_doc_id', 'cr_amt', 'stmt_txn_date', 'descr', 'stmt_txn_id', 'account_id', 'dr_amt']);
            }

            $txn_type = 'debit';
            
            $acc_stmt_id = $this->validate_for_recon($data, $stmt_txn, $txn_type);
            
            if($acc_stmt_id){
                $acc_stmt_repo->update_model(["recon_status" => "80_recon_done","id" => $acc_stmt_id, "loan_doc_id" => $data["loan_doc_id"]]);
            }
        }

        if(isset($data['reason_for_skip'])){
            $data['acc_stmt_txn_id'] = $data['txn_id'];
            $data['txn_type'] = 'excess';
            $this->skip_txn_id_check_email($data);
        }

        DB::commit();
		}
		catch (Exception $e) {
			DB::rollback() ;
			throw new Exception($e->getMessage());
		}

        return $result;


}   

public function update_waiver($data, $with_txn = true){
    $field = $data['waiver_field'];
	if($data['new_outstanding_value'] >= $data['waived_amount']){
		$loan_repo      = new LoanRepositorySQL;
		$loan_txn_repo  = new LoanTransactionRepositorySQL;
		$loan_txn['txn_date'] = Carbon::now();
		$loan_txn['txn_type'] = "{$field}_waiver";
        $loan_txn['remarks'] = array_key_exists('remarks' ,$data) ? $data['remarks'] : null;
		$loan_txn['loan_doc_id'] = $data['loan_doc_id'];
		$loan_txn['amount'] = $data['waived_amount'];
		$loan_txn['send_sms'] = false;
        $loan_txn['country_code'] = session('country_code');
		try{
            $with_txn ? DB::beginTransaction() : null;

			$loan_repo->increment_by_code("{$field}_waived",$data['loan_doc_id'] ,$data["waived_amount"]);
            if($field == 'fee'){
                $loan_repo->increment_by_code('current_os_amount', $data['loan_doc_id'], -1 * $data['waived_amount']);
            }
			$loan_txn_repo->create($loan_txn);
            $data['amount'] = 0;
			/*$loan_txn_repo->update_model_by_code(["remarks" => $data['remarks'],
												 "loan_doc_id" => $data['loan_doc_id']]);*/
			$updated_summary = $this->get_payment_summary($data);
			$loan_txn['amount'] = 0;

			 if($updated_summary->to_capture['new_status'] == Consts:: LOAN_SETTLED){
                $loan_txn['is_part_payment'] = false;
                $loan_txn['mode'] = $data['mode'];
                $loan_txn['acc_stmt_id'] = array_key_exists('acc_stmt_id',$data) ? $data['acc_stmt_id'] : null;
				$result = $this->capture_repayment($loan_txn);
				$result['txn_status'] ="{$field}_waived" ;
			}else{
				$result = $updated_summary;
			}

            $with_txn ? DB::commit() : null;
		}
		catch (Exception $e) {
            $with_txn ? DB::rollback() : null;
			throw new Exception($e->getMessage());
		}

		return $result;

	}

}

public function reverse_waived_penalty($loan_doc_id, $reverse_amount, $with_txn = true){

    try{
        $txn_repo =  new LoanTransactionRepositorySQL;
        $with_txn ? DB::beginTransaction() : null;
        (new LoanRepositorySQL)->increment_by_code('penalty_waived', $loan_doc_id, -1 * $reverse_amount);
        $waiver_txns = $txn_repo->get_records_by_many(['loan_doc_id', 'txn_type'], [$loan_doc_id, 'penalty_waiver'], ['id', 'amount']);
        foreach($waiver_txns as $waiver_txn){
            $txn_amount = $waiver_txn->amount;

            if($txn_amount > $reverse_amount){
                DB::update("update loan_txns set amount = amount - ? where id = ?", [$reverse_amount, $waiver_txn->id]);
            }
            else{
                DB::delete("DELETE from loan_txns where id = ?", [$waiver_txn->id]);
            }

            $reverse_amount -= $txn_amount;

            if($reverse_amount <= 0){
                break;
            }
             
        }


        $with_txn ? DB::commit() : null;
    }catch(\Exception $e){
        $with_txn ? DB::rollback() : null;
        DB::rollback();
        thrw($e->getMessage());
    }
}


	public function capture_payment($data){
		$acc_stmt_txn_id = $data['acc_stmt_txn_id'];
		
		$loan_doc_id = $data['loan_doc_id'];
		$amount = $data['amount'];


		$acc_stmt_rep = new AccountStmtRepositorySQL();
		$stmt_txn = $acc_stmt_rep->find($acc_stmt_txn_id , ['loan_doc_id', 'cr_amt', 'stmt_txn_date', 'descr', 'stmt_txn_id', 'account_id']);

		$loan_repo = new LoanRepositorySQL();

	    $loan = $loan_repo->find_by_code($stmt_txn->loan_doc_id, ['status']);
		
		if($loan){

			if(in_array($loan->status, Consts::DISBURSED_LOAN_STATUS)){
				try
				{
					DB::beginTransaction();
					$this->capture_payment_from_acc_stmt_txn($loan_doc_id, $stmt_txn, $stmt_txn->account_id);

					$acc_stmt_rep->update_record_status("80_recon_done", $acc_stmt_txn_id, 'recon_status');

					DB::commit();

				}

				catch (FlowCustomException $e) {
	            	DB::rollback() ;
	            
	            	throw new FlowCustomException($e->getMessage());
       		 	}

   			// $stmt_txn =  $acc_stmt_rep->find($acc_stmt_txn_id,['recon_status']);
			// return $stmt_txn->recon_status;
			}
			else{
	        		thrw("FA {$loan_doc_id } is in {$loan->status} status. Can not capture payment");
	        	}

			
		}else{
			thrw("Unable to capture payment of {$amount} for {$loan_doc_id}");
		}

	}
	public function review_n_sync($data, $payment_status = null,$review_reason = null, $matching_recon_status = Consts::MATCHING_RECON_STATUS){
        $loan_repo = new LoanRepositorySQL();
        $loan_doc_id = $data['loan_doc_id'];
        $acc_stmt_id = null;
        if($data['mode'] == 'recon' || $data['mode'] == 'auto_capture'){
            $acc_stmt_id = $data['acc_stmt_id'];
            $acc_stmt_repo = new AccountStmtRepositorySQL();
            $acc_stmt_obj = $acc_stmt_repo->find($acc_stmt_id , ['loan_doc_id', 'cr_amt', 'stmt_txn_date', 'descr', 'stmt_txn_id', 'account_id','recon_status','recon_desc','id' ,'stmt_txn_type']);

            $loan_txn = $this->get_req_data_for_recon($loan_doc_id, $acc_stmt_obj);

        }else if ($data['mode'] == 'capture'){
            $acc_stmt_txn_id = $data['acc_stmt_txn_id'];
            $acc_stmt_repo = new AccountStmtRepositorySQL();
            $acc_stmt_obj = $acc_stmt_repo->get_record_by_many(['stmt_txn_id','account_id'], [$acc_stmt_txn_id,$data['to_ac_id']], ['id', 'cr_amt', 'stmt_txn_date', 'loan_doc_id']);
            
            $loan_txn = $this->get_req_data_for_capture($data);

            $loan_txn['txn_date'] = array_key_exists('txn_date', $data) ? $data['txn_date'] : null;

        }
        $loan_txn['reason_for_skip'] = array_key_exists('reason_for_skip', $data)  ? $data['reason_for_skip'] : null;

        $loan_txn['photo_payment_proof'] = array_key_exists('photo_payment_proof', $data)  ? $data['photo_payment_proof'] : null;

        
        $txn_type = 'credit';
        $acc_stmt_id = $this->validate_for_recon($loan_txn, $acc_stmt_obj, $txn_type);

	    $loan = $loan_repo->find_by_code($loan_doc_id, ['status']);

		if($loan){

			if(in_array($loan->status, Consts::DISBURSED_LOAN_STATUS)){
				try
				{
    				DB::beginTransaction();

                    $loan_txn['payment_status'] = $payment_status;
                    $loan_txn['review_reason'] =  $review_reason;
                    $loan_txn['mode'] = $data['mode'];
                    $this->capture_repayment($loan_txn, false);

                    $update_acc_stmt = false;
                   
                    if($matching_recon_status == Consts::PENDING_STMT_IMPORT){
                        $recon_status = $matching_recon_status;
                        $update_acc_stmt = true;
                    }else if($acc_stmt_id && ( !array_key_exists('is_skip_txn_id_chk', $data) || 
                                          (array_key_exists('is_skip_txn_id_chk', $data) && $data['is_skip_txn_id_chk'] == false) )){
                        $recon_status = '80_recon_done';
                        $update_acc_stmt = true;
                        
                    }else if($acc_stmt_id && array_key_exists('is_skip_txn_id_chk', $data)&& $data['is_skip_txn_id_chk']){
                        $recon_status = '71_pending_manual_recon';
                        $update_acc_stmt = true;
                    }

                    if($update_acc_stmt){
                        $acc_stmt_repo->update_model(["recon_status" => $recon_status, "id" => $acc_stmt_id, "loan_doc_id" => $loan_txn["loan_doc_id"]]);
                    }

    				$stmt_txn = $acc_stmt_repo->find($acc_stmt_id , ['loan_doc_id', 'cr_amt', 'stmt_txn_date', 'descr', 'stmt_txn_id', 'account_id','recon_status','recon_desc', 'stmt_txn_type']);
                    
    				if(isset($loan_txn['reason_for_skip'])){
                        $data['txn_type'] = 'repayment';
                        $data['txn_id'] = $data['acc_stmt_txn_id'];
                        $this->skip_txn_id_check_email($data);
                    }
                    
                    DB::commit();
                    $today_date = Carbon::now();
                    $records = (new FARepeatQueue)->get_records_by_many(['loan_doc_id', 'date(created_at)'], [$loan_doc_id, date_db($today_date)], ['cust_id', 'mobile_num', 'status']);
                    if(sizeof($records) == 1 && $records[0]->status == 'requested'){
                        (new AitSMSService)->process_repeat_fa($records[0]->cust_id, $records[0]->mobile_num);
                        (new FARepeatQueue)->update_model_by_code(['loan_doc_id' => $loan_doc_id, 'status' => 'processed']);
                    }
                    else if(sizeof($records) > 1){
                        Mail::to(config('app.app_support_email'))->queue((new FlowCustomMail('fa_repeat_queue', ['cust_id' => $records[0]->cust_id, 'country_code' => session('country_code'), 'mobile_num' => $records[0]->mobile_num ]))->onQueue('emails'));
                    }
				}
                
				catch (FlowCustomException $e) {
                	DB::rollback() ;
                	throw new FlowCustomException($e->getMessage());
       		 	}

				return $stmt_txn;
   			// $stmt_txn =  $acc_stmt_rep->find($acc_stmt_txn_id,['recon_status']);
			// return $stmt_txn->recon_status;
			}

		}

	}

    private function skip_txn_id_check_email($data){

        $loan = (new LoanRepositorySQL)->find_by_code($data['loan_doc_id'], ['biz_name', 'cust_name', 'acc_number']);
        $photo_type  = $data['txn_type'] == 'repayment' ? 'photo_payment_proof' : 'photo_reversal_proof';
        $photo_proof = $data[$photo_type];
        $photo_proof_path = get_file_path("loan_txns", $data['txn_id'],  $photo_type); 
        $photo_proof_full_path = Storage::path($photo_proof_path.'/'.$photo_proof);
        $mail_data = ['cust_name' => $loan->cust_name, 'biz_name' => $loan->biz_name,'acc_number' => $loan->acc_number, 'full_path' => $photo_proof_full_path, 'txn_type' => $data['txn_type'], 'country_code' => session('country_code'), 'txn_id' => $data['acc_stmt_txn_id'], 'loan_doc_id' => $data['loan_doc_id'], 'txn_date' => $data['txn_date'], 'reason_for_skip' => $data['reason_for_skip']] ;
        Mail::to(get_l3_email())->queue((new FlowCustomMail('skip_txn_id_notification', $mail_data))->onQueue('emails'));
    }


	

	public function validate_for_recon($loan_txn, $acc_stmt_obj, $txn_type){

        $acc_stmt_txn_id = $loan_txn['txn_id'];
        
        if($acc_stmt_obj){
            if($txn_type == 'credit' && $acc_stmt_obj->cr_amt != $loan_txn['amount']){
                thrw("Cannot capture. For txn ID {$acc_stmt_txn_id}, amount in account statement is {$acc_stmt_obj->cr_amt}");
            }
            if($txn_type == 'debit' && $acc_stmt_obj->dr_amt != $loan_txn['amount']){
                thrw("Cannot capture. For txn ID {$acc_stmt_txn_id}, amount in account statement is {$acc_stmt_obj->dr_amt}");
            }
            if(format_date($acc_stmt_obj->stmt_txn_date) != format_date($loan_txn['txn_date'])){
                thrw("Cannot capture. For txn ID {$acc_stmt_txn_id}, txn date in account statement is {$acc_stmt_obj->stmt_txn_date}");
            }
            if($txn_type == 'credit' && $acc_stmt_obj->loan_doc_id != $loan_txn['loan_doc_id'] && $loan_txn['mode'] == 'recon') {
                thrw("No valid A/C statement record found for {$acc_stmt_txn_id}");
            }
            return $acc_stmt_obj->id;
                
        }
        
    }

     private function get_loan_apprvd_n_disbursed_date($loan_doc_id){

        //$loan_appl_doc_id = "APPL-".$loan_doc_id;
        $loan_appl_repo = new LoanRepositorySQL();

        $date_n_time = $loan_appl_repo->get_record_by('loan_doc_id',$loan_doc_id,['loan_approved_date','disbursal_date']);

        $approved_date  = $date_n_time->loan_approved_date;
        $disbursed_date = $date_n_time->disbursal_date;

        return ["approved_date" => $approved_date,"disbursed_date" => $disbursed_date];
    }

     private function payment_validation(&$loan, &$loan_txn){

        $result = $this->get_loan_apprvd_n_disbursed_date($loan_txn["loan_doc_id"]);

        $disbursed_date = $result['disbursed_date'];

        if(parse_date($disbursed_date, Consts::DB_DATETIME_FORMAT) > parse_date($loan_txn['txn_date'])){
            thrw("Payment date can not be before disbursed date ({$disbursed_date}).");
        }
        if(!in_array($loan->status, Consts::DISBURSED_LOAN_STATUS)){
        #if($loan->status != Consts::LOAN_ONGOING && $loan->status != Consts::LOAN_DUE && $loan->status != Consts::LOAN_OVERDUE ){
                thrw("Float Advance is in '{$loan->status}' status. Can not make payment at this status");
        }

        # To be removed once excess payment warning is shown on UI
        /*if($loan_txn["amount"] > $loan->current_os_amount){
            thrw("Cannot capture payment for an amount higher than Current Outstanding Amount {$loan->current_os_amount}");
        }*/


    //    if($loan_txn["is_part_payment"] == false  && $loan->to_capture['new_status'] != Consts::LOAN_SETTLED){
    //          thrw("ERROR: Either you have a penalty outstanding or you're trying to make a part payment. please check.");  
    //     }  
        if($loan_txn["is_part_payment"] == false && $loan_txn['mode'] == 'capture'){
            if($loan->to_capture['principal'] != $loan->os_principal || $loan->to_capture['fee'] != $loan->os_fee){
                thrw("ERROR: You're trying to make a part payment. please check.");  
            }else if($loan->to_capture['penalty'] - $loan->to_capture['waived_penalty_paid'] != $loan->os_penalty){
                 thrw("ERROR: You have a penalty outstanding. please check.");  
            }
            // else if( $loan->to_capture['excess'] > 0){
            //    thrw("ERROR:You have a excess payment. please check ");
            // }
        }  
        $acc_stmt_id = null;



        if($loan->provisional_penalty > 0 ){  

            /*if(!array_key_exists('penalty_collected', $loan_txn) || $loan_txn['penalty_collected'] == null ){
                thrw("FA is overdue. Please collect penalty.");
            }*/
            $loan->penalty_days = getPenaltyDate($loan->due_date);
            $loan->provisional_penalty = $loan->provisional_penalty * $loan->penalty_days;



        }
        return $acc_stmt_id;
    }

    private function process_condonation($late_days, $cust_id){

        $condonation_overdue_days = config('app.auto_condonation_overdue_days');

        if($late_days > $condonation_overdue_days){

            $prob_period_repo = new ProbationPeriodRepositorySQL();
            $borrow_repo = new BorrowerRepositorySQL();
            $cust_prob = $prob_period_repo->get_record_by_many(['cust_id', 'status','type'],[$cust_id, 'active','condonation'], ['id']);
            if($cust_prob){
                $condonation_delay = config('app.condonation_punishment_delay');
                $start_date = Carbon::now()->addDays($condonation_delay)->startOfDay();  

                $prob_period_repo->update_model(['id' => $cust_prob->id,'start_date' => $start_date]);

                $borrow_repo->update_model_by_code(['cust_id' => $cust_id, 'perf_eff_date' => $start_date]);
            }else{

                $borr_serv = new BorrowerService();
                $borr_serv->allow_condonation($cust_id, false, true); 

            }

        }
    }

    private function late_settlement_handler($borrower, $late_days,$txn_date){
        $late_fields = array();
        //$this->process_condonation($late_days, $borrower->cust_id, $txn_date);

        $ld_field = "late_{$late_days}_day_loans";

        if($late_days > 3){
            $ld_field = 'late_3_day_plus_loans';
        }

        $late_fields[$ld_field] = $borrower->{$ld_field} + 1;
        $late_fields['late_loans'] = $borrower->late_loans + 1;
        return $late_fields;

    }

     private function get_paid_amounts($loan, $to_capture){

       return [
                'paid_principal' => $loan->paid_principal + $to_capture['principal'],
                'paid_fee' => $loan->paid_fee + $to_capture['fee'],
                'paid_excess' => $loan->paid_excess + $to_capture['excess'],
                'penalty_collected' => $loan->penalty_collected + $to_capture['penalty']
            ]; 

   }
    private function get_payment_status($loan){

        $payment_status = null;
        $review_reason = null;
        if(# Full payment w/ penalty
            $loan->to_capture['new_status'] == 'settled'
        ){
            $payment_status = 'settled';
        }
        else if(# Full payment w/ pending penalty or excess payment 
            (
                $loan->to_capture['principal']  == $loan->os_principal && 
                $loan->to_capture['fee'] == $loan->os_fee
            )
            &&
            (   
                $loan->to_capture['penalty']  < $loan->os_penalty ||
                $loan->to_capture['excess'] + $loan->paid_excess > 0
            )
        ){
            if($loan->to_capture['penalty']  < $loan->os_penalty ){
                $review_reason = 'penalty_pending';

            }
            else if($loan->to_capture['excess'] + $loan->paid_excess > 0 ){
                $review_reason = 'excess_paid';
            }
            $payment_status = 'review_pending';
        }

        else if(# Partial payment
            $loan->to_capture['principal'] <= $loan->os_principal && 
            $loan->to_capture['fee']  <= $loan->os_fee &&
            $loan->to_capture['penalty']  <= $loan->os_penalty &&
            $loan->to_capture['excess'] + $loan->paid_excess == 0
        ){
            $payment_status = 'partially_paid';
        }

        return ['payment_status' => $payment_status, 'review_reason' => $review_reason];



    }


    private function insert_penalty_txn_record($penalty_txn,$penalty_collected){
        $loan_txn_repo = new LoanTransactionRepositorySQL();
        $penalty_txn['txn_type'] = Consts::LOAN_PENALTY_PAYMENT;
        $penalty_txn['amount'] = $penalty_collected;
        $penalty_txn['country_code'] = session('country_code');
        $loan_txn_repo->create($penalty_txn, true, Consts::LOAN_PENALTY_PAYMENT, "{\"penalty_collected\": {$penalty_collected}}");

    }

    public function capture_repayment(array $loan_txn, $with_txn = true)
    {
        $result = ["sms_sent" => false];
        $loan_repo = new LoanRepositorySQL();
        // $loan_event_repo = new LoanEventRepositorySQL();
        $brwr_repo = new BorrowerRepositorySQL();
        $person_repo = new PersonRepositorySQL();
        $loan_txn_repo = new LoanTransactionRepositorySQL();
        $br_data = array();

        $loan = $this->get_loan_w_capture_fields($loan_txn['loan_doc_id'], $loan_txn['amount']);
        $borrower = $brwr_repo->find_by_code($loan->cust_id, ['ongoing_loan_doc_id', 'is_og_loan_overdue', 
        'category', 'late_1_day_loans', 'late_2_day_loans', 'late_3_day_loans', 'late_3_day_plus_loans',
        'late_loans','tot_loans', 'prob_fas', 'cond_count', 'perf_eff_date']);


        $due_date = parse_date($loan->due_date, Consts::DB_DATETIME_FORMAT);
        $paid_date = array_key_exists('paid_date', $loan_txn) ? parse_date($loan_txn['paid_date']) : Carbon::now();

        $due_date->endOfDay();
        $paid_date->endOfDay();
        $late_days = 0;
        

        

        try
        {
            $with_txn ? DB::beginTransaction() : null;

            $payment = $loan_txn['amount'];

            $loan_serv = new LoanService();
            # $loan passed by reference. Provisional penalty will be multiplied by no.of days in this func.

            $this->payment_validation($loan, $loan_txn);
            $loan_txn_repo = new LoanTransactionRepositorySQL();

            $category = $borrower->category;
            $SMS_TEMPLATE = null;
            $paid_by = 0;

            if(session('user_id')){
                $person = $person_repo->get_person_by_user_id(session('user_id'));
                if($person){
                    $paid_by = $person->id;
                }
            }

            $loan_txn['txn_exec_by'] = $paid_by;    

            // if(!array_key_exists('txn_type',$loan_txn) && $loan->to_capture['penalty'] == 0){
            //     $loan_txn['txn_type'] = "payment";
                
            // }
            // $loan_txn['country_code'] = session('country_code');
            // $loan_txn_id = $loan_txn_repo->create($loan_txn);

            $to_capture = $loan->to_capture;

            $loan_txn_id = null;

            if(!array_key_exists('txn_type',$loan_txn) && (($to_capture['principal'] + $to_capture['fee'] + $to_capture['penalty'] + $to_capture['excess']) > 0)){
                $loan_txn['country_code'] = session("country_code");
                if(isset($loan_txn['photo_payment_proof'] )){
                    mount_entity_file("loan_txns", $loan_txn, $loan_txn['txn_id'], 'photo_payment_proof');
                    $loan_txn['photo_transaction_proof']['photo_payment_proof'] = $loan_txn['photo_payment_proof'];
                    $loan_txn['photo_transaction_proof'] = json_encode($loan_txn['photo_transaction_proof']);
                }
                
                $loan_txn['txn_type'] = "payment";
                $loan_txn['country_code'] = session('country_code');
                $loan_txn['amount'] =  $to_capture['principal'] + $to_capture['fee'] + $to_capture['penalty'] + $to_capture['excess'];
                $loan_txn['principal'] = $to_capture['principal'];
                $loan_txn['fee'] = $to_capture['fee'];
                $loan_txn['penalty'] = $to_capture['penalty'];
                $loan_txn['excess'] = $to_capture['excess'];
                $loan_txn_id = $loan_txn_repo->create($loan_txn);
            }

            if($to_capture['waived_penalty_paid'] > 0){
                $this->reverse_waived_penalty($loan_txn['loan_doc_id'], $to_capture['waived_penalty_paid']);
            }
            
            $new_status = $to_capture['new_status'];

            // if($to_capture['penalty'] > 0){
            //     $this->insert_penalty_txn_record($loan_txn,$to_capture['penalty']);
            // }

            $ongoing_loan_doc_id = $borrower->ongoing_loan_doc_id;
            $is_og_loan_overdue = $borrower->is_og_loan_overdue;
            $loan_txn['penalty_collected'] = array_key_exists('penalty_collected', $loan_txn) ? $loan_txn['penalty_collected'] : 0;

            $new_loan_data = [
                'loan_doc_id' => $loan_txn["loan_doc_id"],
                'current_os_amount' => $loan->current_os_amount-$to_capture['principal']-$to_capture['fee'],
                'paid_amount' => $loan->paid_amount +  $loan_txn['amount'],
                'paid_by' => $paid_by
            ];

            $late_fields = array();

            $late_days = $paid_date->diffInDays($loan->due_date);
            $write_off_od_days = config('app.write_off_overdue_days');

            if($new_status == Consts::LOAN_SETTLED){
                $event_type = Consts::LOAN_SETTLED;
                $SMS_TEMPLATE = 'REPAYMENT_SETTLED_MSG';
                $br_data['last_loan_doc_id'] = $loan->loan_doc_id;
                if($ongoing_loan_doc_id == $loan_txn["loan_doc_id"]){
                    $ongoing_loan_doc_id = null;
                    if($is_og_loan_overdue){
                        $is_og_loan_overdue = false;
                    }
                }

                $repay_comm = array_key_exists('repay_comm', $loan_txn) ? $loan_txn['repay_comm'] : null;

                //$loan_serv->add_commission('repay', $loan->acc_prvdr_code, $loan_txn["loan_doc_id"],$loan_txn['txn_date'],$repay_comm);
                $borrower->cust_id = $loan->cust_id;
                $br_data['category'] = $loan_serv->get_cust_category($borrower);
                if($loan->status == Consts::LOAN_OVERDUE || $due_date < $paid_date){

                    // if($to_capture['tot_prov_penalty'] > 0 && $to_capture['penalty'] == 0){
                    //     $loan_event_repo->create_event($loan_txn["loan_doc_id"], Consts::PENALTY_WAIVER, "{\"provisional_penalty\": $loan->provisional_penalty}",null,$loan_txn["txn_date"]);            
                    // }

                    // $late_days = $paid_date->diffInDays($loan->due_date);
                    if($late_days > 0){

                        $late_fields = $this->late_settlement_handler($borrower, $late_days,$loan_txn['txn_date']);
                        $br_data += $late_fields;

                        $borr = $brwr_repo->get_record_by('cust_id', $loan->cust_id, ['prob_fas', 'cond_count', 'perf_eff_date', 'cust_id', 'tot_loans']);
                        $br_data['category'] = $loan_serv->get_cust_category($borr);
                    }

                }

            }
            else{
                $event_type = Consts::LOAN_EVENT_PART_PYMNT;
                $SMS_TEMPLATE = 'REPAYMENT_PENDING_MSG';

                $ongoing_loan_doc_id = $loan_txn["loan_doc_id"];
                if($loan->status == Consts::LOAN_OVERDUE || $due_date < $paid_date){
                    $is_og_loan_overdue = true;
                }
                $loan->new_os_amount = $new_loan_data['current_os_amount'];

            }

            $write_off_status = array("approved", "partially_recovered");
            if($loan->write_off_id && in_array($loan->write_off_status, $write_off_status) && $to_capture['principal'] > 0 ){
                $this->write_off_handler($loan_txn['loan_doc_id'], $to_capture['principal'], $loan_txn_id);
            }

            $paid_amounts = $this->get_paid_amounts($loan, $to_capture);
            $fund_repo = new CapitalFundRepositorySQL();
            $fund_repo->increment_by_code('os_amount', $loan->fund_code, -1 * $paid_amounts['paid_principal']);
            $fund_repo->increment_by_code('earned_fee', $loan->fund_code, $paid_amounts['paid_fee']);

            $new_loan_data += $paid_amounts;
            $payment_status = array_key_exists('payment_status', $loan_txn) ? $loan_txn['payment_status'] : $new_status;
            $review_reason = array_key_exists('review_reason', $loan_txn) ? $loan_txn['review_reason'] : null;

            $new_loan_data['status'] = $new_status;
            $new_loan_data['payment_status'] = $payment_status;
            $new_loan_data['review_reason'] = $review_reason;

            if(array_key_exists('is_part_payment', $loan_txn) && $loan_txn['is_part_payment'] == true  ){
                $new_loan_data['paid_date'] = null;
            }

            $new_loan_data['paid_date'] = $new_status == Consts::LOAN_SETTLED ? $loan_txn["txn_date"]: null;

           

            $loan_repo->update_model_by_code($new_loan_data);

            $br_data += ['cust_id' => $loan->cust_id, 
                        'ongoing_loan_doc_id' => $ongoing_loan_doc_id,
                        'is_og_loan_overdue'  => $is_og_loan_overdue,

                        ];

            $brwr_repo->update_model_by_code($br_data);

            if(isset($loan_txn['acc_stmt_id'])){
                $acc_stmt_repo = new AccountStmtRepositorySQL();
                $acc_stmt_repo->update_model(["recon_status" => "80_recon_done","id" =>$loan_txn['acc_stmt_id'], "loan_doc_id" => $loan_txn["loan_doc_id"]]);
            }

            // $loan_event_repo->create_event($loan_txn["loan_doc_id"],$event_type,null,null,$loan_txn["txn_date"]);

            $loan->mobile_num = config("app.customer_success")[$loan->acc_prvdr_code];
            $loan->sms_reply_to = config('app.sms_reply_to')[session('country_code')];
            $loan->payment =  (string) $payment;


           if($loan_txn['send_sms']){

                $sms_serv = new SMSNotificationService();
                $result["sms_sent"] = $sms_serv->notify_loan($loan, $SMS_TEMPLATE);

            }

            $with_txn ? DB::commit() : null;

            $person_id = $brwr_repo->find_by_code($loan->cust_id,['owner_person_id'])->owner_person_id;
            $cust_app_user = DB::selectOne("select messenger_token from app_users where person_id = {$person_id} and status = 'enabled'");

            if($cust_app_user && $cust_app_user->messenger_token){
                $serv = new FireBaseService();
                $data['notify_type'] = 'payment_confirmed';
                $data['loan'] = json_encode($loan_repo->find_by_code($loan_txn['loan_doc_id'], ['loan_doc_id','loan_principal','duration','flow_fee','paid_amount','acc_prvdr_code','acc_number','product_id']));
                $serv($data, $cust_app_user->messenger_token);
            }

            $result['loan_txn_id'] = $loan_txn_id;
            $result["status"] = "success";
        }
        catch (FlowCustomException $e) {
            $with_txn ? DB::rollback() : null;

            throw new FlowCustomException($e->getMessage());
        }
        catch (\Exception $e) {
            $with_txn ? DB::rollback() : null;
            Log::warning($e->getTraceAsString());
            if ($e instanceof QueryException){
                    throw $e;
                }else{
                thrw($e->getMessage());
                }
        }
        return $result;
    }

    private function get_req_data_for_capture($data){

       return [
                "mode"      => $data['mode'],
                "loan_doc_id" => $data['loan_doc_id'],
                "txn_date" => $data['txn_date'],
                "paid_date" => $data['txn_date'], 
                //'txn_exec_by' => '',
                'remarks' => array_key_exists('remarks' ,$data) ? $data['remarks'] : null,
                "txn_id" => $data['acc_stmt_txn_id'],
                "amount" => $data['amount'],
                "to_ac_id" => $data['to_ac_id'],
                "txn_mode" => "wallet_transfer",
                "send_sms" => true,
                "is_part_payment" => $data['is_part_payment'], # To come from UI
                "waive_penalty" => false,
                #'to_capture' => $this->get_amounts_to_capture()

            ];


    }

	private function get_req_data_for_recon($loan_doc_id, $stmt_txn){
		return [
 
				 "loan_doc_id" => $loan_doc_id,
				 "txn_date" => $stmt_txn->stmt_txn_date,
				 "paid_date" => $stmt_txn->stmt_txn_date, 
				 'txn_exec_by' => '',
				 'remarks' => $stmt_txn->descr,
				 "txn_id" => $stmt_txn->stmt_txn_id,
				 "amount" => $stmt_txn->cr_amt,
				 "to_ac_id" => $stmt_txn->account_id,
				 "txn_mode" => "review_n_sync",
				 "send_sms" => true,
				 "is_part_payment" => true, # TODO should we allow part payment?
				 "waive_penalty" => false,
 
			 ];
 
 
	 }
	private function capture_payment_from_acc_stmt_txn($loan_doc_id, $stmt_txn , $account_to_recon){
	   $loan_txn = [
        
                "loan_doc_id" => $loan_doc_id,
                "txn_date" => $stmt_txn->stmt_txn_date,
                "paid_date" => $stmt_txn->stmt_txn_date, 
                'txn_exec_by' => '',
                'remarks' => $stmt_txn->descr,
                "txn_id" => $stmt_txn->stmt_txn_id,
                "amount" => $stmt_txn->cr_amt,
                "to_ac_id" => $account_to_recon,
                "txn_mode" => "wallet_transfer",
                "send_sms" => true,
                "is_part_payment" => true, # TODO should we allow part payment?
                "waive_penalty" => true
            ];
 			
        $loan_serv = new LoanService();
        $loan_serv->capture_repayment($loan_txn,false);
	}

    private function write_off_handler($loan_doc_id, $recovery_amount, $loan_txn_id){
        Log::warning('loan_txn_id '.$loan_txn_id );
        $write_off_repo = new WriteOffRepositorySQL();
        $loan_repo = new LoanRepositorySQL();
        $loan_txn_repo = new LoanTransactionRepositorySQL;
        $write_off_loan = $write_off_repo->get_record_by('loan_doc_id', $loan_doc_id, ['id', 'write_off_status','recovery_amount','write_off_amount']);

        $write_off_status = array("approved", "partially_recovered");

        if($write_off_loan && in_array($write_off_loan->write_off_status, $write_off_status)){
            $recovery_amount = $recovery_amount + $write_off_loan->recovery_amount;
            $write_off_status = $write_off_loan->write_off_amount == $recovery_amount ? 'recovered': 'partially_recovered';

            $write_off_repo->update_model(['id' => $write_off_loan->id, 'recovery_amount' => $recovery_amount, 'write_off_status' => $write_off_status]);

            $loan_repo->update_model_by_code(['loan_doc_id' => $loan_doc_id, 'write_off_status' => $write_off_status]);

            $loan_txn_repo->update_model(['id' => $loan_txn_id, 'write_off_id' => $write_off_loan->id]);
        }
    }

    public function reverse_payment($data){

        $loan_txn_repo = new LoanTransactionRepositorySQL();
        // $loan_event_repo = new LoanEventRepositorySQL();
        $loan_repo = new LoanRepositorySQL();
        $fund_repo = new CapitalFundRepositorySQL();
        $borr_repo = new BorrowerRepositorySQL();
        $acc_stmt_repo =  new AccountStmtRepositorySQL();

        $loan_txn = $loan_txn_repo->find($data['loan_txn_id'], ['loan_doc_id', 'from_ac_id', 'to_ac_id', 'amount', 'txn_type', 'txn_id', 'txn_date']);


        $loan= $loan_repo->get_record_by('loan_doc_id', $loan_txn->loan_doc_id, ['fund_code', 'cust_id', 'current_os_amount', 'paid_amount', 'paid_principal', 'status', 'paid_fee', 'flow_fee', 'loan_principal', 'due_date', 'paid_date', 'duration', 'penalty_collected', 'provisional_penalty', 'due_amount', 'paid_excess', 'waiver_amount']);


        try{
            DB::beginTransaction();

            $today_date = Carbon::now();
            // DB::update("update loan_txns set reversed_date = ?, txn_type = ? where id = ? ",[$today_date, Consts::LOAN_PAYMENT_REVERSED, $data['loan_txn_id']]);
            DB::delete("delete from loan_txns where id = ?", [$data['loan_txn_id']]);

            $to_reverse = $this->get_amounts_to_reverse($loan, $loan_txn->amount);

            $data = ['loan_doc_id' => $loan_txn->loan_doc_id,
                    'paid_principal' => $loan->paid_principal - $to_reverse['principal'],
                    'paid_fee' => $loan->paid_fee - $to_reverse['fee'],
                    'penalty_collected' => $loan->penalty_collected - $to_reverse['penalty'],
                    'paid_excess' => $loan->paid_excess - $to_reverse['excess'],
                    'status' => $to_reverse['new_status'],
                    'paid_amount' => $loan->paid_amount - $to_reverse['principal'] - $to_reverse['fee'] - $to_reverse['penalty'] - $to_reverse['excess'],
                    'current_os_amount' => $to_reverse['current_os_amount'],
                    'paid_date' => null

                    ];

            $loan_repo->update_model_by_code($data);

            $ongoing_loan_doc_id = $loan_txn->loan_doc_id;
            $is_og_loan_overdue = $to_reverse['new_status'] == Consts::LOAN_OVERDUE ? 1 : 0;
            $borr_repo->update_model_by_code(['cust_id' => $loan->cust_id, 
                                            'ongoing_loan_doc_id' => $ongoing_loan_doc_id,
                                            'is_og_loan_overdue' => $is_og_loan_overdue]);

            $fund_repo->increment_by_code('os_amount', $loan->fund_code, $to_reverse['principal']);
            $fund_repo->increment_by_code('earned_fee', $loan->fund_code, -1 * $to_reverse['fee']);

            $acc_stmt_repo->update_model_by_code(['stmt_txn_id' => $loan_txn->txn_id, 'loan_doc_id' => null, 'recon_status' => '90_payment_reversed', 'recon_desc' => null, 'cust_id' => null]);

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            if ($e instanceof QueryException){
                throw $e;
            }else{
            thrw($e->getMessage());
            }
        }
        return ['message' => 'success'];

    }

    private function reverse($txn_amount, $paid_amount){

        if($paid_amount >0){

            if($txn_amount >= $paid_amount){   
                return $paid_amount;
            }
            else {
                return $txn_amount;
            }
        }
        return 0;

    }

    private function get_amounts_to_reverse($loan, $txn_amount){

        $principal = $fee = $penalty = $excess = 0;

        $penalty_days = getPenaltyDate($loan->due_date);
        $tot_prov_penalty = $loan->provisional_penalty * $penalty_days;

        $excess = $this->reverse($txn_amount, $loan->paid_excess);
        $remaining = $txn_amount - $excess;

        $penalty = $this->reverse($remaining, $loan->penalty_collected + $loan->waiver_amount);
        $remaining = $remaining - $penalty;

        $fee = $this->reverse($remaining, $loan->paid_fee);
        $remaining = $remaining - $fee;

        $principal = $this->reverse($remaining, $loan->paid_principal);
        $remaining = $remaining - $principal;

        if($remaining > 0){
            thrw("Unable to reverse an amount higher than paid amount");
        }


        $current_os_amount = $loan->current_os_amount + $principal + $fee;

        $loan->os_principal = $loan->loan_principal - $loan->paid_principal + $principal;

        $loan->os_fee = $loan->flow_fee - $loan->paid_fee + $fee;    

        $loan->os_penalty = $tot_prov_penalty
                                - $loan->waiver_amount 
                                - $loan->penalty_collected - $penalty;

        if($loan->os_principal > 0 || $loan->os_fee > 0 || $loan->os_penalty > 0){

            $current_date = Carbon::now()->format('Y-m-d');

            if($current_date == $loan->due_date){
                $status = Consts::LOAN_DUE;
            }
            else {
                $status = $current_date > $loan->due_date ? Consts::LOAN_OVERDUE : Consts::LOAN_ONGOING;
            }
        }
        else{
            $status = $loan->status;
        }

        return [
            'principal' => $principal,
            'fee' => $fee,
            'penalty' => $penalty,
            'excess' => $excess,
            'penalty_days' => $penalty_days,
            'tot_prov_penalty' => $tot_prov_penalty,
            'new_status' => $status,
            'current_os_amount' => $current_os_amount
        ]; 
    }
}
