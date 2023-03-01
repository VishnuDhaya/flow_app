<?php

namespace App\Scripts\php;

use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Services\LoanService;
use App\Repositories\SQL\TmpLoanRepositorySQL;
use Carbon\Carbon;
use DB;
use Log;


class UpdatePaidFieldsScript{

	public function update_prov_penalty(){
		$loan_prod_repo = new LoanProductRepositorySQL;
		$loan_repo = new LoanRepositorySQL;

		$loans = DB::select("select product_id,id,loan_principal,flow_fee,duration,loan_doc_id from loans where penalty_collected > 0 and provisional_penalty = 0");

		foreach($loans as $loan){		
			$loan_prod = $loan_prod_repo->find($loan->product_id,['penalty_amount']);
			 if($loan_prod == null){
				$loan_prods = $loan_prod_repo->get_records_by_many(['max_loan_amount','flow_fee','duration'],[$loan->loan_principal, $loan->flow_fee, $loan->duration],['penalty_amount']);
			 	if(sizeof($loan_prods) > 0){
			 		$loan_prod = $loan_prods[0];
			 	}
			 }	

			 if($loan_prod){
				$loan_repo->update_model(['provisional_penalty' =>$loan_prod->penalty_amount , 'id' => $loan->id]);
			 }else{
			 	Log::warning("No matching product for {$loan->loan_doc_id}");
			 }	
	
		}

		 $loans = DB::select("select product_id,id from loans where date(paid_date) <= date(due_date) and provisional_penalty > 0");
		foreach($loans as $loan){		
			$loan_repo->update_model(['provisional_penalty' => 0 , 'id' => $loan->id]);
		}
	}
	public function update_paid_fields(){

		$loans = DB::select("select datediff(paid_date,due_date) as penalty_days, paid_date,due_date,status,id,product_id,due_date,loan_doc_id,paid_amount,loan_principal,provisional_penalty,flow_fee,penalty_collected,penalty_waived from loans where paid_amount > 0");
		foreach ($loans as $loan) {
			$this->get_amounts_to_capture($loan);
		}		 
	}

	private function capture($amount, $paid_already, $tot_to_pay){
			
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


	public function get_amounts_to_capture(&$loan){
		$loan_repo = new LoanRepositorySQL;
		$loan->penalty_collected = (int) $loan->penalty_collected;
		$provisional_penalty = (int) $loan->provisional_penalty;

		$loan->paid_amount = $loan->penalty_collected + $loan->paid_amount;
   		$principal = $fee = $penalty = $excess = $penalty_days = 0;
       	$loan->paid_principal = $this->capture($loan->paid_amount, 0, $loan->loan_principal);
        $remaining =$loan->paid_amount - $loan->paid_principal;

        $loan->paid_fee = $this->capture($remaining, 0, $loan->flow_fee);
        $remaining = $remaining - $loan->paid_fee;

        if($loan->status == 'settled' && $loan->due_date < $loan->paid_date  ){
        	$penalty_days = $loan->penalty_days;
        }else if($loan->paid_date == null){
        	$penalty_days = getPenaltyDate($loan->due_date);
        }

        $loan->tot_prov_penalty = $provisional_penalty * $penalty_days;
        
        $loan->penalty_collected = $this->capture($remaining, 0, $loan->tot_prov_penalty);

        $loan->penalty_waived = $loan->status == 'settled' ? $loan->tot_prov_penalty - $loan->penalty_collected : 0 ;
        
        $loan->paid_excess = $remaining - $loan->penalty_collected;
        #$tmp_repo = new TmpLoanRepositorySQL;
        #$tmp_repo->insert_model((array)$loan);

        $loan_repo->update_model(['paid_principal' => $loan->paid_principal,'paid_excess' => $loan->paid_excess,'paid_fee' => $loan->paid_fee,'penalty_collected' => $loan->penalty_collected, 'id' => $loan->id]);

       
       	return [
                'principal' => $loan->paid_principal,
                'fee' => $loan->paid_fee,
                'penalty' =>  $loan->penalty_collected,
                'excess' => $loan->paid_excess,
                'loan_doc_id' =>$loan->loan_doc_id

            ]; 
	         
	   }

}