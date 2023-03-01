<?php

namespace App\Services\Mobile;

use App\Consts;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\AccProviderRepositorySQL;
use App\Repositories\SQL\DisbursalAttemptRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\LeadRepositorySQL;
use DB;
use Log;
use App\Mail\FlowCustomMail;
use Illuminate\Support\Facades\Mail;

class USSDService{


    public static function configure_ussd_accounts($data){
        $accounts = $data['config_arr'];
        $acc_repo = new AccountRepositorySQL();
        $acc_prvdr_repo = new AccProviderRepositorySQL;
        foreach($accounts as $account){
            $acc = $acc_repo->get_account_by(['status', 'acc_number'],['enabled', $account['acc_number']], ['id', 'acc_prvdr_code']);
            $acc_prvdr = $acc_prvdr_repo->find_by_code($acc->acc_prvdr_code, ['mobile_cred_format']);
            $acc_repo->update_model(['mobile_cred' => ['msg_token' => $data['msg_token'], 
                                                        'pin' => $account['pin'], 
                                                        'ussd_code_format' => $acc_prvdr->mobile_cred_format->ussd_code_format,
                                                        'heartbeat' => $acc_prvdr->mobile_cred_format->heartbeat], 
                                     'id' => $acc->id]);
        }
    }

    public static function process_ussd_response($data){
        $disb_repo = new DisbursalAttemptRepositorySQL;
        if(isset($data['agent_id'])){
            $data = self::ussd_status_by_utility_app_status($data);
            unset($data['agent_id']);
        }

        $update_arr = ['id' => $data['disb_id'], 'partner_response' => json_encode($data)];

        $disb = $disb_repo->find($data['disb_id']);
        $disb_resp = json_decode($disb->partner_response);
        if($disb->status == 'unknown' && is_array($disb_resp) && $disb_resp[0]->message == Consts::TIMED_OUT_MSG){
            $disb_status = self::get_disbursal_status_by_ussd_status($data['status']);
            $update_arr['status'] = $disb_status;
            $loan_doc_id = $disb->loan_doc_id;
            $last_attempt = DB::selectOne("select max(id) id from disbursal_attempts where loan_doc_id = ? and country_code = ?", [$loan_doc_id, session('country_code')]);
            if($data['disb_id'] == $last_attempt->id){
                $loan_repo = new LoanRepositorySQL;
                $chk_loan_status = $loan_repo->find_by_code($loan_doc_id, ['status'])->status;
                if ( !in_array( $chk_loan_status, Consts::DISBURSED_LOAN_STATUS ) ) {
                    $loan_repo->update_model_by_code(['loan_doc_id' => $disb->loan_doc_id, 'disbursal_status' => $disb_status]);
                }
            }
        }
        $disb_repo->update_model($update_arr);
    }

    private static function ussd_status_by_utility_app_status($data){
        if($data['status'] == "exception"){
            $data['status'] = "failure";
        }
        elseif($data['status'] == 'response'){
            $acc_prvdr_code = (new AccountRepositorySQL())->get_accounts_by(['acc_number'], [$data['agent_id']], ['acc_prvdr_code'], false, "and status = 'enabled'")[0]->acc_prvdr_code;
            $ussd_disb_success_response = config('app.ussd_disb_success_response')[$acc_prvdr_code];
            $data = match_ussd_disb_response_status($ussd_disb_success_response, $data, "success");
            if($data['status'] != "success"){
                $ussd_disb_failure_response = config('app.ussd_disb_failure_response')[$acc_prvdr_code];
                $data = match_ussd_disb_response_status($ussd_disb_failure_response, $data, "failure");
            }
        }
        if($data['status'] == 'response'){
            $data['status'] = 'unknown';
            $data['country_code'] = session('country_code');
            Mail::to(config('app.app_support_email'))->queue((new FlowCustomMail('new_ussd_response_alert', $data))->onQueue('emails'));
        }
        return $data;
    }

    private static function get_disbursal_status_by_ussd_status($ussd_response_status){
        if($ussd_response_status == "success"){
            return Consts::DSBRSL_CPTR_FAILED;
        }

        if($ussd_response_status == "failure"){
            return Consts::DSBRSL_FAILED;
        }

        return $ussd_response_status;
    }

    public function get_payment_ussd($acc_prvdr_code, $recipient, $amount, $remarks = 'FLOW',$pin = null){
        $ussd_cred = (new AccProviderRepositorySQL())->get_json_field_by_code($acc_prvdr_code,'mobile_cred_format');
        $data = ['recipient' => $recipient, 'amount' => $amount, 'remarks' => $remarks, 'pin' => $pin];
        m_array_filter($data);
        $ussd =  str_replace('*:pin','',__($ussd_cred->ussd_code_format, $data));
        $short_ussd = $ussd_cred->ussd_short_code;

        return ['ussd' => $ussd, 'short_ussd' => $short_ussd];
    }

    public static function save_ussd_response($data){
        try{
            DB::beginTransaction(); 

            $lead_repo = new LeadRepositorySQL();
            $json_condition = ['account' => ['acc_number' => ['value' => $data['agent_id']]]];
            $lead_info = $lead_repo->get_json_by('cust_reg_json', $json_condition, ['cust_reg_json'], " AND profile_status = 'open'");

            //$lead_info = DB::selectOne("select id, cust_reg_json from leads where account_num = ? order by id desc limit 1", [$data['agent_id']]);
            if ($lead_info) {
                $cust_reg_arr = json_decode($lead_info->cust_reg_json, true);
                if (!isset($cust_reg_arr['account']['holder_name'])){
                    $cust_reg_arr['account']['holder_name'] = $data['agent_name'];
                    (new LeadRepositorySQL)->update_cust_reg_json($cust_reg_arr, $lead_info->id, ['status' => Consts::PENDING_AUDIT]);
                }
            }
            DB::commit();
            return;
        } catch (Exception $e) {
            DB::rollback();
            thrw($e->getMessage());
        }
    }
}