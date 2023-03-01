<?php
namespace App\Services;

use App\Models\KycRecord;
use App\Repositories\SQL\AccProviderRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\CapitalFundRepositorySQL;
use App\Repositories\SQL\CustAgreementRepositorySQL;
use App\Services\BorrowerService;
use App\Services\BorrowerServiceV2;
use App\Services\Mobile\RMService;
use App\Services\Support\SMSNotificationService;
use App\Repositories\SQL\AddressInfoRepositorySQL;
use App\Repositories\SQL\RMCustAssignmentsSQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\OrgRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\ProbationPeriodRepositorySQL;
use App\Repositories\SQL\CustEvalChecklistRepositorySQL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Consts;
use App\Models\CustCSFValues;
use App\Repositories\SQL\CustCSFValuesRepositorySQL;
use App\Repositories\SQL\LeadRepositorySQL;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use function Aws\boolean_value;
use App\Mail\FlowCustomMail;
use Illuminate\Support\Facades\Mail;

class CustomerRegService{


	public function get_validation_keys($borrower){
		$validation_models = array();
		$validation_models[] = "biz_address";
		$validation_models[] = "cr_owner_person";
		$validation_models[] = "biz_identity";
		$validation_models[] = "biz_info";
		$validation_models[] = "cr_account";


		if($borrower['same_as_owner_person'] == false){
			  $validation_models[] = "contact_persons";
		}
		if($borrower['same_as_biz_address'] == false){          
			$validation_models[]  = 'owner_address';
		}
		return $validation_models;
	
	}
	
	private function dup_cust_check($borrower, $cust_id = null){
		$rm_serv = new RMService();
		$owner = $borrower['owner_person'];
        $is_create = $cust_id == null;
		$borr_serv = new BorrowerService();
		$this->chk_same_mobile_num($owner,"Owner person's");
		$this->check_duplicate_borrower($borrower, $is_create, $cust_id);
		if(array_key_exists('email_id', $owner) && $owner['email_id'] && is_flow_email($owner['email_id'])){
			thrw("Please enter customer's email ID. The email you entered belongs to Flow Global.");
		  }	 
		if($borrower['same_as_owner_person'] == false){
			foreach ($borrower['contact_persons'] as $contact_person){
				if(!is_string($contact_person)){
					$this->chk_same_mobile_num($contact_person,"Contact person's");
					if($owner['national_id'] == $contact_person['national_id']){
						thrw("You can not upload the owner's national ID for Handler");
					}

				}
			}
		}
	}

    public function get_csf_run_id($account) {

        $ap_codes_w_approval = config('app.acc_prvdrs_allow_approval');
        $ap_codes_w_data = config('app.acc_prvdrs_with_data');

        $csf_data = (new CustCSFValuesRepositorySQL())->get_record_by_many(['acc_number', 'acc_prvdr_code'], [$account['acc_number'], $account['acc_prvdr_code']], ['run_id', 'conditions', 'result']);

        if ( is_null($csf_data) ) {
            if (in_array($account['acc_prvdr_code'], $ap_codes_w_data)) {
                thrw("No transaction statement / score data exists for this account number ( {$account['acc_number']} )");
            }
            return NULL;
        }

        if ( in_array($account['acc_prvdr_code'], $ap_codes_w_approval) ) {
            $conditions = $csf_data->conditions;
            if ( $conditions && isset($conditions->validity) ) {
                $exp_date = $conditions->validity;
                if ( $exp_date == '*' || now()->lessThan(Carbon::parse($exp_date)) ) {
                    return $csf_data->run_id;
                }
            }
        }

        $eligibility = $csf_data->result;
        if ( $eligibility == 'ineligible' || is_null($eligibility) ) {
            thrw("Can not create customer profile. Customer is not eligible to be registered as per their transaction data.");
        }
        return $csf_data->run_id;
    }

	private function set_person_photo_paths(&$person,$paths){

    if(!array_key_exists('photo_consent_letter', $person)){
      $person['photo_selfie_full_path']['value'] = $paths['photo_selfie_path'];
      $person['photo_pps_full_path']['value'] = $paths['photo_pps_path'];
    }
    $person['photo_national_id_full_path']['value'] = $paths['photo_national_id_path'];
    $person['photo_national_id_back_full_path']['value'] = $paths['photo_national_id_back_path'];
		
	}


	public function sync_rekyc_with_cust_profile($new_borrower,$kyc_reason= null, $txn = true, $holder_name=null)
	{
		$lead_repo = new LeadRepositorySQL;


		if (env('APP_ENV') == 'production' && is_fa_acc($new_borrower['acc_purpose'])) {
			$new_borrower['csf_run_id'] = $this->get_csf_run_id($new_borrower['account']);
		}
		try {
			if ($txn) {
				DB::beginTransaction();
			}
            $old_kyc = $this->get_borrower_details($new_borrower['cust_id']);
			$addr_repo = new AddressInfoRepositorySQL();
			$person_repo = new PersonRepositorySQL();
			$borrower_repo = new BorrowerRepositorySQL();
			$cust_id = $new_borrower['cust_id'];
      $new_borrower['last_kyc_date'] = datetime_db();
      
			$old_borrower = $borrower_repo->find_by_code($cust_id);
            $new_borrower['owner_person']['id'] = $new_borrower['owner_person_id'] = $old_borrower->owner_person_id;

			$owner_person = array_merge($new_borrower['biz_identity'], $new_borrower['owner_person']);
            $biz_address = $new_borrower['biz_address'];
            $biz_address['id'] = $new_borrower['biz_address_id'] = $old_borrower->biz_address_id;

            $this->dup_cust_check($new_borrower, $cust_id);


            $this->sync_rekyc_address($new_borrower, $biz_address, $old_borrower);

            $this->sync_rekyc_person($new_borrower, $owner_person);

            $borrower_repo->update_model_by_code($new_borrower);

			$this->mount_borrower_files($new_borrower, $cust_id,$kyc_reason);


			$lead_repo->update_model(['status' => $new_borrower['lead_status'], "id" => $new_borrower['lead_id'], "audit_kyc_end_date" => datetime_db()]);


            $this->sync_rekyc_account($new_borrower,$kyc_reason, $holder_name);

            $new_kyc = $this->get_borrower_details($new_borrower['cust_id']);
            $this->backup_borrower_details($new_borrower['cust_id'],$new_borrower['lead_id'],$new_kyc,$old_kyc);
            if ($txn) {
				DB::commit();
			}
		} catch (\Exception $e) {
			if ($txn) {
				DB::rollback();
			}
			Log::warning($e->getTraceAsString());
			if ($e instanceof QueryException) {
				throw $e;
			} else {
				thrw($e->getMessage());
			}
		}

		return $new_borrower["cust_id"];
	}

	public function create(array $borrower, $txn = true,$cust_reg_json = null){

		$lead_repo = new LeadRepositorySQL;
		$this->dup_cust_check($borrower);


	  try
		{
		if($txn){
			DB::beginTransaction(); 
		}
		$person_id = null;
		$addr_repo = new AddressInfoRepositorySQL();
		$person_repo = new PersonRepositorySQL();
		$cust_id = $borrower['cust_id'];
    $borrower['biz_address']['gps'] = $borrower['gps'];
		$biz_address_id = $addr_repo->create($borrower['biz_address']);
		$borrower['biz_address_id'] = $biz_address_id;
    $new_borrower['last_kyc_date'] = datetime_db();

    if(array_key_exists('third_party_owner', $borrower)){
      
      $result = $person_repo->create($borrower['third_party_owner'],$borrower['lead_id'], 'third_party_owner');
      $this->set_person_photo_paths($cust_reg_json['third_party_owner'],$result);
      $borrower['account']['tp_acc_owner_id'] = $result['person_id'];

    }

    
		$owner_person = array_merge($borrower['biz_identity'],$borrower['owner_person']);
		$result = $person_repo->create($owner_person,$borrower['lead_id'], 'owner_person');
    

    if(array_key_exists('addl_num', $borrower)){
      $addl_num = array_values($borrower['addl_num']);
      $addl_num = json_encode($addl_num);
      $person_repo->update_model(['id' => $result['person_id'], 'addl_mob_num' => $addl_num] );

    }


		$this->set_person_photo_paths($cust_reg_json['owner_person'],$result);
		$borrower['owner_person_id'] = $result['person_id'];
		if($borrower['same_as_biz_address'] == false)  {
			$owner_address_id = $addr_repo->create($borrower['owner_address']);
			$borrower['owner_address_id'] = $owner_address_id;
		}else{
			$borrower['owner_address_id'] = $biz_address_id;
		}

		if($borrower['same_as_owner_person'] == false){
			foreach ($borrower['contact_persons'] as $i=>$contact_person){
        if($i != "country_code"){
          if(is_array($contact_person)){
            $contact_person['address_id'] = $addr_repo->create($contact_person['contact_address']);
            $contact_person['associated_with'] = "borrower";
            $contact_person['associated_entity_code'] = $cust_id;
            $photo_path = $person_repo->create( $contact_person,$borrower['lead_id']);
          }
          $photo_path = $person_repo->create( $contact_person,$borrower['lead_id'], "contact_person");
        }
			}
		}

    $acc_id = $this->create_account($borrower, isset($cust_reg_json['account']['holder_name']) ? $cust_reg_json['account']['holder_name'] : null);
    if(env('APP_ENV') == 'production' && is_fa_acc($borrower['acc_purpose'])){
			$borrower['csf_run_id'] = $this->get_csf_run_id($borrower['account']);
		}

    $borrower_repo = new BorrowerRepositorySQL();
    $borrower['file_data_consent'] = $lead_repo->get_file_data_consent($borrower['lead_id']);
		$borrower_id = $borrower_repo->insert_model($borrower);
    $this->mount_borrower_files($borrower, $cust_id, null, $cust_reg_json);

    $audited_by = session('user_person_id');
    $addl_fields = ['status' => $borrower['lead_status'], "audit_kyc_end_date" => datetime_db(),'audited_by' => $audited_by];
    $cust_reg_json['account']['id'] = $acc_id;
    $lead_repo->update_cust_reg_json($cust_reg_json, $borrower['lead_id'], $addl_fields);

		$prob_period_repo = new ProbationPeriodRepositorySQL();

		$start_date = Carbon::now();
		$prob_period_repo->start_probation($borrower['cust_id'], 'probation', $borrower['prob_fas'], $start_date);
        $new_kyc = $this->get_borrower_details($borrower['cust_id']);
        $this->backup_borrower_details($borrower['cust_id'],$borrower['lead_id'],$new_kyc);


        $rm_cust_reass_repo = new RMCustAssignmentsSQL();
        $rm_cust_reass_repo->insert_model(['cust_id' => $borrower['cust_id'], 'country_code' => $borrower['country_code'], 'rm_id' => $borrower['flow_rel_mgr_id'],'from_date' => $borrower['reg_date'], 'status' => "active", 'territory' => $borrower['territory'], 'temporary_assign' => false, 'reason_for_reassign' => 'initial_assignment' ]);
  
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


  public function get_person_id_fields($person, $is_create = false){
    # owner_person or contact_person comes in $person_field
    $field_names = [];
    $field_values = [];
    $dup_field = [];
    $person_ck_res = $this->check_person_exist($person);
    if($person_ck_res && $is_create == false) {
      $person_ids = $person_ck_res['dup_person_ids'];
      if(array_key_exists('id', $person)){
        $person_id = $person['id'];
        
      }else if(array_key_exists('person_id', $person)){
        $person_id = $person['person_id'];
        
      }
      else if(array_key_exists('owner_person_id', $person)){
        $person_id = $person['owner_person_id'];

      }
      $person_ids = array_remove_values($person_ids, $person_id);
      $person_ids = array_values($person_ids);
      $person_ck_res['dup_person_ids'] = $person_ids ;
    }
    
   
    return $person_ck_res;
    
  }

  public function check_person_exist($person){
    if(!$person ||  !is_array($person)){
      return false;
    }
    $person_repo = new PersonRepositorySQL();
	
	$fields_to_check = array();
  $fields = array();
    if(array_key_exists('national_id', $person) && session('ignore_nat_check') == false){
      array_push($fields_to_check, 'national_id');
      array_push($fields, 'national_id');
    }

    if(array_key_exists('mobile_num', $person) || array_key_exists('alt_biz_mobile_num_1', $person) || array_key_exists('alt_biz_mobile_num_2', $person)){
      array_push($fields_to_check, 'mobile_num', 'alt_biz_mobile_num_1', 'alt_biz_mobile_num_2');
      array_push($fields, 'phone_num', 'whatsapp', 'mobile_num', 'alt_biz_mobile_num_1', 'alt_biz_mobile_num_2' );
    }
    
    // $fields = ['phone_num', 'whatsapp', 'mobile_num', 'national_id', 'alt_biz_mobile_num_1', 'alt_biz_mobile_num_2'];
      $req_fields = array_intersect(array_keys($person), $fields);
	  foreach($req_fields as $req_field){
		$fields_values = array_fill(0, sizeof($fields_to_check), $person[$req_field]);
        $persons = collect($person_repo->get_records_by_any($fields_to_check, $fields_values,['associated_entity_code']));  
		#$persons = collect($person_repo->get_records_by($fld_to_check, $person[$fld_to_check],['associated_entity_code']));
          if(sizeof($persons)  > 0 ){
            $dup_person_ids = $persons->pluck('id')->toArray();
            $dup_cust_ids = $persons->pluck('associated_entity_code')->toArray();

          return ['dup_person_ids' => $dup_person_ids, 'dup_cust_ids' => $dup_cust_ids, 'dup_field' => $req_field, 'dup_value' => $person[$req_field]];
          }
        }
    
    return false;
  }


  public function check_dup_result($model, $dup_ck_res, $dup_person_ids = null, $cust_id = null){
    $dupes = $dup_ck_res['dupes'];
    $dup_field = $dup_ck_res['dup_field'];
    $dup_value = $dup_ck_res['dup_value'];
    if(sizeof($dupes)  > 0 ){
       foreach($dupes as $dup){
        if($dup->profile_status == 'open' && $dup->cust_id != $cust_id){
          thrw("With {$dup_field} = {$dup_value}, an open customer already exist. Cust ID : [{$dup->cust_id}]");
        }
       }
     }else if(is_array($dup_person_ids) &&sizeof($dup_person_ids) > 1){ # check if duplicate person_id is returned by get_person_id_fields function
      thrw("A person already exist with the same details [$dup_person_ids[0]]");
    }
  }

  public  function check_duplicate_borrower($borrower, $is_create = true, $cust_id = null){
    $borrower_repo = new BorrowerRepositorySQL();
    $all_field_names = $all_field_values = $all_dup_cust_ids = [];
    $person_ck_res = $this->get_person_id_fields($borrower['owner_person'], $is_create);

    if($person_ck_res && sizeof($person_ck_res['dup_person_ids']) > 0 ){
      $field_names = array_fill(0, sizeof($person_ck_res['dup_person_ids']), "owner_person_id");
      $person_ck_res['dup_person_ids'] = array_values($person_ck_res['dup_person_ids']);
      $dupes = $borrower_repo->get_records_by_any($field_names, $person_ck_res['dup_person_ids'], ['cust_id', 'data_prvdr_cust_id', 'profile_status']);
        $person_ck_res['dupes'] = $dupes;
        $this->check_dup_result('owner_person', $person_ck_res, $person_ck_res['dup_person_ids'], $cust_id);
    }

    if(array_key_exists('contact_persons', $borrower) ){
      foreach($borrower['contact_persons'] as $person){
        $person_ck_res = $this->get_person_id_fields($person, true);
        if($person_ck_res){
          $dupes = $borrower_repo->get_records_by('cust_id', $person_ck_res['dup_cust_ids'], ['cust_id', 'data_prvdr_cust_id', 'profile_status']);
            $person_ck_res['dupes'] = $dupes;
          $this->check_dup_result('contact_person', $person_ck_res, null, $cust_id);
        }
      }
    }
    
      
    $borrower_repo = new BorrowerRepositorySQL();
    $account_repo = new AccountRepositorySQL;
    $account = $account_repo->get_record_by_many(['acc_number','acc_prvdr_code'],[$borrower['account']['acc_number'],$borrower['account']['acc_prvdr_code']],['cust_id']);
    if($account){
      $dupes = $borrower_repo->get_records_by('cust_id', $account->cust_id, ['cust_id', 'profile_status']);
      $dup_ck_res = ['dupes' => $dupes, 'dup_field' => 'acc_number', 'dup_value' => $borrower['account']['acc_number']];
      $this->check_dup_result('acc_number', $dup_ck_res, null, $cust_id);
    }

  }

  public function append_borrower_obj(&$borrower, $lead_data){

    
	$borrower['lender_code'] = config('app.lender_code')[session('country_code')];
    if($lead_data->type == 'kyc'){
        $borrower['reg_flow_rel_mgr_id'] =  $lead_data->flow_rel_mgr_id;
        $borrower['reg_date'] = Carbon::now();
        $borrower['prob_fas'] = config('app.default_prob_fas');
        $capital_funds_repo = new CapitalFundRepositorySQL();
        $borrower["fund_code"] = $capital_funds_repo->get_default_fund($borrower['lender_code']);
    }
    $borrower['flow_rel_mgr_id'] = $lead_data->flow_rel_mgr_id;
	$borrower['country_code'] = session('country_code');
	$borrower['lead_id'] = $lead_data->id;
	$borrower['acc_purpose'] = $lead_data->acc_purpose;
	$borrower['cr_owner_person'] = $borrower['owner_person'];
	$borrower['cr_account'] = $borrower['account'];
    $borrower['acc_prvdr_code'] = $borrower['account']['acc_prvdr_code'];
    if(!is_tf_only_acc($lead_data->acc_purpose)){
        $borrower['acc_number'] = $borrower['account']['acc_number'];
    }
    $borrower['biz_type'] = Consts::INDIVIDUAL;
	  $borrower['status'] = 'disabled';
	  $borrower['kyc_status'] = 'completed';
	  $borrower['lead_status'] = Consts::PENDING_ENABLE;

	if(array_key_exists('location' , $borrower['biz_address'])){
		$borrower['location'] = $borrower['biz_address']['location'];
	}
	if(array_key_exists('territory' , $borrower['biz_info'])){
		$borrower['territory'] = $borrower['biz_info']['territory'];
	}
	if ( isset($borrower['prob_fas']) && $borrower['prob_fas'] > 0 ){
		$borrower['category'] = 'Probation';
	}

	foreach($borrower['agreements'] as $aggr){
		if(isset($aggr['photo_witness_national_id']) &&
			isset($aggr['photo_witness_national_id_back'])){
			$borrower['agreement'] = $aggr;
			$borrower['agreement']['country_code'] = session('country_code');
		}
        if(array_key_exists('acc_purpose', (array)$aggr) && $aggr['acc_purpose'] === 'float_advance' && $aggr['status'] === 'signed'){
            $borrower['aggr_status'] = 'active';
            $borrower['current_aggr_doc_id'] = $aggr['aggr_doc_id'];
            $cust_aggr_repo = new CustAgreementRepositorySQL();
            $aggrmt = $cust_aggr_repo->get_record_by('aggr_doc_id',$borrower['current_aggr_doc_id'],['valid_upto']);
            $borrower['aggr_valid_upto'] = $aggrmt->valid_upto;
        }
	}

	foreach($borrower['references'] as  $key=>$value){
		if(is_array($value)){
			foreach($value as $inner_key=>$inner_value){
				$borrower[$inner_key] = $inner_value;
			}
		}
	}
	$borrower = array_merge($borrower,$borrower['biz_info'],$borrower['biz_identity']);



  }

  private function get_acc_elig_reason($acc_prvdr_code, $acc_number) {

    $acc_elig_reason = NULL;
    $repo = new CustCSFValues;
    $csf_data = $repo->get_record_by_many(['acc_number', 'acc_prvdr_code'], [$acc_number, $acc_prvdr_code], ['conditions']);
    $conditions = $csf_data ? $csf_data->conditions : null;
    if($conditions && isset($conditions->acc_elig_reason)){
      $acc_elig_reason = $conditions->acc_elig_reason;

    }
    return $acc_elig_reason;
  }
  
  private function create_account($borrower, $holder_name = null){
	$acc_repo = new AccountRepositorySQL();
	$account = array();
	$account['cust_id'] = $borrower['cust_id'];
	$account['entity'] = 'customer';
  if(array_key_exists('branch', $borrower['account'])){
    $account['branch'] = $borrower['account']['branch'];
  }

  if(array_key_exists('alt_acc_num', $borrower['account'])){
    $account['alt_acc_num'] = $borrower['account']['alt_acc_num'];
  }

  if(array_key_exists('third_party_owner', $borrower)){
    $account['photo_consent_letter'] = $borrower['third_party_owner']['photo_consent_letter'];
    $account['tp_acc_owner_id'] = $borrower['account']['tp_acc_owner_id'];
  }
	$account['acc_number'] = $borrower['account']['acc_number'];
	$account['country_code'] = session('country_code');
	$account['acc_prvdr_code'] = $borrower['account']['acc_prvdr_code'];
//	$account['type'] = 'wallet';
	$account['holder_name'] = isset($holder_name) ? $holder_name : $borrower['owner_person']['first_name'] ." ".$borrower['owner_person']['last_name'];
  $account['holder_name_mismatch_reason'] = $borrower['holder_name_mismatch_reason'];
  //	$account['is_primary_acc'] = true;
	$account['acc_purpose'] = $borrower['acc_purpose']; //gotta check acc_purpose of lead
    if(is_fa_acc($account['acc_purpose'])){
        $account['assessment_type'] = 'self';
    }
    $acc_serv = new AccountService;
    $acc_serv->check_appr_elig($account);
    $account['acc_prvdr_name'] = $acc_serv->get_acc_prvdr_name($account['acc_prvdr_code']);
    $account['acc_elig_reason'] = $this->get_acc_elig_reason($account['acc_prvdr_code'], $account['acc_number']);
	return $acc_repo->create($account);

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


    public function mount_borrower_files(array $borrower, $cust_id,$kyc_reason = null, &$cust_reg_json = null )
    {
      if($kyc_reason && $kyc_reason == 'new_account'){
        $cust_reg_json['account']['photo_new_acc_letter_full_path']['value'] = move_entity_file("leads", "borrowers", $borrower['account'], $cust_id, 'photo_new_acc_letter', $borrower['lead_id']);
      } 

      if(array_key_exists('third_party_owner',$borrower)){
        $cust_reg_json['third_party_owner']['photo_consent_letter_full_path']['value'] = move_entity_file("leads", "borrowers", $borrower['third_party_owner'], $cust_id, 'photo_consent_letter', $borrower['lead_id']);
      }
      move_entity_file("leads", "borrowers", $borrower['biz_identity'], $cust_id, 'photo_biz_lic', $borrower['lead_id']);
        move_entity_file("leads", "borrowers", $borrower['biz_identity'], $cust_id, 'photo_shop', $borrower['lead_id']);
        if (array_key_exists('agreement', $borrower)) {
            mount_entity_file("borrowers", $borrower['agreement'], $cust_id, 'photo_witness_national_id');
            mount_entity_file("borrowers", $borrower['agreement'], $cust_id, 'photo_witness_national_id_back');
        }
        if (is_tf_acc($borrower['acc_purpose'])) {
            mount_entity_file("borrowers", $borrower, $cust_id, 'guarantor1_doc');
            mount_entity_file("borrowers", $borrower, $cust_id, 'guarantor2_doc');
            mount_entity_file("borrowers", $borrower, $cust_id, 'lc_doc');
        }
    }

    public function backup_borrower_details($cust_id, $lead_id, $new_kyc, $old_kyc = null)
    {
        $backup['cust_id'] = $cust_id;
        $backup['lead_id'] = $lead_id;
        $backup['cust_json_before'] = $old_kyc;
        $backup['cust_json_now'] = $new_kyc;
        (new KycRecord)->insert_model($backup);
    }

    public function get_borrower_details($cust_id){
        $borrower = (new BorrowerRepositorySQL)->find_by_code($cust_id);
        $addr_repo = new AddressInfoRepositorySQL();
        $person_repo = new PersonRepositorySQL();
        $biz_address = $addr_repo->find($borrower->biz_address_id);
        $owner_address = $addr_repo->find($borrower->owner_address_id);
        $owner_person = $person_repo->find($borrower->owner_person_id);
        $contact_persons = $person_repo->get_records_by_many(["associated_with","associated_entity_code"],["borrower",$cust_id]);
        $accounts = (new AccountRepositorySQL)->get_accounts_by(["cust_id"],[$cust_id]);

        $kyc = json_encode(['borrower' => $borrower, 'biz_address' => $biz_address, 'owner_person' => $owner_person, 'owner_address' => $owner_address, 'contact_persons' => $contact_persons, 'accounts' => $accounts] );

        return $kyc;
    }

    public function holder_name_evidence_verification_mail($cust_reg_json, $lead_id, $is_third_party_owner){
        $mail_data['holder_name'] = $cust_reg_json['account']['holder_name'];
        $mail_data['biz_name'] = $cust_reg_json['biz_info']['biz_name']['value'];
        $mail_data['lead_id'] = $lead_id;
        $mail_data['country_code'] = session('country_code');
        $mail_data['auditor_mail'] = (new PersonRepositorySQL())->get_email_n_msgr_token(session('user_person_id'))[0];
        
        if(isset($cust_reg_json['owner_person']['middle_name']['value'])){
            $mail_data['national_id_name'] = $cust_reg_json['owner_person']['first_name']['value']." ".$cust_reg_json['owner_person']['middle_name']['value']." ".$cust_reg_json['owner_person']['last_name']['value'];
        }
        else{
            $mail_data['national_id_name'] = $cust_reg_json['owner_person']['first_name']['value']." ".$cust_reg_json['owner_person']['last_name']['value'];
        }

        if ($is_third_party_owner){
            $mail_data['third_party_owner_name'] = $cust_reg_json['third_party_owner']['first_name']['value']." ".$cust_reg_json['third_party_owner']['last_name']['value'];
            if(!(preg_replace('/\s+/', '', strtolower($mail_data['third_party_owner_name'])) === preg_replace('/\s+/', '', strtolower($cust_reg_json['account']['holder_name'])))){
                Mail::to(get_ops_admin_email())->queue((new FlowCustomMail('account_name_mismatch_on_audit_submit', $mail_data))->onQueue('emails'));
            }
        }
        else if(!((preg_replace('/\s+/', '', strtolower($cust_reg_json['biz_info']['biz_name']['value'])) === preg_replace('/\s+/', '', strtolower($cust_reg_json['account']['holder_name'])) || preg_replace('/\s+/', '', strtolower($mail_data['national_id_name'])) === preg_replace('/\s+/', '', strtolower($cust_reg_json['account']['holder_name']))))){
            Mail::to(get_ops_admin_email())->queue((new FlowCustomMail('account_name_mismatch_on_audit_submit', $mail_data))->onQueue('emails'));
        }
    }

    public function validate_for_rekyc($data, $cust_id)
    {
        $borrower_repo = new BorrowerRepositorySQL();
        $person_repo = new PersonRepositorySQL();
        $borrower = (array)$borrower_repo->find_by_code($cust_id);
        $person = (array)$person_repo->find($borrower['owner_person_id']);

        $person_fields = ['national_id'];

        foreach($person_fields as $field){
            if(array_key_exists($field, $data) && $data[$field] != $person[$field]){
                $field_name = dd_value($field);
                thrw("$field_name does not match.\nIn new KYC {$data[$field]}.\nIn existing customer profile {$person[$field]}.");
            }
        }

    }

    private function sync_rekyc_address(&$new_borrower, $biz_address, $old_borrower)
    {
        $addr_repo = new AddressInfoRepositorySQL();
        $addr_repo->update($biz_address);

        if ($new_borrower['same_as_biz_address'] == true) {
            $new_borrower['owner_address_id'] = $new_borrower['biz_address_id'];

        } else {
            $owner_address_id = $addr_repo->create($new_borrower['owner_address']);
            $new_borrower['owner_address_id'] = $owner_address_id;
        }

        if ($old_borrower->owner_address_id != $new_borrower['biz_address_id']) {
            $addr_repo->delete_model($old_borrower->owner_address_id);
        }
    }

    private function sync_rekyc_person($new_borrower, $owner_person)
    {
        $person_repo = new PersonRepositorySQL;
        $addr_repo = new AddressInfoRepositorySQL;

        $person_repo->update_person_kyc($owner_person, $new_borrower['lead_id']);

        if ($new_borrower['same_as_owner_person'] == false) {
            $person_repo->delete_contact_people($new_borrower['cust_id']);
            foreach ($new_borrower['contact_persons'] as $contact_person) {
                if (is_array($contact_person)) {
                    $contact_person['address_id'] = $addr_repo->create($contact_person['contact_address']);
                    $contact_person['associated_with'] = "borrower";
                    $contact_person['associated_entity_code'] = $new_borrower['cust_id'];
                    $person_repo->create($contact_person, $new_borrower['lead_id']);
                }
            }
        }
    }

    private function sync_rekyc_account($new_borrower,$kyc_reason, $holder_name): void
    {
        $account = [];
        $account['holder_name'] = isset($holder_name) ? $holder_name : $new_borrower['owner_person']['first_name'] . " " . $new_borrower['owner_person']['last_name'];
        $account['holder_name_mismatch_reason'] = $new_borrower['holder_name_mismatch_reason'];
        $account['acc_number'] = $new_borrower['account']['acc_number'];
        $account['status'] = 'enabled';
        if($kyc_reason && $kyc_reason == 'new_account'){
          $account['photo_new_acc_letter'] = $new_borrower['account']['photo_new_acc_letter'];
        }
        $account['id'] = $new_borrower['account']['id'];
        $acc_repo = new AccountRepositorySQL();
        $borr_repo = new BorrowerRepositorySQL();
        $acc_repo->update_model($account);
        $borr_repo->update_model_by_code(['cust_id' => $new_borrower['cust_id'], 'acc_number' => $new_borrower['account']['acc_number'], 'acc_prvdr_code' => $new_borrower['account']['acc_prvdr_code']]);
    }

}
