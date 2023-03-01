<?php

namespace App\Scripts\php;

use Illuminate\Http\Request;
use DB;
use Excel;
use Illuminate\Support\Facades\Log;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;

class UpdateRegRMScript{


    public function update(){

        $borr_repo = new BorrowerRepositorySQL();
        $loan_repo = new LoanRepositorySQL();

        $borrowers = $borr_repo->get_records_by('country_code', session('country_code'), ['id', 'cust_id', 'first_loan_date', 'flow_rel_mgr_id']);
        
        foreach($borrowers as $borrower){

            $reg_flow_rel_mgr_id = 0;
            if($borrower->first_loan_date == null){
                $reg_flow_rel_mgr_id = $borrower->flow_rel_mgr_id;
            }
            else{
                $loans = $loan_repo->get_records_by_many(['cust_id', 'date(disbursal_date)'], [$borrower->cust_id, $borrower->first_loan_date], ['flow_rel_mgr_id'], 'and', 'limit 1');
                
                $reg_flow_rel_mgr_id = $loans[0]->flow_rel_mgr_id;
                
            }
            $borr_repo->update_model(['id' => $borrower->id,
                                    'reg_flow_rel_mgr_id' => $reg_flow_rel_mgr_id]);
        }

        
    }
}

