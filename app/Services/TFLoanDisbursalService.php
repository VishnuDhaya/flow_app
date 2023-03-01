<?php

namespace App\Services;

use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\LeadRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Services\AccountService;
use App\Services\LoanService;
use App\Services\Support\SMSNotificationService;
use App\SMSTemplate;
use App\Consts;
use App\Repositories\AddressRepositoryEloquent;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\AddressInfoRepositorySQL;
use App\Repositories\SQL\LoanTransactionRepositorySQL;
use Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class TFLoanDisbursalService{

    public function tf_disburse($cust_id){

        try
        {

            $prod_repo = new LoanProductRepositorySQL();
            $borr_repo = new BorrowerRepositorySQL;
            $lead_repo = new LeadRepositorySQL;
            $loan_serv = new LoanService();
            $loan_repo = new LoanRepositorySQL;
            $acc_serv = new AccountService();
            $acc_repo = new AccountRepositorySQL;
            
            $flow_disb_result = ['disb_status' => 'unknown'];
            $field_val = json_encode(['cust_id' => $cust_id]);
            $lead_data =  DB::select("select id,acc_purpose,product,tf_status from leads where json_contains(cust_reg_json, '$field_val') and JSON_CONTAINS(acc_purpose, JSON_ARRAY('terminal_financing')) and tf_status = ?",[Consts::TF_PENDING_FLOW_DISB]);
            if(sizeof($lead_data) == 1){
                $lead_data = $lead_data[0];
                $borrower_data = $borr_repo->find_by_code($cust_id,['lead_id','lender_code','acc_prvdr_code','cust_acc_id','fund_code','flow_rel_mgr_id',
                                                                'cust_id','biz_name','owner_person_id','biz_address_id','cust_acc_id','country_code']);
                session()->put('country_code',$borrower_data->country_code);
                $disb_acc = $acc_serv->get_lender_disbursal_account($borrower_data->lender_code, $borrower_data->acc_prvdr_code);
                $product_json = $prod_repo->get_tf_product($lead_data->product);
                $acc_data = $acc_repo->get_account_by(['cust_id','acc_purpose','status'] , [$cust_id,'terminal_financing','enabled'],['id','acc_number']);
                if($acc_data){
                    $borrower_data->acc_number = $acc_data->acc_number;
                    $loan_txn['from_ac_id'] = $disb_acc->id;
                    $loan_txn['to_ac_id'] =  $acc_data->id;
                    $loan_txn['amount'] = $product_json['amount'];
                }else{
                    thrw( "No valid account available for this customer to disburse terminal finance");
                 }
            }
            else if(sizeof($lead_data) > 1){
                thrw( "More than one accounts available for this customer to disburse terminal finance");
            }
            else{
                thrw( "No valid account available for this customer to disburse terminal finance");
            }

            if($product_json['amount'] > config("app.flow_disbursal_limit")){
                thrw("Cannot disburse more than the limit : {config('app.flow_disbursal_limit')}");
            }

            $loan = array_merge($product_json, (array)$borrower_data);
            $loan_data = $this->create_loan_record($loan);
            $flow_disb_result = $loan_serv->make_instant_disbursal($loan_txn, $disb_acc);
            if($flow_disb_result['disb_status'] != Consts::DSBRSL_SUCCESS){
                $loan_repo->update_record_status($flow_disb_result['loan_status'], $loan_data['loan_id']);
            }else if($flow_disb_result['disb_status'] == Consts::DSBRSL_SUCCESS){
                try{
                    DB::beginTransaction();
                    $loan_txn['txn_id'] = $flow_disb_result['txn_id'];
                    $loan_txn = array_merge($loan_txn, $loan_data);
                    $loan_txn = $this->create_loan_txn_arr($loan_txn);

                    $loan_data['disbursal_status'] = Consts::DSBRSL_SUCCESS;
                    $loan_data['disbursal_date'] = $loan_txn['txn_date'];
                   
                    $loan_serv->capture_disbursal($loan_txn, $loan_data, false);
                    
                    $json_field = json_encode(['loan' => ['status' => CONSTS::LOAN_ONGOING,'amount' => $product_json['amount'],'txn_id' => $flow_disb_result['txn_id'],'date' => date_db()]]);
                    $update_fields = sprintf( "tf_status = '%s'",CONSTS::TF_PENDING_POS_TO_RM);
                    DB::update("update leads set $update_fields, update_data_json = json_merge_patch(update_data_json,'$json_field') where id = {$lead_data->id}");
                    
                    $sms_serv = new SMSNotificationService();
                    $loan_data['repayment_date'] = format_date(Carbon::now()->addDays(2));
                    $loan_data['country_code'] = session('country_code');
                    $flow_disb_result["sms_sent"] = $sms_serv->send_notification_message($loan_data, 'TF_DISBURSEMENT_MSG');
                    
                    DB::commit();
                } catch (\Exception $e) {

            
                    Log::warning($e->getTraceAsString());
                    $flow_disb_result['exp_msg'] = $e->getMessage();
                    if($flow_disb_result['disb_status'] == Consts::DSBRSL_SUCCESS){
                        $flow_disb_result['disb_status'] = Consts::DSBRSL_CPTR_FAILED;
                    }
        
                }
            }else{
                thrw('Disbursal Failed');
            }

           

        } catch (\Exception $e) {

           
            Log::warning($e->getTraceAsString());
            $flow_disb_result['exp_msg'] = $e->getMessage();
            if($flow_disb_result['disb_status'] == Consts::DSBRSL_SUCCESS){
                $flow_disb_result['disb_status'] = Consts::DSBRSL_CPTR_FAILED;
            }

        }

    

    return  $flow_disb_result;

    }

    public function create_loan_record($data){
        $loan_repo = new LoanRepositorySQL;
        $common_repo = new CommonRepositorySQL();
        $person_repo = new PersonRepositorySQL;
        $addr_repo = new AddressInfoRepositorySQL();
        $person_repo = new PersonRepositorySQL;
        $data['cust_name'] = $person_repo->full_name($data['owner_person_id']);
        $biz_address = $addr_repo->find($data['biz_address_id']);
        $data['currency_code'] = (new CommonRepositorySQL())->get_currency()->currency_code;
        $data['cust_addr_text'] = full_addr($biz_address);
        $loan_appl_id = $common_repo->get_new_flow_id(session('country_code'), 'loan_appl');
        $data['loan_doc_id'] = "{$data['cust_id']}-{$loan_appl_id}";
        $data['loan_approver_name'] = $person_repo->full_name($data['flow_rel_mgr_id']);
        $data['cust_mobile_num'] = $person_repo->get_mobile_num($data['owner_person_id']);
        $data['due_date'] = Carbon::Now()->addMonths($data['duration']);
        $data['flow_fee'] = calc_tf_fee($data['amount'],$data['flow_fee'],$data['duration']);
        $data['due_amount'] = $data['current_os_amount'] = $data['flow_fee'] + $data['amount'];
        $loan_data = ['status' => Consts::LOAN_HOLD,
                    'country_code' => session('country_code'),
                    'loan_purpose' => 'terminal_financing',
                    'loan_principal' => $data['amount'],
                    'flow_fee_type' => 'Flat',
                    'loan_applied_by' => $data['flow_rel_mgr_id'],
                    'loan_approver_id' => $data['flow_rel_mgr_id'],
                    'loan_appl_date' => datetime_db(),
                    'loan_approved_date' => datetime_db(),
                ];
        $loan_data = array_merge($data,$loan_data);
        $loan_data['loan_id'] = $loan_repo->insert_model($loan_data);
        return $loan_data;
    }

    private function create_loan_txn_arr($data){
        $loan_txn['txn_mode'] = "instant_disbursal";
        $loan_txn['txn_exec_by'] = $data['flow_rel_mgr_id'] ;
        $loan_txn['from_ac_id'] = $data['from_ac_id'];
        $loan_txn['to_ac_id'] = $data['to_ac_id'];
        $loan_txn['txn_id'] = $data['txn_id'];
        $loan_txn['txn_type'] = "disbursal";
        $loan_txn['txn_date'] = datetime_db();
        $loan_txn['loan_doc_id'] = $data['loan_doc_id'];

        return $loan_txn;

    }
}
