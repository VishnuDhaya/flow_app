<?php

namespace App\Scripts\php;
use DB;
Use \Carbon\Carbon;
use Log;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\LoanApplicationRepositorySQL;



class UpdateAccPrvdrCodeScript{

	public function updateAccPrvdrCode(){
		session()->put('country_code','UGA');

		// $borr_repo = new BorrowerRepositorySQL();
        // $accounts = DB::select("select cust_id, acc_prvdr_code from accounts where country_code = ? ", [session('country_code')]);
        // Log::warning($accounts);

		// foreach($accounts as $account){
        //     $borr_repo->update_model_by_code(['acc_prvdr_code' => $account->acc_prvdr_code, 'cust_id' => $account->cust_id]);
        // }
		
		$loan_repo = new LoanRepositorySQL();
		$loan_appl_repo = new LoanApplicationRepositorySQL();
		$acc_repo = new AccountRepositorySQL();
		$loans = $loan_repo->get_records_by('country_code', session('country_code'), ['loan_doc_id', 'cust_acc_id']);

		foreach($loans as $loan){
			// $account = $acc_repo->get_record_by('id', $loan->cust_acc_id, ['acc_prvdr_code', 'acc_number']);
			$account = DB::select("select acc_prvdr_code, acc_number from accounts where id = ? and id != 2628 ", [$loan->cust_acc_id]);
			if($account){
				$loan_repo->update_model(['loan_doc_id' => $loan->loan_doc_id, 'acc_prvdr_code' => $account[0]->acc_prvdr_code, 'acc_number' => $account[0]->acc_number], 'loan_doc_id');

				$loan_appl_repo->update_model(['loan_doc_id' => $loan->loan_doc_id, 'acc_prvdr_code' => $account[0]->acc_prvdr_code, 'acc_number' => $account[0]->acc_number], 'loan_doc_id');
			}
			
		}
	}


	public function updateAccNumColumn(){
		session()->put('country_code','UGA');

		$borr_repo = new BorrowerRepositorySQL();
        $borrowers = $borr_repo->get_records_by('country_code', session('country_code'), ['cust_id']);

		foreach($borrowers as $borrower){
			$account = DB::select("select id, acc_number from accounts where cust_id = ? and status = 'enabled' and is_primary_acc = 1 and acc_number not in ('password', '@rn01d#5')", [$borrower->cust_id]);

			if($account){
				$borr_repo->update_model_by_code(['cust_acc_id' => $account[0]->id, 'acc_number' => $account[0]->acc_number, 'cust_id' => $borrower->cust_id]);
			}
			else{
				Log::warning("Customer don't have enabled account  : $borrower->cust_id");
			}
            
        }
	}

}