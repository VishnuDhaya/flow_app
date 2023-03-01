<?php

namespace App\Services;
use App\Repositories\SQL\FieldVisitRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\AddressInfoRepositorySQL;
use App\Services\Support\SMSNotificationService;
use App\Services\Support\SMSService;
use App\Services\BorrowerService;
use App\Models\FlowApp\AppUser;
use Carbon\Carbon;
use App\Consts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Log;
use Mail;
use App\Mail\FlowCustomMail;
use App\Models\Borrower;
use App\Services\Mobile;
use App\Services\Mobile\RMService;
use Carbon\CarbonPeriod;
use App\Services\Support\FireBaseService;


class FieldVisitService {

	public function submit_checkin($data){
	 
		$checkin_req = $data['checkin_req'];
		$val_result = $this->validate_last_visit($checkin_req['cust_id']);
		
		if(!isset($checkin_req['force_checkin'])){
			$this->check_biz_hours();
		}

		if($val_result === true){
			$cust_id = $checkin_req['cust_id'] ;
			$visit_start_time = datetime_db();
			$visit_start_time_utc =  gmdate(Consts::DB_DATETIME_FORMAT, strtotime($visit_start_time));
			$checkin_req['sch_status'] = 'checked_in';
			$checkin_req['country_code'] = session('country_code');	
			$borrower_serv = new BorrowerService;
			$borrower_repo = new BorrowerRepositorySQL;
			$borrower_data = $borrower_serv->get_borrower_profile($cust_id, true, false, true);
			$checkin_req['cust_gps'] = $borrower_data->gps;
			if($borrower_data->ongoing_loan_doc_id){
				$checkin_req['loan_doc_id']  = $borrower_data->ongoing_loan_doc_id;
			}
			if(array_key_exists("sch_id",$checkin_req)){
				$visit_id = $this->check_in_for_schedule($checkin_req);
			}else{
				$checkin_req['location']  = $borrower_data->location;
				$checkin_req['cust_name'] =  $borrower_data->cust_name;
				$visit_id = $this->direct_checkin($checkin_req);
				$updt_next_visit_date = $borrower_repo->update_model(['next_visit_date'=> null,'cust_id'=>$checkin_req['cust_id']],'cust_id');
			}

			return ["action" => "checkout", 'biz_name' => $borrower_data->biz_name, 'visit_id' => $visit_id, "cust_id" => $cust_id ,"visit_start_time" => $visit_start_time, "visit_start_time_utc"=>$visit_start_time_utc];

		}else{
			return $val_result;
		}
	}


	private function check_biz_hours($return = false){
		$startTime = Carbon::createFromFormat('H:i', config('app.biz_start_hour'));
    	$endTime = Carbon::createFromFormat('H:i', config('app.biz_end_hour'));
    	if(!Carbon::now()->between($startTime, $endTime, true)){
        	return $return ? true :thrw("You can't log customer visits after business hours. \nYou have to log the visits when you are at the customer's place.");
    	}
	}

	private function direct_checkin($checkin_req){
		$person_repo = new PersonRepositorySQL;
		$field_repo = new FieldVisitRepositorySQL;
		

		if(isset($checkin_req['force_checkin'])){
			$this->append_force_checkin_and_notify($checkin_req);
		}

		$visitor_id = session('user_person_id');
		$user_name = $person_repo->full_name($visitor_id);	
		$checkin_req['time_spent'] = 0;
		$checkin_req['visit_start_time'] = datetime_db();
		$checkin_req['visitor_name'] = $user_name;
		$checkin_req['visitor_id'] = $visitor_id;
		$checkin_req['sch_status'] = 'checked_in';
		$checkin_req['sch_from'] = Consts::RM;
		$visit_id = $field_repo->insert_model($checkin_req);
		
		return $visit_id;
	}

	private function check_in_for_schedule ($checkin_req){
		$field_repo = new FieldVisitRepositorySQL;
		$update_arr = ["id" => $checkin_req['sch_id'],"visit_start_time" => datetime_db(),"country_code"=> $checkin_req['country_code'],"sch_status" => $checkin_req['sch_status']];
		if($checkin_req['force_checkin']){
			$this->append_force_checkin_and_notify($checkin_req ,$update_arr);
		}
		$field_repo->update_model($update_arr);
		$visit_id = $checkin_req['sch_id'];
		return $visit_id;
	}

	private function append_force_checkin_and_notify (&$data, &$update_arr = null){

		$biz_hours = $this->check_biz_hours(true);
		
		if($biz_hours){
			$update_arr['after_biz_hours'] = $data['after_biz_hours'] =  true;
		}

		if($data['checkin_distance'] > config('app.max_dist_to_force_checkin')){
			$borro_repo  = new BorrowerRepositorySQL;
			$borrower = DB::selectOne("select allow_force_checkin_on from borrowers where date(allow_force_checkin_on) = ? and cust_id = ?",[date_db(),$data['cust_id']]);
			$allow_force_checkin = ($borrower != null) ? true : false;
		}else{
			$allow_force_checkin = true;
		}

		if($allow_force_checkin){
			$this->send_force_checkin_email($data);
			$borro_repo  = new BorrowerRepositorySQL;
			$borrower = $borro_repo->find_by_code($data['cust_id'],['allow_force_checkin_on']);
			$update_arr['force_checkin'] = $data['force_checkin'];
			$update_arr['checkin_distance'] = $data['checkin_distance'];
			$update_arr['force_checkin_reason'] = $data['force_checkin_reason'];
		}else{
			thrw("The App Support has not yet allowed you to force check-in. Can you request App Support team to allow you force check-in for this customer today?");
		}
		

	}

	private function send_force_checkin_email($data){
        $person_repo = new PersonRepositorySQL;
        $data['user_name'] = $person_repo->full_name(session('user_person_id'));
		$data['checkin_time'] = datetime_db();
        $data['country_code'] = $data['country_code'] ?? session('country_code');
        Mail::to(['praveen@flowglobal.net','kevina@flowglobal.net'])->send(new FlowCustomMail('force_checkin', $data));
    }

	public function get_field_visits($data){
		
		$fields = ["cust_id","cust_name","remarks","visit_start_time","visitor_name","visit_purpose"];
	
		if(array_key_exists('visit_purpose', $data) && $data['visit_purpose'] != null){
			$addl_sql = " and json_contains(visit_purpose,'[\"{$data['visit_purpose']}\"]') ";
		}else{
			$addl_sql = "";
		}
		$individual_visits = $data["individual_visits"] ;
		m_array_filter($data);
		$params = [$data['visit_start_time__from'], $data['visit_start_time__to'], session('country_code')];
		

		if(array_key_exists('cust_id', $data) && $data['cust_id'] != null){
			$params[] = $data['cust_id'];
			$addl_sql .= " and cust_id = ?"; 
		}else if(array_key_exists('search_type', $data) && $data['search_type']=="self_search"){
			$data['visitor_id'] = session('user_person_id');
		}

		if(array_key_exists('visitor_id', $data) && $data['visitor_id'] != null){
			$params[] = $data['visitor_id'];
			$addl_sql .= " and visitor_id = ?"; 
		}

		
		if($individual_visits === true  ){
			unset( $data['individual_visits']);
			$visits = DB::select(  "select cust_id, cust_name, remarks, visit_start_time, visitor_name, visit_purpose from field_visits where date(visit_start_time) >= ?  and   date(visit_start_time) <= ? and   country_code = ?  $addl_sql order by visit_start_time desc" ,$params);
			
			foreach ($visits as $visit) {
				 $visit->visit_purpose = json_decode($visit->visit_purpose);
			}
			
		}else{
			$visits = DB::select("select visitor_name, date(visit_start_time) visit_date, count(1) visits from field_visits where date(visit_start_time) >= ? and  date(visit_start_time)<= ? and  country_code = ? $addl_sql group by visitor_name,visitor_id, date(visit_start_time) order by date(visit_start_time) desc " , $params) ; 
		}
		return ['visits' => $visits,'individual_visits' => $individual_visits];
	}

	public function get_field_visit_details($data){
		$field_repo = new FieldVisitRepositorySQL;
		if($data && array_key_exists ('id',$data)){
			$results = $field_repo->find($data['id'],['visit_start_time']);

			$time_diff = time_diff_since($results->visit_start_time);

			$results->sec_diff = $time_diff['sec_diff'];
			$results->min_diff = $time_diff['min_diff'];

		}

		return $results;
	}

	public function submit_checkout($data){
		$field_repo = new FieldVisitRepositorySQL;
		$borrower_serv = new BorrowerService;
		$person_repo = new PersonRepositorySQL;
		$borrower_repo = new BorrowerRepositorySQL;
		$checkout_req = $data['checkout_req'];

		$purpose = $checkout_req['visit_purpose'];

		$last_visit_date = datetime_db();

		$update_arr = ['id'=> $checkout_req['id'], 'visit_purpose' => json_encode($purpose), 'remarks' => $checkout_req['remarks'], 
					   'visit_end_time' => $last_visit_date, 'sch_status' => 'checked_out' ];

		if(array_key_exists('early_checkout', $checkout_req) && $checkout_req['early_checkout'] ){
			$validate_checkout = true;
			$update_arr['early_checkout'] = $checkout_req['early_checkout'];
		}else{
			$validate_checkout = $this->validate_checkout($purpose);
		}

		if($validate_checkout === true){
			$cust_id = $checkout_req['cust_id'] ;

			try
				{
					DB::beginTransaction();
					
					$field_repo->update_model($update_arr);
					$borrower_repo->update_model_by_code(["cust_id" => $cust_id,"last_visit_date" => $last_visit_date]);

					$borrower_prfl = $borrower_serv->get_borrower_profile($cust_id,true);	

					$sms_serv = new SMSNotificationService();
					$person_repo = new PersonRepositorySQL;
					$addr = $borrower_prfl->cust_addr_txt;
					$cust_name = $person_repo->get_first_name($borrower_prfl->owner_person_id);		
					
					if (session('user_person_id') ) {
						$checkout_req['visitor_id'] = session('user_person_id');
					}
					$country_code = session('country_code');
					$user_name = $person_repo->full_name($checkout_req['visitor_id']);
					$non_activities = ['cust_not_available', 'shop_closed'];
					
					if(!has_any($non_activities, $purpose)){
						$sms_serv->send_cust_visit_feedback(
										['cust_name' => $cust_name,
										'cust_mobile_num' => $borrower_prfl->cust_mobile_num,
										'cust_address' => $addr,
										'visit_time' => format_date($last_visit_date),
										'visitor_name' =>$user_name ,
										'country_code' =>$country_code ,
										'market_head_mobile' => config('app.market_head_mobile')[$country_code ]
																	]);

					}

					DB::commit();


				}

				catch (FlowCustomException $e) {
	            	DB::rollback() ;
	            
	            	throw new FlowCustomException($e->getMessage());
       		 	};
		    			            
		    	
       		return ["action" => "to_success","message" => "The field visit has been registered on Flow App successfully"];
        }else{

            return ["action" => "show_error","min_diff" => $validate_checkout["min_diff"],"sec_diff" => $validate_checkout["sec_diff"]] ;
        }	
		
	}
	private function validate_checkout($purpose){

		$non_activities = ['cust_not_available', 'shop_closed'];

		if(has_any($non_activities, $purpose)){
			return true;
		}

		$visitor_id = session('user_person_id');
		$last_checkin = DB::selectOne("select cust_id, visit_start_time from field_visits where visitor_id = ? and !json_contains(visit_purpose,'[\"cust_not_available\"]') and visit_end_time is null  order by visit_start_time desc limit 1", [$visitor_id]);
		if($last_checkin){
			$last_checkin_ago = time_diff_since($last_checkin->visit_start_time);

			
			if($last_checkin_ago["min_diff"] < config('app.visit_checkout_delay')){
				
				return $last_checkin_ago;
				/*thrw("Please ensure all the visit activities are performed before you checkout. You have checked-in just {$last_checkin_ago["min_diff"]} minutes ago");
				 ;*/

			}
		}
		return true;
	}
	private function validate_last_visit($cust_id){		
		
		$startTime = Carbon::createFromFormat('H:i', config('app.biz_start_hour'));
    	$endTime = Carbon::createFromFormat('H:i', config('app.biz_end_hour'));	

		$visitor_id = session('user_person_id');
		$pending_checkout = DB::selectOne("select id, cust_id,visit_start_time,sch_slot from field_visits where visitor_id = ?  and visit_end_time is null and sch_status = 'checked_in' order by visit_start_time desc limit 1 " ,[$visitor_id]);	

		if($pending_checkout){
			$resp =  ['action' => 'lastvisit', 'cust_id' => $pending_checkout->cust_id, 'visit_id' => $pending_checkout->id,'visit_start_time' => $pending_checkout->visit_start_time,'pending_checkout' => $pending_checkout];
			if($pending_checkout->cust_id == $cust_id){
		 		$message = "##### WARNING ##### \nYou have already checked-in at this customer. \n\nDo you want to checkout now?";
			}else {
				$message =  "##### WARNING ##### \nYour previous visit at another customer {$pending_checkout->cust_id} is still not yet checked out. You can not checkin a new customer without logging a checkout for the last visit. \n\nDo you want to checkout now?";
			}
			$resp['message'] = $message;
			return $resp;
		}

		$params = [$startTime,$endTime,$cust_id];
		$today_visit = DB::select("select visitor_id, visitor_name, visit_start_time from field_visits where visit_start_time >= ? and visit_start_time <= ? and visit_end_time is not null and cust_id = ? ",$params);

		if(sizeof($today_visit) >= 2){
			thrw("Already 2 visits were logged today for this customer by {$today_visit[0]->visitor_name} at {$today_visit[0]->visit_start_time}");
		}

		$last_visit = DB::selectOne("select cust_id, visit_end_time from field_visits where visitor_id = ? and visit_purpose != 'cust_not_available' order by visit_end_time desc limit 1", [$visitor_id]);

		// if($last_visit){
		// 	$last_visit_ago = Carbon::parse($last_visit->visit_end_time)->diffInMinutes();
		// 	if($last_visit_ago < config('app.visit_checkin_delay')){
				
		// 		thrw("Last customer visit for {$last_visit->cust_id} was logged just {$last_visit_ago} minutes ago. \nYou have to log the visits when you are at the customer's place.");
		// 	}
		// }		
		return true;	
	}



	public function process_visit_suggestions(){

		$country_code = session('country_code');

		$borro_repo = new BorrowerRepositorySQL;

		$list_rms =  AppUser::where(['role_codes'=>'relationship_manager','status'=>'enabled'])->get(['person_id'])->pluck("person_id");
 	
		$updt_next_visit_date = $borro_repo-> update_model(['next_visit_date' => null,'country_code' => $country_code],'country_code');
		
		foreach($list_rms as $rm){
	
		$cust_to_visit = DB::select("select cust_id, datediff (curdate() , last_visit_date )  as days  from borrowers where status = 'enabled' and profile_status != 'closed' and flow_rel_mgr_id = ?  and country_code = ? order by  days DESC",[$rm, $country_code]);

		$visit_suggestion = $this->set_next_visit_date($cust_to_visit);

		}
	}

	public function set_next_visit_date($cust_to_visit){
		
		$borro_repo = new BorrowerRepositorySQL;
		$visit_dates = [];
		
    	$period= CarbonPeriod::create(Carbon::tomorrow(),  Carbon::now()->addDays(6));

		foreach($period->filter("isWorkingDay")as $date){
			$visit_dates[] = $date->format('Y-m-d');
		}
		 
		$split_datas = array_chunk($cust_to_visit,4);
		
		foreach($visit_dates as $visit_date){
		
			$this_visit_date_cust = array_shift($split_datas);
			
			foreach($this_visit_date_cust as $cust_ids){
				
				$borro_repo-> update_model_by_code(['cust_id'=>$cust_ids->cust_id,'next_visit_date'=> $visit_date]);
			}
		}
	}

	private function send_allow_force_checkin_notification($cust_id){
        
		$borr_repo  = new BorrowerRepositorySQL;
		$serv = new FireBaseService();
		$borrower  = $borr_repo->find_by_code($cust_id,['flow_rel_mgr_id']);
		$messenger_token = AppUser::where('person_id',$borrower->flow_rel_mgr_id)->get('messenger_token')->pluck("messenger_token")[0];
		$data['notify_type'] = 'allow_force_checkin';
		$data['messsage'] = "Now you have been allowed to do force checkin for the customer {$cust_id}";
		$serv($data, $messenger_token);

	}

	public function allow_force_checkin($data){
		$borro_repo  = new BorrowerRepositorySQL;
		$borrower = DB::selectOne("select allow_force_checkin_on from borrowers where date(allow_force_checkin_on) = ? and cust_id = ?",[date_db(),$data['cust_id']]);
		$result = null;
		if($borrower == null){
			$result = $borro_repo->update_model_by_code(['cust_id' => $data['cust_id'],'allow_force_checkin_on' =>datetime_db()]);
			$this->send_allow_force_checkin_notification($data['cust_id']);
		}
		return $result;
	}
}

