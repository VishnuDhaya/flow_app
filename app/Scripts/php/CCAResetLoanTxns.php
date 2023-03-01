<?php

namespace App\Scripts\php;

use App\Repositories\SQL\LoanTransactionRepositorySQL;
use App\Repositories\SQL\AccountStmtRepositorySQL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class CCAResetLoanTxns{

    public function run(){

        try{
    
            DB::beginTransaction();

            session()->put('country_code', 'UGA');
            session()->put('user_id',0);    
    
            $loan_txns_repo = new LoanTransactionRepositorySQL();
            $acc_stmt_repo  = new AccountStmtRepositorySQL();
    
            $this->breakup_disbursal_record($loan_txns_repo, $acc_stmt_repo);
            $this->breakup_payment_record($loan_txns_repo, $acc_stmt_repo);
            DB::commit();
    
        }
        catch(\Exception $e){
            DB::rollBack();
            thrw($e);
    
        }
    
    }

    public function breakup_payment_record($loan_txns_repo, $acc_stmt_repo, $account_id = '1783'){
        
        Log::warning("----CCA PAYMENT BREAKUP SCRIPT STARTED---");

        if($account_id == '1783'){
            $this->split_txn_id_n_capture($loan_txns_repo, $acc_stmt_repo);
        }
        
        $addl_sql = "and EXTRACT(YEAR_MONTH from txn_date) >= '202201' and EXTRACT(YEAR_MONTH from txn_date) <= '202210' and (txn_id regexp ',|/')";
        $loan_txns = $loan_txns_repo->get_records_by_many(['to_ac_id', 'txn_type'], [$account_id, 'payment'], ['id', 'txn_id', 'amount', 'recon_amount', 'loan_doc_id'], "and", $addl_sql);

        foreach($loan_txns as $loan_txn){

            if(Str::contains($loan_txn->txn_id, ',')){
                $txn_ids = explode(',', $loan_txn->txn_id);
            }elseif(Str::contains($loan_txn->txn_id, '/')){
                $txn_ids = explode('/', $loan_txn->txn_id);
            }else{
                continue;
            }
            
            $this->iterate_each_txn_n_update($loan_txn, $txn_ids, $acc_stmt_repo, $loan_txns_repo);
        }

        Log::warning("----CCA PAYMENT BREAKUP SCRIPT ENDED---");

    }

    private function split_txn_id_n_capture($loan_txns_repo, $acc_stmt_repo){

        $addl_sql = "and EXTRACT(YEAR_MONTH from txn_date) >= '202201' and EXTRACT(YEAR_MONTH from txn_date) <= '202210' and LENGTH(txn_id) = 16";
        $loan_txns = $loan_txns_repo->get_records_by_many(['to_ac_id', 'txn_type'], ['1783', 'payment'], ['id', 'txn_id', 'amount', 'recon_amount', 'loan_doc_id'], "and", $addl_sql);

        foreach($loan_txns as $loan_txn){

            $txn_ids = str_split($loan_txn->txn_id, 8);

            $this->iterate_each_txn_n_update($loan_txn, $txn_ids, $acc_stmt_repo, $loan_txns_repo);

        }
    }

    private function breakup_disbursal_record($loan_txns_repo, $acc_stmt_repo){

        Log::warning("----CCA DISBURSAL BREAKUP SCRIPT STARTED---");

        $addl_sql = "and EXTRACT(YEAR_MONTH from txn_date) >= '202201' and EXTRACT(YEAR_MONTH from txn_date) <= '202210' and (txn_id regexp ',')";
        $loan_txns = $loan_txns_repo->get_records_by_many(['from_ac_id', 'txn_type'], ['1783', 'disbursal'], ['id', 'txn_id', 'amount', 'recon_amount', 'loan_doc_id'], "and", $addl_sql);

        foreach($loan_txns as $loan_txn){

            $txn_ids = explode(',', $loan_txn->txn_id);

            foreach($txn_ids as $key => $txn_id){

                $acc_stmt = $acc_stmt_repo->get_record_by_many(['stmt_txn_id', 'recon_status'], [$txn_id, '80_recon_done'], ['account_id', 'stmt_txn_type', 'dr_amt', 'amount', 'loan_doc_id', 'stmt_txn_date', 'stmt_txn_id', 'country_code']);

                if($acc_stmt){

                    if($key == 0){

                        $data = ['txn_id' => $acc_stmt->stmt_txn_id, 'recon_amount' => 0, 'amount' => $acc_stmt->dr_amt, 'id' => $loan_txn->id];
                        $loan_txns_repo->update_model($data);

                    }elseif($key == 1){

                        $new_loan_txns_data = ['country_code' => $acc_stmt->country_code,
                        'loan_doc_id' => $acc_stmt->loan_doc_id,
                        'from_ac_id' => $acc_stmt->account_id,
                        'amount' => $acc_stmt->dr_amt,
                        'txn_type' => 'disbursal',
                        'txn_id' => $acc_stmt->stmt_txn_id,
                        'txn_mode' => 'instant_disbursal',
                        'txn_date' => $acc_stmt->stmt_txn_date];

                        $loan_txns_repo->insert_model($new_loan_txns_data);
                    }   

                    $acc_stmt_repo->update_model(['loan_doc_id' => null, 'recon_status' => null, 'id' => $acc_stmt->id]);
                }
            }
        }

        Log::warning("----CCA DISBURSAL BREAKUP SCRIPT ENDED---");
    }


    private function iterate_each_txn_n_update($loan_txn, $txn_ids, $acc_stmt_repo, $loan_txns_repo){

        foreach($txn_ids as $key => $txn_id){
  
            $acc_stmt = $acc_stmt_repo->get_record_by_many(['stmt_txn_id', 'recon_status'], [$txn_id, '80_recon_done'], ['account_id', 'stmt_txn_type', 'cr_amt', 'amount', 'loan_doc_id', 'stmt_txn_date', 'stmt_txn_id', 'country_code']);

            if($acc_stmt){

                if($key == 0){

                    $data = ['txn_id' => $acc_stmt->stmt_txn_id, 'recon_amount' => 0, 'amount' => $acc_stmt->cr_amt, 'to_ac_id' => $acc_stmt->account_id, 'id' => $loan_txn->id];
                    $loan_txns_repo->update_model($data);

                }elseif($key == 1){

                    $new_loan_txns_data = ['country_code' => $acc_stmt->country_code,
                    'loan_doc_id' => $acc_stmt->loan_doc_id,
                    'to_ac_id' => $acc_stmt->account_id,
                    'amount' => $acc_stmt->cr_amt,
                    'txn_type' => 'payment',
                    'txn_id' => $acc_stmt->stmt_txn_id,
                    'txn_mode' => 'wallet_transfer',
                    'txn_date' => $acc_stmt->stmt_txn_date];

                    $loan_txns_repo->insert_model($new_loan_txns_data);
                }   

                $acc_stmt_repo->update_model(['loan_doc_id' => null, 'recon_status' => null, 'id' => $acc_stmt->id]);
            }
        }                


    }


    
}
