<?php

namespace App\Scripts\php;
use DB;
Use \Carbon\Carbon;
use Log;
use App\Repositories\SQL\BorrowerRepositorySQL;



class RemoveDuplIAccIDScript{

	public function removeDupliAccID(){
		session()->put('country_code','UGA');
		// DB::select("SET sql_mode = (SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");
		// $accounts = DB::select("select cust_id, id from accounts where cust_id is not null and status = 'enabled'  group by cust_id having count(1) > 1 ");

		// foreach($accounts as $account){
		// 	$dupli_accs = DB::select("select id from accounts where cust_id = ? and id != ?", [$account->cust_id, $account->id]);

        //     foreach($dupli_accs as $dupli_acc){
		// 		$loan_txns = DB::select("select loan_doc_id from loan_txns where to_ac_id = ?", [$dupli_acc->id]);
                
		// 		if(sizeof($loan_txns) > 0){
		// 			DB::update("update loan_txns set to_ac_id= ? where to_ac_id = ? ", [$account->id, $dupli_acc->id]);
		// 		}
        //         // DB::delete("delete from accounts where id = ?", [$dupli_acc->id]); 
        //     }
		// }
		try{
			DB::beginTransaction();
			$borr_repo = new BorrowerRepositorySQL();
			
			$borrowers = $borr_repo->get_records_by('country_code', session('country_code'), ['cust_id', 'data_prvdr_cust_id']);
			
			foreach($borrowers as $borrower){
				DB::update("update accounts set is_primary_acc = 0, status='disabled' where cust_id = ?", [$borrower->cust_id]); 

				$loans = DB::select("select distinct cust_id, cust_acc_id, data_prvdr_cust_id from loans where cust_id = ? ", [$borrower->cust_id]);

				$is_pri_acc_updated =0;
				$pri_acc_id = 0;
				foreach($loans as $loan){
					if($borrower->data_prvdr_cust_id == $loan->data_prvdr_cust_id && $loan->cust_acc_id){

						if($is_pri_acc_updated == 1){
							$loan_txns = DB::select("select loan_doc_id from loan_txns where to_ac_id = ?", [$loan->cust_acc_id]);
					
							if(sizeof($loan_txns) > 0){
								DB::update("update loan_txns set to_ac_id= ? where to_ac_id = ? ", [$pri_acc_id, $loan->cust_acc_id]);
							}
							DB::delete("delete from accounts where id = ?", [$loan->cust_acc_id]);
							break;
						}else{
							DB::update("update accounts set is_primary_acc = 1, status='enabled' where id = ?", [$loan->cust_acc_id]);
							$is_pri_acc_updated = 1;
							$pri_acc_id = $loan->cust_acc_id;
						}
					}
					else{

					}
				}	

			}
			DB::commit();
		}
		catch(\Exception $e){
			DB::rollback();
			if ($e instanceof QueryException){
				throw $e;
			}else{
			thrw($e->getMessage());
			}
		}

	}

}