<?php

namespace App\Jobs;
use App\Mail\FlowCustomMail;
use App\Models\FlowApp\AppUser;
use App\Services\AccountService;
use Illuminate\Support\Str;
use Mail;
use App\Mail\EmailReviewInstantDisbursal;
use App\Repositories\SQL\CommonRepositorySQL;
use Throwable;
use App\Consts;
use App\Services\LoanService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Repositories\SQL\LoanRepositorySQL;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Repositories\SQL\DisbursalAttemptRepositorySQL;
use App\Services\DisbursalService;
use DB;

class InstantDisbursalQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    protected $data;
    protected $loan_serv;

    
    public function __construct($data)
    {
        $this->data = $data;
    }


    public function handle()
    {
        set_app_session($this->data['country_code']);
        
        
        $this->loan_serv = new LoanService;
        $disb_repo = new DisbursalAttemptRepositorySQL;
        $loan_repo = new LoanRepositorySQL();
        $acc_serv = new AccountService();
        $disburs_serv = new DisbursalService;

        $is_duplicate = $this->check_for_duplicate_fa();
        if($is_duplicate){
            return;
        }
        $req_arr = [];
        $req_arr['loan_txn'] = $this->data;
        $req_arr['from_acc'] = $acc_serv->get_lender_disbursal_account($this->data['lender_code'],$this->data['acc_prvdr_code'], $this->data['to_acc_num']);
        $req_arr['from_acc']->api_cred = '*********';
        $disb_id = $disb_repo->insert_model(['status' => Consts::DSBRSL_IN_PROGRESS, 'loan_doc_id' => $this->data['loan_doc_id'], 'cust_id' => $this->data['cust_id'], 'flow_request' => json_encode($req_arr, JSON_PRETTY_PRINT), 'country_code' => session('country_code')]);
        $loan_repo->update_model_by_code(['loan_doc_id' => $this->data['loan_doc_id'], 'disbursal_status' => Consts::DSBRSL_IN_PROGRESS]);

        $no_of_atmpts = DB::selectOne("select count(*) as count from disbursal_attempts where loan_doc_id =? ",[$this->data['loan_doc_id']]);
       
        if ($no_of_atmpts->count == 1){
            $loan_repo->update_loan_event('first_atmpt_start_time',$this->data['loan_doc_id']);
        } 

        $this->data['disb_id'] = $disb_id;
        $flow_disb_result = $this->loan_serv->disburse($this->data);

  
        $partner_combined_resp = null;
         
        
        $status = $flow_disb_result['disb_status'];
        $disb_attmpt = [];

        if(isset($flow_disb_result['partner_combined_resp']['partner_requests'])){
            $disb_attmpt['partner_request'] = json_encode($flow_disb_result['partner_combined_resp']['partner_requests'], JSON_PRETTY_PRINT);
        }
        if(isset($flow_disb_result['partner_combined_resp']['partner_responses'])){
            $disb_attmpt['partner_response'] = json_encode($flow_disb_result['partner_combined_resp']['partner_responses'], JSON_PRETTY_PRINT);
        }
        if(isset($flow_disb_result['flow_request'])){
            unset($flow_disb_result['flow_request']);
        }

        if(isset($flow_disb_result['partner_combined_resp'])){
            $partner_combined_resp = $flow_disb_result['partner_combined_resp'];
            $disb_attmpt['partner_combined_response'] = json_encode($partner_combined_resp, JSON_PRETTY_PRINT);
            unset($flow_disb_result['partner_combined_resp']);
        }
        


        $disb_attmpt['flow_response'] = json_encode($flow_disb_result, JSON_PRETTY_PRINT);
        
        $disb_attmpt['id'] = $disb_id;
        $disb_attmpt['status'] = $status;
        $disb_attmpt['loan_doc_id'] = $this->data['loan_doc_id'];



        $disb_repo->update_model($disb_attmpt);
        $loan_repo->update_model_by_code(['loan_doc_id' => $this->data['loan_doc_id'], 'disbursal_status' => $status]);
       
        $to_update['no_of_atmpts'] = $no_of_atmpts->count;
            if($no_of_atmpts->count == 1 ){
                $to_update['first_atmpt_end_time'] = datetime_db();
            }
            $loan_repo->update_json_arr_by_code('loan_event_time', $to_update ,$this->data['loan_doc_id']);

        if($status == Consts::DSBRSL_SUCCESS || $status == Consts::DSBRSL_CPTR_FAILED){
            $loan_repo->update_loan_event('success_atmpt_end_time',$this->data['loan_doc_id']);
            
            $disburs_serv->update_event_durations($this->data['loan_doc_id']);    
        }
        $loan = $loan_repo->get_record_by('loan_doc_id',$this->data['loan_doc_id'],['status','cust_name',]);
        
        $disb_attempt_info = $disb_repo->get_last_disburse_attempt($this->data['loan_doc_id'],['id','count']);

        // $is_ussd_success = ($status == Consts::DSBRSL_CPTR_FAILED && $req_arr['from_acc']->disb_int_type == 'mob');

        if($status!=Consts::DSBRSL_SUCCESS && $status != Consts::DSBRSL_CPTR_FAILED){

            $disb_attempt = $disb_repo->find($disb_id); 
            Mail::to(config('app.app_support_email'))->queue(new EmailReviewInstantDisbursal((array)$disb_attempt, $disb_attempt_info, $loan, $this->data['country_code']));

            if(isset($partner_combined_resp['message']) && Str::contains($partner_combined_resp['message'], 'OTP Not Received')
                                                                               && $this->data['acc_prvdr_code'] != Consts::BOK_AP_CODE){
                $recipients = get_ops_admin_csm_email();
                Mail::to($recipients)
                      ->queue(new FlowCustomMail('util_otp_not_rcvd', ['time' => datetime_db(),
                                                                           'loan_doc_id' => $this->data['loan_doc_id'],
                                                                           'purpose' => 'disbursal',
                                                                           'account' => (array)$req_arr['from_acc'],
                                                                           'country_code' => session('country_code')]));
            }
        }


    }

    public function failed()
    {   

    }

    public function check_for_duplicate_fa()
    {
        $disb_repo = new DisbursalAttemptRepositorySQL;
        $addl_sql_condition = " and loan_doc_id != '{$this->data['loan_doc_id']}'";
        Log::warning('BEFORE DUP FA CHECK');
        $records = $disb_repo->get_records_by_many(['cust_id', 'date(created_at)'],[$this->data['cust_id'], today()], ['loan_doc_id'], "and", $addl_sql_condition);
        Log::warning('AFTER DUP FA CHECK');
        if(!empty($records)) {
            $loans = csv((collect($records)->pluck('loan_doc_id')->unique()->toArray()));
            $duplicates = DB::select("select loan_doc_id from loans where loan_doc_id in ($loans) and status not in ('settled', 'voided')");
            if(sizeof($duplicates) > 0){
                $this->data['existing_records'] = array_values(collect($duplicates)->pluck('loan_doc_id')->toArray());
                Mail::to(config('app.app_support_email'))
                ->queue(new FlowCustomMail('duplicate_fa', $this->data));
                return true;
            }
        }  

        return false;

    }

}
