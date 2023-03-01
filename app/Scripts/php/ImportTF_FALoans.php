<?php

namespace App\Scripts\php;

use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Services\BorrowerService;
use App\Services\LoanApplicationService;
use App\Services\LoanService;
use App\Services\RepaymentService;
use App\Services\CommonService;
use App\Repositories\SQL\LoanRepositorySQL;
use Excel;
use DB;
use Carbon\Carbon;
use Log;

class ExcelImport
{
    public function get_products_data()
    {
        $product_repo = new LoanProductRepositorySQL();
        $prods_data = $product_repo->get_records_by_many(['acc_prvdr_code'],['UEZM'],['id','product_code','duration','product_type','max_loan_amount','flow_fee','flow_fee_type','flow_fee_duration']);
        return $prods_data;
    }

    public function get_cust_acc_id($cust_id,$acc_number)
    {
        $account_repo = new AccountRepositorySQL();
        $account = $account_repo->get_record_by_many(['cust_id','status','acc_number'],[$cust_id, 'enabled', $acc_number], ['id']);
        return $account->id;
	}

    private function initiate_loan_capture($record_obj,$xl_record,$cust_id){

        $loan_repo = new LoanRepositorySQL();

        $loan_appl = (new LoanApplicationService())->apply_loan($record_obj,false,false);

        $loan = $this->approve_record($record_obj,$loan_appl);

        $loan_repo->update_model_by_code(['customer_consent_rcvd' => 1,"loan_doc_id" => $loan['loan']->loan_doc_id]);

        $disburse = $this->disburse_record($record_obj,$loan,$xl_record);

        if(isset($xl_record[6]) && isset($xl_record[7])){
            $this->repay_record($record_obj,$loan,$xl_record);
        }

        return $loan['loan']->loan_doc_id;
    }

    private function approve_record($record_obj,$loan_appl){

        $loan_appl = $loan_appl['loan_application'];
        $record_obj['loan_appl_doc_id'] = $loan_appl->loan_appl_doc_id;
        $record_obj['action'] = "approve";
        $record_obj['credit_score'] = 0;
        $record_obj['loan_apprvd_date'] = $record_obj['loan_appl_date'];
        $record_obj['appr_reason'] = 'Eligible';
        $record_obj['created_at'] = $record_obj['loan_appl_date'];

        $loan = (new LoanApplicationService())->approval($record_obj, false, false);
        return $loan;	
    }

    private function disburse_record($record_obj,$loan, $xl_record){

        $loan = $loan['loan'];
        $disbursal_req = array();

        $disbursal_date = Carbon::createFromFormat("d/m/Y",$xl_record[5]);
        $from_acc = (new \App\Services\AccountService)->get_lender_disbursal_account($record_obj['lender_code'],$record_obj['acc_prvdr_code']);

        $disbursal_req['amount'] = $record_obj['loan_principal'];
        $disbursal_req['loan_doc_id'] = $loan->loan_doc_id;
        $disbursal_req['from_ac_id'] = $from_acc->id;
        $disbursal_req['to_ac_id'] = $record_obj['account_id'];
        $disbursal_req['txn_id'] = $xl_record[4];
        $disbursal_req['txn_date'] = $disbursal_date; 
        $disbursal_req['txn_exec_by'] = $record_obj['flow_rel_mgr_id'];
        $disbursal_req['send_sms'] = false;
        $disbursal_req['txn_mode'] = 'wallet_portal';
        
        $loan = (new LoanService())->disburse($disbursal_req, false, true);
    }

    private function repay_record($record_obj,$loan, $xl_record){

        $loan = $loan['loan'];
        $payment_req = array();

        $paid_date = Carbon::createFromFormat("d/m/Y",$xl_record[7]);
        $to_acc = (new \App\Services\AccountService)->get_lender_disbursal_account($record_obj['lender_code'],$record_obj['acc_prvdr_code']);

        if($xl_record[6] == '110872039'){
            $payment_req['amount'] = 520000;
        }else{
            $payment_req['amount'] = $xl_record[2] + $xl_record[8];
        }
        $payment_req['loan_doc_id'] = $loan->loan_doc_id;
        $payment_req['from_ac_id'] = $record_obj['account_id'];
        $payment_req['to_ac_id'] = $to_acc->id;
        $payment_req['txn_date'] = $paid_date;
        $payment_req['txn_id'] = $xl_record[6];
        $payment_req['txn_exec_by'] = $record_obj['flow_rel_mgr_id'];
        $payment_req['send_sms'] = false;
        $payment_req['is_part_payment'] = false;
        $payment_req['waive_penalty'] = false;
        $payment_req['txn_mode'] = 'wallet_transfer';
        $payment_req['mode'] = 'capture';

        $loan = (new RepaymentService())->capture_repayment($payment_req, false);
    }

    public function import_record($row_id, $xl_record,$prods_data)
    {
        $data = [];

        $this->missing_custs = [];
		$this->skipped_row_ids = [];
		$this->loan_list = [];

        try
        {
            DB::BeginTransaction();
            $cust_id = $xl_record[0];

            $data = (new BorrowerService())->get_borrower($cust_id,false);
            
            if($data == null){
                $this->missing_custs[] = $cust_id;
                Log::warning("Missing Customers: $cust_id");
            }

            $sc_code = $xl_record[1];
            $loan_amount = $xl_record[2];
            $flow_fee = $xl_record[8];
            $duration = $xl_record[3];
            $disburse_date = $xl_record[5];
    
            $record_obj = ((array)$data);
            
            $prod_identified = false;
            
            foreach($prods_data as $prod_data){

                if($prod_data->max_loan_amount == $loan_amount && $prod_data->flow_fee == $flow_fee && $prod_data->duration == $duration){

                    $prod_identified = true;
                    $record_obj['product_name'] = $prod_data->product_code;
                    $record_obj['flow_fee'] = $prod_data->flow_fee;
                    $record_obj['product_id'] = $prod_data->id;
                    $record_obj['product_type'] = $prod_data->product_type;
                    $record_obj['loan_principal'] = $prod_data->max_loan_amount;
                    $record_obj['due_amount'] = $prod_data->max_loan_amount + $prod_data->flow_fee;
                    $record_obj['duration'] = $prod_data->duration;
                    $record_obj['flow_fee_type'] = $prod_data->flow_fee_type;
                    $record_obj['flow_fee_duration'] = $prod_data->flow_fee_duration;
                }
            }  
            
            if(!$prod_identified){
                thrw("Product Not found");
            }

            $record_obj['loan_approver_id'] = $record_obj['flow_rel_mgr_id'];
            $appl_date = Carbon::createFromFormat("d/m/Y",$disburse_date);
            $record_obj['loan_appl_date'] = $appl_date;
            $record_obj['cust_acc_id'] = $this->get_cust_acc_id($record_obj['cust_id'],$sc_code);;
            $record_obj['currency_code'] = 'UGX';
            $record_obj['cs_result_code'] = 'eligible';
            $record_obj['loan_appl_doc_id'] = (new LoanApplicationService())->gen_new_loan_appl_id(session('country_code'),$cust_id);#'APPL-'.$xl_record[0];

            $this->loan_list[$row_id] =  $this->initiate_loan_capture($record_obj,$xl_record,$cust_id);

            DB::commit();
            Log::warning("Imported Row IDs");
            Log::warning($this->loan_list);   
        }
        catch(\Exception $e)
        {
            DB::rollback();
            $this->skipped_row_ids[$row_id] = $cust_id . ' : '. $e->getMessage();
            Log::warning($e->getMessage());
            Log::warning($e->getTraceAsString());

            Log::warning("Skipped Row IDs");
            Log::warning($this->skipped_row_ids);    
        }
    }
}

class ImportTF_FALoans
{
    public function import_tf_loans()
    {  
        session()->put('country_code', 'UGA');
        session()->put('user_id',10);
		$path = 'app/Scripts/php/Track sheet for TF FA disbursements.xlsx';

	    $import = new ExcelImport();
        $prods_data = $import->get_products_data();
	    $data = Excel::toArray([],$path);
        foreach($data[0] as $row_id => $record){
           if($row_id == 0){
               continue;
            }
            $import->import_record($row_id, $record,$prods_data);
        }
    }
}


