<?php

namespace App\Services;

use App\Services\AccountService;
use App\Consts;
use Illuminate\Support\Facades\DB;
use Log;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;

class ImportLoanService {

	public function __construct($lender_code, $data_prvdr_code){
		
		$this->flow_rel_mgr = $this->get_flow_rel_mgr();

		$acc = DB::selectOne('select id from accounts where lender_code = ? and acc_purpose = ? ', [$lender_code, "disbursement"]);
	    $comm_acc = DB::selectOne('select id from accounts where data_prvdr_code = ? and acc_purpose = ?',[$data_prvdr_code, "commission"]);
	    //Log::warning($acc);
	    //Log::warning($acc->id);

	    $this->data = ["country_code" => session('country_code'),"acc_id" => $acc->id];

	    $this->comm_data = ["country_code" => session('country_code'), "acc_id" => $comm_acc->id];

	}
    public function get_flow_rel_mgr(){
		$person_repo = new PersonRepositorySQL();
		$result = $person_repo->get_records_by('associated_with', 'FLOW', ['id', 'first_name', 'middle_name', 'last_name']);
    	
    	$rel_mgr_arr = array();
		foreach($result as $person){
			$rel_mgr_arr[$person->id] = full_name($person);
		}
		return $rel_mgr_arr;
    }

	public function insert_commission_txn($loan, $reversal = false){
	 	$data = $this->comm_data;
		
		$data["txn_mode"] = "flow_platform";
		$data["txn_date"] = $loan->paid_date;
		$data["acc_txn_category"] = 'credit';
		if($reversal){
			$data['amount'] = -500;
			$data["acc_txn_type"] = "repay_comm_reversal";
		}else{
			$data['amount'] = 500;
			$data["acc_txn_type"] = "repay_comm";
		}
			
		$data["txn_exec_by"] = "seed";

		$data["created_at"] = $data["txn_date"];
		$data['txn_id'] = $loan->loan_doc_id;

		$result = $this->get_loan_txn($data["acc_txn_type"],$loan->loan_doc_id);
		if($result){
			
			$data['created_by'] = $result->created_by;
		}	

		(new AccountService())->create_acc_txn($data);

    }

	public function insert_cust_commission_txn($borrower, $reversal = false){
	 	$data = $this->comm_data;

		$data["acc_txn_type"] = "new_cust_comm";
		$data["txn_mode"] = "flow_platform";
		$data["txn_date"] = $borrower->first_loan_date;
		$data["acc_txn_category"] = 'credit';
		
		if($reversal){
			$data['amount'] = -25000;
			$data["acc_txn_type"] = "new_cust_comm_reversal";
		}else{
			$data['amount'] = 25000;
			$data["acc_txn_type"] = "new_cust_comm";
		}

		$data["txn_exec_by"] = "seed";
		$data["created_at"] = $data["txn_date"];
		$data['txn_id'] = $borrower->cust_id;
		$data['created_by'] = 0;

		(new AccountService())->create_acc_txn($data);

    }

    public function insert_disbursal_txn($loan, $reversal = false){
    	$data = $this->data;
		$data['ref_acc_id'] = $loan->cust_acc_id;

		$data["acc_txn_type"] = "disbursal";
		$data["txn_mode"] = "data_provider_portal";
		$data["txn_date"] = $loan->disbursal_date;
		$data["acc_txn_category"] = 'debit';
		if($reversal){
			$data['amount'] = -1 * $loan->loan_principal;
			$data["acc_txn_type"] = "disbursal_reversal";
		}else{
			$data['amount'] = $loan->loan_principal;
			$data["acc_txn_type"] = "disbursal";
		}
		
		$data["txn_exec_by"] = $this->flow_rel_mgr[$loan->flow_rel_mgr_id];
		
		$data["created_at"] = $data["txn_date"];
		$data['txn_id'] = $loan->loan_doc_id;

		$result = $this->get_loan_txn($data["acc_txn_type"],$loan->loan_doc_id);
	
		
		if($result){
			
			$data['created_by'] = $result->created_by;
		}
		// (new AccountService())->create_acc_txn($data, true);
    }

	 public function insert_repayment_txn($loan, $reversal = false){
	 	$data = $this->data;

		$data["acc_txn_type"] = "payment";
		$data["txn_mode"] = "data_provider_transfer";
		$data["txn_date"] = $loan->paid_date;
		$data["acc_txn_category"] = 'credit';
		if($reversal){
			$data['amount'] = -1 * $loan->paid_amount;
			$data["acc_txn_type"] = "payment_reversal";
		}else{
			$data['amount'] = $loan->paid_amount;
			$data["acc_txn_type"] = "payment";
		}

		
		$data["txn_exec_by"] = "Customer";

		$data["created_at"] = $data["txn_date"];
		$data['txn_id'] = $loan->loan_doc_id;

		$result = $this->get_loan_txn($data["acc_txn_type"],$loan->loan_doc_id);
		if($result){
			
			$data['created_by'] = $result->created_by;
		}	

		// (new AccountService())->create_acc_txn($data);

    }



    public function get_loan_txn($acc_txn_type,$loan_doc_id){

    	$result = DB::selectOne("select txn_id,created_by from loan_txns where loan_doc_id= ? and txn_type= ?",[$loan_doc_id,$acc_txn_type]);

    	return $result;

	}


	public function reverse_loans(array $loan_ids){
			 
		$loans = (new LoanRepositorySQL())->get_records_by('loan_doc_id', $loan_ids, ['loan_doc_id','id','paid_date','paid_amount','disbursal_date','loan_principal','cust_acc_id','flow_rel_mgr_id','status', 'loan_appl_id']);

		foreach ($loans as $loan) {
			if($loan->status =='settled'){
				$reversal = true;
				$this->insert_disbursal_txn($loan,$reversal);
	        	$this->insert_repayment_txn($loan, $reversal);
	        	$this->insert_commission_txn($loan, $reversal);
	        }
			else if(in_array($loan->status, Consts::DISBURSED_LOAN_STATUS)){
				$this->insert_disbursal_txn($loan,$reversal);
			}

			DB::table('loan_applications')->where('id',$loan->loan_appl_id)->delete();
			DB::table('loans')->where('loan_doc_id',$loan->loan_doc_id)->delete();
			DB::table('loan_txns')->where('loan_doc_id',$loan->loan_doc_id)->delete();
			DB::table('loan_events')->where('loan_doc_id',$loan->loan_doc_id)->delete();
		}
	}
}

