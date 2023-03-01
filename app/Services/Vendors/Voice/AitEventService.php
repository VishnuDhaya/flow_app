<?php
namespace App\Services\Vendors\Voice;

use App\Models\CsDevices;
use App\Mail\FlowCustomMail;
use App\Repositories\SQL\BorrowerRepositorySQL;
use Illuminate\Support\Facades\Mail;
use App\Repositories\SQL\CsDevicesRepositorySQL;
use App\Repositories\SQL\CSMMissedCallLogsRepositorySQL;
use App\Repositories\SQL\InboundVoiceCallLogsRepositorySQL;
use App\Services\BorrowerService;
use App\Repositories\SQL\MarketRepositorySQL;
use Log;
use App\Consts;
use App\Repositories\SQL\PersonRepositorySQL;
use Carbon\Carbon;

class AitEventService{

    public function handle_inbound_calls($data){

        $call_session_state = $data['callSessionState'];
        
        if($call_session_state == 'Dialing'){
            $this-> activity_dialing($data);

        }elseif($call_session_state == 'Bridged'){
            $this-> change_cs_availability($data);
        
        }else if($call_session_state == 'Completed'){ 
           $this -> activity_completed($data);
            
        }
    } 
       
    private function activity_dialing($data){

        $market_repo = new MarketRepositorySQL;
        $call_log_repo = new InboundVoiceCallLogsRepositorySQL;
        $borrow_serve = new BorrowerService;
        $cs_dev_repo = new CsDevicesRepositorySQL();
        $borrower_repo = new BorrowerRepositorySQL;
        $cur_date = Carbon::now()->format('Y-m-d');
        $session_id = $data['sessionId'];
        $caller_country_code = $data['callerCountryCode'];
        
        $cust_id = $borrower_repo -> get_cust_id_from_mobile_num($data['mobile_num'],true) ;
        if(!isset($cust_id)){
            $cust_id = 'unknown_customer';
        }
        
        $country_code_as_array = $market_repo -> get_record_by("isd_code", $caller_country_code,['country_code']);
        $country_code = $country_code_as_array->country_code;

        $person_id =  $cs_dev_repo->get_record_by('number',$data['dialDestinationNumbers'], ['person_id'])->person_id;
        $cs_id = $person_id !=null ? $person_id : null;
        $result = $call_log_repo -> insert_model(['vendor_ref_id' => $data['sessionId'],
                                                  'direction' =>$data['direction'],
                                                  'csm_number' => $data['dialDestinationNumbers'], 
                                                  'cust_id' => $cust_id,
                                                  'cust_number' => $data['mobile_num'],
                                                  'country_code' => $country_code,
                                                  'cs_id' => $cs_id,
                                                  'date' => $cur_date]);
        return $result;

    }

    private function activity_completed($data){

        $call_log_repo = new InboundVoiceCallLogsRepositorySQL;
        $cs_dev_repo = new CsDevicesRepositorySQL();
        $borrower_repo = new BorrowerRepositorySQL;

        $cs_num  = array_key_exists('dialDestinationNumber',$data) ? $data['dialDestinationNumber'] : null;
        $call_dur_per_call = null;
        if(array_key_exists('dialDurationInSeconds',$data)){

            $call_duration = gmdate("H:i:s", $data['dialDurationInSeconds']); 
            $call_dur_per_call = $call_duration;
            $cs_device = $cs_dev_repo->get_record_by('number', $cs_num, ['call_duration']);

            if(isset($cs_device->call_duration)){
                
                $secs = strtotime($call_duration)-strtotime("00:00:00");
                $call_duration = date("H:i:s",strtotime($cs_device->call_duration)+$secs);
            }
            $cs_device_data['call_duration'] = $call_duration;
        }

        $cs_device_data['call_status'] = Consts::AVAIL_STATUS;
        $cs_device_data['number'] = $cs_num;

        $cs_dev_repo->update_model($cs_device_data, 'number');
        $cust_id = $borrower_repo -> get_cust_id_from_mobile_num($data['mobile_num'],true) ;
        if(!isset($cust_id)){
            $cust_id = 'unknown_customer';
        }
        $session_id = $data['sessionId'];
        $session_ids = $call_log_repo -> get_record_by("vendor_ref_id",[$session_id],['vendor_ref_id']);
        
        $vendor_ref_id = $session_ids->vendor_ref_id;
        $cost_of_call = round($data['amount'],2);
        $country_code = session('country_code');

        if(array_key_exists('lastBridgeHangupCause', $data)){

            $cs_device  = $call_log_repo->get_record_by('vendor_ref_id', $session_id, ['csm_number', 'cs_id']);
            $cs_name = (new PersonRepositorySQL)->full_name($cs_device->cs_id);
            $cs_device_num = $cs_device->csm_number;
            $hang_up_cause =$data['lastBridgeHangupCause'];
                
            if ($vendor_ref_id == $data['sessionId']){
                    
                $result = $call_log_repo -> update_model(['vendor_ref_id' =>$session_id,
                                                          'hangup_causes' => $hang_up_cause,
                                                          'call_duration' => $call_dur_per_call,
                                                          'cost_of_call' => $cost_of_call],'vendor_ref_id');   
                if($hang_up_cause == 'UNSPECIFIED'){                                                   
                    $mail_data['issue'] = $hang_up_cause;
                    $mail_data['reason'] = "CALL_HANGUP_UNSPECIFIED_REASON";
                    $mail_data['country_code'] = $country_code;
                    $mail_data['cust_id'] = $cust_id;
                    $mail_data['cs_name'] = $cs_name;
                    $mail_data['destination_num'] = $cs_device_num;
                    $mail_data['mobile_num'] = $data['callerNumber'];
                    $mail_data['error_msg'] = config("app.ait_voice_hangup_msgs.{$hang_up_cause}");

                    send_email('inbound_call_failed_alert', [get_csm_email()], $mail_data);  
                    send_email('inbound_missed_call_alert', [get_csm_email()], $mail_data);                                          
                }

                if($hang_up_cause != 'UNSPECIFIED' ){
                    
                    $mail_data['country_code'] = $country_code;
                    $mail_data['mobile_num'] = $data['callerNumber'];
                    $mail_data['cust_id'] = $cust_id;
                    send_email('inbound_missed_call_alert', [get_csm_email()], $mail_data);
                    
                }
            }
            
        }else if($vendor_ref_id == $data['sessionId']){

            $result = $call_log_repo -> update_model(['vendor_ref_id' =>$session_id,
                                                        'cost_of_call' => $cost_of_call,
                                                        'recording_url' => $data['recordingUrl'],
                                                        'call_duration' => $call_dur_per_call,
                                                        'status' => $data['status']],'vendor_ref_id');                                       
        }
        return $result;
    }

    public function change_cs_availability($data){

        $cs_dev_repo = new CsDevicesRepositorySQL();
        
        $cs_num = $data['dialDestinationNumber'];
        $cs_dev_repo->update_model(['call_status' => Consts::UNAVAIL_STATUS,'number'=>$cs_num ], 'number');

    }
}