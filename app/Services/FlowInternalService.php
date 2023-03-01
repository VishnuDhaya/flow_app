<?php

namespace App\Services;

use App\Mail\FlowCustomMail;
use App\Repositories\Eloquent\MarketRepository;
use App\Repositories\SQL\OtpRepositorySQL;
use App\Services\Support\SMSService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Repositories\SQL\DisbursalAttemptRepositorySQL;
use App\Repositories\SQL\AccountStmtRepositorySQL;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\MarketRepositorySQL;
use App\Repositories\SQL\SmsLogRepositorySQL;
use App\Repositories\SQL\AccProviderRepositorySQL;
use App\Services\Schedule\ScheduleService;
use App\Services\AutoCapturePaymentService;
use App\Consts;
use Carbon\Carbon;
use App\Services\Support\FireBaseService;


class FlowInternalService{
    public function log_forwarded_otp ($data)
    {
        try {

            $country = (new MarketRepositorySQL())->get_market_info(session('country_code'));
            $sms_serv = new SMSService();

            $sms_serv->log_incoming_sms($data['mob_num'], $data['otp'], $country->isd_code, 'disb_portal_verify', $data['vendor']);

            $fields = ['otp' => $data['otp'], 'otp_type' => 'disb_portal_verify', 'entity' => $data['entity'],
                'entity_id' => $data['agent_id'], 'mob_num' => $data['mob_num'],
                'generate_time' => $data['received_at'], 'expiry_time' => null, 'status' => 'received',
                'cust_id' => 'null', 'country_code' => $country->country_code];
            $repo = new OtpRepositorySQL();
            $repo->insert_model($fields);
        }catch(Exception $e){
            $data['err_msg'] = $e->getMessage();
            $data['country_code'] = session('country_code');
             Mail::to(config('app.app_support_email'))->send(new FlowCustomMail('process_util_otp', $data));
         }
    }

    public function auto_capture($txn_type, $account_id, $acc_stmt_id, $acc_prvdr_code){

        $schedule_serv = new ScheduleService();
        $acc_stmt_repo = new AccountStmtRepositorySQL();

        if($txn_type == 'credit'){
            $schedule_serv->recon_credit_txns($account_id, $acc_prvdr_code, 111, $acc_stmt_id);
            (new AutoCapturePaymentService)->auto_capture($account_id, Consts::PENDING_STMT_IMPORT, $acc_stmt_id);
            $acc_stmt_repo->update_model(["sms_import_status" => Consts::SMS_IMPORT_DONE, "id" => $acc_stmt_id]);
        }
        else if($txn_type == 'debit'){
            $schedule_serv->recon_debit_txns($account_id, $acc_prvdr_code, 111, $acc_stmt_id);
            $acc_stmt_repo->update_model(["sms_import_status" => Consts::SMS_IMPORT_DONE, "id" => $acc_stmt_id]);
        }
    }

    public function process_transaction_sms($data)
    {
        $account = (new AccountRepositorySQL())->get_account_by(['acc_number', 'status'],[$data['agent_id'], Consts::ENABLED],['id','network_prvdr_code','acc_prvdr_code', 'process_txn_sms']);

        if(isset($account->process_txn_sms) && $account->process_txn_sms){

            try{

                DB::beginTransaction();

                $date_obj = Carbon::parse($data['txn_date']);

                $addl_sql = "";

                if ($data['txn_type'] == "debit" || $data['country_code'] == "UGA"){
                    $addl_sql = "and stmt_txn_id = '{$data['txn_id']}'";
                }
                $result = DB::selectOne("select id, sms_import_status, date(stmt_txn_date), recon_status from account_stmts where (ROUND(amount) = ? or TRUNCATE(amount, 0) = ?) and account_id = ? and country_code= ? and date_format(stmt_txn_date, '%Y-%m-%d %H:%i') = ? $addl_sql order by id desc limit 1",[$data['txn_amount'], $data['txn_amount'], $account->id, $data['country_code'], $date_obj->format('Y-m-d H:i')]);

                if (!$result){
                    
                    $sms_logs_repo = new SmsLogRepositorySQL();
                    $sms_log_id = $sms_logs_repo->create(['country_code' => $data['country_code'], 'mobile_num' => substr($data['msisdn'], 3), 'status' => 'received', 'direction' => 'otp-app', 'vendor_code' => $data['acc_prvdr_code'], 'content' => $data['message']]);

                    $acc_stmt_repo = new AccountStmtRepositorySQL();

                    $stmt_data['account_id'] = $account->id;
                    $stmt_data['acc_prvdr_code'] = $account->acc_prvdr_code;
                    $stmt_data['network_prvdr_code'] = $account->network_prvdr_code;
                    $stmt_data['ref_account_num'] = $data['msisdn'];
                    $stmt_data['acc_number'] = $data['agent_id'];
                    $stmt_data['stmt_txn_date'] = $data['txn_date'];
                    $stmt_data['stmt_txn_type'] = $data['txn_type'];
                    $stmt_data['amount'] = $data['txn_amount'];
                    $stmt_data['balance'] = $data['balance'];
                    $stmt_data['import_id'] = 0;
                    $stmt_data['country_code'] = $data['country_code'];
                    $stmt_data['created_by'] = 0;
                    $stmt_data['sms_log_id'] = $sms_log_id;
                    $stmt_data['sms_import_status'] = Consts::SMS_IMPORT_INPROGRESS;
                    $stmt_data['source'] = Consts::SMS_CAPTURE;

                    unset($data['token'], $data['sender'], $data['request_attempt']);
                    $stmt_data['sms_content'] = $data;
                    
                    if ($data['acc_prvdr_code'] == 'RATL'){

                        $stmt_data['descr'] = (string)$data['msisdn']."/".$data['cust_name'];
                        $stmt_data['stmt_txn_id'] = $data['txn_id'];
                        ($data['txn_type'] == 'credit') ? ($stmt_data['cr_amt'] = $data['txn_amount']) : ($stmt_data['dr_amt'] = $data['txn_amount']);
                        $acc_stmt_id = $acc_stmt_repo->insert_model($stmt_data);
                        
                        $this->auto_capture($data['txn_type'], $account->id, $acc_stmt_id, $data['acc_prvdr_code']);
                    }
                    else if($data['country_code'] == "UGA"){

                        $stmt_data['descr'] = (string)$data['remarks']."/".$data['cust_name'];
                        $stmt_data['stmt_txn_id'] = $data['txn_id'];
                        ($data['txn_type'] == 'credit') ? ($stmt_data['cr_amt'] = $data['txn_amount']) : ($stmt_data['dr_amt'] = $data['txn_amount']);
                        $acc_stmt_id = $acc_stmt_repo->insert_model($stmt_data);

                        $this->auto_capture($data['txn_type'], $account->id, $acc_stmt_id, $data['acc_prvdr_code']); 
                    }
                    else if ($data['country_code'] == "RWA") {

                        if ($data['txn_type'] == 'credit'){
                            $stmt_data['descr'] = (string)$data['remarks']."/".$data['cust_name'];
                            $stmt_data['stmt_txn_id'] = $data['msisdn']."-".(strtotime($data['txn_date']));
                            $stmt_data['cr_amt'] = $data['txn_amount'];
                            $acc_stmt_id = $acc_stmt_repo->insert_model($stmt_data);

                            $this->auto_capture($data['txn_type'], $account->id, $acc_stmt_id, $data['acc_prvdr_code']);
                        }
                        if ($data['txn_type'] == 'debit'){
                            $stmt_data['descr'] = "/".$data['cust_name'];
                            $stmt_data['dr_amt'] = $data['txn_amount'];
                            $stmt_data['stmt_txn_id'] = $data['txn_id'];
                            $acc_stmt_id = $acc_stmt_repo->insert_model($stmt_data);

                            $this->auto_capture($data['txn_type'], $account->id, $acc_stmt_id, $data['acc_prvdr_code']);

                        }
                    }

                }

                DB::commit();

            }
            catch(\Exception $e){
                
                DB::rollBack();
                $data = ['country_code' => session('country_code'), 'failed_at' => Carbon::now(), 'exception' => $e->getMessage(), 'account_id' => $account->id, 'acc_num' => $data['agent_id']]; 
                send_email('sms_import_failure', [config('app.app_support_email')], $data);
                thrw($e->getMessage());
            
            }
        
        }

    }

    public function switch_sms_import($acc_number, $value){
        $messenger_serv = new FireBaseService();
        $acc_mobile_creds = (new AccountRepositorySQL())->get_account_by(['acc_number', 'status'], [$acc_number, Consts::ENABLED], ['mobile_cred']);
        if (isset($acc_mobile_creds->mobile_cred->msg_token)){
            $messenger_serv(['action' => 'switch_sms_import', 'data' => $value], $acc_mobile_creds->mobile_cred->msg_token, false);
        }
    }

    public function email_insufficient_balance($data){

        $mail_data = ['country_code' => session('country_code'), 'acc_prvdr_code' => $data['acc_prvdr_code'], 'acc_number' => $data['acc_number'], 'current_datetime' => Carbon::now()];
        $ops_admin_email = get_ops_admin_email($mail_data['country_code']);
        $market_admin_email = get_market_admin_email($mail_data['country_code']);
        send_email('email_insufficient_balance', [config('app.app_support_email'), get_l3_email(), $ops_admin_email, $market_admin_email], $mail_data);

    }

}
