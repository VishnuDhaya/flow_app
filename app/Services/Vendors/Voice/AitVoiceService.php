<?php
namespace App\Services\Vendors\Voice;
use Log;
use App\Mail\FlowCustomMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Services\BorrowerService;
use AfricasTalking\SDK\AfricasTalking;
use App\Models\VoiceCallLog;
use App\Services\Vendors\Voice\AitCallQueueService;
use App\Repositories\SQL\CsDevicesRepositorySQL;
use App\Repositories\SQL\MarketRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use Carbon\Carbon;
use App\Models\CsDevices;
use DB;  
use App\Consts;
use App\Repositories\SQL\LoanApplicationRepositorySQL;

class AitVoiceService{

    public function process_inbound_calls($data){
    
        $is_active  = $data['isActive'];
        $call_session_state = $data['callSessionState'];
        $actions = null;
        
        if($is_active == '1' ){
            if ($call_session_state == 'Ringing')  {
                $actions = $this->handle_ringing($data);
            }              
        }
        
        if($actions){
        
            $response  = '<?xml version="1.0" encoding="UTF-8"?>';
            $response .= '<Response>';
            $response .= $actions;
            $response .= '</Response>';
    
            header('Content-type: text/plain');
            Log::warning($response);

            return $response;
        } 
    }

    function handle_ringing($data){

        $cs_num = $this->get_cs_num();
        $mobile_num = $cs_num ? $cs_num->number : config('app.swap_sip_to_mobile')['XXX'];

        $text    = "Welcome to Flo Uganda.";
        $actions = "";

            $actions .=    '<Say>'.$text.' Please wait while we connect you with a customer success officer</Say>';
            $actions .=    "<Dial phoneNumbers=\"{$mobile_num}\" record=\"true\"
                            sequential=\"true\">
                            </Dial>";

        return $actions;
    }

    
    public function update_cs_devices_duration(){

        $cur_date = Carbon::now()->format('Y-m-d');
        DB::update("update cs_devices set call_duration = null, date = ?",[$cur_date]);
        
    }

    public function get_cs_num(){
        
        $cur_date = Carbon::now()->format('Y-m-d');
        $cs_repo = new CsDevicesRepositorySQL; 

        $cs_num = (new CsDevices)->get_record_by_many(['date', 'status', 'call_status'], 
                                                      [$cur_date, Consts::ENABLED,Consts::AVAIL_STATUS ],
                                                      ['number'], 'and', 'order by call_duration asc limit 1');
        return $cs_num;
    }

    public function make_calls_outbound($data){

        $call_session_state = $data['callSessionState'];
        $is_active = $data['isActive'];
        $caller_number = $data['callerNumber'];
        $action = null;

        if($is_active == '1'){

            $action .= '<?xml version="1.0" encoding="UTF-8"?>';
            $action .= '<Response>';    
            $action .= "<Dial phoneNumbers=\"{$caller_number}\" record=\"true\"
                        sequential=\"true\">
                        </Dial>";
            $action .= '</Response>';
        }
        return $action;    
    }

    public function reminder_call_for_rm($data){

        $country_code = session('country_code');
        
        $market_repo = new MarketRepositorySQL();
        
        $isd_code = $market_repo->get_isd_code($country_code);

        $vendor_code = 'UAIT';
       
        $credentials = config('app.vendor_credentials')[$vendor_code]['SMS-OB'][$country_code];
        
        $username = $credentials['username'];
        $api_key   = $credentials['api_key'];
       
        $AT       = new AfricasTalking($username, $api_key);
        $voice    = $AT->voice();

        $call_from = config("app.{$country_code}_contact_number");
        
        $person_repo = new PersonRepositorySQL;
        $lazy_rms = $this->get_pending_req_count();
        
        $rm_mob_nums = [];
        $call_log_data = [];
        $pending_fas = null;

        if(sizeof($lazy_rms) == 0){
            return;
        }

        foreach($lazy_rms as $lazy_rm){
            $mob_num = $person_repo->get_mobile_num($lazy_rm['person_id']);
            $mob_num = "+{$isd_code->isd_code}{$mob_num}";
            unset($lazy_rm['person_id']);
            $pending_fas[$mob_num] = $lazy_rm;

        }
       
        $rm_mob_nums = array_keys($pending_fas);
        $rm_num_str = implode(",", $rm_mob_nums);
        $from     =  $call_from;
        $to       =  $rm_num_str;

        try {
            // Make the call
            $results =  $voice->call([
                'from' =>  $from,
                'to'   =>  $to,
                'clientRequestId' => $data,
            ]);
            
            $entries = $results['data']->entries;
            $error  = $results['data'] -> errorMessage;

            foreach($entries as $entry){

                if($entry->status == "Queued"){

                    $mobile_num = split_mobile_num($entry->phoneNumber);
                    $rm_details = $person_repo->get_rm_by_contact($mobile_num[0]);
                    $pending_counts = $pending_fas[$entry->phoneNumber];
                    $call_log_data['vendor_ref_id'] = $entry->sessionId;
                    $call_log_data['status'] = $entry->status;
                    $call_log_data['country_code'] = $country_code;
                    $call_log_data['mobile_num'] = $mobile_num[0]; 
                    $call_log_data['person_id']= $rm_details->id;
                    $call_log_data['details'] =  $pending_counts;
                    $call_log_data['details']['first_name'] = $rm_details->first_name;
                    $call_log_data['purpose'] = $data ;
                    $call_log_data['direction'] = 'outbound';
                    $call_log_data['vendor_code'] = $vendor_code;

                    $loan_appl_doc_ids = (new LoanApplicationRepositorySQL)->get_os_loan_appln($call_log_data['person_id'], Consts::LOAN_APPL_PNDNG_APPR);

                    $call_log_data['loan_appl_doc_ids']  = json_encode($loan_appl_doc_ids);

                    (new VoiceCallLog) -> insert_model($call_log_data);

                }
                elseif($entry->status != "Queued"){
                    $mail_data['country_code'] = $country_code;
                    $mail_data['reason'] = "CALL_QUEUE_TO_ONE_DIALER_FAILED";
                    $mail_data['status'] = $entry->status;
                    $mail_data['error_msg'] = config("app.ait_voice_error_msgs.{$entry->status}");
                    Mail::to(config('app.app_support_email'))->send((new FlowCustomMail('reminder_call_failed_alert', $mail_data))->onQueue('emails'));
                }
                 
            } 

            if($error != 'None'){
                $mail_data['country_code'] = $country_code;
                $mail_data['reason'] = "CALL_QUEUE_TO_ALL_DIALERS_FAILED";
                $mail_data['status'] = 'Aborted';
                $mail_data['error_msg'] = $error;
                Mail::to(config('app.app_support_email'))->queue((new FlowCustomMail('reminder_call_failed_alert', $mail_data))->onQueue('emails'));
            }

        } catch (\Exception $e) {
            Log::warning("Error: ".$e->getMessage()) ;
            $trace = $e->getTraceAsString();
            $mail_data['reason'] = "EXCEPTION_WHILE_QUEUING_CALLS";
            $mail_data['error_msg'] = "Error: ".$e->getMessage();
            $mail_data['exception'] = $trace;
            $mail_data['country_code'] = $country_code;   
        
            Mail::to(config('app.app_support_email'))->queue((new FlowCustomMail('reminder_call_failed_alert', $mail_data))->onQueue('emails'));
        }
    }


	public function get_pending_req_count(){

		$current_time = datetime_db();

		$pending_fa_appr = DB::select("select count(*) as pending_fa_appl_counts, loan_approver_id as person_id from loan_applications where TIMEDIFF( ? ,loan_appl_date)>'00:10:00' and 
         status = 'pending_approval' and country_code = ?  group by loan_approver_id ",[$current_time,session('country_code')]);

        
        // $pending_fa_upgrade_reqs = DB::select("select count(*) as pending_fa_upgrade_req_counts ,json_extract(approval_json,'$[0].person_id') as person_id from fa_upgrade_requests where TIMEDIFF( ? ,created_at)>'00:10:00' and status = 'requested' and country_code = ?  group by person_id ",[$current_time,session('country_code')]);

        $pending_fa_upgrade_reqs = [];

        $task_reqs = DB::select("select count(*) as task_req_counts ,json_extract(approval_json,'$[0].person_id') as person_id from tasks where TIMEDIFF( ? ,created_at)>'00:10:00' and json_extract(approval_json,'$[0].approved') = false and status = 'requested' and country_code = ?  group by person_id ",[$current_time, session('country_code')]);

    
        $combined_arr = array_merge($pending_fa_appr, $task_reqs, $pending_fa_upgrade_reqs);

        $cpy_combined_arr = json_decode(json_encode($combined_arr), true);
        
        $person_ids = array_unique(collect($combined_arr)->pluck('person_id')->toArray());
        $pending_req_arr = [];

        if(sizeof($combined_arr) > 0){
            foreach($person_ids as $person_id){
                $combined_arr = $cpy_combined_arr;
    
                foreach($combined_arr as $key => $value){
                    if($person_id == $value['person_id']){
                        $pending_req_arr[$person_id]['person_id'] = $value['person_id'];
                        unset($value->person_id);
                        $pending_req_arr[$person_id][array_keys($value)[0]] = array_values($value)[0];
                    }
                }
            }
        }

		return $pending_req_arr;

	}



    public function handle_active_call($data){


        if($data['callSessionState'] == 'Answered') {
            if($data['isActive'] == '1' ){
                $call_log_repo = new VoiceCallLog;

                $rm_details = $call_log_repo->get_record_by('vendor_ref_id',$data['sessionId'],['details']);

                $details = $rm_details->details;
            
                $text_1 = "Hello {$details->first_name}.I m Flow Akello. Your Flow reminder assistant";

                $text_2 = "";

                if(isset($details->pending_fa_appl_counts)){
                   $text_2 = "{$details->pending_fa_appl_counts} Float Advances,";
                }


                if(isset($details->pending_fa_upgrade_req_counts)){
                    $text_2 .= "{$details->pending_fa_upgrade_req_counts} FA upgrades,";
                }


                if(isset($details->task_req_counts)){
                    $text_2 .= "{$details->task_req_counts} other approvals, ";
                }

                if($text_2){
                    $text_2 = "there are {$text_2} waiting for your approval. ";
                }
                

                $count = count(array_filter(explode(' ', $text_2), "is_numeric"));

                if($count > 1){
                    $text_2 = preg_replace("/,([^,]+)$/", " and $1", $text_2);
                }


                $text_2 .= "Please take action immediately. Good Bye!";

                $call_log_repo->update_json_arr_by_code('details',['content_1' => $text_1,'content_2' => $text_2],$data['sessionId']);

                $response  = '<?xml version="1.0" encoding="UTF-8"?>';
                $response  .='<Response>';
                $response  .="<Say><speak>$text_1<break time=\"1s\"/>$text_2</speak></Say>";
                $response  .='</Response>';

                header('Content-type: text/plain');

                return $response;
            }  

        }
 
    }

    public function update_call_session($data){

        $cost = null;

        if($data['callSessionState'] == 'Completed'){
            $status = $data['status'];
            $cost = round($data['amount'],2);
        }else{
            $status =$data['callSessionState'];
        }

        $session_id = $data['sessionId'];
        $isd_code = $data['callerCountryCode'];
        
        $country_code = get_country_code_by_isd($isd_code);
        
        $call_log_repo = new VoiceCallLog;

        $rm_details = $call_log_repo->get_record_by('vendor_ref_id',$data['sessionId'],['details']);

        $details = $rm_details->details;

        if(array_key_exists('callSessionState',$data)){
        
            if(array_key_exists('hangupCause',$data)){
                
                $hang_up_cause = $data['hangupCause'];
                
                if($hang_up_cause != "UNSPECIFIED"){

                    $mail_data['reason'] = $hang_up_cause ;
                    $mail_data['error_msg'] = config("app.ait_voice_hangup_msgs.{$hang_up_cause}");
                    $mail_data['country_code'] = $country_code;
                    $mail_data['first_name'] = $details->first_name;
                    
                    Mail::to(get_ops_admin_csm_email(),config('app.app_support_email'))->queue((new FlowCustomMail('fa_approval_for_rm_call_failed', $mail_data))->onQueue('emails'));

                }

                if($data['hangupCause'] == "UNSPECIFIED"){
                
                    $mail_data['issue'] = $hang_up_cause;
                    $mail_data['mob_num'] = $data['mobile_num'];
                    $mail_data['reason'] = "CALL_HANGUP_UNSPECIFIED_REASON";
                    $mail_data['country_code'] = $country_code;
                    $mail_data['error_msg'] = config("app.ait_voice_hangup_msgs.{$hang_up_cause}");
                    
                    Mail::to(config('app.app_support_email'))->queue((new FlowCustomMail('reminder_call_failed_alert', $mail_data))->onQueue('emails'));
                }

                (new VoiceCallLog) -> update_model_by_code(['hang_up_cause' => $hang_up_cause,
                                                            'vendor_ref_id'=> $session_id],'vendor_ref_id');
            }
        }
    
        (new VoiceCallLog) -> update_model_by_code(['status' => $status, 'cost_of_call' => $cost, 'vendor_ref_id'=> $session_id],'vendor_ref_id');         
    }

}
