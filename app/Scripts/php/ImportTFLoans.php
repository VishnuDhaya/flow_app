<?php

namespace App\Scripts\php;

use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Services\TFLoanDisbursalService;
use App\Consts;
use App\Services\AccountService;
use App\Services\LoanService;
use Carbon\Carbon;
use Log;
use DB;
use Excel;

class ImportTFLoans
{
    private function create_loan_record($row_id,$record){

        $data = [];

        $borr_repo = new BorrowerRepositorySQL();
        $prod_repo = new LoanProductRepositorySQL();
        $acc_repo = new AccountRepositorySQL();
        $prdctrepo = new LoanProductRepositorySQL();
        $loan_repo = new LoanRepositorySQL();

        try
        {
            DB::BeginTransaction();

            $sc_code = $record[0];
            $loan_amount = $record[1];
            $duration = $record[2];
            $disbursal_date = $record[3];
            $txn_id = $record[4];

            $acc_data = $acc_repo->get_account_by(['acc_number','status'] , [$sc_code,'enabled'],['id','cust_id']);

            if($acc_data == null){
                thrw( "No valid account available for this customer");
            }

            $borrower = $borr_repo->find_by_code($acc_data->cust_id,['lender_code','acc_prvdr_code','country_code','fund_code','flow_rel_mgr_id','cust_id','biz_name','owner_person_id','biz_address_id']);

            if($borrower == null){
                thrw("Customer not found");
            }
            $prod_json_arr = [12 => 0, 15 => 1, 18 => 2, 24 => 3];

            if($loan_amount == 500000){
                $prods_data = $prdctrepo->get_record_by_many(['loan_purpose','cs_model_code','status','product_code'],['terminal_financing','tf_products','enabled','TFP2'],['max_loan_amount','product_json','product_name','id']);
            }else{
                $prods_data = $prdctrepo->get_record_by_many(['loan_purpose','cs_model_code','status','product_code'],['terminal_financing','tf_products','enabled','TFP1'],['max_loan_amount','product_json','product_name','id']);
            }
            
            $disbursal_date = Carbon::createFromFormat("d/m/Y",$disbursal_date);

            $prdctjson = json_decode($prods_data->product_json,true);

            $product = $prdctjson[$prod_json_arr[$duration]];

            $product['amount'] = $prods_data->max_loan_amount;
            $product['product_name'] = $prods_data->product_name;
            $product['product_id'] = $prods_data->id;
     
            $loan_data = array_merge($product,(array)$borrower);

            $loan_data['acc_number'] = $sc_code;
            $loan_data['disbursal_date'] = $disbursal_date;
            $loan_data['disbursal_status'] = Consts::DSBRSL_SUCCESS;
            $loan_data['cust_acc_id'] = $acc_data->id;
            
            $loan_record = (new TFLoanDisbursalService)->create_loan_record($loan_data);

            // DB::update('update loans set loan_appl_date = ?, loan_approved_date = ?, customer_consent_rcvd = ?, status = ? where cust_id = ? and loan_doc_id = ?',[$disbursal_date, $disbursal_date, 1, 'pending_disbursal', $acc_data->cust_id, $loan_record['loan_doc_id']]);

            $loan_repo->update_model_by_code(['loan_appl_date' => $disbursal_date, 'loan_approved_date' => $disbursal_date, 'customer_consent_rcvd' => 1,"status" => "pending_disbursal", "loan_doc_id" => $loan_record['loan_doc_id']]);
    
            $disbursal_req = array();
    
            $disb_acc = (new AccountService)->get_lender_disbursal_account($borrower->lender_code, $borrower->acc_prvdr_code);

            $disbursal_req['amount'] = $prods_data->max_loan_amount;
            $disbursal_req['loan_doc_id'] = $loan_record['loan_doc_id'];
            $disbursal_req['from_ac_id'] = $disb_acc->id;
            $disbursal_req['to_ac_id'] = $acc_data->id;
            $disbursal_req['txn_id'] = $txn_id;
            $disbursal_req['txn_date'] = $disbursal_date;
            $disbursal_req['txn_exec_by'] = $loan_record['flow_rel_mgr_id'];
            $disbursal_req['send_sms'] = false;
            $disbursal_req['txn_mode'] = 'wallet_portal';
    
            (new LoanService())->disburse($disbursal_req, false, true);
    
            $due_date = $disbursal_date->addMonths($product['duration']);

            $loan_repo->update_model_by_code(['due_date'=>$due_date,'loan_doc_id'=>$loan_record['loan_doc_id']]);

            DB::commit();

        }
        catch (\Exception $e) {
            DB::rollback();
            Log::warning($e->getMessage());
            Log::warning($e->getTraceAsString());
        }

    }

    public function initiate_capture_loans()
    {       

        session()->put('country_code', 'UGA');
        session()->put('user_id',0);
        $path = 'app/Scripts/php/Terminal Financing Tracking.xlsx';

        $data = Excel::toArray([],$path);

        foreach($data[0] as $row_id => $record){
            if($row_id == 0){
                continue;
            }
            $this->create_loan_record($row_id,$record);
        }
    }
}