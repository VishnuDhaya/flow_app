<?php
namespace App\Services;
use App\Repositories\SQL\LoanApplicationRepositorySQL;
use App\Services\LoanService;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Repositories\SQL\AddressInfoRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\OrgRepositorySQL;
use App\Repositories\SQL\LenderRepositorySQL;
use App\Repositories\SQL\LeadRepositorySQL;
use App\Repositories\SQL\LoanApproversRepositorySQL;
use App\Repositories\SQL\RecordAuditRepositorySQL;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\AccountStmtRepositorySQL;
use App\Repositories\SQL\AccProviderRepositorySQL;
use App\Repositories\SQL\AddressRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\LoanTransactionRepositorySQL;
use App\Repositories\SQL\CustAgreementRepositorySQL;
use App\Repositories\SQL\CustKYCRepositorySQL;
use App\Repositories\SQL\AgreementRepositorySQL;
use App\Repositories\SQL\ProbationPeriodRepositorySQL;
use App\Repositories\SQL\MarketRepositorySQL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exception\FlowCustomException;
use App\Models\ScoreRun;
use App\Models\PreApproval;
use App\Consts;
use Exception;
use Carbon\Carbon;
use PDF;
use File;
use Illuminate\Database\QueryException;
use App\Models\CorePlatform\CoreUser;
use App\Repositories\SQL\CapitalFundRepositorySQL;
use App\Services\AgreementService;
use Illuminate\Support\Facades\Session;
use App\Services\Support\SMSNotificationService;
use App\Services\Support\SMSService;
use App\Services\AccountService;
use App\Services\CustomerRegService;
use App\Services\BorrowerServiceV2;
use App\Services\RecordAuditService;
use App\Services\Schedule\ScheduleService;
use App\Services\Vendors\Whatsapp\WhatsappWebService;
use App\Services\PartnerService;

class BorrowerService{

  public function __construct()
    {
        $this->country_code = session('country_code');

        // session()->put('acc_prvdr_code', "UEZM");
        // session()->put('country_code', "UGA");
    }
  public function get_validation_keys($borrower){
    if(!array_key_exists('biz_type',$borrower)){
      $biz_type = Consts::INDIVIDUAL;
    }else{
      $biz_type = $borrower['biz_type'];
    }
    $validation_models = array("borrower");
    
    if($biz_type == Consts::INDIVIDUAL){
       $validation_models[] = "biz_address";
       $validation_models[] = "owner_person";
        if(array_key_exists('contact_persons', $borrower)){
          $validation_models[] = "contact_persons";
        }
          if($borrower['same_as_biz_address'] == false)
          {          
            $validation_models[]  = 'owner_address';
           }
  

    }

    return $validation_models;
  }


  public function create(array $borrower, $txn = true, $duplicate_checked = false){

    if(($borrower['biz_type'] == Consts::INDIVIDUAL || $borrower['biz_type'] == Consts::INSTITUTION) 
                && $duplicate_checked == false){
      $cust_reg_serv = new CustomerRegService();
      $cust_reg_serv->check_duplicate_borrower($borrower);
    }

    // $dp_code = session('acc_prvdr_code');
    // $acc_prvdr_code = $dp_code == 'UFLO' ? 'UMTN' : $dp_code;
    
    $capital_funds_repo = new CapitalFundRepositorySQL();
    $default_fund = $capital_funds_repo->get_record_by_many(
                                          ['is_lender_default', 'lender_code', 'country_code'], 
                                          [true, $borrower['lender_code'], $borrower['country_code']], 
                                          ['fund_code']);
                           
    if (isset($default_fund)) {
      $borrower["fund_code"] = $default_fund->fund_code;
    }
    else {
      thrw('No default fund configured for the lender');
    }                         


    $acc_prvdr_code = session('acc_prvdr_code');
    if(env('APP_ENV') == 'production'){
      $ap_codes = config('app.acc_prvdrs_with_data'); 
      $cust_data = DB::select("select run_id, csf_type from cust_csf_values where acc_number = ? and acc_prvdr_code = ? and csf_type like 'result%' ", [$borrower['acc_number'], $acc_prvdr_code]);
      
      if(count($cust_data) == 1){
        $borrower['csf_run_id'] = $cust_data[0]->run_id;

        $split_eligibility = explode(":", $cust_data[0]->csf_type);
        
        if($split_eligibility[1] == 'ineligible'){
          thrw("Can not create customer profile. Customer is not eligible to be registered as per their transaction data.");
        }      
      }
      else if(count($cust_data) > 1){
        thrw("Unable to create new customer because this customer has more than one run ID");
      }
      else if(count($cust_data) == 0 && in_array($borrower['acc_prvdr_code'], $ap_codes)){
        thrw("No data exists for this account number (".$borrower['acc_number'].") ");
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
      $borr_servv2 = new BorrowerServiceV2();
      #$borrower['biz_type'] = $borrower['borrower_type'];
      $borrower['acc_prvdr_code'] = session('acc_prvdr_code');
      $borrower['reg_flow_rel_mgr_id'] = session('user_person_id');
      $borrower['prob_fas'] = 0;
      $acc_number = $borrower['acc_number'];
      #$dp_code = $borrower['acc_prvdr_code'];
      // if($dp_code == 'CCA')
      // {
       #preg_match("/^[^0][0-9]{8}$/", $dp_cust_id) ? true : thrw("Invalid {$dp_code} Data Provider Customer ID");
       
      // }
      // else if($dp_code == 'UEZM')
      // {
        #preg_match("/^[0-9]{7,9}[/][\d]$/", $dp_cust_id) || preg_match("/^[0-9]{7,9}$/", $dp_cust_id) ? true : thrw("Invalid {$dp_code} Data Provider Customer ID");
      // }
      
      
      if($borrower['biz_type'] == Consts::INDIVIDUAL || $borrower['biz_type'] == Consts::INSTITUTION){
          $biz_address_id = $addr_repo->create($borrower['biz_address']);
      
          $borrower['biz_address_id'] = $biz_address_id;
          if(array_key_exists('gps' , $borrower)){
            $borrower['biz_address']['gps'] = $borrower['gps'];


          }

          if(array_key_exists('location' , $borrower['biz_address'])){
            $borrower['location'] = $borrower['biz_address']['location'];

          }
          if ($borrower['biz_type'] == Consts::INDIVIDUAL){
              $borrower['prob_fas'] = config('app.default_prob_fas');
              $person_id = $person_repo->create($borrower['owner_person']);
              $borrower['owner_person_id'] = $person_id;
      
              if($borrower['same_as_biz_address'] == false)  
              {
                $owner_address_id = $addr_repo->create($borrower['owner_person']['owner_address']);
                $borrower['owner_address_id'] = $owner_address_id;
              }else{
                $borrower['owner_address_id'] = $biz_address_id;
              }
            
          }elseif ($borrower['biz_type'] == Consts::INSTITUTION){
              $borrower['prob_fas'] = config('app.default_prob_fas');
              $org_repo = new OrgRepositorySQL();
    
              if($borrower['same_as_biz_address'] == false) {
                $biz_address_id = null;          
              }
    
              $org_id = $org_repo->create($borrower['org'], $biz_address_id);
              $borrower['org_id'] = $org_id;
            
          }
          
          
          if(array_key_exists('owner_person', $borrower)){
            $mob_num = $borrower['owner_person']['mobile_num'];  
            $borr_servv2->chk_same_mobile_num($borrower['owner_person'],"Owner person's");
          }
          
          
      
      }elseif ($borrower['biz_type'] == Consts::FLOW_RM){
        $person_id = $borrower['flow_rel_mgr_id'];
        $borrower['owner_person_id'] = $person_id;
      }elseif ($borrower['biz_type'] == Consts::DP_RM){
        $person_id = $borrower['dp_rel_mgr_id'];
        $borrower['owner_person_id'] = $person_id;
        
      }else{
        thrw("Invalid biz_type");
      }
     
     
               
      $cust_id = (new CommonRepositorySQL())->get_new_flow_id($borrower['country_code'], 'customer');
      $lender_code = session('lender_code');
      $cust_id =  "{$lender_code}-{$cust_id}";
      $borrower['cust_id'] = $cust_id;

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
         
      if($borrower['biz_type'] == Consts::INSTITUTION || $borrower['biz_type'] == Consts::INDIVIDUAL){
        if(isset($borrower['account'])){
            $borrower['account']['cust_id'] =  $cust_id;
            (new AccountRepositorySQL())->create($borrower['account']);
        }
  
        mount_entity_file("borrowers", $borrower, $cust_id, 'photo_biz_lic');
        mount_entity_file("borrowers", $borrower, $cust_id, 'photo_shop');
        
        // $sms_serv = new SMSNotificationService();  
        // $cust_name = $borrower['owner_person']['first_name'] ." ".$borrower['owner_person']['first_name'];

        // $mobile_verify_otp = $this->get_cust_reg_otp($borrower,$mob_num);
        // $dp_code = $borrower['acc_prvdr_code'];
        // $sms_serv->notify_welcome_customer(['cust_mobile_num' => $mob_num, 
        //                                       'country_code' => $borrower['country_code'],
        //                                       'acc_prvdr_code' => $dp_code,
        //                                       'cust_name' => $cust_name,
        //                                       'cust_id' => $cust_id,
        //                                        'customer_success' => config('app.customer_success_mobile')[$dp_code],
        //                                        'otp_code' => $mobile_verify_otp[0],
        //                                        'otp_id' => $mobile_verify_otp[1],
        //                                        'sms_reply_to' => config('app.sms_reply_to')[session('country_code')]
        //                                     ]);
      }
      

      

      //  if($borrower['use_as_ac_num'] == true )
      // {
      //   $acc_serv = new AccountService();
      //   $account = array();
      //   $account['cust_id'] = $borrower['cust_id'];
      //   $account['entity'] = 'customer';

      //   $split_dp_cust_id = explode("/", $borrower['data_prvdr_cust_id']);
      //   $account['acc_number'] = $split_dp_cust_id[0];
       

      //   $account['country_code'] = $this->country_code;

      //   // $acc_prvdr_code = $dp_code == 'UFLO' ? 'UMTN' : $dp_code;
      //   $account['acc_prvdr_code'] = $acc_prvdr_code;
      //   $account['type'] = 'wallet';
      //   $account['holder_name'] = $borrower['owner_person']['first_name'] ." ".$borrower['owner_person']['last_name'];
      //   $account['is_primary_acc'] = true;
      //   $account['acc_purpose'] = 'float_advance';
        
        
      //   $acc_serv->create($account, false);


      // }
      
      
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
  
  public function verify_mobile_number($person_id, $entity_verify_col){
        $person_repo = new PersonRepositorySQL();

        $person_repo->update_model(['id' => $person_id, $entity_verify_col => true]);

    }
  public function get_cust_reg_otp($borrower,$mob_num){

    //To get due date
    
    $otp_serv = new SMSService();
    $confirm_code = $otp_serv->get_otp_code(['entity_verify_col' => $borrower['entity_verify_col'], 'entity_update_value' => $borrower['entity_update_value'], 'cust_id' => $borrower['cust_id'], 'entity' => 'person', 'entity_id' => $borrower['person_id'],
                                    'otp_type' => 'verify_mobile','mobile_num' => $mob_num,'country_code'=>session('country_code')]);
    
    return $confirm_code;


    
  }

    public function set_cust_app_access($cust_id,$status)
    {
        $brwr_repo = new BorrowerRepositorySQL();
        $person_repo = new PersonRepositorySQL();
        $borrower = $brwr_repo->find_by_code($cust_id,['owner_person_id','lender_code']);
        $person_id = $borrower->owner_person_id;

        $lender_code = $borrower->lender_code;
        $person = $person_repo->find($person_id,['mobile_num','country_code']);

        $user = DB::table('app_users')->where('mobile_num',$person->mobile_num)->pluck('id');
        if(isset($user[0])){
            DB::table('app_users')->where('mobile_num',$person->mobile_num)->update(['status' => $status, 'updated_at' => now(), 'updated_by' => \session('user_id')]);
        }
        else {
            $pass = rand(111111,999999);
            DB::table('app_users')->insert(['person_id' => $person_id, 'mobile_num' => $person->mobile_num, 'password' => bcrypt($pass), 'country_code' => $person->country_code, 'is_new_user' => 1,'role_codes' => 'customer', 'belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'lender_code' => $lender_code, 'status' => $status, 'created_at' => now(), 'created_by' => \session('user_id')]);
        }
        if($status == 'enabled'){
          return " The Customer App Access for this customer is Enabled";
        }elseif ($status == 'disabled'){
          return " The Customer App Access for this customer is Disabled";
        }
  }

  public function get_cust_os($cust_id){
    
    $borrowers = $this->search_borrower($cust_id, ['cust_id','ongoing_loan_doc_id', 'country_code', 'acc_prvdr_code']);
    
    if($borrowers & sizeof($borrowers) > 0){
      if(sizeof($borrowers) > 1){
        thrw("Unable to locate a single customer profile for {$cust_id}");
      }
      $borrower = $borrowers[0];
      $ongoing_loan_doc_id = $borrower->ongoing_loan_doc_id;
      
      $loan = null;
       
      
      if(isset($ongoing_loan_doc_id) && $ongoing_loan_doc_id != "")
      {
         $loan_repo = new LoanRepositorySQL(); 
         $loan = $loan_repo->get_outstanding_loan($ongoing_loan_doc_id); 
      }else{
        thrw("No due amount");
        
      }
     
      return ["status" =>  "success", 
              "fa_status" => "due on ". $loan->due_date, 
              "due_date" => $loan->due_date,
              "disbursal_date" => $loan->disbursal_date,
              "due_amount" => $loan->current_os_amount,
              "flow_fee" =>  $loan->flow_fee,
              "contact" =>  config('app.customer_success')[$loan->acc_prvdr_code],
              "loan_doc_id" => $ongoing_loan_doc_id,
              "flow_cust_id" => $borrower->cust_id];
    }
    else
    {
          thrw("User does not have a customer profile");
    }
  }
  
  public function get_cust_profile($cust_id){
          
    $borrowers = $this->borrower_search(['req_parameter' => $cust_id]);
    if($borrowers & sizeof($borrowers) > 0){
      $borrower = $borrowers[0];
      $ongoing_loan_doc_id = $borrower->ongoing_loan_doc_id;
      $pending_loan_appl_doc_id = $borrower->pending_loan_appl_doc_id;
      $loan = null;
      $loan_appl = null;
       
      
      if(isset($ongoing_loan_doc_id) && $ongoing_loan_doc_id != "")
      {
         $loan_repo = new LoanRepositorySQL(); 
         $loan = $loan_repo->get_outstanding_loan($ongoing_loan_doc_id); 
      }
      else if(isset($pending_loan_appl_doc_id) &&  $pending_loan_appl_doc_id != "")
      {
          $loan_appl_repo = new LoanApplicationRepositorySQL();
          $loan_appl = $loan_appl_repo->get_loan_appl($pending_loan_appl_doc_id);
      }
     $aggr_serv = new AgreementService();
     $agreement_type = $aggr_serv->check_mobile_agreement($cust_id);
      return ['borrower' => $borrower, 'loan' => $loan , 'loan_appl' => $loan_appl,'agreement_type'=>$agreement_type];
    }
    else
    {
          return $this->respondWithError("User does not have a Customer Profile");
    }
  }
  public function update(array $borrower){    
    $brwr_repo = new BorrowerRepositorySQL();
    $borrower['country_code'] = $this->country_code;
    $brwr_repo = new BorrowerRepositorySQL();
      if(array_key_exists('cust_id', $borrower)){
        $result = $brwr_repo->update_model($borrower,'cust_id');
        $cust_id = $borrower['cust_id'];
      }else{
        $result  = $brwr_repo->get_record_by('id',$borrower['id'],['cust_id']);
        $cust_id = $result->cust_id;
        $result = $brwr_repo->update_model($borrower);
      }

      if(array_key_exists('photo_biz_lic', $borrower)){
        mount_entity_file("borrowers", $borrower, $cust_id, 'photo_biz_lic');
      }else if(array_key_exists('photo_shop', $borrower)){
        mount_entity_file("borrowers", $borrower, $cust_id, 'photo_shop');
      }
    
    return $result;
  }

  /*private function mount_cust_files($borrower, $cust_id){

      $source_file = separate([flow_file_path(),$borrower['country_code'],"borrowers","tmp",'photo_biz_lic',$borrower['photo_biz_lic']]);

      $rel_path = separate([flow_file_path(),$borrower['country_code'],"borrowers",$cust_id,'photo_biz_lic']);
      create_dir($rel_path);
        
      $dest_file = $rel_path.DIRECTORY_SEPARATOR.$borrower['photo_biz_lic'];
      File::copy($source_file, $dest_file);

      $source_file = separate([flow_file_path(),$borrower['country_code'],"borrowers","tmp",'photo_shop',$borrower['photo_shop']]);

      $rel_path = separate([flow_file_path(),$borrower['country_code'],"borrowers",$cust_id,'photo_shop']);
      create_dir($rel_path);
     

      $dest_file = $rel_path.DIRECTORY_SEPARATOR.$borrower['photo_shop'];
      File::copy($source_file, $dest_file);
      

  }*/
  

  public function update_person(array $person){
    
    $this->check_within_person_record($person);

    $this->check_across_person_record($person);
    $person_repo = new PersonRepositorySQL();
 		$result = $person_repo->update_model($person);
 		
      $person['country_code'] = $this->country_code;    
      //$result  = parent::get_record_by('id',$person['id'],['cust_id'],$this->country_code);

      if(array_key_exists('photo_national_id_back', $person)){
        mount_entity_file("persons", $person, $person['id'], 'photo_national_id_back');
      }
      if(array_key_exists('photo_national_id', $person)){
        mount_entity_file("persons", $person, $person['id'], 'photo_national_id');
      }else if(array_key_exists('photo_pps', $person)){
        mount_entity_file("persons", $person, $person['id'], 'photo_pps');
      }else if(array_key_exists('photo_selfie', $person)){
        mount_entity_file("persons", $person, $person['id'], 'photo_selfie');
      }
    
    return $result;
	} 
  public function get_third_party_details($data){
    $account_repo = new AccountRepositorySQL();
    $tp_owner_id=$account_repo->get_record_by_many(['cust_id','status'],[$data['cust_id'],'enabled'],['tp_acc_owner_id','photo_consent_letter','acc_number','acc_prvdr_code','acc_prvdr_name','cust_id']);
    
    if(isset($tp_owner_id->tp_acc_owner_id)){
      $person_repo=new PersonRepositorySQL();
      $thirdparty_details=$person_repo->find($tp_owner_id->tp_acc_owner_id,['first_name','dob','national_id','alt_biz_mobile_num_1',
    'middle_name','gender','mobile_num','alt_biz_mobile_num_2','last_name','email_id','whatsapp','photo_national_id ','photo_national_id_back']);
    
      $thirdparty_details->photo_consent_letter_file_path = get_file_path("borrowers", $tp_owner_id->cust_id,"photo_consent_letter");;
      $thirdparty_details->photo_national_id_back_path = get_file_path("persons", $tp_owner_id->tp_acc_owner_id,"photo_national_id_back");;
      $thirdparty_details->photo_national_id_path = get_file_path("persons", $tp_owner_id->tp_acc_owner_id,"photo_national_id");;
      $thirdparty_details->tp_acc_owner_id = $tp_owner_id->tp_acc_owner_id;
      $thirdparty_details->photo_consent = $tp_owner_id->photo_consent_letter;
      $thirdparty_details->acc_number = $tp_owner_id->acc_number;
      $thirdparty_details->acc_prvdr_code = $tp_owner_id->acc_prvdr_code;
      $thirdparty_details->acc_prvdr_name = $tp_owner_id->acc_prvdr_name ;
      return ["third_party" => (array)$thirdparty_details];

    
    }
 
   
  }
	
  public function check_within_person_record($person){
    $borr_servv2 = new BorrowerServiceV2();
    $person_repo = new PersonRepositorySQL();
    
    $mobile_num_fields = ['mobile_num' => null, 'alt_biz_mobile_num_1' => null, 'alt_biz_mobile_num_2' => null];
    $req_num_fields = array_intersect_key($person, $mobile_num_fields);
    
    if(sizeof($req_num_fields) > 0){
      $fields_to_check=array_keys(array_diff_key($mobile_num_fields, $req_num_fields));
      // array_keys(array_diff_key($mobile_num_fields, $fields));
      if(sizeof($fields_to_check) > 0){
        $person_repo = new PersonRepositorySQL();
        $person_data = $person_repo->find($person['id'],$fields_to_check);
        $person_data = (array) $person_data;
        
        $data = array_merge($person_data,$req_num_fields);
      }
      else{
        $data = $req_num_fields;
      }

      $borr_servv2->chk_same_mobile_num($data,"Owner person's");
    }
  }

  public function check_across_person_record($person){
    $borrower_repo = new BorrowerRepositorySQL();
    $cust_reg_serv = new CustomerRegService();
    $person_ck_res = $cust_reg_serv->get_person_id_fields($person);
    
    if($person_ck_res && sizeof($person_ck_res['dup_person_ids']) > 0){
      $field_names = array_fill(0, sizeof($person_ck_res['dup_person_ids']), "owner_person_id");
      
      $dupes = $borrower_repo->get_records_by_any($field_names, $person_ck_res['dup_person_ids'], ['cust_id', 'data_prvdr_cust_id', 'profile_status']);
      $person_ck_res['dupes'] = $dupes;
      $cust_reg_serv->check_dup_result('owner_person', $person_ck_res, $person_ck_res['dup_person_ids']);

      $dupes = $borrower_repo->get_records_by('cust_id', $person_ck_res['dup_cust_ids'], ['cust_id', 'data_prvdr_cust_id', 'profile_status']);
        $person_ck_res['dupes'] = $dupes;
      $cust_reg_serv->check_dup_result('contact_person', $person_ck_res);

    }
  }
  


  public function get_aggr_file_rel_path($cust_id, $current_aggr_doc_id){

    if($current_aggr_doc_id){
      
      $aggr_file = get_file_rel_path($cust_id, "agreement", "pdf").DIRECTORY_SEPARATOR.$current_aggr_doc_id.".pdf";
     
      if(File::exists(flow_file_path().$aggr_file)){
        return separate(['files', $aggr_file]);
      }else{
        return  null;
      }
    }
  
  }

  public function get_borrower($search_param , $check_status = true){

        $person_repo = new PersonRepositorySQL();
        $borrower_repo = new BorrowerRepositorySQL();
        $borrowers = $this->search_borrower($search_param);
        if(sizeof($borrowers) == 1){
          $borrower = $borrowers[0];
          $borrower = $borrower_repo->view($borrower->cust_id, ["borrower", "owner_person", "biz_address","contact_person"],
          ["borrower" => ["id", "status","acc_number", "biz_name", "cust_id", "biz_type", 
          "owner_person_id", "biz_address_id", "lender_code", "acc_prvdr_code", "flow_rel_mgr_id", "reg_flow_rel_mgr_id", "acc_prvdr_code",
          "dp_rel_mgr_id", 'perf_eff_date', "tot_loan_appls","tot_loans","late_loans",
          "first_loan_date","current_aggr_doc_id","aggr_valid_upto","csf_run_id", 
          "country_code","prob_fas","kyc_status","category","acc_purpose","aggr_status","location"],
          "owner_person" => ["id","mobile_num","first_name","last_name","middle_name","photo_pps", "national_id"],
          "contact_persons"=> ["id","mobile_num","first_name","last_name","middle_name"], "biz_address"=> ["*"]]);

          if($borrower->aggr_valid_upto && $borrower->aggr_valid_upto < Carbon::now()){
            $borrower->is_aggr_valid = false;
          }else{
            $borrower->is_aggr_valid = true;
          }
          $borrower->cust_name = full_name($borrower->owner_person);
          $borrower->cust_addr_text = full_addr($borrower->biz_address);
          $borrower->cust_mobile_num = $borrower->owner_person->mobile_num;
          $borrower->photo_pps_path = get_file_path("persons",$borrower->owner_person_id,"photo_pps");
          $borrower->photo_pps = $borrower->owner_person->photo_pps;
          $borrower->borrower_id = $borrower->id;
          $borrower->national_id = $borrower->owner_person->national_id;
          $this->get_extra_fields($borrower);
       
          $borrower->biz_address = null;
          $borrower->owner_person = null;          
         
          $borrower->aggr_file_rel_path = $this->get_aggr_file_rel_path($borrower->cust_id, $borrower->current_aggr_doc_id);
          
          if($check_status && $borrower->status == "disabled"){
            thrw("Customer for the search {$search_param} is in disabled status");
          }
         
          //Log::warning($borrower->aggr_file_rel_path);
          return $borrower;
        }else if (sizeof($borrowers) > 1){
          return $borrowers;
          //thrw("More than one result found. Please refine your search");
        }

      return null;
    }

    private function get_extra_fields(&$borrower){

      $lender_repo = new LenderRepositorySQL();
      $lender_details = $lender_repo->find_by_code($borrower->lender_code,["name", "lender_code"]);

      $acc_prvdr_repo= new AccProviderRepositorySQL();
      $acc_prvdr_details = $acc_prvdr_repo->find_by_code($borrower->acc_prvdr_code, ["name", "acc_prvdr_code"]);

      $person_repo = new PersonRepositorySQL();
      $reg_flow_rel_mgr = $person_repo->find($borrower->reg_flow_rel_mgr_id, ["first_name", "middle_name", "last_name"]);
      $flow_rel_mgr = $person_repo->find($borrower->flow_rel_mgr_id, ["first_name", "middle_name", "last_name","mobile_num","id"]);
       
      $dp_rel_mgr =  $person_repo->find($borrower->dp_rel_mgr_id, ["first_name", "middle_name", "last_name", "mobile_num", "id"]);

      $account_repo = new AccountRepositorySQL();
      $accounts = $account_repo->get_accounts_by(["cust_id","status", "acc_purpose"], [$borrower->cust_id,'enabled', 'float_advance'], ["type", "acc_number", "acc_prvdr_name","acc_prvdr_code"]);

      if(sizeof($accounts)  > 0){ 
        $borrower->account_id = $accounts[0]->id;
        $borrower->acc_type = $accounts[0]->type;
        $borrower->acc_prvdr_name = $accounts[0]->acc_prvdr_name;
        $borrower->acc_number = $accounts[0]->acc_number;
     }


      $borrower->lender_name = $lender_details->name;
      $borrower->lender_code = $lender_details->lender_code;
      $borrower->acc_prvdr_code = $acc_prvdr_details->acc_prvdr_code;
      // $borrower->data_prvdr_id = $data_prvdr_details->id;
      $borrower->acc_prvdr_name = $acc_prvdr_details->name;
      // $borrower->contract_name = $data_prvdr_details->contract_name;
      
      $borrower->reg_flow_rel_mgr_name = full_name($reg_flow_rel_mgr);

      $borrower->flow_rel_mgr_id = $flow_rel_mgr->id;
      $borrower->flow_rel_mgr_mobile_num = $flow_rel_mgr->mobile_num;
      $borrower->flow_rel_mgr_name = full_name($flow_rel_mgr);

      $borrower->dp_rel_mgr_id = $dp_rel_mgr->id;

      $borrower->dp_rel_mgr_mobile_num = $dp_rel_mgr->mobile_num;
      $borrower->dp_rel_mgr_name = full_name($dp_rel_mgr);
    }

  public function view($data, array $models = ["*"], array $columns = ["borrower" => ["*"], "owner_person" => ["*"], "owner_address" => ["*"], "org" => ["*"], "reg_address" => ["*"], "biz_address" => ["*"], "contact_person" => ["*"],"contact_persons" => ["*"],"contact_address" => ["*"]]){
 
    $person_repo = new PersonRepositorySQL();
    $brwr_repo = new BorrowerRepositorySQL();
    $addr_repo = new AddressInfoRepositorySQL();
    $loan_appl_serv = new LoanApplicationService();
    $lead_repo = new LeadRepositorySQL();
    // TO DO SET Country Code in addr_repo
      //var_dump($id);

    $cust_id = $data['cust_id'];
   
    $borrower = $brwr_repo->find_by_code($cust_id, $columns["borrower"]);
    if($borrower == null){
      $borrower = $this->get_one_borrower($cust_id, $columns["borrower"]);
    }

    if($borrower == null){
      thrw("Unable to search customer $cust_id");
      
    }
    if(array_key_exists('screen',$data) && $data['screen'] == "view"){
      $this->get_extra_fields($borrower);
    }
    $borrower_file_rel_path = get_file_rel_path("borrowers", $borrower->cust_id);
    if($borrower->biz_type == Consts::INDIVIDUAL){
      
      if(in_array("*", $models) || in_array("owner_person", $models)){


        $borrower->owner_person = $person_repo->find($borrower->owner_person_id, $columns["owner_person"]);
        if($borrower->owner_person){
          $borrower->owner_name = full_name($borrower->owner_person);
          $file_rel_path = get_file_rel_path("persons", $borrower->owner_person_id);
          $borrower->owner_person->photo_national_id_path = separate(["files", $file_rel_path, "photo_national_id"]);
          $borrower->owner_person->photo_national_id = $borrower->owner_person->photo_national_id;
          $borrower->owner_person->photo_national_id_back_path = separate(["files", $file_rel_path, "photo_national_id_back"]);

          $borrower->photo_pps_path = separate(["files", $file_rel_path, "photo_pps"]);
          $borrower->photo_pps = $borrower->owner_person->photo_pps;

          $borrower->photo_pps_path = separate(["files", $file_rel_path, "photo_pps"]);
          
          $borrower->photo_selfie = $borrower->owner_person->photo_selfie;
          
          $borrower->photo_selfie_path = separate(["files", $file_rel_path, "photo_selfie"]);
          
          $borrower->photo_shop_path = separate(["files", $borrower_file_rel_path, "photo_shop"]);
          $borrowerphoto_shop = $borrower->photo_shop;

          #$borrower->owner_person->file_rel_path = file_rel_path();
        }
      }

      if(in_array("*", $models) || in_array("owner_address", $models)){
        if($borrower->owner_person){
        $borrower->owner_person->owner_address = $addr_repo->find($borrower->owner_address_id, $columns["owner_address"]);
        }
      }

    }elseif ($borrower->biz_type == Consts::INSTITUTION){
      $org_repo = new OrgRepositorySQL();

      if(in_array("*", $models) || in_array("org", $models)){
        $org  = $org_repo->view($borrower->org_id, $columns["org"]);
        //Log::warning($org);
        if(in_array("reg_address", $models))
        { 
          $address = $addr_repo->find($org->reg_address_id, $columns["reg_address"]);
          $org->reg_address = $address;
        } 
        $borrower->org = $org;
      }
    }
    $borrower->aggr_file_rel_path = $this->get_aggr_file_rel_path($borrower->cust_id, $borrower->current_aggr_doc_id);
    if($borrower->aggr_valid_upto){
      $borrower->aggr_valid_upto = format_date($borrower->aggr_valid_upto);
    }else if($borrower->category == 'Probation' || $borrower->category == 'Condonation' ){
      $borrower->aggr_valid_upto = "Until Completing ".$borrower->prob_fas." FAs";
    }else{
      $borrower->aggr_valid_upto = "NA";
    }

    if(isset($borrower->file_data_consent)){
      $file_rel_path = get_file_rel_path("leads",$borrower->lead_id) ;
      $borrower->data_consent_path = separate(["files", $file_rel_path, "signed_consent"]);
    }
    if(isset($borrower->biz_address_id) && (in_array("*", $models) || in_array("biz_address", $models))){
      $biz_address = $addr_repo->find($borrower->biz_address_id, $columns["biz_address"]);
      
      $borrower->biz_address = $biz_address;
      $borrower->cust_addr_txt = full_addr($biz_address);
      //$borrower->biz_address['photo_shop_path'] = separate(["files", $borrower_file_rel_path, "photo_shop"]);
      //$borrower->biz_address['photo_shop'] = $borrower->photo_shop;
      


    }
    

    if(in_array("*", $models) || in_array("contact_person", $models)){


      $contact_persons = $person_repo->get_handlers($cust_id,$columns["contact_persons"]);

      if($contact_persons){
        foreach($contact_persons as $contact_person){
          $contact_address = $addr_repo->find($contact_person->address_id, $columns["contact_address"]);
          $contact_person->contact_address = $contact_address;

          $file_rel_path = get_file_rel_path("persons", $contact_person->id);
          $contact_person->photo_national_id_path = separate(["files", $file_rel_path, "photo_national_id"]);
          $contact_person->photo_pps_path = separate(["files", $file_rel_path, "photo_pps"]);
          $borrower->contact_persons[] = $contact_person;
          
        }

      }
    }
    
    
    //$borrower->photo_shop_path = flow_file_path().$photo_shop_path;
    $borrower->photo_biz_lic_path = separate(["files",$borrower_file_rel_path, "photo_biz_lic"]);
    $borrower->max_allowed_condonation = config('app.max_allowed_condonation');

    if($borrower->category == 'Probation'){
      $borrower->tot_prob_cond_fas = config('app.default_prob_fas');
    }
    else if($borrower->category == 'Condonation'){
      $borrower->tot_prob_cond_fas = config('app.default_cond_fas');
    }
    
    
     if(!$borrower->allow_force_checkin_on || date('Y-m-d', strtotime($borrower->allow_force_checkin_on)) != date_db()){
        $borrower->show_force_checkin_btn = true;
     }
     $person_id = DB::selectOne("select owner_person_id from borrowers b, app_users ap where b.owner_person_id = ap.person_id and ap.status = 'enabled' and b.cust_id = ? and b.country_code = ? ",[$data['cust_id'],session('country_code')]);     
     if($person_id == null){
       $borrower->cust_app_access = false;
     }else{
        $borrower->cust_app_access = true;
     }
    //$borrower->photo_biz_lic_path = flow_file_path().$photo_biz_lic_path;
    
#      $borrower->file_rel_path = file_rel_path();


    //$borrower->all_ineligible = $loan_appl_serv->check_if_all_ineligible($borrower);
    
    
    return $borrower;   

  }
  /*
  public function search_borrower($search_param, $status = 'enabled'){
    
    $person_repo = new PersonRepositorySQL();
    //$person_details = $person_repo->get_person_by("mobile_num", $search_param, ["id"]);
    $field_names = ["mobile_num","national_id"];
    $field_values = [$search_param, $search_param];
    $person_id_arr = $person_repo->pluck_by_any($field_names, $field_values, ["id"]);
    $borrower_repo = new BorrowerRepositorySQL();
      $no_of_persons = sizeof($person_id_arr);
      $result = null;
      if($no_of_persons == 0){
        $result = $borrower_repo->get_records_by("cust_id", $search_param, ["*"], $this->country_code, $status);

        if(empty($result)){
          
          $result = $borrower_repo->get_records_by("data_prvdr_cust_id", $search_param, ["*"], $this->country_code, $status);
        }
      }else if($no_of_persons == 1){
        $result = $borrower_repo->get_records_by("contact_person_id", $person_id_arr[0], ["*"], $this->country_code, $status);
      }
      else{
        $result = $borrower_repo->get_records_by("contact_person_id", $person_id_arr, ["*"], $this->country_code, $status);
      } 

      return $result;
   
  }
  */
   public function search_borrower($search_param, $select_fields = ['*'], $check_by_cust_id_first = true){
    
    $borrower_repo = new BorrowerRepositorySQL();
    $person_repo = new PersonRepositorySQL();
    $person_ids = array();
    $borrowers = array();

    $field_names = ["acc_number", "status"];
    $field_values = [ $search_param, "enabled"];
    
    $acc_repo = new AccountRepositorySQL();
    $customers = $acc_repo->get_records_by_many($field_names, $field_values, ['cust_id']);
    
    if(sizeof($customers) == 0){
      $field_names = ["alt_acc_num", "status"];
      $field_values = [ $search_param, "enabled"];

      $customers = $acc_repo->get_records_by_many($field_names, $field_values, ['cust_id']);
    }

    $cust_ids=[];
    foreach($customers as $cust){
      array_push($cust_ids, $cust->cust_id);
    }
    $cust_ids = array_unique($cust_ids);
    
    if(sizeof($cust_ids) == 1){
      $field_names = ["cust_id"];
      $field_values = [$cust_ids[0]];
    }else if(sizeof($cust_ids) > 1){
      $field_names = array_fill(0, sizeof($cust_ids), 'cust_id');
      $field_values  = $cust_ids;
    }else{
      $field_names = ["cust_id"];
      $field_values = [$search_param];
    }

    $addl_sql_condition = "";
    $add_country = true;
    if(session('country_code') == 'global'){
        $addl_sql_condition = "";
        $add_country = false;  
    }
    if($check_by_cust_id_first){
        $borrowers = $borrower_repo->get_records_by_any($field_names, $field_values, $select_fields, $addl_sql_condition, $add_country);
    }
   
    if(sizeof($borrowers) > 0 ){
        return $borrowers;
    }else{
       
        $field_names = ["mobile_num","national_id"];
        $field_values = [$search_param, $search_param];
        $person_ids = $person_repo->pluck_by_any($field_names, $field_values, ["id"]);

      
        if(sizeof($person_ids) == 0){
          $sch_serv = new ScheduleService();
          $cust_ids = $sch_serv->get_cust_id($search_param);
          if (sizeof($cust_ids) == 0){
            return [];
          }  
          else if(sizeof($cust_ids) == 1){
            return $borrower_repo->get_records_by('cust_id', $cust_ids[0], $select_fields);
          }
          else if(sizeof($cust_ids) > 1){
            $field_names = array_fill(0, sizeof($cust_ids), "cust_id");
            $field_values = array_values($cust_ids);
          }
        }

        else if(sizeof($person_ids) == 1){
            $field_names = ["owner_person_id"];
            $field_values = [$person_ids[0]];
        }
        else if (sizeof($person_ids) > 1){
          #unset($person_ids[0]);
          return $borrower_repo->get_records_by('owner_person_id', [$person_ids, session('acc_prvdr_code')], $select_fields);
        } 
        
        return  $borrower_repo->get_records_by_any($field_names, $field_values, $select_fields, $addl_sql_condition, $add_country);
    } 
       
      
  }



  private function get_add_sql_condition(&$criteria_array){
    $addl_sql_condition_arr = array();
    if(!array_key_exists('have_ongoing_loan', $criteria_array)){
      $criteria_array['have_ongoing_loan'] = false; 
    }

    if($criteria_array['have_ongoing_loan'] == true){
       $loan_repo = new LoanRepositorySQL(); 
        $ongoing_customer = $loan_repo->get_ongoing_cust_id();
    }else{
      $ongoing_customer = 'ongoing_loan_doc_id IS NOT NULL'; 
    }

    $criteria_arr = ['have_ongoing_loan' => $ongoing_customer, 
                    'have_overdue_loan' => 'is_og_loan_overdue = 1', 
                    'have_pending_loan_appl' => 'pending_loan_appl_doc_id IS NOT NULL', 
                    'do_not_have_score' =>  'csf_run_id IS NULL',
                    #'active_cust' => "(ongoing_loan_doc_id is NOT NULL or last_loan_date >= '".Carbon::now()->subDays(30)."')",
                    'active_cust' => "last_loan_date >= '".Carbon::now()->subDays(30)."'",
                    'have_active_agrmnt' => "aggr_status = 'active'",
                    'not_have_ongoing_loan' => "ongoing_loan_doc_id IS NULL", 
                    'not_have_overdue_loan' => '(is_og_loan_overdue = 0 or is_og_loan_overdue is null)', 
                    'not_have_pending_loan_appl' =>  'pending_loan_appl_doc_id IS NULL', 
                    'have_score' => 'csf_run_id IS NOT NULL',
                    #'non_active_cust' => "(ongoing_loan_doc_id is NULL and (last_loan_date IS NULL or last_loan_date < '".Carbon::now()->subDays(30)."'))",
                    'non_active_cust' => "(last_loan_date IS NULL or last_loan_date < '".Carbon::now()->subDays(30)."')",
                    'not_have_active_agrmnt' => "aggr_status = 'inactive'",
                    // 'have_expired_agrmnt' => "((prob_fas = 0 and aggr_valid_upto is null)  or (aggr_valid_upto <'" .Carbon::now()->addDays(7)."'  and prob_fas = 0) or aggr_status = 'inactive') and status = 'enabled'",
                    'have_expired_agrmnt' => "((aggr_valid_upto between '" .Carbon::now()->subDays(7). "'and'" .Carbon::now()->addDays(14)."'  and prob_fas = 0) or (aggr_status = 'inactive' and status ='enabled')) ",
                    'belong_to_user' => "flow_rel_mgr_id = ".session('user_person_id'),
                    'pending_kyc' => "(status != 'disabled' or last_loan_date is null) and kyc_status = 'pending' and profile_status = 'open' and reg_date > '2021-09-01'",
                    // 'cust_needs_visit'=> "(next_visit_date IS NOT NULL or ((aggr_valid_upto between '" .Carbon::now()->subDays(7). " 'and' " .Carbon::now()->addDays(7)."'  and prob_fas = 0) or (aggr_status = 'inactive' and status ='enabled')) or is_og_loan_overdue = 1)",
                    // 'pre_approved_cust' => "pre_appr_count > 0"
                  ];

  if(array_key_exists('profile_status', $criteria_array) && $criteria_array['profile_status'] == 'closed'){
    $criteria_array['profile_status'] = 'true';
    $criteria_arr['profile_status'] = "profile_status in ('open', 'closed')";
  }
  if(array_key_exists('cust_needs_visit',$criteria_array)){
    $criteria_array['cust_needs_visit'] = true;
    $criteria_arr['cust_needs_visit'] = "(next_visit_date IS NOT NULL or ((aggr_valid_upto between '" .Carbon::now()->subDays(7). " 'and' " .Carbon::now()->addDays(7)."'  and prob_fas = 0) or (aggr_status = 'inactive' and status ='enabled')) or is_og_loan_overdue = 1)";
  }
  if(array_key_exists('pre_approved_cust',$criteria_array)){
    $criteria_array['pre_approved_cust'] = true;
    $cur_date = carbon::now()->format('Y-m-d');
    $criteria_arr['pre_approved_cust'] = "pre_appr_count > 0 and  date(pre_appr_exp_date) >=  '$cur_date' ";
  }

  //if(array_key_exists('with_third_party_account',$criteria_array) && $criteria_array['with_third_party_account'] == true){
  //  $cust_w_tp_acc = (new AccountRepositorySQL)->get_cust_id_by_tp_acc();
  //  $criteria_arr['with_third_party_account'] = $cust_w_tp_acc;
  //}
  if(array_key_exists('with_tp_acc',$criteria_array) && $criteria_array['with_tp_acc'] == true){
    $criteria_arr['with_tp_acc'] = (new AccountRepositorySQL)->get_cust_id_by_tp_acc('with_tp_acc');
  }
  if(array_key_exists('without_tp_acc',$criteria_array) && $criteria_array['without_tp_acc'] == true){
    $criteria_arr['without_tp_acc'] = (new AccountRepositorySQL)->get_cust_id_by_tp_acc('without_tp_acc');

  }


  foreach ($criteria_arr as $criteria_key => $criteria) {
   if(array_key_exists($criteria_key, $criteria_array)){
      if($criteria_array[$criteria_key] == 'true'){
        $addl_sql_condition_arr[] = $criteria;
      }
      unset($criteria_array[$criteria_key]);
    }
  }

  if(array_key_exists('region', $criteria_array)){
     $addl_sql_condition_arr[]  = $this->add_region_condition($criteria_array);
     unset($criteria_array['region']);
  }

  if(array_key_exists('location', $criteria_array)){
     $addl_sql_condition_arr[]  = $this->add_location_condition($criteria_array);
     unset($criteria_array['location']);
  }

  if(array_key_exists('field_2', $criteria_array)){
    $addl_sql_condition_arr[]  = $this->add_district_condition($criteria_array);
    unset($criteria_array['field_2']);
 }


    $addl_sql_condition = "";
    if(sizeof($addl_sql_condition_arr) > 0){
      $addl_sql_condition = implode(" and ", $addl_sql_condition_arr);
      if(sizeof($criteria_array) > 0 ){
        $addl_sql_condition = " and " .$addl_sql_condition;
      }

    }

    

    if(array_key_exists('belong_to_user',$criteria_array))
    {
      $order_by = "aggr_valid_upto asc";
    
    }else if(array_key_exists('cust_needs_visit',$criteria_arr)){
      $order_by = "next_visit_date, last_visit_date";
    }
    else if(array_key_exists('pre_approved_cust',$criteria_arr)){
      $order_by = "pre_appr_count , pre_appr_exp_date asc";
    }
    else{
      $order_by = "status desc";
    }
    //Log::warning(Carbon::now()->addDays(7));
    return $addl_sql_condition." order by ".$order_by;
    //return $addl_sql_condition;
  }

  private function add_region_condition($criteria_array){
        $result = DB::selectOne("select field_num from addr_config where field_code = ? and country_code = ?  and status = ?  limit 1", ['region', $this->country_code, 'enabled']);
        $field_num = $result->field_num;
        
        $addr_repo = new AddressInfoRepositorySQL();
        $field_names = [$field_num, 'country_code'];
        $field_values = [$criteria_array['region'], $this->country_code];

        $addr_ids = $addr_repo->pluck_by_many($field_names, $field_values, ['id']);
        if(empty($addr_ids)){
          thrw ("No results for your search");
        }else
        {
          $addr_ids = implode(',', $addr_ids);
          return "biz_address_id in ($addr_ids)";
        }
   
  }

  private function add_district_condition($criteria_array){
    
    $addr_repo = new AddressInfoRepositorySQL();
    $field_names = ['field_2', 'country_code'];
    $field_values = [$criteria_array['field_2'], $this->country_code];

    $addr_ids = $addr_repo->pluck_by_many($field_names, $field_values, ['id']);
    if(empty($addr_ids)){
      thrw ("No results for your search");
    }else
    {
      $addr_ids = implode(',', $addr_ids);
      return "biz_address_id in ($addr_ids)";
    }

}

  private function add_location_condition($criteria_array){
        $result = DB::selectOne("select field_num from addr_config where field_code = ? and country_code = ?  and status = ?  limit 1", ['location', $this->country_code, 'enabled']);
        $field_num = $result->field_num;
        
        $addr_repo = new AddressInfoRepositorySQL();
        $field_names = [$field_num, 'country_code'];
        $field_values = [$criteria_array['location'], $this->country_code];

        $addr_ids = $addr_repo->pluck_by_many($field_names, $field_values, ['id']);
        if(empty($addr_ids)){
          thrw ("No results for your search");
        }else
        {
          $addr_ids = implode(',', $addr_ids);
          return "biz_address_id in ($addr_ids)";
        }
   
  }

  private function set_names(&$borrowers){

    $lender_codes = array();
    $acc_prvdr_codes = array();
    $dp_rel_mgr_ids = array();
    $person_repo = new PersonRepositorySQL();
    $flow_rel_mgr_ids = array();
    
    foreach ($borrowers as $borrower) {
       $lender_codes[] = $borrower->lender_code;
       $acc_prvdr_codes[] = $borrower->acc_prvdr_code;
       $flow_rel_mgr_ids[] = $borrower->flow_rel_mgr_id;
       $dp_rel_mgr_ids[] = $borrower->dp_rel_mgr_id;
       $flow_rel_mgr_ids[] = $borrower->reg_flow_rel_mgr_id;
       $person = $person_repo->get_person_name($borrower->owner_person_id);

       if($person)
       {
           $borrower->mobile_num = $person->mobile_num;
           $borrower->first_name = $person->first_name;
           $borrower->last_name = $person->last_name;
           $borrower->middle_name = $person->middle_name;
           $borrower->owner_name = full_name($person);
           
       }
    
      
      $fields_arr = ['first_name','middle_name','last_name'];
      $handler = $person_repo->get_handlers($borrower->cust_id,$fields_arr);
      
      if($handler){
        $borrower->handler_name = full_name($handler[0]);
      }


          
    }
    if(sizeof($lender_codes) > 0)
    {
    $lender_codes = array_unique($lender_codes);
    $lender_repo = new LenderRepositorySQL();
    $lender_names = array();
    $acc_prvdr_names = array();
    foreach ($lender_codes as  $lender_code) {
          $lender = $lender_repo->get_lender_name($lender_code);
          $lender_names[$lender_code] = $lender->name; 
      }
   }
   if(sizeof($acc_prvdr_codes)>0)
   {
    $acc_prvdr_codes = array_unique($acc_prvdr_codes);   
    $acc_prvdr_repo = new AccProviderRepositorySQL();
    foreach ($acc_prvdr_codes as $acc_prvdr_code) {
       $acc_prvdr = $acc_prvdr_repo->find_by_code($acc_prvdr_code, ["name"]);
       $acc_prvdr_names[$acc_prvdr_code] = $acc_prvdr->name;               
     } 
    }


    if(sizeof($flow_rel_mgr_ids)>0){
      $flow_rel_mgr_ids = array_unique($flow_rel_mgr_ids);
      $flow_rel_mgr_names = array();
      foreach($flow_rel_mgr_ids as $flow_rel_mgr_id){
        $flow_rel_mgr = $person_repo->find($flow_rel_mgr_id, ["first_name"]);
        if($flow_rel_mgr){
          $flow_rel_mgr_names[$flow_rel_mgr_id] = $flow_rel_mgr->first_name;
        }
      }
   
    }


    if(sizeof($dp_rel_mgr_ids)>0)
   {
    $dp_rel_mgr_ids = array_unique($dp_rel_mgr_ids);  
     $dp_rel_mgr_name = array();
    foreach ($dp_rel_mgr_ids as $dp_rel_mgr_id) {

      $dp_rel_mgr = $person_repo->find($dp_rel_mgr_id, ["first_name"]);
    if($dp_rel_mgr){
      $dp_rel_mgr_name[$dp_rel_mgr_id] =  $dp_rel_mgr->first_name ;

    }
     } 

    }

     foreach ($borrowers as $borrower){
           $borrower->lender_name = $lender_names[$borrower->lender_code];
           $borrower->acc_prvdr_name = $acc_prvdr_names[$borrower->acc_prvdr_code];
           $borrower->flow_rel_mgr_name = $flow_rel_mgr_names[$borrower->flow_rel_mgr_id];
           if($borrower->reg_flow_rel_mgr_id){
            $borrower->reg_flow_rel_mgr_name = $flow_rel_mgr_names[$borrower->reg_flow_rel_mgr_id];
           }

           if($borrower->dp_rel_mgr_id){
            $borrower->dp_rel_mgr_name = $dp_rel_mgr_name[$borrower->dp_rel_mgr_id];
           }
            $borrower->total_probation_fa_count = config('app.default_prob_fas');

          if($borrower->owner_person_id){
              $file_rel_path = get_file_rel_path("persons", $borrower->owner_person_id);

              $borrower->photo_pps_path = separate(["files", $file_rel_path, "photo_pps"]);
              
              $person_obj =  $person_repo->find($borrower->owner_person_id,['photo_pps']);
              if($person_obj){
                $borrower->photo_pps = $person_obj->photo_pps;
              }
        } 


      }
    }


    public function get_one_borrower($param, $select_fields = ['*']){
      $borrowers = $this->search_borrower($param, $select_fields);
      
      if(sizeof($borrowers) == 1){
        return $borrowers[0];
      }else if(sizeof($borrowers) > 1){
        thrw("More than one results found");
      }else{
        thrw("No matching borrower exist");
      }
    }
     public function borrower_search($criteria_array,  $fields_arr = ['cust_id',
     'master_cust_id','biz_name','biz_type','status','lender_code','owner_person_id','ongoing_loan_doc_id',
     'pending_loan_appl_doc_id', 'csf_run_id', 'first_loan_date', 'perf_eff_date','acc_prvdr_code',
     'tot_loan_appls', 'tot_loans', 'late_loans', 'is_og_loan_overdue','id','remarks', 
     'category','country_code','last_loan_doc_id','flow_rel_mgr_id','last_visit_date','dp_rel_mgr_id',
     'current_aggr_doc_id','aggr_valid_upto','prob_fas','category','reg_date','biz_address_id', 'activity_status', 'profile_status',
     'cond_count', 'last_loan_date','location','gps','territory','reg_flow_rel_mgr_id', 'risk_category', 'acc_number', "allow_force_checkin_on"]){
      m_array_filter($criteria_array);
     
      $addl_sql_condition = null;

      if(array_key_exists('mode', $criteria_array)){
        $mode = $criteria_array['mode'];
        unset($criteria_array['mode']);
      }else{
        $mode = 'search';
      }
      

      if(array_key_exists("req_parameter", $criteria_array)){
       
          $borrowers = $this->search_borrower($criteria_array['req_parameter']); 
          if(sizeof($borrowers) == 1){
            $borrower = $borrowers[0];
            // $criteria_array = ["cust_id" => $borrower->cust_id];
            if(array_key_exists('flow_rel_mgr_id', $criteria_array)){
              $criteria_array['cust_id'] = $borrower->cust_id;
              unset($criteria_array['req_parameter']);
            }
            else{
                $criteria_array = ["cust_id" => $borrower->cust_id];
            }
              
          }else if(sizeof($borrowers) == 0){
                thrw("Please enter a valid search criteria");
          }
          else{
            return ["results" => $borrowers, 'mode' => 'search'];
          }
          // else{
          //       thrw("More than one results found");
          // }
      }else{
       
        
          $addl_sql_condition = $this->get_add_sql_condition($criteria_array);
          
      }
    
      if($addl_sql_condition == null && sizeof($criteria_array) == 0){
            thrw("Please enter a valid search criteria");
            
      }

      $borrower_repo = new BorrowerRepositorySQL();

       $person_repo = new PersonRepositorySQL();
       $addr_repo = new AddressInfoRepositorySQL;

      //$addl_sql_condition = "country_code ='{$this->country_code}'";

     // $criteria_array['acc_prvdr_code'] = session::get('acc_prvdr_code');
      unset($criteria_array['mode']);
      $borrowers = $borrower_repo->get_records_by_many(array_keys($criteria_array),  array_values($criteria_array),  $fields_arr, "and", $addl_sql_condition);
      $this->set_names($borrowers);

    
    if(empty($borrowers)){
        thrw("No results found for your search!");
    }

    if($mode == 'view' && count($borrowers) == 1){
      return ["cust_id" => $borrowers[0]->cust_id, 'mode' => 'view'];
    }else{
        return ["results" => $borrowers, 'mode' => 'search'];
    }
    // return $borrowers;`
    }


    public function get_customer_details($cust_id)
    {
      $borrower_repo = new BorrowerRepositorySQL();
      $acc_prvdr_repo = new AccProviderRepositorySQL();
      $account_repo = new AccountRepositorySQL();
      $borrower = $borrower_repo->get_customer($cust_id);
      $acc_prvdr = $acc_prvdr_repo->find_by_code($borrower->acc_prvdr_code, ["name"]);
      $account = $account_repo->getCustomerAccount($cust_id);
      return ['acc_prvdr'=>$acc_prvdr,'account'=>$account];
    }

    public function allow_condonation($cust_id, $with_txn = true, $txn_date = null, $ignore_eligibility_check = false)
    {
      try
      {
          
          thrw("Condonation is not possible now.");
          $borrower_repo = new BorrowerRepositorySQL();
          $loan_appl_serv = new LoanApplicationService();

          $prob_fas = config('app.default_cond_fas');
          
          $prob_period_repo = new ProbationPeriodRepositorySQL();
          //$cust_probations = $prob_period_repo->get_records_by_many(['cust_id','type'],[$cust_id,'condonation']);

          $borrower = $borrower_repo->get_record_by('cust_id',$cust_id,['cust_id','cond_count','biz_type','acc_prvdr_code',
              'lender_code', 'current_aggr_doc_id', 'csf_run_id', 'acc_number','prob_fas', 'perf_eff_date',
              'country_code', 'category']);
          
          $borrower->all_ineligible = false;
          if($ignore_eligibility_check == false){
            [$all_ineligible, $all_eligible] = $loan_appl_serv->check_if_all_ineligible($borrower);
            $borrower->all_ineligible =$all_ineligible;
          }
          
          if($borrower->category == 'Condonation' || $borrower->category == 'Probation'){
            thrw("Can not allow condonation when a customer is on {$borrower->category}");
          }
          else if($borrower->all_ineligible || $ignore_eligibility_check ){
              $with_txn ? DB::beginTransaction() : null;
              $prob_period_repo = new ProbationPeriodRepositorySQL();
              $result = null;

              if(!$ignore_eligibility_check){
                $cust_prob = $prob_period_repo->get_record_by_many(['cust_id', 'status'],[$cust_id, 'active'], ['id']);

                if($cust_prob){
                    $prob_period_repo->complete_probation($cust_prob->id, $cust_id);
                }
  
              }
              $condonation_delay = config('app.condonation_punishment_delay');

              $start_date = $txn_date ? $txn_date : Carbon::now();
              $start_date = Carbon::parse($start_date)->addDays($condonation_delay)->startOfDay();
              
              $condonation_limit = config('app.max_allowed_condonation');
              
              if($borrower->cond_count < $condonation_limit){
                  
                  $prob_period_repo->start_probation($cust_id, 'condonation', $prob_fas, $start_date);
              
                  $data =['prob_fas'=> $prob_fas, 'category' => 'Condonation', 
                          'cust_id'=> $cust_id, 'perf_eff_date' => $start_date,
                          'cond_count' => $borrower->cond_count + 1, 'kyc_status' => 'pending'];
                          
                  $cust_kyc_repo = new CustKYCRepositorySQL();
                  $cust_kyc_repo->delete_cust_kyc_record($cust_id);

                  $result = $borrower_repo->update_model_by_code($data);
              }
              else if(!$ignore_eligibility_check){
                thrw("The customer has already exhausted their {$condonation_limit} chances of condonation");
              }
              $with_txn ? DB::commit() : null;
             
              return $result;
          }
          else if(!$ignore_eligibility_check){
            thrw("You can not allow condonation when the customer is eligible for one or more products");
          }
            
      }
     
      catch (\Exception $e) {
          $with_txn ? DB::rollback() : null;
          if ($e instanceof QueryException){
            throw $e;
          }else{
            thrw($e->getMessage());
          }
         
      }

      
    }

    public function get_borrower_profile($cust_id, $incl_addr = false, $incl_rel_mgr = false ,$incl_fa = false)
    {
      
      $borrower_repo = new BorrowerRepositorySQL();
      $person_repo = new PersonRepositorySQL();
      $addr_repo = new AddressInfoRepositorySQL();
      $loan_repo = new LoanRepositorySQL();
      $loan_appl_repo = new LoanApplicationRepositorySQL();
      $acc_repo = new AccountRepositorySQL();
      $acc_smt_repo = new AccountStmtRepositorySQL;


      $borrower = $borrower_repo->find_by_code($cust_id, ['owner_person_id', 'biz_name', 'biz_address_id', 'cust_id', 'acc_prvdr_code','last_visit_date','ongoing_loan_doc_id','last_loan_doc_id','pending_loan_appl_doc_id','is_og_loan_overdue','owner_person_id','acc_number','gps','location']);

      if($borrower){
        $cust_person = $person_repo->find($borrower->owner_person_id, ["first_name", "middle_name", "last_name","mobile_num","dob"]); 

        $borrower->cust_name = full_name($cust_person);
        $borrower->cust_mobile_num = $cust_person->mobile_num;
        $borrower->cust_dob = $cust_person->dob;
        
       
        if($incl_addr){
            $biz_address_id = $borrower->biz_address_id;
            $addr =  $addr_repo->find($biz_address_id);
            if($addr){
             $borrower->cust_addr_txt = short_addr($addr); 
            }
        }

        if($incl_rel_mgr){

          $flow_rel_mgr = $person_repo->find($borrower->flow_rel_mgr_id, ["first_name", "middle_name", "last_name"]); 
            if($flow_rel_mgr){
              $borrower->flow_rel_mgr_name = full_name($flow_rel_mgr);  
            }

        }  
        if($incl_fa){

          if($borrower->pending_loan_appl_doc_id && $loan_appl_repo->get_record_status_by_code($borrower->pending_loan_appl_doc_id)->status == 'approved') {
            $loan_appl = $loan_appl_repo->find_by_code($borrower->pending_loan_appl_doc_id,['loan_doc_id']);
            $borrower->current_loan = $this->get_loan_for_profile($loan_appl->loan_doc_id);
          }else if($borrower->ongoing_loan_doc_id) {
              $borrower->current_loan = $this->get_loan_for_profile($borrower->ongoing_loan_doc_id);
              $acc_stmt = (new AccountStmtRepositorySQL())->get_records_by_many(['loan_doc_id','recon_status'],[$borrower->ongoing_loan_doc_id, "10_capture_payment_pending"],['id','stmt_txn_id','cr_amt'],"and","order by stmt_txn_date desc");
              $borrower->acc_stmts = $acc_stmt;
          }else{
              if($borrower->last_loan_doc_id){
                $borrower->last_loan = $this->get_loan_for_profile($borrower->last_loan_doc_id); 
              }  
            }
        }
      }

      
      return $borrower;
    }
    
    

    public function get_loan_for_profile($loan_doc_id){
      $loan_repo = new LoanRepositorySQL();
      $acc_repo = new AccountRepositorySQL();
      $loan = $loan_repo->get_outstanding_loan($loan_doc_id);
      
      $loan->overdue_days = get_od_days($loan->due_date, $loan->paid_date, $loan->status);
      if($loan->cust_acc_id){
        $loan->to_acc_num = $acc_repo->get_acc_num($loan->cust_acc_id);
      }
      return $loan;
    }

    
    public function check_kyc_status($data){
      $key = key($data);
      $status = $data[$key]['status'];	
      $kyc_status = $data[$key]['kyc_status'];
      
      if($status == 'enabled' && $kyc_status != 'completed'){
        thrw("Please complete the KYC process before enable the customer.");
      }
    }
    public function validate_customer($data)

      { 
       
        $borrowers = $this->search_borrower($data['cust_token'], ['cust_id']);
        $incl_addr = false;
        $incl_rel_mgr = false;
        $incl_fa = false;
        if(sizeof($borrowers) > 1){
          thrw("More than one customer found"); # Have to handle in the future
        }else if(sizeof($borrowers) == 1){
          if(array_key_exists('incl_profile', $data)){
            if(array_key_exists('incl_addr', $data)){
            $incl_addr = $data['incl_addr'];
            }
            if(array_key_exists('incl_rel_mgr', $data)){
                $incl_rel_mgr = $data['incl_rel_mgr'];
            }
            if(array_key_exists('incl_fa', $data)){
                $incl_fa = $data['incl_fa'];
            }
            return $this->get_borrower_profile($borrowers[0]->cust_id, $incl_addr, $incl_rel_mgr, $incl_fa); 
          }

          return ["cust_id" => $borrowers[0]->cust_id];
        }else if (sizeof($borrowers) == 0){
          thrw("Please enter a valid customer ID / Mobile Number / National ID "); # Have to handle in the future

        }
    }

  public function update_status($data){
    $current_status = $data['borrowers']['status'];
    $cust_id = $data['borrowers']['cust_id'];
    $borrower_repo = new BorrowerRepositorySQL();
    $cust_aggr_repo = new CustAgreementRepositorySQL;
    $person_repo = new PersonRepositorySQL();
    $lead_repo = new LeadRepositorySQL();
    $sms_serv = new SMSNotificationService();
    $borrower= [];
    if($current_status == "enabled"){

      $borrower = $borrower_repo->find_by_code($cust_id, ['kyc_status','current_aggr_doc_id','aggr_valid_upto','prob_fas', 'flow_rel_mgr_id', 'first_loan_date', 'acc_prvdr_code', 'owner_person_id','lead_id','acc_purpose','lead_id','last_kyc_date']);

      $cust_aggr = $cust_aggr_repo->get_record_by_many(["aggr_doc_id", "status"],[$borrower->current_aggr_doc_id, 'active'],['duration_type','aggr_type','valid_upto']);

        $json_condition = ['cust_id' => $cust_id];
        $lead_data = $lead_repo->get_json_by('cust_reg_json', $json_condition, ['status'], $addl_sql = "and profile_status != 'closed'");
        if($lead_data && $lead_data->status != Consts::PENDING_ENABLE){
          thrw("Cannot enable this customer. A KYC process is still going on");
        }
      if($borrower->current_aggr_doc_id == null){
        thrw("Cannot enable this customer. No active agreement.");
      }
      if(($cust_aggr->duration_type == "days" && format_date($cust_aggr->valid_upto, 'Ymd') < format_date(carbon::now(), 'Ymd')) || ($cust_aggr->duration_type == "fas" && $borrower->prob_fas == 0) ){
        thrw("Cannot enable this customer. The Agreement is expired.");
      }
      if($borrower->flow_rel_mgr_id == 13 || $borrower->flow_rel_mgr_id == null){
        thrw("Can not enable a customer who is not assigned to an RM.");
      }
    }
    try
    {
      $common_repo = new CommonRepositorySQL();
      if($borrower && ($current_status == 'enabled'))
      {
        $last_kyc_date = $borrower->last_kyc_date;
        $today_date = Carbon::now();
        $days = $today_date->diffInDays(Carbon::parse($last_kyc_date));
        $kyc_cut_off_days = config('app.kyc_cut_off_days');
        // if($days > $kyc_cut_off_days && (!$this->is_cust_temp_disabled($cust_id))){
        if(false){
          DB::beginTransaction();
          $lead_serv = new LeadService();
          $account_repo = new AccountRepositorySQL();
          $account = $account_repo->get_account_by(['cust_id','status','acc_purpose'],[$cust_id,Consts::ENABLED,'float_advance'],['acc_prvdr_code','acc_number','cust_id','acc_purpose','id']);
          $lead_serv->initiate_rekyc_for_new_account((array)$account);
          DB::commit();
          
          thrw("Cannot enable this customer. Re-KYC is needed. \nNew Lead has been created to perform Re-KYC.");
        }else{
          $update_status = $common_repo->update_status($data);
        }
      }else{
        $update_status = $common_repo->update_status($data);
      }
      
      // $borrower_repo = new BorrowerRepositorySQL();
      // $current_status= $data['borrowers']['status'];
      // $reason = $data['borrowers']['status_reason'];
      // if($current_status == "disabled"){
      //   $borrower_repo->update_model_by_code(['cust_id' => $data['borrowers']['cust_id'], 'disable_reason' => json_encode($reason)]);
      // } 
      // else{
      //   $borrower_repo->update_model_by_code(['cust_id' => $data['borrowers']['cust_id'], 'disable_reason' => null]);
      // }

      $serv = new RecordAuditService();
      $serv->audit_borrower_status_change($data);
      

       if($current_status == "enabled" && $borrower){
          if($borrower->lead_id){
            $lead_repo = new LeadRepositorySQL;
            $lead = $lead_repo->find($borrower->lead_id, ['acc_purpose']);
            if(is_tf_acc($lead->acc_purpose)){
              $status = Consts::TF_PENDING_PROCESS;
              $addl_sql = ['tf_status' => Consts::TF_PENDING_SC_GEN];
            }else{
              $status = Consts::CUSTOMER_ONBOARDED;
              $addl_sql = ['onboarded_date' => datetime_db(),'profile_status' => 'closed','close_reason' => 'customer_onboarded'];
              $info = $lead_repo->find($borrower->lead_id, ["flow_rel_mgr_id", "first_name", "last_name", "mobile_num"]);
              $flow_rel_mgr_info = $person_repo->find($info->flow_rel_mgr_id, ["whatsapp", "country_code"]);
              $country_code = DB::selectOne("select isd_code from markets where country_code = '$flow_rel_mgr_info->country_code'");
              $whatsapp = new WhatsappWebService();
              $notification = "The customer profile of {$info->first_name} {$info->last_name} {$info->mobile_num} has been enabled. He is now in probation and can avail low value FA products.";
              $whatsapp->send_message(["body" => $notification, "to" => $flow_rel_mgr_info->whatsapp, "isd_code"=> $country_code->isd_code, "session" => config('app.whatsapp_notification_number')]);
            }
            $update_arr = ['status' => $status ,'id' => $borrower->lead_id];
            $update_arr =  array_merge($update_arr,$addl_sql);
            $lead_repo->update_model($update_arr);
            
            if ($status == Consts::CUSTOMER_ONBOARDED) {
              (new PartnerService)->notify_lead_status( $borrower->lead_id );
            }
          }
          if($borrower->first_loan_date == null){

            $person = $person_repo->get_record_by('id', $borrower->owner_person_id, ['mobile_num', 'first_name', 'middle_name', 'last_name']);
            $cust_name = $person->first_name." ".$person->last_name;

            $sms_serv->notify_welcome_enabled_customer(['cust_mobile_num' => $person->mobile_num,
                      'country_code' => session('country_code'),
                      'acc_prvdr_code' => $borrower->acc_prvdr_code,
                      'cust_name' => $cust_name,
                      'cust_id' => $cust_id,
                      'customer_success' => config('app.customer_success_mobile')[$borrower->acc_prvdr_code]
                    ]);
         }
      }

      DB::commit();
    }
    catch (Exception $e) {
      DB::rollback() ;
      thrw($e->getMessage());
    }  
    return $update_status;
  }

  

  public function close_profile($data){

    $borr_repo = new BorrowerRepositorySQL();
    $record_serv = new RecordAuditService();
    $comm_repo = new CommonRepositorySQL();
    $lead_repo = new LeadRepositorySQL();
    $acc_repo = new AccountRepositorySQL();
    $loan_appl_repo = new LoanApplicationRepositorySQL();
    $loan_appl_serv = new LoanApplicationService();
    
    try
    {
      DB::beginTransaction();
      
      $borrower = $borr_repo->get_record_by('cust_id', $data['cust_id'], ['id', 'ongoing_loan_doc_id','pending_loan_appl_doc_id', 'lead_id']);

      if($borrower->ongoing_loan_doc_id){
        thrw("Cannot close this profile({$data['cust_id']}) because this customer has ongoing FA.");
      }
      elseif ($borrower->pending_loan_appl_doc_id){
              thrw("Cannot close this profile({$data['cust_id']}) because this customer has a FA application({$borrower->pending_loan_appl_doc_id}).");
      }

      $borr_repo->update_model_by_code(['cust_id' => $data['cust_id'],
                                        'profile_status' => 'closed',
                                        'flow_rel_mgr_id' => 13
                                      ]);
      $lead_repo->update_model(['id' => $borrower->lead_id, 'profile_status' => 'closed', 'close_reason' => 'borrower_profile_closed']);

      $account = $acc_repo->get_record_by_many(['cust_id', 'status'], [$data['cust_id'], 'enabled'], ['id']);
      
      $acc_data = [ 'accounts' => [
          'status' => 'disabled',
          'id' => $account->id
        ]
      ];
      
      $comm_repo->update_status($acc_data);

      $record = ['borrowers' => [
        'status' => 'disabled',
        'status_reason' => 'profile_closure',
        'cust_id' => $data['cust_id'],
        'id' => $borrower->id,
        'remarks' => 'Profile closure'    ]
      ];                                
      $record_serv->audit_borrower_status_change($record);
      $comm_repo->update_status($record);
      
      DB::commit();
    }
    catch (Exception $e) {
      DB::rollback() ;
      throw new Exception($e->getMessage());
    }
  }
  
  private function is_cust_temp_disabled($cust_id)
  {
    $status = json_encode(['status'=>'disabled']);

    $status_cut_off_days = config('app.cust_status_cut_off_days');

    $audit_record = DB::select('select id from record_audits where record_code = ? and date(created_at) between (curdate() - INTERVAL ? DAY) and CURDATE() and JSON_CONTAINS(data_after,?) ORDER BY id DESC LIMIT 1',[$cust_id,$status_cut_off_days,$status]);

    if($audit_record){
      return true;
    }else{
      return false;
    }
  }

  public function list_pre_appr_customers($data){
		
    $pre_appr= new PreApproval;
    $date = date_db();
    $addl_sql = "and appr_count > 0 and date(appr_exp_date) >= '$date'";

    $pre_apprs = $pre_appr->get_records_by('status', 'enabled',['*'], null, $addl_sql  );
    if($pre_apprs){
      $flow_rel_mgr_ids = collect($pre_apprs)->pluck('flow_rel_mgr_id')->toarray();
      
      if(sizeof($flow_rel_mgr_ids) > 0){
        $flow_rel_mgr_ids = array_unique($flow_rel_mgr_ids);
        foreach($flow_rel_mgr_ids as $flow_rel_mgr_id){
          $flow_rel_mgr_names[$flow_rel_mgr_id] = (new personRepositorySQL)->full_name($flow_rel_mgr_id);
        }
      }

      foreach($pre_apprs as $pre_appr){
        $pre_appr->rm_name = $flow_rel_mgr_names[$pre_appr->flow_rel_mgr_id];
      }
    }        

    return $pre_apprs;
      
  }

  public function allow_manual_capture($data){
    $borr_repo = new BorrowerRepositorySQL;

    $borrower =  $borr_repo->find_by_code($data['cust_id'], ['allow_tp_ac_owner_manual_id_capture']);
  
    if($borrower && $borrower->allow_tp_ac_owner_manual_id_capture == true){
      thrw('Already allowed to do capture National ID for this customer.');
    }

    $result = $borr_repo->update_model_by_code(['cust_id' => $data['cust_id'], 'allow_tp_ac_owner_manual_id_capture' =>true ]);

    return $result;
  }

  public function get_cust_id($mobile_num){

    $person_repo = new PersonRepositorySQL();
    $borrower_repo = new BorrowerRepositorySQL();
    $market_repo = new MarketRepositorySQL();
    

    $field_names = ['mobile_num','alt_biz_mobile_num_1','alt_biz_mobile_num_2'];
    $field_values = [$mobile_num,$mobile_num,$mobile_num];
    $persons = $person_repo -> get_record_by_many($field_names, $field_values, ['id']);
    if(isset($persons)){
      
      $borrower = $borrower_repo->get_record_by("owner_person_id", $persons[0]->id, ['cust_id']);

        if($borrower){
          $cust_id = $borrower -> cust_id; 
        }
    }
    else{
      $cust_id = "unknown customer";
    }
    return $cust_id;
  }
}


