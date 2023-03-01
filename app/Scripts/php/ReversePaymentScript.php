<?php

namespace App\Scripts\php;

use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\LoanTransactionRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\CapitalFundRepositorySQL;
use App\Services\LoanService;
use App\Consts;
use Carbon\Carbon;
use DB;
use Log;

class ReversePaymentScript{

    public function reverse(){

        $loan_repo = new LoanRepositorySQL();
        $fund_repo = new CapitalFundRepositorySQL();
        $borr_repo = new BorrowerRepositorySQL();
        $loan_txn_repo = new LoanTransactionRepositorySQL();

        $loan_txns = DB::select("select id, loan_doc_id, txn_id, from_ac_id, to_ac_id, amount from loan_txns where id in (67727, 74591, 74592)");

        foreach($loan_txns as $data){
            
            $loan = $loan_repo->get_record_by('loan_doc_id', $data->loan_doc_id, ['loan_doc_id', 'loan_principal', 'flow_fee', 'due_amount', 'current_os_amount', 'paid_amount', 'paid_principal', 'paid_fee', 'paid_excess', 'penalty_collected', 'penalty_collected', 'status', 'fund_code']);
            Log::warning('----------------------');
            Log::warning(array($loan));
            $loan_txn_data = [
                'loan_doc_id' => $data->loan_doc_id,
                'from_ac_id' => $data->from_ac_id,
                'to_ac_id' => $data->to_ac_id,
                'amount' => $data->amount,
                'txn_type' => Consts::LOAN_REVERSE_PAYMENT,
                'txn_id' => $data->txn_id,
                'txn_date' => Carbon::now()
            ];

            try{
                DB::beginTransaction();
                DB::delete("delete from loan_txns where id = ? ",[$data->id]);
    
                $loan_txn_repo->create($loan_txn_data, true);
                
                $loan_repo->update_model_by_code([
                    'loan_doc_id' => $loan->loan_doc_id,
                    'current_os_amount' => $loan->current_os_amount + $data->amount,
                    'paid_amount' => $loan->paid_amount - $data->amount,
                    'paid_principal' => 0,
                ]);
                
                if(in_array($data->id, [79098])){
                    $fund_repo->increment_by_code('earned_fee', $loan->fund_code, -1 * $data->amount);
                }
                else{
                    $fund_repo->increment_by_code('os_amount', $loan->fund_code, $data->amount);
                }
                
                
    
                DB::commit();
            }catch(\Exception $e){
                DB::rollback();
                if ($e instanceof QueryException){
                    throw $e;
                }else{
                thrw($e->getMessage());
                }
            }
        }
    }

    public function run(){
        DB::delete("delete from loan_txns where id = 87547");
        DB::insert("insert into loan_txns (`loan_doc_id`, `from_ac_id`, `to_ac_id`, `amount`, `txn_type`, `txn_id`, `txn_date`, `created_at`, `created_by`) values ('CCA-890654-32798',null,1783,100000.0,'reverse_payment',null,'2021-12-14 09:27:46','2021-12-14 09:27:46',null)");

        DB::insert("insert into loan_events (`loan_doc_id`, `event_type`, `created_at`, `event_data`, `created_by`) values ('CCA-890654-32798','reverse_payment','2021-12-14 09:27:46',null,null)");

        DB::update("update loans set `current_os_amount` = 312000.0, `paid_amount` = 200000.0, `paid_principal` = 200000.0, `updated_at` = '2021-12-14 09:27:46' ,`updated_by` = null  where loan_doc_id = 'CCA-890654-32798'");

        DB::update("update `capital_funds` set `os_amount` = `os_amount` + 100000 , `updated_at` = '2021-12-14 09:27:46' ,`updated_by` = null  where fund_code = 'FLOW-INT'");
    }
}