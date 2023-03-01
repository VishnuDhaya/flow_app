<?php
namespace App\Services\Support;
use App\Exceptions\FlowCustomException;
use App\Jobs\SendSMSJob;
use App\Mail\FlowCustomMail;
use App\Mail\SmsDeliveryReportError;
use App\Models\FlowApp\AppUser;
use App\Models\LoanRecovery;
use App\Services\LoanApplicationService;
use App\Services\LoanRecoveryService;
use Log;
use Carbon\Carbon;
use App\Models\Otp;
use App\SMSTemplate;

use \GuzzleHttp\Client;
use App\Mail\InvalidOtpEmail;
use App\Services\LoanService;
use App\Services\BorrowerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\NonDeliveryReceiptEmail;
use Doctrine\DBAL\Query\QueryException;
use App\Repositories\SQL\OtpRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\SmsLogRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Models\LoanEventTime;
use App\Services\Vendors\SMS\SimplySMSService;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Services\Vendors\SMS\AitSMSService;
use App\Models\Person;
use App\Models\Loan;
use App\Repositories\SQL\LeadRepositorySQL;
use App\Services\Mobile\RMService;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;


class SMSService {

    private $log_sms_data;

    public function __invoke($recipients, $message, $isd_code, $async = true, $log_data = []){
        $connection = $async ? config('queue.default') : 'sync';
        SendSMSJob::dispatch($recipients, $message, $isd_code, $log_data)->onConnection($connection)->onQueue('sms');
    }

    public function send_sms($recipient, $message, $isd_code, $log_data = []){
        Log::warning("-------SMS -------");
		Log::warning($isd_code);
		Log::warning($recipient);
		Log::warning("-------SMS Message Start-------");
		Log::warning($message);
		Log::warning("-------SMS Message End  -------");

		if(env("SEND_SMS", false) == true){

			$recipient = clean_mobnums($isd_code, $recipient);
            $country_code = get_country_code_by_isd($isd_code);

            $vendor = config('app.sms_vendor')[$country_code];
            $VENDOR_SERVICE = $this->get_sms_vendor_service($vendor);

            $result = $VENDOR_SERVICE($recipient, $message, $country_code);
            $status = $result['status'];
            $vendor_ref_id = $result['vendor_id'] ?? null;
            $response = $result['response'] ?? null;
            $log_sms_data = array_merge($log_data,['mobile_num' => $recipient,
                                                        'content' => $message,
                                                        'status' => $status,
                                                        'vendor_code' => $vendor,
                                                        'vendor_ref_id' => $vendor_ref_id,
                                                        'response' => $response]);
            $this->log_outgoing_sms($log_sms_data);
            #TODO add ISD code for multiple $recipients
            
        return $status;

		}else{
			Log::warning("SMS HAS NOT BEEN SENT BECAUSE SETTING IS DISABLED IN .env FILE");
			return true;
		}
    }


	private function log_outgoing_sms($data){

        $data['purpose'] = $data['purpose'] ?? 'notification';

		[$data['mobile_num'], $isd_code] = split_mobile_num($data['mobile_num']);
		$data['country_code'] = get_country_code_by_isd($isd_code);
        $data['direction'] = 'outgoing';

		if(str_contains($data['purpose'],'otp')){
			$otp_repo = new OtpRepositorySQL;
			$otp_repo->update_record_status($data['status'], $data['otp_id']);
		}

		$sms_repo = new SmsLogRepositorySQL;

        if($data['status'] == 'send_failed'){
            $last_id = $sms_repo->get_last_id();
            $last_sms = $sms_repo->find($last_id, ['status', 'vendor_code']);
            if($last_sms && $last_sms->status == 'send_failed' && $last_sms->vendor_code == $data['vendor_code']){
                Mail::to([get_l3_email(), config('app.app_support_email')])
                    ->queue((new FlowCustomMail('sms_send_failure', $data))->onQueue('emails'));
            }
        }

		$sms_repo->insert_model($data);
	}

    public function log_incoming_sms($mobile_num, $message, $isd_code, $purpose, $vendor, $vendor_ref_id = null) {
        $sms_repo = new SmsLogRepositorySQL;
        $country_code = get_country_code_by_isd($isd_code);
		$data = ['mobile_num' => $mobile_num, 'content' => $message, 'status' => 'received',
				 'vendor_ref_id' => $vendor_ref_id, 'purpose' => $purpose, 'direction' => 'incoming',
				 'country_code' => $country_code, 'vendor_code' => $vendor];
		$sms_repo->insert_model($data);
    }



    public function get_otp_code($data,$length = 6){
        
	    $otp = substr(str_shuffle("0123456789"), 0, $length);
	    $gen_time = Carbon::now();
	    $exp_time = Carbon::now()->addMinutes(config('app.otp_validity'));
	    $fields =  ['otp' => $otp, 'otp_type' => $data['otp_type'], 'entity' => $data['entity'],
                    'entity_id' => $data['entity_id'], 'mob_num' => $data['mobile_num'],
                    'generate_time' => $gen_time, 'expiry_time' => $exp_time, 'status' => 'generated',
                    'country_code' => $data['country_code'], 'entity_verify_col' => $data['entity_verify_col'],
                    'entity_update_value' => $data['entity_update_value']];
        if(array_key_exists('cust_id',$data)){
            $fields['cust_id'] = $data['cust_id'];        
        }else if(array_key_exists('lead_id',$data)){
            $fields['lead_id'] = $data['lead_id'];        
        }

       
	    $repo = new OtpRepositorySQL();
        $otp_id = $repo->insert_model($fields);
        return [$otp, $otp_id];
    }

    public function verify_otp_code($data)
    {
       
        try {
            DB::beginTransaction();
            $gen_time_check = Carbon::now()->subMinute(2);
            preg_match('/\d+/', $data['message'], $substrings);
            $otp = $substrings[0];
            $entry = DB::selectOne("select id,otp,entity,entity_id,entity_verify_col,entity_update_value,otp_type,expiry_time, created_by from otps where country_code = ? and mob_num = ?
	                    and (status = 'delivered' or (status = 'sent' and generate_time > '$gen_time_check' )) and otp = ? order by id desc limit 1 ",
                        [session('country_code'), $data['mobile_num'], $otp]);

	    // By adding this condition generate_time > '$gen_time_check',  we allow only 2 mins for AiT to make their best attempt to get the SMS deilivered.
	    //If even after 2 mins, we dont get the delivery notification, we just ignore it.
            $otp_sent_by = null;
            
            if (isset($entry)) {
                $otp_sent_by = $entry->created_by;
                if (Carbon::parse($entry->expiry_time)->lessThan(Carbon::now())) {
                    $otp_validity = 'expired';
                }else {
                    $otp_validity = 'valid';
                    $otp_repo = new OtpRepositorySQL();                   
                    if($entry->entity == 'lead'){
                        $lead_repo = new LeadRepositorySQL;
                        $cust_reg_arr = $lead_repo->get_cust_reg_arr($entry->entity_id);                                                
                        if(Str::contains($entry->entity_verify_col, 'verified_addl_mobile_num') ){
                            $index = filter_var($entry->entity_verify_col, FILTER_SANITIZE_NUMBER_INT);
                            $cust_reg_arr['addl_num'][$index][$entry->entity_verify_col] = (int)$entry->entity_update_value;
                        }else{                            
                            $cust_reg_arr['biz_identity'][$entry->entity_verify_col] =  (int)$entry->entity_update_value;
                        }

                        $lead_repo->update_cust_reg_json($cust_reg_arr, $entry->entity_id);
                    }else{
                        $entity = str_replace(' ', '', 'App\\Models\\'.dd_value($entry->entity));
                        $repo = new $entity();
                        $code_name = $repo::CODE_NAME;
                        $repo->update_model_by_code([$code_name => $entry->entity_id, $entry->entity_verify_col => $entry->entity_update_value]);

                    }
                    if ($entry->entity == 'loan' && $entry->otp_type == 'confirm_fa') {
                        
                        $serv = new LoanService();
                        $loan_repo =new LoanRepositorySQL;
                        $loan_repo->update_loan_event('otp_match_time',$entry->entity_id);
                        $serv->send_to_disbursal_queue($entry->entity_id, null, 'cust_otp', $entry->id);
                     }
                    
                    $otp_repo->update_model(['id' => $entry->id, 'status' => 'received', 'rcvd_msg' => $data['message']]);
                }
            }else {
               $otp_validity = 'invalid';
            }


            if ($otp_validity != 'valid') {
                $this->send_invalid_otp_msg($data, $otp_validity, $otp_sent_by);
            }

            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollback();
            if ($e instanceof QueryException) {
                throw $e;
            }else {
                thrw($e->getMessage());
            }
        }
    }



	private function send_invalid_otp_msg($data, $otp_validity, $otp_sent_by){
        
        $common_repo = new CommonRepositorySQL;
        $brwr_repo = new BorrowerRepositorySQL();
        [$entity,$entity_id] = $common_repo->get_entity_id_from_mobile_num($data['mobile_num']);
        if(!is_array($entity_id) && $entity == 'customer' ){
            $cust = $brwr_repo->get_record_by('cust_id',$entity_id,['acc_prvdr_code', 'biz_name', 'cust_id', 'acc_number', 'ongoing_loan_doc_id']);
            if($otp_sent_by){
                $cs_email = get_user_email($otp_sent_by);
                Mail::to($cs_email)->queue((new InvalidOtpEmail($data, $cust, $otp_validity, session('country_code')))->onQueue('emails'));
            }
            $cs_num = config('app.customer_success_mobile')[$cust->acc_prvdr_code];
        }
        else if($entity == 'lead'){
            $cs_num = 'Relationship Manager';
        }else{
            $cs_num = 'customer success';
        }
       

        $sms_template = ($otp_validity == 'invalid') ? 'INVALID_OTP_SMS' : 'EXPIRED_OTP_SMS';

        $message = compile_sms($sms_template, ['cs_num' => $cs_num]);
        $this($data['mobile_num'], $message, $data['isd_code']);
    }


	public function check_for_valid_otp($otp_type, $entity_id) {
        $country_code = session('country_code');
		$time = Carbon::now();
		$gen_time_check = Carbon::now()->subMinute(2);
		DB::update("update otps set status = 'timed_out' where entity_id = ? and otp_type = ? and status = 'sent' and generate_time < ? and country_code = ?", [$entity_id, $otp_type, $gen_time_check, $country_code]);
		$otps = DB::select("select otp, expiry_time, generate_time from otps where entity_id = ? and otp_type = ? and (status = 'sent' or status = 'delivered') and country_code = ? order by id desc", [$entity_id, $otp_type, $country_code]);
        if(sizeof($otps) > config('app.max_resends')){
            $result = ['status' => true, 'expired' => true];
        }
		elseif(sizeof($otps) > 0) {
            $otp = $otps[0];
            $time_to_retry = Carbon::parse($otp->generate_time)->addMinutes(5);
		    $time_left = $time_to_retry->greaterThan($time) ? $time_to_retry->diffInSeconds($time) : null;
			$result = ['status' => true, 'time_left' => $time_left, 'expired' => false];
		}
		else {
            $result = ['status' => false];
        }
		return $result;
	}


	public function process_sms_delivery_report($vendor_ref_id, $status, $request_object, $mobile_num, $failure_reason = null)
    {
        try {

                DB::beginTransaction();

                $cust = get_cust_from_mobile_num($mobile_num, ['biz_name', 'flow_rel_mgr_id']);

                $repo = new SmsLogRepositorySQL;
                $repo->update_model(['vendor_ref_id' => $vendor_ref_id, 'status' => $status, 'callback_json' => json_encode($request_object)], 'vendor_ref_id');
                $sms = $repo->get_record_by('vendor_ref_id', $vendor_ref_id);

                if (!isset($sms)) {
                    thrw('No record found in sms_logs for the delivery report');
                }
                if (in_array($status, ['delivery_failed', 'rejected'])) {
                    $is_otp = false;
                    if (str_contains($sms->purpose, 'otp')) {
                        $otp_repo = new OtpRepositorySQL;
                        $otp_repo->update_record_status($status, $sms->otp_id);
                        $sms_sent_by = $sms->created_by;
                        
                        $cs_email = null;
                        if($sms_sent_by) {
                            $cs_email = get_user_email($sms_sent_by);
                        }

                        $flow_rm_mail = (new PersonRepositorySQL)->get_person_contacts($cust->flow_rel_mgr_id)->email_id;
                        $email_arr  = [$flow_rm_mail];
                        
                        if($cs_email){
                            array_push($email_arr, $cs_email);
                        }

                        $is_otp = true;

                        Mail::to($email_arr)
                            ->queue((new NonDeliveryReceiptEmail($mobile_num, $status, $cust->biz_name, $failure_reason, session('country_code'), $is_otp))
                            ->onQueue('emails'));
                    }
                }
                DB::commit();
            }
            catch (\Exception $e) {
                DB::rollback();
                Mail::to(config('app.app_support_email'))
                    ->queue((new SmsDeliveryReportError(['vendor_ref_id' => $vendor_ref_id,
                                                        'mobile_num' => $mobile_num,
                                                        'cust_name' => $cust->biz_name,
                                                        'delivery_report_json' => $request_object,
                                                        'exception_msg' => $e->getMessage(),
                                                        ]))->onQueue('emails'));

        }
    }

    public function resend_otp($data)
    {
        try{
            if($data['otp_type'] == 'confirm_fa') {

                $repo = new LoanRepositorySQL();
                $serv = new LoanApplicationService();
                $loan = $repo->get_record_by('loan_doc_id', $data['entity_id']);
                $serv->send_appl_confirmation($loan);
            }
            elseif($data['otp_type'] == 'confirm_recovery') {
                $repo = new LoanRecovery;
                $serv = new LoanRecoveryService();
                $loan_recovery = $repo->find($data['entity_id']);
                $serv->send_recovery_confirmation((array)$loan_recovery);
            }

		}catch (\Exception $e) {
			Log::warning($e->getTraceAsString());
			if ($e instanceof QueryException){
					throw $e;
				}else{
				thrw($e->getMessage());
				}

	    }
    }

    private function get_sms_vendor_service($vendor)
    {
        if($vendor == 'USIS'){
            return new SimplySMSService();
        }
        elseif($vendor == 'UAIT'){
            return new AitSMSService();
        }
    }

    public function search_sms_logs($data){


        $criteria = $data['sms_search_criteria'];

        $add_sql = $this->get_addl_sql($criteria);

        $fields = "vendor_code, vendor_ref_id, mobile_num, status, direction, purpose, content, loan_doc_id, created_at";

        $sms_logs_results = DB::select("select {$fields} from sms_logs where {$add_sql} and country_code = ?  order by created_at desc",[session('country_code')]);
         
        return $sms_logs_results;

    }

    private function get_addl_sql($criteria){

        m_array_filter($criteria);
        $add_sql = "";

        foreach( $criteria as $key => $value){
            if($add_sql != ""){
                $add_sql .= " and ";
            } 
            if($key == 'search_text'){
                $add_sql .= "content LIKE '%{$criteria[$key]}%'";
            }
            else if($key == 'start_date'){  
                $add_sql .= "date(created_at) >= '{$criteria[$key]}'";
            }
            else if($key == 'end_date'){
                $add_sql .= " date(created_at) <= '{$criteria[$key]}'";
            }else{
                $add_sql .="$key = '{$criteria[$key]}'";
            }
        }
        return $add_sql;    
    }

}
