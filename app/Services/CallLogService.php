<?php

namespace App\Services;
use App\Consts;
use App\Repositories\SQL\LoanRepositorySQL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Repositories\SQL\CallLogRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\AddressInfoRepositorySQL;
use App\Repositories\SQL\CsDevicesRepositorySQL;
use App\Services\Support\SMSNotificationService;
use App\Services\Support\SMSService;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use App\Models\CsDevices;

class CallLogService {

	public function start_call_log($data){
		$call_log = $data['call_log'];
		$val_result = $this->validate_last_call($call_log['cust_id']);
		if($val_result === true){
			$person_repo = new PersonRepositorySQL;
			$call_repo = new CallLogRepositorySQL;
			$addr_repo = new AddressInfoRepositorySQL;
			$borrower_serv = new BorrowerService;
			$cust_id = $call_log['cust_id'] ;
			$borrower_data = $borrower_serv->get_borrower_profile($cust_id,false,false,true);
			
			if(isset($borrower_data->current_loan)){
				$call_log['loan_doc_id']  = $borrower_data->current_loan->loan_doc_id;
			}

			$user_person = $person_repo->find(session('user_person_id'));
		  	$user_name = full_name($user_person);	
			$addr_id = $borrower_data->biz_address_id;		
			$call_log['time_spent'] = 0;
			$call_log['call_start_time'] = datetime_db();
			$call_log['cust_name'] =  $borrower_data->cust_name;
		  	$call_log['call_logger_name'] = $user_name;
			$call_log['call_logger_id'] = session('user_person_id');
			$call_log['country_code'] = session('country_code');
			$call_log_id = $call_repo->insert_model($call_log);
		
			return ["action" => "log_call", 'call_log_id' => $call_log_id, "cust_id" => $cust_id ];

		}else{
			return $val_result;
		}
	}
	public function cancel_call_log($data){
		$call_logger_id = session('user_person_id');
		if($data && array_key_exists('log_id', $data)){
			$result = DB::delete("delete from call_logs where id = ? and call_logger_id = ?", [$data['log_id'],$call_logger_id]);
			if($result){
				return "The call log has been cancelled successfully";
			}else{
				return "Unable to cancel the Call Log";
			}
		}	
	}
	public function complete_call_log($data){
		$call_log = $data['call_log'];
		$call_repo = new CallLogRepositorySQL;
		$call_pur_arr = $call_log['call_purpose'];

		$call_log['call_end_time'] = datetime_db();
		
		$call_log['call_purpose'] = json_encode($call_log['call_purpose']);
		if(array_key_exists('remarks', $call_log)){
			$remarks = $call_log['remarks'];
		}else{
			$remarks = null;
		}
        $conf_pur_arr = ['cust_sms_not_rcvd', 'cust_sms_not_sent'];
        if(has_any($call_pur_arr,$conf_pur_arr)){
            $this->verify_conf_call_log($call_log);
        }


		$result = $call_repo->update_model(['id'=> $call_log['id'],'call_purpose' => $call_log['call_purpose'],'call_end_time' => $call_log['call_end_time'],'remarks'=>$remarks]);
		$paymt_arr = ['payment_confirmation','repeat_last_fa','topup','fa_upgrade'];
		$contains = has_any($call_pur_arr,$paymt_arr);
		
		// if(!$contains){

		// 	$sms_serv = new SMSNotificationService();
		// 	$sms_serv->send_call_feedback(['cust_mobile_num' => $call_log['mob_num'],'country_code' => session('country_code')]);
		// }
	}

	private function validate_last_call($cust_id){
		$call_logger_id = session('user_person_id');

		$pending_call_log = DB::selectOne("select id,call_logger_id,call_logger_name, cust_id from call_logs  where  (call_logger_id = ? or cust_id = ?) and call_end_time is null order by call_start_time desc limit 1 ",[$call_logger_id,$cust_id]);

		if($pending_call_log){
			$resp =  ['action' => 'lastcall', 'cust_id' => $pending_call_log->cust_id, 'call_log_id' => $pending_call_log->id];

			if($pending_call_log->cust_id == $cust_id){
			 	if($pending_call_log->call_logger_id == $call_logger_id){
		 			$message = "Your previous call with this customer is still not yet logged completely. You can not log a new customer call without completing the last call. \n\nDo you want to complete the last call now?";
		 		}else{
		 			$message = "Another Flow user {$pending_call_log->call_logger_name} is on the call with this customer.\nPlease ask the user to complete the call";
		 		}
			}else {
				$message =  "Your previous call with another customer {$pending_call_log->cust_id} is still not yet logged completely. You can not log a new customer call without completing the last call. \n\nDo you want to complete the last call now?";          		
			}
			$resp['message'] = "##### WARNING ##### \n$message";
			return $resp;
		}
		return true;
	}

	public function get_call_log_details($data){
		$call_repo = new CallLogRepositorySQL;
		if($data && array_key_exists ('id',$data)){
			$results = $call_repo->find($data['id'],['call_start_time']);
			$time_diff = time_diff_since($results->call_start_time);
			$results->sec_diff = $time_diff['sec_diff'];
			$results->min_diff = $time_diff['min_diff'];
		}
		return $results;
	}

	public function list_call_logs($data){		
		if(array_key_exists('call_purpose', $data) && $data['call_purpose'] != null){
			
			$call_purpose = $data['call_purpose'];
		$addl_sql = "and json_contains(call_purpose,'[\"{$data['call_purpose']}\"]')";
													
		}else{
			$addl_sql = "";
		}

		$individual_logs = $data["individual_logs"] ;
		m_array_filter($data);

		$params = [$data['call_start_time'], $data['call_end_time'], session('country_code')];

		if(array_key_exists('call_type', $data)&& $data['call_type'] != "all"){
			$params[] = $data['call_type'];
			$addl_sql .= " and call_type = ?"; 
		}

		if(array_key_exists('cust_id', $data) && $data['cust_id'] != null){
			$params[] = $data['cust_id'];
			$addl_sql .= " and cust_id = ?"; 
		}else if(array_key_exists('search_type', $data) && $data['search_type']=="self_search"){
			$data['call_logger'] = session('user_person_id');
		}

		if(array_key_exists('call_logger', $data) && $data['call_logger'] != null){
			$params[] = $data['call_logger'];
			$addl_sql .= " and call_logger_id = ?"; 
		}

		if($individual_logs === true  ){
			unset( $data['individual_logs']);
			$logs = DB::select(  "select cust_id, cust_name, remarks, call_start_time,call_end_time,call_type,call_logger_name, call_purpose from call_logs where date(call_start_time) >= ?  and   date(call_end_time) <= ? and   country_code = ?  $addl_sql order by call_start_time desc" ,$params);
			
			foreach ($logs as $log) {
				$log->call_purpose = json_decode($log->call_purpose);
				$result = time_diff_between($log->call_start_time,$log->call_end_time);

				$log->dur_secs = $result['dur_secs'];
				$log->dur_human = $result['dur_human'];
			}
		}else{
			$logs = DB::select("select call_logger_name, date(call_start_time) log_date, count(1) logs from call_logs where date(call_start_time) >= ? and  date(call_end_time)<= ? and  country_code = ? $addl_sql group by call_logger_name,call_logger_id, date(call_start_time) order by date(call_start_time) desc " , $params) ; 

		}
		return ['logs' => $logs,'individual_logs' => $individual_logs];

	}

    private function verify_conf_call_log($call_log)
    {
        $loan_doc_id = $call_log['loan_doc_id'];
        $loan = (new LoanRepositorySQL)->get_record_by_many(['loan_doc_id', 'status', 'customer_consent_rcvd'], [$loan_doc_id, Consts::LOAN_PNDNG_DSBRSL, false]);
        if (!$loan) {
            thrw("This FA is not pending confirmation from this customer.");
        }
        $amount = $loan->loan_principal;
        $fee = $loan->flow_fee;
        $duration = $loan->duration;
        $remarks = str_replace(',', '', $call_log['remarks']);
        $has_amount = preg_match("/(\b(FA)(\w*\W*){0,4}($amount)\b|\b($amount)(\w*\W*){0,4}(FA)\b)/", $remarks);
        $has_duration = preg_match("/(\b(days|day)(\w*\W*){0,2}($duration)\b|\b($duration)(\w*\W*){0,2}(days|day)\b)/i", $remarks);
        $has_fee = preg_match("/(\b(fee)(\w*\W*){0,3}($fee)\b|\b($fee)(\w*\W*){0,3}(fee)\b)/i", $remarks);
        if ($has_amount && $has_duration && $has_fee) {
			$loan_repo = new LoanRepositorySQL;
			$loan_repo->update_loan_event('cs_log_time',$call_log['loan_doc_id']);
            (new LoanService)->send_to_disbursal_queue($loan->loan_doc_id, null, 'call_log');
        }else{

             $details = [];
             if(!$has_amount){
                 $details[] = "Amount";
             }
             if(!$has_fee){
                 $details[] = "Fee";
             }
             if(!$has_duration){
                 $details[] = 'Duration';
             }

             $missing_details = implode(', ', $details);
             thrw("Call Log remarks is missing or has incorrect $missing_details information");
        }
    }

	public function get_cs_rosters($data){


		$cs_devices = (new CsDevices)->get_records_by('country_code', $data['country_code'], ['number', 'type', 'person_id', 'status']);
		
		foreach ($cs_devices as $cs_device){

			$cs_name = (new PersonRepositorySQL)->full_name($cs_device->person_id);

			$records[] = ["name" => $cs_name,
						 "person_id" => $cs_device->person_id,
						  "type" => $cs_device->type,
						  "number" => $cs_device->number,
						  "status" => $cs_device->status];

		}
		$header = ["person_name" => "CS Name", "type" => "Type", "number" => "Number", "status" => "Status"];

		return ['records_arr'=>$records, 'headers'=> $header];

	}

	public function update_cs_status($data){

		(new CsDevicesRepositorySQL())-> update_model(['status' => $data['status'],
													  'number' => $data['cs_number']], 'number');
	}


}
