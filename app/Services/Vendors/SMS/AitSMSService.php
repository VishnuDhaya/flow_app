<?php
namespace App\Services\Vendors\SMS;

use App\Mail\FlowCustomMail;
use App\Mail\InvalidOtpEmail;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\SmsLogRepositorySQL;
use App\Services\LoanApplicationService;
use App\Services\LoanService;
use App\Services\Support\SMSNotificationService;
use App\Services\Support\SMSService;
use App\SMSTemplate;
use DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Mail;
use Log;
use Illuminate\Http\Request;
use AfricasTalking\SDK\AfricasTalking;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use Carbon\Carbon;
use Consts;
use App\Models\FARepeatQueue;
use App\Repositories\SQL\LoanApplicationRepositorySQL;

class AitSMSService
{
    const VENDOR_CODE = 'UAIT';

    public function __invoke($recipients,$message, $country_code){
        $credentials = config('app.vendor_credentials')[self::VENDOR_CODE]['SMS-OB'][$country_code];
        $username = $credentials['username'];
        $api_key   = $credentials['api_key'];
        $at     = new AfricasTalking($username, $api_key);

        $sms = $at->sms();
        $this->status = false;
        $response = $sms->send([
            'to'      => $recipients,
            'message' => $message
        ]);
        Log::warning('Message-AiT');
        Log::warning($response);
        $recipient_stats = [];
        foreach ($response['data']->SMSMessageData->Recipients as $rec){
            $recipient_stats[] = ['status' => $rec->status == 'Success' ? 'delivered' : 'send_failed',
                                  'response' => $rec->status,
                                  'mobile_num' => $rec->number, 'vendor_id' => $rec->messageId];
        }
        if($response['status'] == 'success'){
            $this->status = true;
            if(sizeof($recipient_stats) == 1){
                $result =  ['status' => $recipient_stats[0]['status'],
                            'response' => $recipient_stats[0]['response'],
                            'vendor_id' => $recipient_stats[0]['vendor_id']];
            }
            else{
                $result = ['status' => $this->status, 'recipient_stats' => $recipient_stats];
            }
        }
        else{
            $result = ['status' => 'send_failed'];
        }

        return $result;

    }



    public function process_inbound_sms(Request $req){
        

            $data = $this->get_message($req);
            
            $data['ref_id'] = $req->id;
            $sms_serv = new SMSService;
            $sms_serv->log_incoming_sms($data['mobile_num'], $data['message'], $data['isd_code'], null, self::VENDOR_CODE, $data['ref_id']);
            $brwr_repo = new BorrowerRepositorySQL;
            $common_repo = new CommonRepositorySQL();
            [$entity,$entity_id] = $common_repo->get_entity_id_from_mobile_num($data['mobile_num']);
            #$cust_id = $brwr_repo->get_cust_id_from_mobile_num($data['mobile_num']);
            if(!$entity_id){
                $purpose = $this->handle_unregistered_cust_number($data);
            }
            else{
                $purpose = 'ib_sms_unknown_text';
                if(preg_match('/FLOW/i', $data['message'])){
                    
                    if(preg_match('/repeat/i',$data['message'])){
                       
                        $purpose = 'ib_sms_repeat_fa';
                    }
                    elseif(preg_match('/(?<!\d)\d{6}(?!\d)/', $data['message'])){
                       
                        $purpose = 'ib_sms_otp';
                    }
                }
            }

            $sms_repo = new SmsLogRepositorySQL();
            $sms_repo->update_model(['purpose' => $purpose, 'vendor_ref_id' => $data['ref_id']], 'vendor_ref_id');

            if($purpose == 'ib_sms_otp') {
                $sms_serv->verify_otp_code($data);
            }
            elseif($purpose == 'ib_sms_repeat_fa'){
                if(is_array($entity_id)){
                    $sms_repo->update_model(['purpose' => 'duplicate', 'vendor_ref_id' => $data['ref_id']], 'vendor_ref_id');
                    $data['cust_ids'] = $entity_id;
                    $data['mobile_field'] = 'primary';
                    Mail::to(config('app.app_support_email'))->queue((new FlowCustomMail('duplicate_profile_sms', $data))->onQueue('emails'));
                }
                else{
                    $this->process_repeat_fa($entity_id, $data['mobile_num']);
                }
            }
    }



    public function get_message(Request $req){

            $mobile_num = $req->from;

            $message = $req->text;

            [$mobile_num, $isd_code] = split_mobile_num($mobile_num);


            return ['mobile_num' => $mobile_num, 'message' => $message, 'isd_code' => $isd_code];
        }


    public function process_sms_delivery_report(Request $req){

            $ait_ref_id = $req->id;
            $status = $req->status;
            if ($status == "Success") {
                $status = "delivered";
            }else if ($status == "Failed") {
                $status = 'delivery_failed';
            }else {
                $status = strtolower($status);
            }

            $failure_reason = $req->failureReason ?? null;
            [$mobile_num, $isd] = split_mobile_num($req->phoneNumber);
            $sms_serv = new SMSService();
            // $sms_serv->process_sms_delivery_report($ait_ref_id, $status, $req->all(), $mobile_num, $failure_reason);
        }

    public function process_repeat_fa($cust_id, $mobile_num)
    {
        $message = null;
        try{
            $brwr_repo = new BorrowerRepositorySQL();
            $cust = $brwr_repo->find_by_code($cust_id, ['cust_id', 'biz_name', 'last_loan_doc_id', 'acc_prvdr_code']);
            $repeat_fa_result = $this->check_and_queue_repeat_fa($cust_id, $mobile_num);
            if($repeat_fa_result['queued']){
                if($repeat_fa_result['send_sms']){
                    $message = "REPEAT_FA_QUEUE";
                }
                return;
            }
            if($repeat_fa_result['applied_repeat_fa']){
                $message = 'REPEAT_FA_MSG';
            }
        }
        catch(\Exception $e){
            Log::error($e);
            $msg = $e->getMessage();
            $cust->mobile_num = $mobile_num;
            $recipient = get_last_fa_applier_email($cust_id) ?? get_csm_email();
            Mail::to([$recipient, config('app.app_support_email')])->queue((new FlowCustomMail('repeat_loan_failure', ['cust' => $cust, 'country_code' => session('country_code'), 'msg' => $msg]))->onQueue('emails'));
            $err_code = isset($e->err_code) ? $e->err_code : null;
            $message = $this->get_repeat_fa_failure_msg($err_code);
        }
        finally{
            $acc_repo = new AccountRepositorySQL();
            $cust_acc = $acc_repo->get_record_by_many(['cust_id','status'],[$cust_id,'enabled'],['acc_number']);
            $notify_serv = new SMSNotificationService();
            if($message == 'MERCHANT_CHANGE_FA_REPEAT'){
                         $acc_number = $cust_acc->acc_number;
            
            }
            if(isset($message)){
                $cs_num = config('app.customer_success_mobile')[$cust->acc_prvdr_code];
                // $message .= "\nIf you have any queries, please contact customer success at $cs_num";
                $notify_serv = new SMSNotificationService();
                $notify_serv->send_notification_message(['cust_mobile_num' => $mobile_num,
                                                        'country_code' => session('country_code'),
                                                        'cs_num'=>$cs_num,'acc_number'=> $cust_acc->acc_number], $message);
            }
        }
    }

    private function handle_unregistered_cust_number(array $data)
    {
        $person_repo = new PersonRepositorySQL;
        $brwr_repo = new BorrowerRepositorySQL;
        $notify_serv = new SMSNotificationService();

        $cust_id = $brwr_repo->get_cust_id_from_mobile_num($data['mobile_num'], true);
        if(!$cust_id){
            $notify_serv->send_notification_message(['cust_mobile_num' => $data['mobile_num'],
                                                     'country_code' => session('country_code')], 'UNKNOWN_NUM_SMS_RESPONSE');
            $purpose = 'ib_sms_unreg';

        }
        elseif(is_array($cust_id)){
            $data['cust_ids'] = $cust_id;
            $data['mobile_field'] = 'alternate';
            Mail::to(config('app.app_support_email'))->queue((new FlowCustomMail('duplicate_profile_sms', $data))->onQueue('emails'));
            $purpose = 'dupl_alt_num';
        }
        else{
            $cust = $brwr_repo->get_record_by('cust_id',$cust_id,['ongoing_loan_doc_id', 'biz_name', 'cust_id', 'acc_number', 'owner_person_id']);
            $reg_mobile_num = $person_repo->get_mobile_num($cust->owner_person_id);

            $notify_serv->send_notification_message(['reg_mobile_num' => $reg_mobile_num,
                                                     'cust_mobile_num' => $data['mobile_num'],
                                                     'country_code' => session('country_code') ], 'ALT_NUM_SMS_RESPONSE');
            $purpose = 'ib_sms_alt_num';
            $recipient = config('app.app_support_email');
            Mail::to($recipient)->queue((new InvalidOtpEmail($data, $cust, 'alt_num'))->onQueue('emails'));
        }
        #TODO send to DLQ
        return $purpose;
    }

    private function get_repeat_fa_failure_msg($err_code)
    {
        
        if($err_code == 'disabled_cust'){
            $message = 'DISABLED_CUST_REPEAT_FA';
        }
        else if($err_code == 'pending_fa_appl'){
            $message = 'PENDING_FA_APPL_FA_REPEAT';
        }
        else if($err_code == 'ongoing_fa'){
            $message = 'LAST_FA_OS_REPEAT_FA';
        }
        else if($err_code == 'merchant_change'){
            $message = 'MERCHANT_CHANGE_FA_REPEAT';
        }
        else if($err_code == 'fa_repeat_queue'){
            $message = 'REPEAT_FA_QUEUE';
        }
        else{
           $message = "UNABLE_TO_REPEAT_FA";
        }
        return $message;
    }

    public function check_and_queue_repeat_fa($cust_id, $mobile_num){
        $today_date = Carbon::now();
        $queued = $send_sms = $applied_repeat_fa = false;

        $disbursed_loan_status = Consts::DISBURSED_LOAN_STATUS; // overdue, due, ongoing
        $os_loan = (new LoanRepositorySQL())->get_loan_by_status($cust_id, $disbursed_loan_status, 'float_advance');
        
        if(count($os_loan) == 1){
            $result = (new FARepeatQueue)->get_record_by_many(['loan_doc_id', "date(created_at)"], [$os_loan[0]->loan_doc_id, date_db($today_date)]);
            $queued = true;
            if($result == null){
                $data = ['country_code' => session('country_code'), 'cust_id' => $cust_id, 'loan_doc_id' => $os_loan[0]->loan_doc_id, 'mobile_num' => $mobile_num, 'status' => 'requested', "created_at" => now()];
                (new FARepeatQueue)->insert($data);
                $send_sms = true;
            }
        }else if(count($os_loan) > 1){
            thrw("More than one outstanding loan for this customer {$os_loan[0]->cust_id}");
        }else{
            $last_loan = (new LoanRepositorySQL())->get_last_loan_by_purpose($cust_id, $today_date, 'float_advance');
            $loan_appl_pending_status = Consts::LOAN_APPL_PNDNG_APPR;
            $loan_appl = (new LoanApplicationRepositorySQL)->get_loan_appl_by_status($cust_id, $loan_appl_pending_status , 'float_advance');

            if($loan_appl == null){

                $last_loan_appl = (new LoanApplicationRepositorySQL)->get_last_loan_appl_by_purpose($cust_id, $today_date, 'float_advance');
                if($last_loan_appl && $last_loan_appl->status == Consts::LOAN_APPL_REJECTED){
                    thrw("Unable to repeat previous FA because your last FA application was cancelled by RM.");
                }
                session()->put('acc_prvdr_code', $last_loan->acc_prvdr_code);
                $appl_serv = new LoanApplicationService();
                $result = $appl_serv->repeat_fa(['cust_id' => $cust_id, 'loan_doc_id' => $last_loan->loan_doc_id]);
                if(array_key_exists('loan_application', $result)){
                    $applied_repeat_fa = true;
                }
            }
            

        }
        return ['queued' => $queued, 'send_sms' => $send_sms, 'applied_repeat_fa' => $applied_repeat_fa];
    }

    public function get_balance($country_code){

        $credentials = config('app.vendor_credentials')[self::VENDOR_CODE]['SMS-OB'][$country_code];

        $username = $credentials['username'];
        $api_key = $credentials['api_key'];

        // Initialize the SDK
        $AT = new AfricasTalking($username, $api_key);

        // Get the application service
        $application = $AT->application();

        $exc_message = "";
        $balance = "";

        try{
            // Fetch the application data
            $data = $application->fetchApplicationData();
            $balance = $data['data']->UserData->balance;
            $status = $data['status'];
        }
        catch(\Exception $e){
            $status = "failure";
            $exc_message = $e->getMessage();
        }

        return ["status" => $status, "message" => $exc_message, "balance" => $balance, ];

    }

}
