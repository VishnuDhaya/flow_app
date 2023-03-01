<?php

namespace App\Scripts\php;

use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Repositories\SQL\LoanTransactionRepositorySQL;
use App\Services\LoanService;
use App\Repositories\SQL\TmpLoanRepositorySQL;
use App\Services\RepaymentService;
use Carbon\Carbon;
use DB;
use Log;


class UpdatePaidFieldsScriptV2{

	
	public function update_paid_fields(){
        $loan_repo = new LoanRepositorySQL;
        $loan_txn_repo = new LoanTransactionRepositorySQL;
        DB::update ("update loans set paid_principal = 0, paid_fee = 0, penalty_collected = 0, paid_excess = 0, penalty_waived = 0");

		$loans = DB::select("select product_id,datediff(paid_date,due_date) as penalty_days,id,provisional_penalty,paid_amount,paid_date,paid_principal,loan_principal,flow_fee,paid_fee,penalty_waived,penalty_collected,loan_doc_id,due_date,status,paid_excess from loans");

        foreach ($loans as $loan) {
            $loan_products = DB::select("select penalty_amount from loan_products where id = {$loan->product_id}");
            if(sizeof($loan_products) > 0){
                $loan->provisional_penalty = $loan_products[0]->penalty_amount;
            }else{
                $loan->provisional_penalty = 0;
            }
            $loan_txns = DB::select("select sum(amount) as amount from loan_txns where txn_type  in ('payment', 'penalty_payment') and loan_doc_id = '{$loan->loan_doc_id}'");
			if(sizeof($loan_txns) > 0){
                $paid_fields = $this->get_amounts_to_capture($loan,$loan_txns[0]->amount);

            }else{
                Log::warning("NO PAYMENT | {$loan->status} | {$loan->loan_doc_id}");
            }
		}		 
	}

	private function capture($amount, $paid_already, $tot_to_pay){
			Log::warning($amount);
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

		$principal = $fee = $penalty = $excess = $penalty_waiver = 0;

	    $principal = $this->capture($txn_amount, $loan->paid_principal, $loan->loan_principal);
	    $remaining = $txn_amount - $principal;

	    $fee = $this->capture($remaining, $loan->paid_fee, $loan->flow_fee);
	    $remaining = $remaining - $fee;

        if($loan->status == 'settled' && $loan->paid_date > $loan->due_date){
            $paid_date = Carbon::parse($loan->paid_date);
            $penalty_days = getPenaltyDate($loan->due_date, $paid_date);

            $loan->penalty_collected = (int) $loan->penalty_collected;
            $provisional_penalty = (int) $loan->provisional_penalty;

            $tot_prov_penalty = $provisional_penalty * $penalty_days;
            
            
            
            if($remaining >= $tot_prov_penalty ){
                $penalty = $tot_prov_penalty;
                $excess = $remaining - $tot_prov_penalty;
                $penalty_waiver  = 0;
            }else{
                $penalty = $remaining;
                $excess = 0;
                $penalty_waiver = $tot_prov_penalty - $penalty;
                if($penalty_waiver > 0){
                    $loan_txn_repo = new LoanTransactionRepositorySQL;
                    $waiver_data['loan_doc_id'] = $loan->loan_doc_id;
                    $waiver_data['amount'] = $penalty_waiver; 
                    $waiver_data['txn_date'] = $loan->paid_date;
		            $waiver_data['txn_type'] = "waiver";
                    $loan_txn_repo->create($waiver_data);
                }
            }
                       

        }
        $loan_repo = new LoanRepositorySQL;

        $loan_repo->update_model(['paid_principal' => $principal, 'penalty_waived' => $penalty_waiver, 'penalty_collected' => $penalty, 'paid_excess' => $excess, 'paid_fee' => $fee, 'id' => $loan->id]);

        
        return [
                'principal' => $principal,
                'fee' => $fee,
                'penalty' => $penalty,
                'excess' => $excess,
            ]; 

}



}