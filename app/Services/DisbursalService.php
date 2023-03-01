<?php

namespace App\Services;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\AccProviderRepositorySQL;
use App\Services\Vendors\Payment\ChapChapService;
use App\Consts;
use App\Repositories\SQL\DisbursalAttemptRepositorySQL;
use App\Services\Partners\RRTN\RRTNService;
use App\Services\Support\FireBaseService;
use \GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Log;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use App\Models\LoanEventTime;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\LoanApplicationRepositorySQL;
use DB;
use App\Repositories\SQL\BorrowerRepositorySQL;

class DisbursalService {
    public function make_instant_disbursal($loan_txn, $from_acc)
    {
        Log::warning('disbursal');
        Log::warning($loan_txn);
        $ap_code = session('acc_prvdr_code');
        $acc_repo = new AccountRepositorySQL();
        $acc_prvdr_repo = new AccProviderRepositorySQL();
        /*$from_ac_id = $loan_txn['from_ac_id'];
        
        $from_acc = $acc_repo->find($from_ac_id);*/
        $acc_prvdr_code = $from_acc->acc_prvdr_code;
        
        $acc_prvdr = $acc_prvdr_repo->find_by_code($acc_prvdr_code, ['api_url', 'acc_prvdr_code']);
    
        //$int_type = $from_acc->int_type ? $from_acc->int_type : $acc_prvdr->int_type;

        $int_type =  isset($loan_txn['int_type']) ? $loan_txn['int_type'] : $from_acc->disb_int_type;

        $to_acc_num = $acc_repo->get_acc_num($loan_txn['to_ac_id']);

        $supported_aps = [Consts::CHAP_CHAP_AP_CODE, Consts::EZEEMONEY_AP_CODE, Consts::MTN_AP_CODE, Consts::RMTN_AP_CODE, Consts::BOK_AP_CODE, Consts::RRTN_AP_CODE, Consts::RATL_AP_CODE];

        $aps_w_limits = [Consts::CHAP_CHAP_AP_CODE];
    
        
        if(in_array($acc_prvdr_code, $supported_aps)){
            
            $results = [];
            $amount = $loan_txn['amount'];
            unset($loan_txn['amount']);
            
            if($acc_prvdr_code == Consts::CHAP_CHAP_AP_CODE){
                $to_acc_num = clean_mobnum($to_acc_num);
            }
            if(in_array($acc_prvdr_code, $aps_w_limits) 
                    && $amount > config('app.acc_prvdr_disbursal_limits')[$acc_prvdr_code]){
                $limit = config('app.acc_prvdr_disbursal_limits')[$acc_prvdr_code];
                $amounts = $this->get_disbursal_breakups($amount, $limit);
                
                if($int_type == 'api'){
                    $results = $this->iterate_call_disburse_api($acc_prvdr, $to_acc_num, $amounts, $from_acc->api_cred, $loan_txn);
                }else if($int_type == 'web'){
                    $results = $this->iterate_call_disburse_scripts($acc_prvdr_code, $to_acc_num, $amounts, $from_acc->web_cred, $from_acc->acc_number, $loan_txn);
               
                }
                
            }
            else{
                
                if($int_type == 'api'){
                    $result = $this->call_disburse_api($acc_prvdr, $to_acc_num, $amount, $from_acc->api_cred, $loan_txn);
                }else if($int_type == 'web'){
                    $result = $this->call_disburse_scripts($acc_prvdr_code, $to_acc_num, $amount, $from_acc->web_cred, $from_acc->acc_number, $loan_txn);
                }
                else if($int_type == 'mob'){
                    $result = $this->send_request_to_phone($from_acc, $to_acc_num, $amount, $loan_txn);
                }

                $results[] = $result;
            }
           

            $response = $this->format_result($results, $acc_prvdr_code);
           
            return $response;
        }else{
            thrw("Instant disbursal not supported for {$acc_prvdr_code}");
        }
    }
    
    function format_result($results, $acc_prvdr_code){
        
        $txn_ids = [];
        $amount = 0;
        $status = 'success';
        $message = '';
        $formatted_result =  $txn_data =  [];

        foreach($results as $result){
            if(isset($result['partner_request'])){
                $formatted_result['partner_requests'][] = $result['partner_request'];
            }
            if(isset($result['partner_response'])){
                $formatted_result['partner_responses'][] = $result['partner_response'];
            }
            if($result['status'] != 'success'){
                $status = $result['status'];
                $message = $result['message'];
                break;
            }else{
                $txn_ids[] = $result['txn_id'];
                $amount += $result['amount'];
                $aps_w_limits = array_keys(config('app.acc_prvdr_disbursal_limits'));
    
                if(in_array($acc_prvdr_code, $aps_w_limits)) {
                    $txn_data[] = ['txn_id' => $result['txn_id'], 'amount' => $result['amount']];
                }

            }
        }

        $txn_ids = implode(',', $txn_ids);
       
        return  array_merge($formatted_result, ['status' => $status, 
                                        'txn_ids' => $txn_ids, 
                                        'amount' => $amount, 
                                        'results' => $results,
                                        'message' => $message,
                                        'txn_data' => $txn_data ]);

    }
               
    function iterate_call_disburse_scripts($acc_prvdr_code, $to_acc_num, $amounts, $web_cred, $from_acc_num, $loan_txn){
        $results = [];
      
        foreach($amounts as $amount){
            
            $result = $this->call_disburse_scripts($acc_prvdr_code, $to_acc_num, $amount, $web_cred, $from_acc_num, $loan_txn);
            
            $results[] = $result;
            if($result['status'] != 'success'){
                break;
            }
            
        }
       
        return $results;
    }
    
    function iterate_call_disburse_api($acc_prvdr, $to_acc_num, $amounts, $api_cred, $loan_txn){
        $results = [];
      
        foreach($amounts as $amount){
            
            $result = $this->call_disburse_api($acc_prvdr, $to_acc_num, $amount, $api_cred, $loan_txn);
           
            
            $results[] = $result;
            if($result['status'] != 'success'){
                break;
            }
            
        }
       
        return $results;
    }
    
    function get_disbursal_breakups($tot_amount, $limit){
        $amounts = [];
        $last_txn_reminder = $tot_amount % $limit;
        $dividend = $tot_amount - $last_txn_reminder; 
        $divisor  = $dividend / $limit;
        
        for($i=1; $i <= $divisor; $i++){
            $amounts[] = $limit;
           
        }
        if($last_txn_reminder > 0 ){
            $amounts[] = $last_txn_reminder;
           
        }
       

        return $amounts;
    }

    function call_disburse_api($acc_prvdr, $to_acc_num, $amount, $api_cred, $loan_txn){
        
        /*
        if($acc_prvdr->acc_prvdr_code == 'CCA'){
            $cca_serv = new ChapChapService($acc_prvdr, $api_cred);
            $result = $cca_serv->transfer_money($to_acc_num, $amount);
            return $result;
        }
        */
        if($acc_prvdr->acc_prvdr_code == 'RRTN'){
            $rrtn_serv = new RRTNService();
            $loan_txn['to_acc_num'] = $to_acc_num;
            $resp = $rrtn_serv->transfer_money($acc_prvdr, $api_cred, $loan_txn, $amount);
        }
        $resp['partner_response'] = $resp;
        $resp['partner_request']['recipient'] = $to_acc_num;
        $resp['partner_request']['amount'] = $amount;
        $resp['partner_request']['storage_path'] = null;

        return $resp;
    }
    function call_disburse_scripts($acc_prvdr_code, $to_acc_num, $amount, $web_cred, $from_acc_num, $loan_txn){
        $storage_path = env('FLOW_STORAGE_PATH');
        $storage_path = $storage_path.DIRECTORY_SEPARATOR."files".DIRECTORY_SEPARATOR."disbursals";

        $args_json = (array)json_decode($web_cred);
        $args_json['storage_path'] = $storage_path;
        $args_json['to_acc_num'] = $to_acc_num;
        $args_json['amount'] = $amount;
        if($acc_prvdr_code == Consts::MTN_AP_CODE){
            $args_json['agent_id'] = $from_acc_num;
        }elseif($acc_prvdr_code == Consts::BOK_AP_CODE){
            $args_json['from_acc_num'] = $from_acc_num;
            $args_json['disb_id'] = $loan_txn['disb_id']; 
        }
        $args_json = json_encode($args_json);

        if(env('APP_ENV') == 'production'){
            $resp = run_py_script("vendors/payment/{$acc_prvdr_code}.py", ["'{$args_json}'"]);
            $resp = json_decode($resp, true);
        }
        else if(env('TEST_REAL_DISBURSAL') && $acc_prvdr_code == "CCA"){
            $resp = run_py_script("vendors/payment/CCA_test.py", ["'{$args_json}'"]);
            $resp = json_decode($resp, true);
        }
        else{
            $resp = ['status' => 'success', 'txn_id' => "999999999", 'message' => "Mock Response",
                     'screenshot_path' => env('FLOW_STORAGE_PATH').'/files/disbursals/UGA/payments/CCA',
                     'traceback' => ''];
        }

        $resp['screenshot_path'] = trim_abs_path($resp['screenshot_path']);

        $resp['partner_response'] = $resp;
        $resp['partner_request']['recipient'] = $to_acc_num;
        $resp['partner_request']['amount'] = $amount;
        $resp['partner_request']['storage_path'] = $storage_path ;
        if(array_key_exists('status', $resp)){
            if($resp["status"] == 'failure'){
                return $resp;
                //thrw($resp["message"]);
            }else if($resp["status"]  == 'unknown'){
                #thrw($UNKNOWN_MSG ."\n". $resp["message"]);
                return $resp;
            }else if($resp["status"]  == 'success'){
                $resp['amount'] = $amount;
                if($acc_prvdr_code == Consts::BOK_AP_CODE){
                    $resp = array_merge($resp, ['txn_id' => "rbok_transfer"]);
                }
                return $resp;
            }else{
                return ['status' => 'unknown', 'message' => 'unknown1','partner_request' => $resp['partner_request'], 'partner_response' => $resp['partner_response']];
            }
        }
        else{
            return ['status' => 'unknown', 'message' => 'unknown2','partner_request' => $resp['partner_request'], 'partner_response' => $resp['partner_response']];
        }
        

    }

    function send_request_to_phone($from_acc, $to_acc_num, $amount, $loan_txn){
        $disb_id = $loan_txn['disb_id'];
        $wait_secs = config('app.ussd_disbursal_timeout_secs');

        $mobile_creds = $from_acc->mobile_cred;
        
        $messenger_serv = new FireBaseService;
        $disb_repo = new DisbursalAttemptRepositorySQL;

        $customer = (new BorrowerRepositorySQL)->find_by_code($loan_txn['cust_id'], ['biz_name']);
        $cust_name = $customer->biz_name ?? null;
        
        $code = compile_sms_old($mobile_creds->ussd_code_format, ['recipient' => $to_acc_num, 'amount' => $amount, 
                                                              'remarks' => $disb_id, 'pin' => $mobile_creds->pin], false);
        // $code = "*123*1#";
        $params = ['ussd_code' => $code, 'disb_id' => $disb_id,
                   'from_acc' => $from_acc->acc_number, 
                   'to_acc' => $to_acc_num, 'amount' => $amount,
                   'loan_doc_id' => $loan_txn['loan_doc_id'],
                   'cust_name' => $cust_name, 'acc_prvdr_code' => $from_acc->acc_prvdr_code];
        
        if(env('APP_ENV') == 'production' || env("TEST_USSD_DISBURSAL") == 'true'){
            $ttl_secs = $wait_secs - config('app.ussd_disbursal_app_process_time');
            $addl_options = ["ttl" => "{$ttl_secs}s"];
            $messenger_serv->send_message(['action' => 'disburse', 'data' => json_encode($params)], $mobile_creds->msg_token, $addl_options);
            $disb_repo->update_model(['id' => $disb_id, 'partner_request' => json_encode($params)]);
            for($i = 0; $i < $wait_secs; ++$i) {
                $attempt = $disb_repo->find($disb_id, ['partner_response']);
                if($attempt->partner_response){
                    break;
                }
                sleep(1);
            }
        
            if($attempt->partner_response){
                $resp = json_decode($attempt->partner_response, true);
            }else{
                $resp = ['status' => 'unknown', 'message' => Consts::TIMED_OUT_MSG];
                $disb_repo->update_model(['id' => $disb_id, 'partner_response' => json_encode($resp)]);

            }
        
        }else{
            $resp = ['status' => 'success','txn_id' => "999999999",'message' => "Mock Response", 'traceback' => ''];
        }
        
        $resp['partner_request'] = $params;
        $resp['partner_response'] = $resp;
        if($resp['status'] == 'success'){
            $resp = array_merge(['txn_id' => "ussd", "amount" => $amount], $resp);
        }
        return $resp;
        
        
    }

    public function update_ussd_disb_status_to_unknown($acc_number, $disb_id = null){
        $account = (new AccountRepositorySQL)->get_account_by(['acc_number', 'status', 'acc_purpose'], [$acc_number, 'enabled', 'disbursement'],['mobile_cred']);
        $mobile_creds = $account->mobile_cred;
        $messenger_serv = new FireBaseService;

        $messenger_serv->send_message(['action' => 'make_disb_status_unknown', 'attempt_id' => json_encode($disb_id)], $mobile_creds->msg_token);

    }

    public function update_event_durations($loan_doc_id){


        $loan_appl_repo = new LoanApplicationRepositorySQL();
        $loan_repo = new LoanRepositorySQL();

        $disbursal_event_time = DB::selectone("select country_code,cust_conf_channel,flow_rel_mgr_id,loan_doc_id,loan_appl_id,
        TIMEDIFF(json_extract(loan_event_time,'$.loan_appr_time'),json_extract(loan_event_time,'$.loan_appl_time'))as rm_time , 
        TIMEDIFF(json_extract(loan_event_time,'$.otp_match_time'),json_extract(loan_event_time,'$.first_otp_sent_time'))as cust_time, 
        TIMEDIFF(json_extract(loan_event_time,'$.success_atmpt_start_time'),json_extract(loan_event_time,'$.first_queue_insert_time')) as total_wait_time,
        TIMEDIFF(json_extract(loan_event_time,'$.success_atmpt_start_time'),json_extract(loan_event_time,'$.first_atmpt_end_time')) as ops_wait_time,
        TIMEDIFF(json_extract(loan_event_time,'$.success_atmpt_end_time'),json_extract(loan_event_time,'$.success_atmpt_start_time'))as disbursal_time,
        TIMEDIFF(json_extract(loan_event_time,'$.first_queue_insert_time'),json_extract(loan_event_time,'$.loan_appr_time')) as cs_time,
        TIMEDIFF(json_extract(loan_event_time,'$.manual_disb_end_time'),json_extract(loan_event_time,'$.manual_disb_start_time')) as manual_disb_time, 
        json_extract(loan_event_time,'$.no_of_atmpts') as no_of_atmpts,manual_disb_user_id from loans where loan_doc_id = ?",[$loan_doc_id]);
        
        $data = []; 

        $loan_appl_doc_id = $loan_appl_repo->get_record_by('loan_doc_id',$loan_doc_id,['loan_appl_doc_id']);
        $flow_rel_mgr_id = $disbursal_event_time->flow_rel_mgr_id;
        $rm_time = $disbursal_event_time->rm_time  != null ? Carbon::parse($disbursal_event_time->rm_time)->format('H:i:s') : null;
        
        $total_wait_time = $disbursal_event_time->manual_disb_user_id == null? Carbon::parse($disbursal_event_time->total_wait_time)->format('H:i:s'): null;
        if($disbursal_event_time->manual_disb_user_id != null){
            $disbursal_time = Carbon::parse($disbursal_event_time->manual_disb_time)->format('H:i:s');
        }else{
            $disbursal_time = Carbon::parse($disbursal_event_time->disbursal_time)->format('H:i:s');
        }
        

        $data['loan_appl_doc_id'] =  $loan_appl_doc_id->loan_appl_doc_id;
        $data['loan_doc_id'] = $loan_doc_id;
        $data['flow_rel_mgr_id'] = $flow_rel_mgr_id;
        $data['country_code'] = $disbursal_event_time->country_code;
        $data['rm_time'] = $rm_time;
        $data['total_wait_time'] = $total_wait_time;
        $data['disbursal_time'] = $disbursal_time;
        $data['no_of_attempts'] = $disbursal_event_time->no_of_atmpts;
        $data['cust_conf_channel'] = $disbursal_event_time->cust_conf_channel;
        
        if($disbursal_event_time->cust_conf_channel == 'cust_otp'){
            $cust_time = Carbon::parse($disbursal_event_time->cust_time)->format('H:i:s');
            $data['cust_time'] = $cust_time;
        }elseif ($disbursal_event_time->cust_conf_channel == 'call_log'){
            $cs_time = Carbon::parse($disbursal_event_time->cs_time)->subMinutes(config('app.fa_late1'))->format('H:i:s');
            $data['cs_time'] = $cs_time;
        }

        if($disbursal_event_time->no_of_atmpts > 1){
            $ops_wait_time = Carbon::parse($disbursal_event_time->ops_wait_time)->format('H:i:s');
            
            $data['ops_wait_time'] = $ops_wait_time;
        }
        if($disbursal_event_time->manual_disb_user_id != null){
            $data['disbursal_mode'] = 'manual_disbursal';
        }else{
            $data['disbursal_mode'] = 'instant_disbursal';
        }

        (new LoanEventTime) ->insert_model($data);

    }
  
}
