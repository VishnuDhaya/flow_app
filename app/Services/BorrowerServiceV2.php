<?php
namespace App\Services;

use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Services\BorrowerService;
use App\Services\CustomerRegService;
use App\Services\Mobile\RMService;
use App\Services\Support\SMSNotificationService;
use App\Repositories\SQL\AddressInfoRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\OrgRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\ProbationPeriodRepositorySQL;
use App\Repositories\SQL\CustEvalChecklistRepositorySQL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Consts;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class BorrowerServiceV2{


	public function create(array $borrower, $txn = true, $duplicate_checked = false){
		$borr_serv = new BorrowerService();
		$rm_serv = new RMService();
		/*if(array_key_exists('cust_eval_id',$borrower)){

	    	$cust_eval_repo = new CustEvalChecklistRepositorySQL;
			$eval_list = $cust_eval_repo->find($borrower['cust_eval_id'],['cust_kyc_data']);
			if($eval_list){
				$cust_kyc_data = json_decode($eval_list->cust_kyc_data);
				if($cust_kyc_data->acc_prvdr_code != $borrower['acc_prvdr_code']){
					thrw("The acc_prvdr_code submitted in this profile does not match with the acc_prvdr_code in the pre evaluation.");
				}
				if($cust_kyc_data->data_prvdr_cust_id != $borrower['data_prvdr_cust_id']){
					thrw("The data_prvdr_cust_id submitted in this profile does not match with the data_prvdr_cust_id in the pre evaluation.");
				}
				if($cust_kyc_data->biz_name != $borrower['biz_name']){
					thrw("The biz_name submitted in this profile does not match with the biz name in the pre evaluation.");
				}
			}else{
				thrw("You can not register a customer without doing pre evaluation.");
			}
					

	    }*/

		$rm_serv->dup_check_cust(["acc_prvdr_code" => $borrower['acc_prvdr_code'],
            "acc_number" =>$borrower['acc_number']]); 

        // $rm_serv->dup_check_cust(["biz_name" => $borrower['biz_name']]);

        $rm_serv->dup_check_person(["mobile_num" => $borrower['owner_person']['mobile_num']]);


	    if(($borrower['biz_type'] == Consts::INDIVIDUAL) 
	      && $duplicate_checked == false){
			$cust_reg_serv = new CustomerRegService();
	    	$cust_reg_serv->check_duplicate_borrower($borrower);		
	    }
		// $dp_code = session('acc_prvdr_code');
    	// $acc_prvdr_code = $dp_code == 'UFLO' ? 'UMTN' : $dp_code;

		$tf_exception_list = config('app.tf_exception_list');
	    if(env('APP_ENV') == 'production' && !in_array($borrower['data_prvdr_cust_id'], $tf_exception_list)){
		    $dp_codes = config('app.data_prvdrs_with_data'); 
		    $cust_data = DB::select("select run_id, csf_type from cust_csf_values where acc_number = ? and acc_prvdr_code = ? and csf_type like 'result%' ", [$borrower['acc_number'], session('acc_prvdr_code')]);
		 
		    if(count($cust_data) == 1){
		      $borrower['csf_run_id'] = $cust_data[0]->run_id;

		      $split_eligibility = explode(":", $cust_data[0]->csf_type);
        
        		if($split_eligibility[1] == 'ineligible'){
          			thrw("Can not create customer profile. Customer is not eligible to be registered as per their transaction data.");
        		} 
		    }else if(count($cust_data) > 1){
		        thrw("Unable to create new customer because this customer has more than one run ID");
		    }else if(count($cust_data) == 0 && in_array(session('acc_prvdr_code'), $dp_codes)){
		        thrw("No data exists for this data provider cust ID (".$borrower['acc_number'].") ");
		    }
		}
	   try
	     {
	      if($txn){
	        DB::beginTransaction(); 
	      }

	      $person_id = null;
	      $addr_repo = new AddressInfoRepositorySQL();
	      $person_repo = new PersonRepositorySQL();
	      #$borrower['biz_type'] = $borrower['borrower_type'];
	      $borrower['acc_prvdr_code'] = session('acc_prvdr_code');
		  $borrower['reg_flow_rel_mgr_id'] = session('user_person_id');
	      $borrower['prob_fas'] = 0;
	      $acc_number = $borrower['acc_number'];
	      #$dp_code = $borrower['acc_prvdr_code'];
	      if($dp_code == 'CCA')
	      {
	       #preg_match("/^[^0][0-9]{8}$/", $dp_cust_id) ? true : thrw("Invalid {$dp_code} Data Provider Customer ID");
	       
	      }
	      else if($dp_code == 'UEZM')
	      {
	        #preg_match("/^[0-9]{7,9}[/][\d]$/", $dp_cust_id) || preg_match("/^[0-9]{7,9}$/", $dp_cust_id) ? true : thrw("Invalid {$dp_code} Data Provider Customer ID");
	      }
	      
	      $cust_id = (new CommonRepositorySQL())->get_new_flow_id($borrower['country_code'], 'customer');
		  $lender_code = session('lender_code');
	      $cust_id =  "{$lender_code}-{$cust_id}";
	      $borrower['cust_id'] = $cust_id;
	      if($borrower['biz_type'] == Consts::INDIVIDUAL){
	        $biz_address_id = $addr_repo->create($borrower['biz_address']);
	        $borrower['biz_address_id'] = $biz_address_id;
	        if(array_key_exists('gps' , $borrower['biz_address'])){
            	$borrower['gps'] = $borrower['biz_address']['gps'];
          	}
			if(array_key_exists('location' , $borrower['biz_address'])){
				$borrower['location'] = $borrower['biz_address']['location'];
	
			}
	        $borrower['prob_fas'] = config('app.default_prob_fas');
	        $person_id = $person_repo->create($borrower['owner_person']);
	        $borrower['owner_person_id'] = $person_id;
	        if($borrower['same_as_biz_address'] == false)  {
	            $owner_address_id = $addr_repo->create($borrower['owner_person']['owner_address']);
	            $borrower['owner_address_id'] = $owner_address_id;
	        }else{
	          $borrower['owner_address_id'] = $biz_address_id;
	        }   
	        
			$owner = $borrower['owner_person'];
			if(array_key_exists('email_id', $owner) && $owner['email_id'] && is_flow_email($owner['email_id'])){
				thrw("Please enter customer's email ID. The email you entered belongs to Flow Global.");
				
			}
			$mob_num = $owner['mobile_num'];
			$this->chk_same_mobile_num($owner,"Owner person's");	           
		

	    if(array_key_exists('contact_persons', $borrower)){	            	
				foreach ($borrower['contact_persons'] as $contact_person){
				    $this->chk_same_mobile_num($contact_person,"Contact person's");	
		          	$contact_person['address_id'] = $addr_repo->create($contact_person['contact_address']);
		          	$contact_person['associated_with'] = "borrower";
		          	$contact_person['associated_entity_code'] = $cust_id;
		          	if($owner['national_id'] != $contact_person['national_id']){
		            	$person_repo->create( $contact_person);
		          	}else{
		          		thrw("You can not upload the owner's national ID for Handler");
		          	}
	          	}
	      	}  
	      }elseif ($borrower['biz_type'] == Consts::FLOW_RM){
	        $borrower['owner_person_id'] = $borrower['flow_rel_mgr_id'];
	      }elseif ($borrower['biz_type'] == Consts::DP_RM){
	        $borrower['owner_person_id'] = $borrower['dp_rel_mgr_id'];	        
	      }else{
	        thrw("Invalid biz_type");
	      }
	     
	     
	               
	      

	      // if(get_arr_val($borrower, 'csf_run_id')){
	      //   $common_repo = new CommonRepositorySQL(ScoreRun::class);
	      //   $result = $common_repo->get_record_by('run_id',[$borrower['csf_run_id']],['data_prvdr_cust_id']);
	        
	      //   if(!$result){
	      //     thrw("Run ID does not exist.");
	      //   }else if($result->data_prvdr_cust_id != $borrower['data_prvdr_cust_id']){
	      //     //thrw("This run ID is associated with another customer : $borrower['data_prvdr_cust_id']");
	      //     thrw("This run ID is associated with another customer .");
	      //   }
	      // }

	      $borrower_repo = new BorrowerRepositorySQL();
	      
	      if ($borrower['prob_fas'] > 0 ){
	        $borrower['category'] = 'Probation';
	      }else{
	        $borrower['category'] = 'Regular';
	      } 
	      $borrower['status'] = 'disabled';
	      $borrower['kyc_status'] = 'pending';

	      $borrower_id = $borrower_repo->insert_model($borrower);
	         
	      if($borrower['biz_type'] == Consts::INDIVIDUAL){
	        if(isset($borrower['account'])){
	            $borrower['account']['cust_id'] =  $cust_id;
	            (new AccountRepositorySQL())->create($borrower['account']);
	        }
	  
	        mount_entity_file("borrowers", $borrower, $cust_id, 'photo_biz_lic');
	        mount_entity_file("borrowers", $borrower, $cust_id, 'photo_shop');
	        
	        // $sms_serv = new SMSNotificationService();  
	        // $cust_name = $owner['first_name'] ." ".$owner['first_name'];

	        // $mobile_verify_otp = $borr_serv->get_cust_reg_otp($borrower,$mob_num);
	        // $ap_code = $borrower['acc_prvdr_code'];
	        // $sms_serv->notify_welcome_customer(['cust_mobile_num' => $mob_num, 
	        //                                       'country_code' => $borrower['country_code'],
	        //                                       'acc_prvdr_code' => $ap_code,
	        //                                       'cust_name' => $cust_name,
	        //                                       'cust_id' => $cust_id,
	        //                                        'customer_success' => config('app.customer_success_mobile')[$ap_code],
	        //                                        'otp_code' => $mobile_verify_otp[0],
	        //                                        'otp_id' => $mobile_verify_otp[1],
	        //                                        'sms_reply_to' => config('app.sms_reply_to')[session('country_code')]
	        //                                     ]);
	      }
	      

	      

	    //    if($borrower['use_as_ac_num'] == true )
	    //   {
	    //     $acc_serv = new AccountService();
	    //     $account = array();
	    //     $account['cust_id'] = $borrower['cust_id'];
	    //     $account['entity'] = 'customer';

	    //     $split_dp_cust_id = explode("/", $borrower['data_prvdr_cust_id']);
	    //     $account['acc_number'] = $split_dp_cust_id[0];
	       

	    //     $account['country_code'] = session('country_code');
	    //     $acc_prvdr_code = $dp_code == 'UFLO' ? 'UMTN' : $dp_code;
	    //     $account['acc_prvdr_code'] = $acc_prvdr_code;
	    //     $account['type'] = 'wallet';
	    //     $account['holder_name'] = $owner['first_name'] ." ".$owner['last_name'];
	    //     $account['is_primary_acc'] = true;
	    //     $account['acc_purpose'] = 'float_advance';
	        
	        
	    //     $acc_serv->create($account, false);


	    //   }
	      
	      
	      $prob_period_repo = new ProbationPeriodRepositorySQL();

	       $start_date = Carbon::now();
      		$prob_period_repo->start_probation($borrower['cust_id'], 'probation', $borrower['prob_fas'], $start_date);
	      if($txn){
	            DB::commit();
	            
	      } 
	      
	    }
	    catch (\Exception $e) {
	      if($txn){
	        DB::rollback();
	      }
	      Log::warning($e->getTraceAsString());
	      if ($e instanceof QueryException){
	          throw $e;
	        }else{
	        thrw($e->getMessage());
	        }
	    }

	    return $borrower["cust_id"];
  }

  public function chk_same_mobile_num($data,$person){
  	if((array_key_exists('alt_biz_mobile_num_1', $data) && $data['alt_biz_mobile_num_1'] && $data['alt_biz_mobile_num_1'] == $data['mobile_num'] && array_key_exists('mobile_num', $data))
  		|| array_key_exists('mobile_num', $data) && (array_key_exists('alt_biz_mobile_num_2', $data) && $data['alt_biz_mobile_num_2'] && $data['alt_biz_mobile_num_2'] == $data['mobile_num'])
    ){
  		thrw("{$person} alternate biz mobile number can't be same as main mobile number");
	}else if(array_key_exists('alt_biz_mobile_num_1', $data) && $data['alt_biz_mobile_num_1'] && array_key_exists('alt_biz_mobile_num_2', $data) && $data['alt_biz_mobile_num_2']
			&& $data['alt_biz_mobile_num_1'] == $data['alt_biz_mobile_num_2']){
      thrw("{$person} alternate biz mobile number 1 can't be same as alternate biz mobile number 2.");

	    
	}
  }
}