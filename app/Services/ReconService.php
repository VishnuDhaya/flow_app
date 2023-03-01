<?php

namespace App\Services;
use App\Repositories\SQL\AccountStmtRepositorySQL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;




class ReconService{
	public function unlink_payment($data){
		
		$acc_stmt_id = $data['acc_stmt_id'];
		$loan_doc_id = $data['loan_doc_id'];
		if($acc_stmt_id){
			DB::update("update account_stmts set recon_status = Null, loan_doc_id = Null,recon_desc =Null where id = ?" , [$acc_stmt_id]);
		}
		else{
			thrw("Unable to unlink payment for {$loan_doc_id}");
		}
		
	}

}