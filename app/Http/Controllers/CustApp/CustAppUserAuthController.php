<?php

namespace App\Http\Controllers\CustApp;

use App\Http\Controllers\ApiController;
use App\Repositories\SQL\AccProviderRepositorySQL;
use App\Services\BorrowerService;
use App\Mail\FlowCustomMail;
use App\Models\Task;
use App\Services\Mobile\CustAppService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\OtpRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Services\Support\SMSNotificationService;
use App\Services\Support\SMSService;
use App\Services\LeadService;
use App\Services\TaskService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use JWTAuth;
use Response;
use Illuminate\Support\Facades\Log;
use App\Validators\FlowValidator;



//use App\Http\Controllers\ApiController;
//https://github.com/tymondesigns/jwt-auth/wiki/Creating-Tokens

class CustAppUserAuthController extends ApiController
{
    use AuthenticatesUsers;

    protected $maxAttempts = 5;
    protected $decayTime = 900;

    public function validate_mobile_num(Request $request){
        $mobile_num = $request['mobile_num'];
        $isd = $request['isd_code'];
        $country_code = get_country_code_by_isd($isd);
        $resp = DB::selectOne("select id,status,person_id from app_users where role_codes = 'customer' and mobile_num = {$mobile_num} and country_code = '{$country_code}'");
        if($resp){
            session(['country_code' => $country_code]);
            if($resp->status == 'disabled'){
                return $this->respondWithError("Your mobile login has been disabled, Please contact customer success at " . config('app.customer_success')['XXX']);

            }

            if(isset($request['check'])){
                $result['cust_app_access'] = 'has_app_access'; 
                return $this->respondData($result);
            }
            $confirmation = $this->get_cust_verification_otp($resp->person_id,$mobile_num);
            $cust_name = (new PersonRepositorySQL())->get_full_name($resp->person_id);
            $sms_serv = new SMSNotificationService();
            $sms_serv->send_cust_app_otp(['cust_mobile_num' => $mobile_num, 'country_code' => $country_code, 'otp_code' => $confirmation[0], 'otp_id' => $confirmation[1]]);

            $response_data = ['v_id' => $confirmation[1],'country_code' => $country_code, 'cust_name' => $cust_name];
            if(env('APP_ENV') != 'production'){
                $response_data['otp_code'] = $confirmation[0];
            }
            return $this->respondData($response_data);
        }
        else{
            
            $borrower_status = DB::selectOne("select p.id, b.owner_person_id, b.status,b.cust_id  from  borrowers b , persons p where p.id = b.owner_person_id and b.status = 'enabled' and p.mobile_num = ?",[$mobile_num]);

            if(isset($borrower_status) && $borrower_status->status == 'enabled'){
                    $cust_request = (new Task)->get_record_by_many(['cust_id','status'] ,[$borrower_status->cust_id,'requested'], ['status']);
                    if(isset($cust_request)){
                        $result['cust_app_access'] = "pending_access_request";
                        $message = "Your request for the customer app access is pending with the Relationship Manager";
                        return $this->respondData($result,$message);      
                    }else{
                        $result['cust_app_access'] = 'no_app_access';
                        return $this->respondData($result);
                    }
                    
            }else{
                return $this->respondWithError("You are not a KYC registered customer of FLOW. Register with us using the below 'Register' link.");
            } 

        }

    }

    public function verify_otp(Request $request)
    {
        $mobile_num = $request['mobile_num'];
        $otp = $request['otp'];
        $id = $request['v_id'];
        $country_code =  $request['country_code'];
        session(['country_code' =>$country_code]);
        $app_user = db::selectOne("select person_id from app_users where mobile_num = {$mobile_num} and status ='enabled' and country_code = '{$country_code}'");
//        $person = (new PersonRepositorySQL())->get_record_by_many(['mobile_num','status'],[$mobile_num,'enabled'],['id']);
        session()->put('user_id',$app_user->person_id);
        $otp_repo = new OtpRepositorySQL();
        $resp = $otp_repo->get_record_by_many(['id','otp','mob_num'],[$id,$otp,$mobile_num],['expiry_time']);
        if($resp){
            $expiry = carbon::parse($resp->expiry_time);
            $time_now = carbon::now();
            if($expiry > $time_now){
                $otp_repo->update_model(['mob_num' => $mobile_num,'status' => 'received'],'mob_num');
                $android_version = (string)$request['android_version'];
                DB::table('app_users')->where('mobile_num',$mobile_num)->where('country_code',$country_code)->update(['android_version' => $android_version]);
                return $this->respondData(['verified' => true]);
            }
            else{
                return $this->respondWithError('OTP Expired, please click on Resend OTP ');
            }
        }
        else{
            return $this->respondWithError('Invalid OTP');
        }

    }

    public function get_cust_verification_otp($person_id,$mobile_num)
    {
        $borrower_rep = new BorrowerRepositorySQL();

        $borrower = $borrower_rep->get_record_by('owner_person_id',$person_id,['cust_id','country_code']);
        $otp_serv = new SMSService();
        $gen_time = carbon::now();
        $exp_time = $gen_time->addMinutes(15);
        $confirm_code = $otp_serv->get_otp_code(['cust_id' => $borrower->cust_id, 'otp_type' => 'cust_app_ver','mobile_num' => $mobile_num,'country_code'=>$borrower->country_code, 'generate_time' => $gen_time, 'expiry_time' => $exp_time, 'entity' => null, 'entity_id' => null, 'entity_verify_col' => null, 'entity_update_value' => null],4);

        return $confirm_code;
    }

    public function set_cust_app_pin(Request $request){
        $mobile_num = $request['mobile_num'];
        $pin = $request['pin'];
        $id = $request['v_id'];
        session(['country_code' => $request['country_code']]);

        $verfication = (new OtpRepositorySQL())->get_record_by_many(['id','mob_num'],[$id,$mobile_num],['id']);
        if($verfication) {
            DB::table('app_users')->where('mobile_num', $mobile_num)->where('role_codes', 'customer')->update(['password' => bcrypt($pin)]);
            DB::table('app_users')->where('mobile_num', $mobile_num)->where('is_new_user', 1)->update(['is_new_user'=> 0]);
            return $this->respondData(['set_pin' => true]);
        }

        return $this->respondWithError('Unable to set PIN, Please try again later');
    }

    public function username()
    {
        return 'mobile_num';
    }

    public function login(Request $request)
    {
        $mobile_num = $request['mobile_num'];
        $password = $request['pin'];
        session(['country_code' => $request['country_code']]);
        $messenger_token = $request['messenger_token'];
        $app_version = $request['app_version'];

        $credentials = ["mobile_num" => $mobile_num, "password" => $password, 'country_code' => session('country_code')];

        $person_repo = new PersonRepositorySQL();
        $borrower_repo = new BorrowerRepositorySQL();

        //dd($credentials);
        JWTAuth::factory()->setTTL(60);
        JWTAuth::getJWTProvider()->setSecret(env('CUST_JWT_SECRET'));
        $token = auth()->attempt($credentials);
        if (! $token ) {
            $this->incrementLoginAttempts($request);
            $attempts = $this->limiter()->attempts($this->throttleKey($request));
            $seconds = $this->limiter()->availableIn($this->throttleKey($request));
            if ($this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);
                $this->send_cust_app_lockout_mail($mobile_num,$attempts);
                return $this->respondWithErrorAndData("Too many attempts",['attempts' => $attempts, 'time_remaining' => $seconds]);

            }

            return $this->respondWithError("Invalid PIN number, attempt remaining ".($this->maxAttempts - $attempts));
        }

        if(auth()->user()->status != 'enabled'){
            return $this->respondWithError("Your mobile login has been disabled, Please contact customer success at" . config("app.customer_success")['XXX']);
        }

        $person_id = auth()->user()->person_id;
        $user_id = auth()->id();
        $borrower = $borrower_repo->get_record_by('owner_person_id',$person_id,['category','flow_rel_mgr_id','cust_id']);
        $person_name = $person_repo->get_person_name($person_id,['first_name','last_name']);
        $data['category'] = $borrower->category;
        $data['cust_name'] = $person_name;
        $person_pps = $person_repo->find($person_id,['photo_pps']);
        $rm_pps = $person_repo->find($borrower->flow_rel_mgr_id,['photo_pps']);
        $data['photo_pps_path'] = get_file_path("persons",$person_id,"photo_pps")."/s_".$person_pps->photo_pps;
        if($rm_pps->photo_pps){
            $data['rm_photo_pps_path'] = get_file_path("persons",$borrower->flow_rel_mgr_id,"photo_pps")."/s_".$rm_pps->photo_pps ;
        }
        $data['flow_rel_mgr_name'] =  (new PersonRepositorySQL())->get_person_name($borrower->flow_rel_mgr_id,['first_name','middle_name','last_name']);
        $data['acc_prvdr_logos'] = config('app.acc_prvdr_logo')[session('country_code')];
        $data['acc_num_label'] = config('app.acc_num_label')[session('country_code')];
        $data['acc_prvdr_name'] = (new AccProviderRepositorySQL())->get_all_acc_prvdr_name_by_country(session('country_code'));
        $currency = (new CommonRepositorySQL())->get_currency_code(session('country_code'));
        $data['country_code'] = session('country_code');
        $data['currency_code'] = $currency->currency_code;
        $data['acc_prvdr_support_ussd'] = config('app.acc_prvdr_support_ussd')[session('country_code')];
        $data['network_operator_code'] = config('app.network_operator_code')[session('country_code')];
        $data['get_mobile_no_ussd'] = config("app.get_mobile_no_ussd")[session('country_code')];
        $data['conseq_late_pay'] = config('app.conseq_late_pay');
        $data['performance'] = (new CustAppService())->get_performance($borrower->cust_id);
        $data['cust_loan_details'] = (new CustAppService())->get_cust_loan_details($borrower->cust_id);
        m_array_filter($data['acc_prvdr_logos']);
        $time = Carbon::now();
        DB::update("update app_users set messenger_token = '{$messenger_token}', app_version = '{$app_version}', updated_at = '{$time}', updated_by = {$person_id} where id = {$user_id}");
        $this->clearLoginAttempts($request);
        return $this->respondWithTokenOnly($token,$data);
    }

    public function logout(Request $req)
    {
        auth()->logout();
        return parent::respondWithMessage('Successfully logged out');
        //return response()->json   (['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        $token = $request->token;
        JWTAuth::factory()->setTTL(180);
        return $this->respondWithRefreshToken(auth()->refresh($token));
    }
    public function me()
    {
        return response()->json(auth()->user());
    }

    public function send_cust_app_lockout_mail($mobile_num,$attempts){
        $data=array();
        $person_repo = new PersonRepositorySQL();
        $borrower_repo = new BorrowerRepositorySQL();
        $person = $person_repo->get_record_by('mobile_num',$mobile_num,['id','country_code']);
        $data['cust_name'] = $person_repo->full_name($person->id);
        $data['mobile_num'] = $mobile_num;
        $data['attempts'] = $attempts;
        $data['cust_id'] = $borrower_repo->get_cust_id($person->id)->cust_id;
        $data['last_attempt'] = Carbon::now();
        $data['country_code'] = $person_repo->country_code;
        Mail::to(config('app.app_support_email'))->send(new FlowCustomMail('cust_app_lockout', $data));
    }

    public function create_lead(Request $req){
        
        $data = $req->data;
        $lead_data['lead'] = $data;
        $lead_data['country_code'] = session("country_code");

        if($data['type'] == "cust_lead"){
            $lead_data['lead']['acc_purpose'][] = 'float_advance';
            if(array_key_exists('location',$lead_data['lead'])){
                $gps_arr = explode(",",$lead_data['lead']['location']);
                if(count($gps_arr) > 1){
                    $lead_data['lead']['latitude'] = $gps_arr[0];
                    $lead_data['lead']['longitude'] = $gps_arr[1];
                }
            }
        }

        $lead_serv  = new LeadService();
        $check_validate = FlowValidator::validate($lead_data, array("lead"), $data['type']);
        if(is_array($check_validate)){
            return $this->respondValidationError($check_validate);
        }
        $lead_id = $lead_serv->create_lead($lead_data);
        if( $lead_id ){
            $this->send_rm_alloc_email_notification($data, $lead_id);
        }
        if($data['type'] == 'refer_agent'){
            return $this->respondData($lead_id,"You have successfully referred an agent. Our Relationship Manager will reach the agent");
        }else{
            return $this->respondData($lead_id,"Your registration with FLOW is successfull, Our Relationship Manager will reach you within the next 3 business days");
        }
    }

    public function send_rm_alloc_email_notification($data, $lead_id){
        
		$mail_data = ['country_code' => session('country_code'), 'cust_name' => "{$data['first_name']} {$data['last_name']}" ,
					  'id' => $lead_id ];

        if(array_key_exists('acc_prvdr_code', $data) &&  array_key_exists('acc_number', $data)){
            $mail_data['acc_prvdr_code'] = $data['acc_prvdr_code'];
            $mail_data['account_num'] = $data['acc_number'];
        }

        Mail::to(get_ops_admin_email())->queue((new FlowCustomMail('lead_rm_assign', $mail_data))->onQueue('emails'));
    }
    
    public function init_check(Request $request)
    {
        $country_code = session("country_code");
        $lastest = config("app.cust_app_version");
        $crnt = $request["app_version"];
        $update = "";
        $level = check_for_update_level($lastest, $crnt);
        if($level == 0){
            $update = "nill";
        }
        elseif ($level == 1){
            $update = "tweak";
        }
        elseif ($level > 1){
            $update = "major";
        }
        $language_array = $this->get_language_json($country_code);
        $lang = $language_array['language_json'];
        $default_lang = $language_array['default_language'];
        $data['ap_ac_num_name'] = config('app.ap_ac_num_name');
        $data['ap_alt_ac_num_name'] = config('app.ap_alt_ac_num_name');
        $data['ap_ac_num_hint'] = config('app.ap_ac_num_hint');
        $data['ap_alt_ac_num_hint'] = config('app.ap_ac_num_hint');
        (new AccProviderRepositorySQL())->get_all_acc_prvdr_name_by_country(session('country_code'));

        return $this->respondData(["force_upgrade" => $update, "lang" => $lang, "default" => $default_lang, "mobile_config" => $data ]);
    }

    public function get_language_json($country_code, $send_mail = false)
    {
        $default_lang = config("app.cust_app_default_lang")[$country_code] ?? "en";
        $common_lang_array = json_decode(file_get_contents(".././resources/lang/en/cust_app.json"), true);
        $default_lang_array = json_decode(file_get_contents(".././resources/lang/".$default_lang."/cust_app.json"), true);
        $missing_keys = [];
        if($default_lang != 'en'){
            $missing_keys = array_diff_key($common_lang_array, $default_lang_array);
        }
        $lang[$default_lang] = $default_lang_array + $missing_keys;
        if($send_mail && count($missing_keys) > 0){
            $mail_data = ['country_code' => $country_code, 'missing_keys' => $missing_keys, 'default_lang' => $default_lang];
            Mail::to(config('app.app_support_email'))->send((new FlowCustomMail('missing_translation', $mail_data)));
        }
        return ['language_json' => $lang, 'default_language' => $default_lang];
    }

    public function request_cust_app_access(Request $req){
      
        $brow_repo = new BorrowerRepositorySQL();
        $data['country_code'] = get_country_code_by_isd($req['isd_code']);
        $data['cust_id'] = $brow_repo->get_cust_id_from_mobile_num($req['mobile_num']);    
        $data['rm_id'] = $brow_repo->get_flow_rel_mgr_id($data['cust_id']);
        $data['task_type'] = $req['task_type'];
        $data['device_info'] = $req['dvic_info'];
        $create_task = (new TaskService)->create_task($data);
        return $this->respondData($create_task); 
    }
}  