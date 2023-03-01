<?php
namespace App\Services;
use App\Mail\FlowCustomMail;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\LeadRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\AccProviderRepositorySQL;
use App\Repositories\SQL\AccountStmtRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Services\Mobile\RMService;
use App\Services\Schedule\ScheduleService;
use Carbon\Carbon;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\LoanTransactionRepositorySQL;
use App\Models\CustCSFValues;
use App\Repositories\SQL\LenderRepositorySQL;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Consts;
use App\Models\AccountTxn;
use App\Models\Account;
use App\Repositories\SQL\CustCSFValuesRepositorySQL;
use Illuminate\Support\Facades\Mail;
use Log;
use App\Models\StatementImport;
use App\Models\PreApproval;

use Illuminate\Database\QueryException;
use App\Exceptions\FlowCustomException;
use Illuminate\Support\Facades\Storage;


class AccountService{
  public function __construct()
  {
       $this->country_code = session('country_code');

  }
  private function clear_unrelated_fields(&$account){
    if($account['entity'] == "customer"){
      $account['lender_code'] = null;
      $account['network_prvdr_code'] = null;
      // $account['acc_prvdr_code'] = null;
    }
    else if($account['entity'] == "lender"){
      $account['cust_id'] = null;
      // $account['acc_prvdr_code'] = null;
    }
    // else if($account['entity'] == "data_prvdr"){
    //   $account['cust_id'] = null;
    //   $account['lender_code'] = null;
    //   $account['lender_acc_prvdr_code'] = null;
    // }

  }
  public function create(array $account , $txn = true){
    try
        {
          if($txn){
            DB::beginTransaction(); 
          }
          if($account['acc_number'] == 'dummy'){
              $acc_num = "dummy_".strtoupper(uniqid());
              $account['acc_number'] = $account['reconfirm_acc_number'] = $acc_num;
          }
          $this->check_account_exists($account);
          $rm_repo = new RMService();
          $rm_repo->dup_check_account(['acc_prvdr_code' => session('acc_prvdr_code'), 'acc_number' => $account['acc_number']]);
          $account_repo = new AccountRepositorySQL();
          $updateByColumn = $updateByValue = null;
          if($account['entity'] == 'customer'){
              $this->process_new_cust_acc($account);
          }
          else{
              $this->load_update_fields($account, $updateByColumn, $updateByValue);
              $account_repo->make_accounts_regular($updateByColumn,$updateByValue);
              $account['is_primary_acc'] = true;
          }

          $this->clear_unrelated_fields($account);
          $account['acc_prvdr_name'] = $this->get_acc_prvdr_name($account['acc_prvdr_code']);

          $account_id = $account_repo->create($account);
          if($account['entity'] == 'customer'){
              $account['id'] = $account_id;
              $lead_serv = new LeadService;
              $pre_appr_repo = new PreApproval;

              $pre_appr = $pre_appr_repo->get_record_by_many(['cust_id', 'status'], [$account['cust_id'], 'enabled'], ['id']);
              
              if($pre_appr){
                $rm_serv = new RMService();
					      $data['cust_id'] = $account['cust_id'];
					      $rm_serv->remove_pre_approval($data);
              }
              $lead_serv->initiate_rekyc_for_new_account($account);
          }
//          $borr_repo->update_model_by_code(['cust_acc_id' => $account_id, 'acc_number' => $account['acc_number'], 'cust_id' => $account['cust_id'] ]);

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
     return $account_id;
  }

  public function update($data){

    try{

      DB::beginTransaction(); 
    
      $account = $data['account'];
      $send_email = false;
      if(isset($data['person']) && sizeof($data['person']) > 0){
        $person = $data['person'];
        $person['country_code'] = session('country_code');

        $this->dup_check_for_tp_national_id($data['person'], $data['account']['cust_id']);
        
        $person_id = (new PersonRepositorySQL())->create($person);

        mount_entity_file("borrowers", $person, $account['cust_id'], 'photo_consent_letter');

        $account['photo_consent_letter'] = $person['photo_consent_letter'];
        $send_email =true;
        
        if($person_id){
          $account['tp_acc_owner_id'] = $person_id;
        }

      }
      $acc_repo = new AccountRepositorySQL();
        if(isset($account['cust_id'])){
            $this->validate_fa_acc($account, false);
        }
        $this->check_appr_elig($account);

        $result =  $acc_repo->update($account);

        if($send_email){
          $this->third_party_ac_owner_details_email($account, $person);
        }

        DB::commit();

        return $result;



    }catch (Exception $e) {
        DB::rollback();
        throw new FlowCustomException($e->getMessage());
    }
  }
  private function third_party_ac_owner_details_email($account, $person){

    $cust_name = (new PersonRepositorySQL)->full_name_by_cust_id($account['cust_id']);

    $photo_national_id_path = get_file_path("persons",$account['tp_acc_owner_id'],"photo_national_id");
    
    $photo_nid_full_path = Storage::path($photo_national_id_path.'/'.$person['photo_national_id']);

    $mail_data = ['cust_id' =>$account['cust_id'], 'photo_nid_full_path' => $photo_nid_full_path, 'cust_name' => $cust_name, 'acc_number' => $account['acc_number'], 'first_name' => $person['first_name'], 'last_name' => $person['last_name'], 
                'dob' => $person['dob'], 'gender' => $person['gender'], 'national_id' => $person['national_id'] ,'country_code' => session('country_code')];
    Mail::to([get_ops_admin_email(),get_l3_email()])->queue((new FlowCustomMail('tp_ac_owner_notification',$mail_data))->onQueue('emails'));

  }

  private function dup_check_for_tp_national_id($data, $cust_id){
    $chk_nat_id = ["id_type" => "national_id", 
                               "national_id" => $data["national_id"]
                            ];
    $chk_names = [];
    if(isset($data['last_name']) && isset($data['first_name'])){
                    $chk_names = [ "first_name" => $data["first_name"],
                                "last_name" => $data["last_name"]
                            ];
    }

    if(array_key_exists('person_id', $data)){
        $chk_nat_id['person_id'] = $data['person_id'];
        $chk_names['person_id'] = $data['person_id'];
    }

    $rm_serv = new RMService(); 
    $rm_serv->dup_check_person($chk_nat_id, $cust_id);
    $rm_serv->dup_check_person($chk_names, $cust_id);


  }
  private function check_account_exists($account){
    $account_repo = new AccountRepositorySQL();
    if(in_array('disbursement',$account['acc_purpose'])){
      $result = $account_repo->get_accounts_by(['lender_code','network_prvdr_code','acc_purpose','status'],[$account['lender_code'], $account['network_prvdr_code'], "disbursement", "enabled"]);
      if($result){
        thrw("Disbursement account already exists for this Lender.");
      }
    }

    if(in_array('commission',$account['acc_purpose'])){
      $result = $account_repo->get_accounts_by(['acc_prvdr_code', 'acc_purpose', 'status'],[$account['acc_prvdr_code'], "commission", "enabled"]);
      if($result){
        thrw("Commission account already exists for this Account Provider.");
      }
    }

    if($account['entity'] == 'customer'){
        $this->validate_fa_acc($account);
    }
  }

  public function load_update_fields($account , &$updateByColumn, &$updateByValue)
    {
          if(isset($account['cust_id']))
          {
            $updateByColumn = "cust_id";
            $updateByValue = $account['cust_id'];
          }
         else if(isset($account['lender_code']))
          {
            $updateByColumn = "lender_code";
            $updateByValue = $account['lender_code'];
          }
          else if(isset($account['acc_prvdr_code']))
          {
            $updateByColumn = "acc_prvdr_code";
            $updateByValue = $account['acc_prvdr_code'];
          }
          else
      {
        throw new FlowCustomException("lender_code or cust_id or acc_prvdr_code not specified");
      }
        $result = array($updateByColumn,$updateByValue);
        return $result;
  }  

  public function get_ref_accounts($data){
  //$field_names = ["country_code"];
  //$field_values = [$data['country_code']];
  if(array_key_exists("lender_code", $data)){
    $field_names[] = "lender_code";
    $field_values[] = $data['lender_code'];
    if(array_key_exists("network_prvdr_code", $data)){
      $field_names[] = "network_prvdr_code";
      
      $field_values[] = $data['network_prvdr_code'];
      
    }
  }else if(array_key_exists("acc_prvdr_code", $data)){
    $field_names[] = "acc_prvdr_code";
    $field_values[] = $data['acc_prvdr_code'];
  }

  if(array_key_exists("to_recon", $data)){
    $field_names[] = "to_recon";
    $field_values[] = $data['to_recon'];
  }
  if(array_key_exists("status", $data)){
    $field_names[] = "status";
    $field_values[] = $data['status'];
  }
  
  $account_repo = new AccountRepositorySQL();
  $fields_arr = ["acc_prvdr_name", "acc_prvdr_code","acc_purpose", "type", "holder_name", "acc_number", "branch","status","is_primary_acc", 'balance'];
  
  $addl_condition = "";

  if(array_key_exists('acc_id', $data)){
    $addl_condition = "and id != {$data['acc_id']}";  
  }
  if(session('country_code') == 'UGA'){
    $addl_condition = "or id = 1783";
  }elseif(session('country_code') == 'RWA'){
    $addl_condition = "or id = 7337";
  }
    
  $accounts = $account_repo->get_records_by_many($field_names, $field_values, $fields_arr, "and",$addl_condition);
  $ref_acc = array();
  foreach($accounts as $account){
    $float_acc_stmt = $this->get_last_stmt_import(['status', 'account_id'], ['imported', $account->id], ['end_time', 'status']);
    $float_acc_stmt_end_time = isset($float_acc_stmt) ? $float_acc_stmt->end_time : null;
    $status = isset($float_acc_stmt) ? $float_acc_stmt->status : null;
    $ref_acc[] =["id" => $account->id, "name" => $this->get_acc_txt($account), "status" => $status, "float_acc_stmt_end_time" => $float_acc_stmt_end_time] ;
  }
  return $ref_acc;

  }

  public function make_primary(array $account)
  {
    try
        {
          DB::beginTransaction(); 
          $updateByColumn = null;
          $updateByValue = null;
          $this->load_update_fields($account, $updateByColumn, $updateByValue);
          $account_repo = new AccountRepositorySQL();
          $account_repo->make_accounts_regular($updateByColumn,$updateByValue);
          $records = $account_repo->update($account);
          DB::commit();
        }
      catch (\Exception $e) {
      DB::rollback();
      if ($e instanceof QueryException){
          throw $e;
        }else{
        thrw($e->getMessage());
        }
      }
       return ($records > 0);
    }
  
  public function get_float_wallet_accounts($lender_code){
    $accs = $this->get_all_lender_accounts(['lender_code' => $lender_code, 'status' => 'enabled', 'account_type' => 'wallet']);
    return $accs;

  }
  public function get_all_lender_accounts($data)
    {
      $account_repo = new AccountRepositorySQL();
      $lender_accounts = $account_repo->get_accounts_by(['lender_code','status'], [$data['lender_code'],$data['status']]);
      if(empty($lender_accounts)){
        thrw("Account not configured for the lender.");
      }
      $lender_result = $this->get_accounts($lender_accounts);
      return $lender_result;
    }
  
  # TODO function name to be changed
  public function get_lender_accounts($data){
    $account_repo = new AccountRepositorySQL();
    /*$field_keys = ['lender_code', 'lender_acc_prvdr_code'];
    $field_values = [$data['lender_code'], $data['lender_acc_prvdr_code']];
    
    if(Arr::exists($data, 'acc_purpose')){
      $field_keys[] = 'acc_purpose';
      $field_values[] = $data['acc_purpose'];
    }*/
    Log::warning($data);
    $accounts = [];
    if(array_key_exists('cust_id', $data)){
        if($data['network_prvdr_code'] == 'RMTN'){
          // $acc_number = get_acc_num_by_district($data['cust_id']);
          // $data['acc_number'] = $acc_number;
          // $accounts = $account_repo->get_accounts_by(['lender_acc_prvdr_code', 'acc_prvdr_code'], ['RMTN', 'RBOK'] ,['*']);
        }
        unset($data['cust_id']);
    }
    $lender_accounts = $account_repo->get_accounts_by(array_keys($data), array_values($data),['*'], true);
    $lender_accounts = array_merge($accounts, $lender_accounts);
     if(empty($lender_accounts)){
        thrw("Account not configured for the lender.");
      }
      $lender_result = $this->get_accounts($lender_accounts);
      return $lender_result;
  }
  
  public function get_lender_disbursal_account($lender_code, $acc_prvdr_code, $acc_number = null){
    $search_fields_arr = ['lender_code', 'network_prvdr_code', 'is_primary_acc', 'acc_purpose', 'status'];
    $search_values_arr = [$lender_code, $acc_prvdr_code, true, 'disbursement', 'enabled'];
    $account_repo = new AccountRepositorySQL();

    if($acc_number && $acc_prvdr_code == 'RMTN'){
        $disb_acc_number = $this->get_disb_acc_by_acc_num($acc_number, $acc_prvdr_code);
        // if($acc_detail[0] == null){
        //     thrw("Disbursement account is not configured for {$acc_detail[1]} district under RMTN");
        // }
        $search_fields_arr[] = 'acc_number';
        $search_values_arr[] = $disb_acc_number;
    }
    $lender_accounts = $account_repo->get_accounts_by($search_fields_arr, $search_values_arr,
                                                  ['acc_prvdr_code' , 'type', 'id', 'acc_number', 'api_cred', 'web_cred', 'disb_int_type', 'stmt_int_type', 'mobile_cred', 'holder_name']);
    if(empty($lender_accounts) ){
        thrw("Primary disbursement account not configured for the lender : $lender_code");
    }
    if(sizeof($lender_accounts) > 1 ){
        thrw("More than one primary disbursement account configured for the lender : $lender_code");
    }
    $acc = $lender_accounts[0];
    
    $acc_prvdr_repo = new AccProviderRepositorySQL();
    $acc_prvdr = $acc_prvdr_repo->find_by_code($acc->acc_prvdr_code, ['api_url']);
    $acc->api_url = $acc_prvdr->api_url;
    
    return $acc;
  }

    public function get_disb_acc_by_acc_num($acc_number, $acc_prvdr_code){
      $account_repo = new AccountRepositorySQL();
      $account = $account_repo->get_record_by_many(['acc_number', 'acc_prvdr_code', 'status'], [$acc_number, $acc_prvdr_code, 'enabled'], ['branch']);
      if(isset($account->branch)){
        if(isset(config("app.RMTN_district_accounts")[$account->branch])){
          return config("app.RMTN_district_accounts")[$account->branch];
        }
        else{
          thrw("Disbursement account is not configured for {$account->branch} district under RMTN");
        }
		    
      }
      else{
        thrw("Report app support to update branch");
      }

    }

    public function get_customer_accounts($data,$cust_app = false)
    {
      $account_repo = new AccountRepositorySQL();
      $cust_accounts = $account_repo->get_accounts_by(['cust_id','status','acc_purpose'], [$data['cust_id'],$data['status'],$data['acc_purpose']]);
      $acc_serv = new AccountService();
       if(empty($cust_accounts)){
        thrw("Account not configured for the Customer.");
      }
        $customer_result = $acc_serv->get_accounts($cust_accounts,$cust_app);
      return $customer_result;
    }

    public function get_accounts($accounts,$cust_app = false)
    {
      $acc_list = array();
      foreach ($accounts as $account) {
        $selected = false;
        if($account->is_primary_acc == 1){
            $selected =  true;
          }
        if(!$cust_app) {
            $item['balance'] = $account->balance;
            $item["acc_num_disp_txt"] = $this->get_acc_txt($account);
            $item["selected"] = $selected;
            $item["acc_type"] = $account->type;
        }
            $item['account_id'] = $account->id;
            $item["acc_number"] = $account->acc_number;
            $item["acc_prvdr_name"] = $account->acc_prvdr_name;
            $item["acc_prvdr_code"] = $account->acc_prvdr_code;
            $acc_list[] = $item;
      }
      return $acc_list;
    }


    public function get_acc_txt($acc_detail){
      $acc_prvdr_code = $acc_detail->acc_prvdr_code;
      $acc_purpose = implode(' | ',$acc_detail->acc_purpose);
      $account_number = $acc_detail->acc_number;
      $balance = $acc_detail->balance;
      $type = $acc_detail->type;
      if(!$acc_purpose){
        $acc_purpose = $type;
      }
      $star = null; 
      if($acc_detail->is_primary_acc == 1){
        $star = "*";
      }
      if($balance){
        //$account_txt = "$account_number BAL : $balance ($acc_prvdr_code - $type) $star";
        $account_txt = "$account_number ($acc_prvdr_code - $acc_purpose) $star";    
      }else{
        $account_txt = "$account_number ($acc_prvdr_code - $acc_purpose) $star";    
      }
      return $account_txt;
  } 

  // public function process_acc_txn_req($data){
  	
  // 	$account_txn_id = null; 
  //   try
  //    {
  //     $acc_txn = $data['acc_txn'];
  //     DB::beginTransaction();
    	
  //   	$acc_txn['country_code'] = $data['country_code'];
  //     $account_txn_id = $this->create_acc_txn($acc_txn);

  //   	if(array_key_exists('ref_acc_id',$acc_txn)){
  //       $acc_txn = $this->get_acc_txn_data_for_pair($acc_txn);
        
  //       $account_txn_id = $this->create_acc_txn($acc_txn);
  //     }
      
  //     DB::commit();
  //   }
  //   catch (\Exception $e) {
  //     DB::rollback();
  //     Log::warning($e->getTraceAsString());
  //     if ($e instanceof QueryException){
  //         throw $e;
  //       }else{
  //       thrw($e->getMessage());
  //       } 
  //   }
  //   return $account_txn_id;
    
  // }      

  
  public function create_platform_acc_txn($acc_id, $acc_txn_category, $amt, $txn_id, $acc_txn_type,$txn_date=null){
    
    $txn_date = ($txn_date == null) ? datetime_db() : $txn_date;
    $acc_txn = ['country_code' => session('country_code'),
                            'acc_id' => $acc_id,
                            'acc_txn_category' => $acc_txn_category,
                            'amount' => $amt,
                            'txn_date' => $txn_date,
                            'txn_id' => $txn_id,
                            'acc_txn_type' => $acc_txn_type,
                            'txn_mode' => 'flow_platform',
                            'txn_exec_by' => session('user_id'),
                            ];
      Log::warning($acc_txn);
      //(new AccountService())->create_acc_txn($acc_txn);
      // $this->create_acc_txn($acc_txn);
  
  }



  public function create_acc_txn($acc_txn, $override_bal_check = false){
    $override_bal_check = config('app.override_bal_check');
    if($acc_txn['amount'] != 0)
    {

      $this->acc_repo = new AccountRepositorySQL();
      $is_missed_txn = $this->acc_repo->check_missed_txn($acc_txn['acc_id'], $acc_txn['txn_date']);
      $old_bal = $this->acc_repo->get_previous_txn_bal($acc_txn['acc_id'], $acc_txn['txn_date'], $is_missed_txn);
      $acc_txt = $this->acc_repo->get_accounts_by_id([$acc_txn['acc_id']]);
      $acc_id = $acc_txn['acc_id'];

      $change_in_bal = 0;
      if(array_key_exists('acc_txn_category', $acc_txn) && $acc_txn['acc_txn_category'] != ""){

        if($acc_txn['acc_txn_category'] == "balance"){
          $acc_txn["balance"] = $acc_txn['amount'];
        }else if ($acc_txn['acc_txn_category'] == "credit"){
          $acc_txn["credit"] = $acc_txn['amount'];
          $acc_txn["balance"] = $old_bal + $acc_txn["amount"];
          $change_in_bal = $acc_txn['amount'];
        }else if ($acc_txn['acc_txn_category'] == "debit"){
          $this->acc_repo->class = Account::class;
          $acc_prvdr_code = $this->acc_repo->find($acc_id, ['network_prvdr_code'])->network_prvdr_code;
          $override_bal_check = in_array($acc_prvdr_code, config('app.override_bal_check'));
          if($old_bal >= $acc_txn['amount'] || $override_bal_check){
            
         # if($old_bal >= $acc_txn['amount']){
            $acc_txn["debit"] = $acc_txn['amount'];
            $acc_txn["balance"] = $old_bal - $acc_txn["amount"];
            $change_in_bal = -1 * $acc_txn['amount'];
         }else{
            thrw("Available balance in $acc_txt[$acc_id] is {$old_bal}");
          }
        } 
      }else{
        thrw("Transaction category is required.");
      }
      $account_txn_id = (new AccountTxn())->insert_model($acc_txn);     
      }
      else
      {
        thrw("Please Enter a valid amount"); 
      }
      // if($change_in_bal != 0 && $is_missed_txn){

      //   $this->update_bal_for_upcoming_txns($acc_txn['acc_id'], $change_in_bal, $acc_txn['txn_date'], $account_txn_id); 
      // }
      
      // $curr_bal = $this->acc_repo->get_last_txn_bal($acc_txn['acc_id']);

      // DB::update("update accounts set balance = ? where id = ?", [$curr_bal, $acc_txn['acc_id']]);
      return $account_txn_id;
  }
  
// private function update_bal_for_upcoming_txns($acc_id, $change_in_bal, $txn_date, $curr_acc_txn_id){
//     DB::update("update account_txns set balance = balance + ? where acc_id = ? and txn_date > ? and id != ? ", [$change_in_bal, $acc_id, $txn_date, $curr_acc_txn_id]);
//   } 

  private function get_acc_txn_data_for_pair($acc_txn){
   
  	if($acc_txn['acc_txn_category'] == "balance"){
  		thrw("Cannot have Ref A/C for Opening Balance Txn");
  	}else if ($acc_txn['acc_txn_category'] == "credit"){
  		$acc_txn['acc_txn_category'] = "debit";
  	}else if ($acc_txn['acc_txn_category'] == "debit"){
  		$acc_txn['acc_txn_category'] = "credit";
  	}	
  	
     $ref_acc_id = $acc_txn['ref_acc_id'];
  	$acc_txn['ref_acc_id'] = $acc_txn['acc_id'];
  	$acc_txn['acc_id'] = $ref_acc_id;

    return $acc_txn;

  }

  public function list_acc_txns($data){
    $acc_repo = new AccountRepositorySQL();
   
    
    $acc_repo->class = AccountTxn::class;
    $field_names = array_keys($data);
    $field_values = array_values($data);
    $fields_arr = ["acc_txn_type", "credit", "debit","balance", "txn_mode", "txn_exec_by", "txn_date","ref_acc_id", 'tp_acc_owner_id'];
    $acc_txns = $acc_repo->get_records_by_many($field_names, $field_values, $fields_arr);
    $this->set_names($acc_txns);

    return ["list" => $acc_txns];
  }
  


  private function set_names(&$acc_txns){
    $acc_ids = array();
    $person_ids = array();
    foreach ($acc_txns as $acc_txn) {
      $person_ids[] = $acc_txn->txn_exec_by;
      if($acc_txn->ref_acc_id){
        $acc_ids[] = $acc_txn->ref_acc_id;
      }
    }
    
    $acc_map = array();
    $name_map = array();
    if(sizeof($acc_ids) > 0)
      {
        $acc_ids  = array_unique($acc_ids);
        $acc_repo = new AccountRepositorySQL();
        $acc_map = $acc_repo->get_accounts_by_id($acc_ids);
     }

     if(sizeof($person_ids)>0)
     {
      $person_ids = array_unique($person_ids);  
      $person_repo = new PersonRepositorySQL();  
      foreach ($person_ids as $person_id) {
         $name = $person_repo->full_name($person_id);
         $name_map[$person_id] = $name;   
       } 
      }
     foreach ($acc_txns as $acc_txn){
           $acc_txn->txn_exec_by = $name_map[$acc_txn->txn_exec_by];
           if($acc_txn->ref_acc_id){
            $acc_txn->ref_acc_txt = $acc_map[$acc_txn->ref_acc_id];
           }
      }
   
}

function get_acc_stmts($data){
    $loan_repo = new LoanRepositorySQL();   
    $acc_repo = new AccountStmtRepositorySQL($data['country_code']);
    if(array_key_exists('today', $data['statement_search'])){
        $data['statement_search']['stmt_txn_date__from'] =  Carbon::now()->subDays(1)->toDateTimeString();
        $data['statement_search']['stmt_txn_date__to'] = Carbon::now()->endOfDay()->toDateTimeString();
    }
    
    m_array_filter($data['statement_search']);
    unset($data['statement_search']['today']);  

    $account_txns = $acc_repo->get_acc_stmt_txns($data['statement_search']);
     /*foreach($account_txns as $account_txn){
        $loan = $loan_repo->get_record_by('loan_doc_id',$account_txn->loan_doc_id,['payment_status']); 
        if($loan){
          if($loan->payment_status == "review_pending"){          
            $account_txn->payment_status = $loan->payment_status;
          }
        }
           }
    */

    $acc_txt = null;
    $float_acc_stmt_end_time=null;
    if(isset($data['statement_search']['account_id'])){
      $acc_repo = new AccountRepositorySQL();
      $account = $acc_repo->find($data['statement_search']['account_id']);
      $acc_txt = $this->get_acc_txt($account);

      $float_acc_stmt = $this->get_last_stmt_import(['status', 'account_id'], ['imported', $data['statement_search']['account_id']], ['end_time']);
      $float_acc_stmt_end_time = isset($float_acc_stmt) ? $float_acc_stmt->end_time : null;
    }

    return ['account_txns' => $account_txns, 'acc_txt' => $acc_txt, 'float_acc_stmt_end_time' => $float_acc_stmt_end_time];
}
  private function get_last_stmt_import($field_keys, $field_values, $field_names){
    $stmt_imp_mod = new StatementImport(); 
    $addl_condn = 'order by id desc limit 1';
    $float_acc_stmt = $stmt_imp_mod->get_record_by_many($field_keys, $field_values, $field_names, 'and', $addl_condn);
    return $float_acc_stmt;
  }
  public function check_account_w_kyc($data){
    $acc_repo = new AccountRepositorySQL();
    $lead_repo = new LeadRepositorySQL();
    $account = $acc_repo->get_record_by('id', $data['id'], ['acc_number']);
    $addl_condtn = " and status != '".Consts::CUSTOMER_ONBOARDED."'";
    $lead = $lead_repo->get_record_by_many(['kyc_reason', 'account_num', 'acc_prvdr_code'], ['new_account', $account->acc_number, session('acc_prvdr_code')], ['status'], 'and', $addl_condtn);

    if($lead){
      thrw("Can not enable. There is a KYC in {$lead->status} for this account.");
    }

  }
  
    public function upsert_cust_csf_approval_conditions($data) {
      
      $condition = $data['condition'];

      $appr_valid_upto = ($condition['validity'] == '*') ? $condition['validity'] : addDays($condition['validity'])->format(Consts::DB_DATE_FORMAT);
      $conditions = ["acc_elig_reason" => $data['acc_elig_reason'], "validity"=> $appr_valid_upto, "limit" => $condition['limit']];

      $repo = new CustCSFValuesRepositorySQL();
      $run_id = $repo->get_run_id($data['acc_number'], $data['acc_prvdr_code']);
      
      if (isset($run_id))
      {
        $record = [ 
          'run_id' => $run_id,
          'conditions' => $conditions
        ];
        $repo->update_model($record, 'run_id');
      }
      else {
        $record = [ 
          'country_code' => session('country_code'),
          'acc_prvdr_code' => $data['acc_prvdr_code'],
          'acc_number' => $data['acc_number'],
          'cust_score_factors' => [],
          'conditions' => $conditions,
          'run_id' => $data['run_id']
        ];
        $repo->insert_model($record);
      }
      return $record;
    }

    public function temp_approve_acc($account)
    {
        $account_repo = new AccountRepositorySQL();
        if($account['acc_elig_reason'] != 'tf_w_fa'){
            $exist_accounts = $account_repo->get_accts_no_require_approval($account['cust_id']);
            if ($exist_accounts == null) {
                thrw("To add an account with approval, customer should have been associated with Flow with another account provider");
            }
        }

        $account['run_id'] = uniqid();
        $account['condition'] = config('app.account_elig_conditions')[$account['acc_prvdr_code']][$account['acc_elig_reason']];

        $record = $this->upsert_cust_csf_approval_conditions($account);

        return $record['run_id'];
    }


    public function validate_fa_acc($account, $create = true){
      $account_repo = new AccountRepositorySQL();
      if($create || $account['status'] == 'enabled'){
        $result = collect($account_repo->get_accounts_by(['cust_id', 'acc_purpose', 'status'], [$account['cust_id'], "float_advance", "enabled"], ['id']))->pluck('id')->toArray();
        if(!$create){
            $result = array_diff($result,[$account['id']]);
        }


        if(is_fa_acc($account['acc_purpose']) && sizeof($result) > 0){
            thrw("An existing 'Float Advance' account is enabled for this customer. Disable the account or change its 'Account Purpose' before adding a new 'Float Advance' account");
        }
        elseif(!is_fa_acc($account['acc_purpose']) && sizeof($result) == 0){
            thrw("A Float Advance account is required to be enabled for a customer");
        }
      }
    }

    public function process_new_cust_acc(&$account)
    {
        $borr_repo = new BorrowerRepositorySQL();
        $borr_id = $borr_repo->get_id($account['cust_id']);
        $borr_repo->update_record_status('disabled', $borr_id);
        $account['status'] = 'disabled';
        $this->check_appr_elig($account);
    }

    public function get_acc_prvdr_name($acc_prvdr_code)
    {
        $acc_prvdr_repo = new AccProviderRepositorySQL();
        $account_provider_name = $acc_prvdr_repo->get_acc_prvdr_name($acc_prvdr_code);
        return $account_provider_name->name;
    }

    public function check_appr_elig(&$account)
    {
//        if (is_tf_acc($account['acc_purpose']) && is_fa_acc($account['acc_purpose'])
//            && in_array('tf_w_fa', array_keys(config('app.account_elig_conditions')[$account['acc_prvdr_code']]))) {
//            $account['acc_elig_reason'] = 'tf_w_fa';
//            $account['acc_elig_appr'] = true;
//        }
        if (array_key_exists('acc_elig_appr', $account) && $account['acc_elig_appr']) {
            $this->temp_approve_acc($account);
        }
    }

    public function update_notify_acc_balance($account_id){

      $account_stmt = DB::selectOne("select balance from account_stmts where account_id = ? and balance > 0 ORDER BY id DESC LIMIT 1",[$account_id]);

      if($account_stmt)
      {
          $balance = $account_stmt->balance;

          $account_repo = new AccountRepositorySQL;
          $account_repo->update_model(['balance' => $balance,"id" => $account_id]);

          $account_record = $account_repo->get_account_by(['id','acc_purpose'],[$account_id,'disbursement'],['country_code', 'balance', 'lower_limit','acc_prvdr_code', 'acc_number']);

          if($account_record){

            $account = (array)$account_record;
            $lower_limit = $account['lower_limit'];

            if($balance < $lower_limit && $lower_limit != null && $account_record->acc_prvdr_code != 'CCA'){
  
                $currency_code = (new CommonRepositorySQL())->get_currency()->currency_code;
  
                $account['current_datetime'] = Carbon::now();
                $account['currency_code'] = $currency_code;
  
                $ops_admin_email = get_ops_admin_email($account['country_code']);
                $market_admin_email = get_market_admin_email($account['country_code']); 

                send_email('notify_balance_below_threshold', [config('app.app_support_email'), get_l3_email(), $ops_admin_email, $market_admin_email], $account);
            }
  
          }
      }

    }

    private function txn_validation($acc_stmt_rec){

      $acc_stmt = (new AccountStmtRepositorySQL)->get_record_by_many(['account_id','stmt_txn_id'], [$acc_stmt_rec['account_id'], $acc_stmt_rec['stmt_txn_id']], ['id']);

      $loan_txns = (new LoanTransactionRepositorySQL)->get_record_by('txn_id', $acc_stmt_rec['stmt_txn_id'], ['id']);

      if($acc_stmt || $loan_txns){
        thrw("Given transaction already exists for this txn id ({$acc_stmt_rec['stmt_txn_id']}).");
      }

      $loan_doc_id = $acc_stmt_rec['loan_doc_id'];

      if($loan_doc_id){

        $loan =  (new LoanRepositorySQL)->find_by_code($loan_doc_id, ['id', 'cust_id', 'acc_prvdr_code', 'loan_doc_id', 'cust_acc_id']);

        if(!$loan){
          thrw('Please enter the valid FA ID');
        }else{
          $loan_rec = $loan;
        }

        $date = Carbon::parse($acc_stmt_rec['stmt_txn_date'])->format(Consts::DATE_FORMAT);

        if($acc_stmt_rec['stmt_txn_type'] == 'credit'){
          $addl_sql = "disbursal_date < {$date}";
          $message = "Entered date should be greater than the disbursal date.";

        }else if($acc_stmt_rec['stmt_txn_type'] == 'debit'){
          $addl_sql = "date(disbursal_date) = {$date}";
          $message = "Entered date should be equal to the disbursal date.";
        }

        $loan = (new LoanRepositorySQL)->get_records_by('loan_doc_id', $loan_doc_id, ['id'], $addl_sql);
        
        if($loan){
          thrw($message);
        }
      }

      return $loan_rec;
      
    }

    private function insert_acc_stmts($acc_stmt_rec){

      $account = (new AccountRepositorySQL())->find($acc_stmt_rec['account_id'], ['network_prvdr_code', 'acc_number', 'acc_prvdr_code']);
        $acc_stmt_rec['network_prvdr_code'] = $account->network_prvdr_code;
        $acc_stmt_rec['acc_prvdr_code'] = $account->acc_prvdr_code;
        $acc_stmt_rec['acc_number'] = $account->acc_number;

        if($acc_stmt_rec['stmt_txn_type'] == 'credit'){
          $acc_stmt_rec['cr_amt'] = $acc_stmt_rec['amount']; 
        }else if($acc_stmt_rec['stmt_txn_type'] == 'debit'){
          $acc_stmt_rec['dr_amt'] = $acc_stmt_rec['amount'];  
        }

        $acc_stmt_rec['country_code'] = session('country_code');

        mount_entity_file("acc_stmts", $acc_stmt_rec, $acc_stmt_rec['stmt_txn_id'], 'photo_statement_proof');

        $acc_stmt_rec['recon_status'] = Consts::PENDING_STMT_IMPORT;
        $acc_stmt_rec['import_id'] = session('user_id');
        $acc_stmt_rec['source'] = 'manual';

        $acc_stmt_id = (new AccountStmtRepositorySQL())->insert_model($acc_stmt_rec);

        return $acc_stmt_id;

    }

    public function add_acc_stmts($data){

      try{

        DB::beginTransaction(); 

        $acc_stmt_rec = $data['acc_stmts'];

        $loan_rec  = $this->txn_validation($acc_stmt_rec);

        $acc_stmt_id = $this->insert_acc_stmts($acc_stmt_rec);

        if($acc_stmt_id ){
          $stmt_txn = (new AccountStmtRepositorySQL())->find($acc_stmt_id, ['ref_account_num', 'descr', 'cr_amt','dr_amt','stmt_txn_date','stmt_txn_id', 'account_id']);

          if($acc_stmt_rec['stmt_txn_type'] == 'credit'){
            (new ScheduleService())->process_stmt_txn_by_loan_doc_id($stmt_txn, [$loan_rec], null, [$loan_rec->cust_id]);

            (new AutoCapturePaymentService())->auto_capture($stmt_txn->account_id, Consts::PENDING_STMT_IMPORT, $acc_stmt_id);

          }else if($acc_stmt_rec['stmt_txn_type'] == 'debit'){
            $acc_map = (new AccountRepositorySQL)->get_accounts_by_id([$loan_rec->cust_acc_id]);
            $disbursal_txn = ['amount' => $stmt_txn->dr_amt, 'txn_date' => $stmt_txn->stmt_txn_date, 
								'txn_mode' => 'instant_disbursal', 'from_ac_id' => $acc_stmt_rec['account_id'], 
                'to_ac_id' => $loan_rec->cust_acc_id, 'txn_id' => $stmt_txn->stmt_txn_id, 
								'loan_doc_id' => $acc_stmt_rec['loan_doc_id']];

            $disbursal_txn['capture_only'] = true;
			      $disbursal_txn['send_sms'] = true;

            $disb_result = (new LoanService)->disburse($disbursal_txn, false,false, true);

            (new ScheduleService())->mark_recon_done($acc_stmt_rec['loan_doc_id'], $loan_rec->cust_id, $acc_stmt_id, $disb_result['loan_txn_id'], $stmt_txn->dr_amt, Consts::PENDING_STMT_IMPORT);

          }
        
        }

        DB::commit();

      return $acc_stmt_id;

    }catch (\Exception $e) {
      DB::rollback();
      thrw($e->getMessage());
    }
        
    }

}
