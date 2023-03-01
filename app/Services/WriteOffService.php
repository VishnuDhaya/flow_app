<?php

namespace App\Services;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\WriteOffRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\LoanProvisioningRepositorySQL;
use App\Repositories\SQL\LoanTransactionRepositorySQL;
use Exception;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

class WriteOffService{

    public function request_write_off($data){
        $loan_repo = new LoanRepositorySQL();
        $loan_txn_repo = new LoanTransactionRepositorySQL();
        $write_off_repo = new WriteOffRepositorySQL();
        $loan_prov_repo = new LoanProvisioningRepositorySQL();
        
        try{
            DB::beginTransaction();
            
            $cut_off_months = config('app.month_write_off_after_no_payment');

            $loan_prov = $this->check_bal_against_req_amount($data['loan_prov_id'], $data['write_off_amount']);

            $loan_txns = $loan_txn_repo->get_last_n_months_txn($data['loan_doc_id'], $cut_off_months, $loan_prov->year);

            if($loan_txns){
                $count = count($loan_txns);
                // thrw("Cannot make write-off request. {$count} payment(s) has been received for this FA within the last {$cut_off_months} months.");
            }
             
            // if(strlen($data['remarks']) < 1000){
            //     thrw("Remarks length should be in more than 1000 characters");
            // }

            $data['country_code'] = session('country_code');
            $data['acc_prvdr_code'] = session('acc_prvdr_code') ? session('acc_prvdr_code') : $data['acc_prvdr_code'] ;
            $data['write_off_status'] = 'requested';
            $data['req_by'] = session('user_id');
            $data['req_date'] = Carbon::now();
            $data['year'] =  $loan_prov->year;
            $data['loan_prov_id'] = $loan_prov->id;
            $data['recovery_amount'] = $this->get_recovery_amount($data);


            // $data['write_off_amount'] = $data['write_off_amount'] + $data['recovery_amount'];
            $write_off_id = $write_off_repo->insert_model($data);
            
            $loan_repo->update_model_by_code(['loan_doc_id' => $data['loan_doc_id'], 'write_off_id' => $write_off_id,
                                                'write_off_status' => $data['write_off_status']]);
            
            $new_req_amt = $loan_prov->requested_amount + $data['write_off_amount'];

            $balance = $loan_prov->balance - $data['write_off_amount'];

            $loan_prov_repo->update_model(['id' => $loan_prov->id, 'balance' => $balance, "requested_amount" => $new_req_amt  ]);

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            if ($e instanceof QueryException){
                throw $e;
            }else{
            thrw($e->getMessage());
            }
        }
        return ['status' => 'request','write_off_id' => $write_off_id];
    }

    public function approve_write_off($data){
        $loan_repo = new LoanRepositorySQL();
        $write_off_repo = new WriteOffRepositorySQL();
        $loan_prov_repo = new LoanProvisioningRepositorySQL();

        try{
            DB::beginTransaction();
            
            $data['appr_by'] = session('user_id');
            $date = Carbon::now();
            $data['appr_date'] =  $date->format('Y-m-d');

            $write_off_loan = $write_off_repo->get_record_by_many(['loan_doc_id', 'id'], [$data['loan_doc_id'], $data['write_off_id']], ['loan_prov_id', 'write_off_amount','write_off_status']);

            if($write_off_loan->write_off_status == 'requested'){
                $loan_prov = $this->check_bal_against_req_amount($write_off_loan->loan_prov_id, $write_off_loan->write_off_amount);
            
                $write_off_repo->update_model(['id' => $data['write_off_id'],
                                                            'appr_by' => $data['appr_by'], 
                                                            'appr_date' => $data['appr_date'],
                                                            'write_off_status' => 'approved']); 

                $loan_repo->update_model_by_code(['loan_doc_id' => $data['loan_doc_id'], 'write_off_status' => 'approved']);
                
                $new_req_amt = $loan_prov->requested_amount -  $write_off_loan->write_off_amount;

                $loan_prov_repo->update_model(['id' => $loan_prov->id, 'requested_amount' => $new_req_amt]);
            }
            else{
                thrw("The write-off request is in {$write_off_loan->write_off_status} status. You can not approve.");
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
        return ['status' => 'approved'];
    }

    public function reject_write_off($data){
        $loan_prov_repo = new LoanProvisioningRepositorySQL();
        $write_off_repo =  new WriteOffRepositorySQL();
        $loan_repo = new LoanRepositorySQL();

        try{
            DB::beginTransaction();

            $write_off_loan = $write_off_repo->get_record_by_many(['loan_doc_id', 'id'], [$data['loan_doc_id'], $data['write_off_id']], ['loan_prov_id', 'write_off_amount','write_off_status']);

            if($write_off_loan->write_off_status == 'requested'){
                $loan_prov = $loan_prov_repo->find($write_off_loan->loan_prov_id);
                $balance = $loan_prov->balance + $write_off_loan->write_off_amount;

                $requested_amount = $loan_prov->requested_amount - $write_off_loan->write_off_amount;

                $loan_prov_repo->update_model(['id' => $write_off_loan->loan_prov_id, 'balance' => $balance, 'requested_amount' => $requested_amount]);

                $write_off_repo->update_model(['id' => $data['write_off_id'], 'write_off_status' => 'rejected']);
                
                $loan_repo->update_model_by_code(['loan_doc_id' => $data['loan_doc_id'], 'write_off_status' => null, 'write_off_id' => null]);
            }
            else{
                thrw("The write-off request is in {$write_off_loan->write_off_status} status. You can not approve.");
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

    public function list_write_off($data){
        $write_off_repo =  new WriteOffRepositorySQL();
        $person_repo = new PersonRepositorySQL();
        $loan_repo = new LoanRepositorySQL();

        $write_off_loans = $write_off_repo->get_records_by('write_off_status', $data['write_off_status']);  

        foreach($write_off_loans as $write_off_loan){
            $write_off_loan->req_name = $person_repo->full_name_by_user_id($write_off_loan->req_by);
            $loan = $loan_repo->get_record_by('loan_doc_id', $write_off_loan->loan_doc_id, ['acc_number']); 
            $write_off_loan->acc_number = $loan->acc_number;  
        }
        
        return $write_off_loans;
        
    }
    public function get_write_off($data){

        $write_off_repo =  new WriteOffRepositorySQL();
        $person_repo = new PersonRepositorySQL();
        $loan_prov_repo = new LoanProvisioningRepositorySQL();

        $write_off_loan = $write_off_repo->find($data['write_off_id']);
        $write_off_loan->req_name = $person_repo->full_name_by_user_id($write_off_loan->req_by);
    
        $loan_prov = $loan_prov_repo->find($write_off_loan->loan_prov_id);
        $write_off_loan->balance = $loan_prov->balance + $loan_prov->requested_amount;
        
        return $write_off_loan;
        
    }

    private function check_bal_against_req_amount($loan_prov_id, $requested_amount){
        $loan_prov_repo = new LoanProvisioningRepositorySQL();
        
        $loan_prov = $loan_prov_repo->find($loan_prov_id);
        
        $balance = $loan_prov->balance + $loan_prov->requested_amount;

        if($balance < $requested_amount){
            thrw("Provisioning balance is less than write off amount. Cannot write off for this customer.");
        }
        
        return $loan_prov;
    }

    public function get_recovery_amount($data){
        $loan_txn_repo = new LoanTransactionRepositorySQL();
        $chk_txns_before_wo = $loan_txn_repo->get_txns_by_year($data['loan_doc_id'], $data['year']);

        $recovery_amount = 0; 
        if($chk_txns_before_wo){
            foreach($chk_txns_before_wo as $txn){
                $recovery_amount += $txn->amount;
            }
        }
        return $recovery_amount;
    }
    
}