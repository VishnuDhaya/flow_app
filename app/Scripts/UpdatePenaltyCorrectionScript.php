<?php

namespace App\Scripts\php;

use App\Models\LoanTransaction;
use App\Repositories\SQL\LoanTransactionRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Services\LoanService;
use App\Repositories\SQL\TmpLoanRepositorySQL;
use Carbon\Carbon;
use DB;
use Log;


class UpdatePenaltyCorrectionScript{
	public function update(){
        $loan_txn_repo = new LoanTransactionRepositorySQL;

        $loan_txns = DB::select("select t.id as id,txn_date,amount, amount - (l.loan_principal + l.flow_fee) excess from loans l, loan_txns t where l.loan_doc_id in ('UEZM 04249','UEZM 04395','UEZM 04396','UEZM 04397','UEZM 04418','UEZM 04449','UEZM 04448','UEZM 04465','UEZM 04499','UEZM 04498','UEZM 04500','UEZM 04237','UEZM 04250','UEZM 04252','UEZM 04256','UEZM 04257','UEZM 04258','UEZM 04259','UEZM 04260','UEZM 04261','UEZM 04262','UEZM 04263','UEZM 04264','UEZM 04290','UEZM 04289','UEZM 04288','UEZM 04292','UEZM 04294','UEZM 04293','UEZM 04313','UEZM 04311','UEZM 04310','UEZM 04309','UEZM 04308','UEZM 04328','UEZM 04325','UEZM 04326','UEZM 04327','UEZM 04329','UEZM 04336','UEZM 04335','UEZM 04334','UEZM 04333','UEZM 04332','UEZM 04331','UEZM 04330','UEZM 04337','UEZM 04341','UEZM 04340','UEZM 04339','UEZM 04338','UEZM 04366','UEZM 04361','UEZM 04365','UEZM 04363','UEZM 04362','UEZM 04368','UEZM 04381','UEZM 04383','UEZM 04384','UEZM 04382','UEZM 04547','UEZM 04612')  and l.loan_doc_id = t.loan_doc_id and txn_type = 'payment'");
        foreach($loan_txns as $loan_txn){
            DB::update("update loan_txns set amount = amount - $loan_txn->excess where id = $loan_txn->id");
            $loan_txn_rec['amount'] =  $loan_txn->excess;
            $loan_txn_rec['txn_date'] = $loan_txn->txn_date;
            $loan_txn_rec['txn_type'] = 'penalty_payment';
            $loan_txn_repo->insert_model($loan_txn_rec);
        }
    }
		

}