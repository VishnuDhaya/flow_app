<?php
namespace App\Services\Mobile;

use App\Repositories\SQL\AgreementRepositorySQL;
use App\Services\BorrowerService;
use App\Services\BorrowerServiceV3;
use App\Services\CustomerRegService;
use App\Validators\FlowValidator;
use App\Services\TextExtractionService;
use App\Services\PartnerService;
use App\Services\FileService;
use App\Services\FieldVisitService;
use App\Services\LoanApplicationService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Services\LoanService;
use App\Models\RMMetrics;
use App\Services\Support\SMSService;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\CallLogRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\FieldVisitRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\AddressInfoRepositorySQL;
use App\Repositories\SQL\DataProviderRepositorySQL;
use App\Repositories\SQL\MasterDataRepositorySQL;
use App\Repositories\SQL\LeadRepositorySQL;
use App\Repositories\SQL\LoanTransactionRepositorySQL;
use App\Repositories\SQL\AccProviderRepositorySQL;
use App\Services\Support\SMSNotificationService;
use App\Services\AgreementService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Consts;
use App\Exceptions\FlowCustomException;
use Illuminate\Support\Str;
use Mail;
use File;
use PDF;
use App\Mail\EmailForceCheckout;
use App\Mail\EmailUpdateGps;
use App\Mail\FlowCustomMail;
use App\Models\Account;
use App\Models\PreApproval;
use App\Models\FAUpgradeRequest;
use App\Repositories\SQL\MarketRepositorySQL;
use Exception;
use Illuminate\Support\Arr;
use App\Models\FlowApp\AppUser;
use App\Models\Lead;
use App\Models\RMLocation;
use App\Models\RMPunchTime;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\CustAgreementRepositorySQL;
use App\Repositories\SQL\CustCSFValuesRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Services\LeadService;
use Symfony\Component\HttpKernel\Profiler\Profile;
use App\SMSTemplate;
use App\Services\Vendors\Whatsapp\WhatsappWebService;
use App\Models\RmTarget;
use App\Models\Task;
use App\Services\Support\FireBaseService;
use App\Jobs\AuditKYC;
use App\Models\RMActivityLogs;
use App\Repositories\SQL\BaseRepositorySQL;
use App\Repositories\SQL\LoanApplicationRepositorySQL;

class RMService{
    public function __construct()
    {
    
    }



    public function borrower_search($data){

        $fields_arr = ['pre_appr_count',"profile_status","cust_id","biz_name","lender_code","acc_prvdr_code",'owner_person_id','status','kyc_status','business_distance','prob_fas','cond_count','last_visit_date','dp_rel_mgr_id','biz_address_id','owner_person_id','current_aggr_doc_id','aggr_valid_upto','category','prob_fas','flow_rel_mgr_id','gps','location','territory','activity_status','reg_flow_rel_mgr_id','ongoing_loan_doc_id','is_og_loan_overdue', 'acc_number', 'acc_purpose', 'allow_force_checkin_on', 'file_data_consent'];

        $data['flow_rel_mgr_id'] = isset($data['flow_rel_mgr_id']) ? $data['flow_rel_mgr_id'] : session('user_person_id'); 

        if(array_key_exists('cust_needs_visit',$data)){
            $fields_arr[]= 'next_visit_date';
        }
    
        $borr_serv = new BorrowerService();
        $borrowers = $borr_serv->borrower_search($data,$fields_arr);
            foreach ($borrowers['results'] as $borrower) {
                if($borrower->acc_prvdr_code){
                    $dp_logo_arr =  config('app.acc_prvdr_logo')[session('country_code')];
                    $borrower->dp_code_path = $dp_logo_arr[$borrower->acc_prvdr_code];
                }
                if($borrower->current_aggr_doc_id){
                    $borrower->aggr_file_rel_path = $borr_serv->get_aggr_file_rel_path($borrower->cust_id, $borrower->current_aggr_doc_id);
                }
                if($borrower->last_visit_date){
                    $borrower->last_visit_ago =  get_days_ago($borrower->last_visit_date);        
                }
        
                if($borrower->aggr_valid_upto == null && $borrower->prob_fas != 0 && $borrower->current_aggr_doc_id){
                $borrower->aggr_valid_upto  = $borrower->prob_fas ." FAs";
                }
        }
        
    
    
    return $borrowers['results'];

   }

    public function view_borrower($data) {
        $borr_serv = new BorrowerService();
        $coloumns = ["borrower" => ['lead_id', 'file_data_consent', 'tot_loans', "pre_appr_count","profile_status","first_loan_date","cust_id","is_og_loan_overdue","ongoing_loan_doc_id","business_distance","cond_count","prob_fas","status","biz_type","owner_person_id","biz_address_id","photo_shop","current_aggr_doc_id","kyc_status","ownership","category","biz_name","owner_address_id","photo_biz_lic","biz_addr_prop_type","acc_prvdr_code","aggr_valid_upto", "acc_number", "acc_purpose", "allow_force_checkin_on"],"owner_person"=>["first_name","middle_name","last_name","photo_national_id","photo_national_id_back","national_id","photo_pps","photo_selfie","gender","photo_national_id","mobile_num",'dob','whatsapp','email_id','alt_biz_mobile_num_1','alt_biz_mobile_num_2', "verified_mobile_num", "verified_alt_biz_mobile_num_1", "verified_alt_biz_mobile_num_2"],"contact_persons" => ["first_name","middle_name","last_name","gender","photo_national_id","photo_national_id_back","national_id","mobile_num","photo_selfie","photo_pps","address_id","id",'dob','whatsapp'] , "owner_address" => ["*"],"biz_address" => ["*"],"contact_address" => ["*"]];
        $results = $borr_serv->view($data,["*"],$coloumns);
        $results->total_probation_fa_count = config('app.default_prob_fas');
        if($results->acc_prvdr_code){
            $ap_logo_arr =  config('app.acc_prvdr_logo')[session('country_code')];
            $results->ap_code_path = $ap_logo_arr[$results->acc_prvdr_code];
        }
        if($results->current_aggr_doc_id){
            $results->aggr_file_rel_path = $borr_serv->get_aggr_file_rel_path($results->cust_id, $results->current_aggr_doc_id);
        }
        $results->owner_person->mobile_num_verified = true;
        $loan_repo = new LoanRepositorySQL;
        if($results->ongoing_loan_doc_id){
            $results->ongoing_fa =  $loan_repo->get_outstanding_loan($results->ongoing_loan_doc_id);

        }
        if($results->aggr_valid_upto == null && $results->prob_fas != 0 && $results->current_aggr_doc_id){
            $results->aggr_valid_upto  = $results->prob_fas ." FAs";
        }
        if(isset($results->contact_persons)){
            foreach($results->contact_persons as $contact_person){
             $contact_person->mobile_num_verified= false;
            }
        }

        $results->allow_pre_approval = $this->should_allow_pre_approval($results->cust_id); 
                
       
        return $results;
   }

 
    public function dup_check_person($fields, $cust_id = null){
        $req_person_id = $person_id = null;
        
        if(array_key_exists('person_id', $fields)){
            $req_person_id = $fields['person_id'];
            unset($fields['person_id']);
        }
        $borrower_repo = new BorrowerRepositorySQL;
        $is_name_check = array_key_exists("first_name",$fields) 
                            || array_key_exists("middle_name",$fields) 
                            || array_key_exists("last_name",$fields)
                            || array_key_exists("dob",$fields);

        $is_id_check = array_key_exists("id_type",$fields)
                            && array_key_exists("national_id",$fields);

        $mob_num_check = array_key_exists("mobile_num",$fields) ||  
                        array_key_exists("alt_biz_mobile_num_1",$fields) ;
        
        
       
        $dup_field = null;
       
        if($is_id_check){
            $dup_field = $fields['id_type'];
        }else if($is_name_check){
            $dup_field = "Customer Name";
        }else if($mob_num_check){
            $dup_field = "Mobile Number";
        }
            
        
        $person_repo = new PersonRepositorySQL;
        $person_ids = $person_repo->pluck_by_many(array_keys($fields), array_values($fields), ["id"]);
        
        if($req_person_id && sizeof($person_ids)  == 1 &&  in_array($req_person_id, $person_ids)){
            return true;
        }else if (sizeof($person_ids)  > 0){
            $addtn_condtn = " and profile_status = 'open'";
            $borrowers = $borrower_repo->get_records_by_in("owner_person_id", $person_ids, ["cust_id"], null, $addtn_condtn);
            thrw_borrower_duplicate($borrowers, $dup_field, $cust_id);
        }   
       return true;
    }
    public function dup_check_account($fields){
        $acc_repo = new AccountRepositorySQL;
        $acc_num_check =  array_key_exists("acc_prvdr_code",$fields)
        && array_key_exists("acc_number",$fields);
        if($acc_num_check){
            $dup_field = "Account Number";
        }
        $account = $acc_repo->get_records_by_many(array_keys($fields), array_values($fields), ["cust_id"]);
        thrw_borrower_duplicate($account, $dup_field);    

    }
    public function dup_check_cust($fields, $cust_id = null){
       
        $borrower_repo = new BorrowerRepositorySQL;
      
        $acc_number_check = array_key_exists("acc_prvdr_code",$fields)
                                && array_key_exists("acc_number",$fields);

        
        
        $biz_name_check = array_key_exists("biz_name",$fields);
        
       
        $dup_field = null;
        $fields['profile_status'] = 'open';
        if($biz_name_check){
            $dup_field = "Biz Name";
        }else if($acc_number_check){
            $dup_field = "Account Number";
        }
        $borrowers = $borrower_repo->get_records_by_many(array_keys($fields), array_values($fields), ["cust_id"]);
        thrw_borrower_duplicate($borrowers, $dup_field, $cust_id);
       return true;
    }
    public function get_visits_schedules($data, $field_arr = ['lead_id', 'id', 'sch_slot', 'sch_date', 'sch_status', 'sch_from', 'resch_id', 'cust_id', 'cust_name', 'visitor_id', 'visitor_name', 'visit_start_time', 'visit_end_time', 'time_spent', 'remarks', 'sch_purpose', 'location', 'visit_purpose', 'cust_mobile_num', 'biz_name', 'owner_person_id', 'cust_gps', 'gps', 'photo_visit_selfie'], $include_start_time_utc = true, $addl_sql = ""){

        $field_repo = new FieldVisitRepositorySQL;
        $borr_repo = new BorrowerRepositorySQL;
        $person_repo = new PersonRepositorySQL;
        $addr_repo = new AddressInfoRepositorySQL;
        $market_repo = new MarketRepositorySQL; 
        $field_names = array_keys($data);
        $field_values = array_values($data);
        $visits = $field_repo->get_records_by_many($field_names, $field_values, $field_arr, "and", $addl_sql);    
        $visit_slots = [];
        $market_info = $market_repo->get_market_info(session('country_code'));        
        foreach($visits as $visit){
            $visit->sch_id = $visit->id;
            $visit->sch_purpose = json_decode($visit->sch_purpose);
            if($include_start_time_utc) $visit->visit_start_time_utc =  gmdate(Consts::DB_DATETIME_FORMAT, strtotime($visit->visit_start_time));
            $visit->visit_purpose = json_decode($visit->visit_purpose);
            if(isset($visit->owner_person_id)){
                $photo_pps_path = get_file_path("persons",$visit->owner_person_id,"photo_pps");
                $visit->photo_visit_selfie_path = get_visit_selfie_path($visit->id);
                $person = $person_repo->find($visit->owner_person_id, ["photo_pps"]); 
                $names = split_names($visit->cust_name);
                $visit->first_name = $names['first_name'];
                if($person){
                    $visit->photo_pps = $photo_pps_path.'/'.$person->photo_pps;
                }
            }
            unset($visit->id);
            $visit->slot_name = dd_value($visit->sch_slot);
            if($visit->sch_status == "rescheduled"){
                $resch_data = $field_repo->find($visit->resch_id,['sch_date','sch_slot']);
                if($resch_data){
                  $visit->resch_date = $resch_data->sch_date;
                  $visit->resch_slot = $resch_data->sch_slot;
                }
            }    
            if(array_key_exists($visit->sch_slot, $visit_slots)){
                $visit_slots[$visit->sch_slot][] = $visit;
            }else{
                $visit_slots[$visit->sch_slot] = [$visit];           
            }
    
        } 
           /* foreach($visits as $visit) {
          if(array_key_exists($visit->sch_slot, $visit_slots)){
            $visit_arr = $visit_slots[$visit->sch_slot];
          }
          else{
            $visit_arr = [];
          }
          $visit_arr[] = $visit;
          $visit_slots[$visit->sch_slot] = $visit_arr; 
        } */ 
        return ["slots" => $visit_slots,"timezone" => $market_info->time_zone];

    }


    public function extract_text_details_from_card($file_data){
        $file_serv = new FileService();
        $file_path = $file_name = null;
        $nid_data= $err_msg = null;

        $lead_repo = new LeadRepositorySQL();
        if(isset($file_data['lead_id'])){
            $data = $lead_repo->get_record_by('id', $file_data['lead_id'], ['account_num', 'biz_name']);
        }else if(isset($file_data['cust_id'])){
            $borrower = (new BorrowerRepositorySQL)->get_record_by('cust_id', $file_data['cust_id'], [ 'biz_name']);
            $account = (new AccountRepositorySQL)->get_record_by_many(['cust_id', 'status'], [$file_data['cust_id'],'enabled' ], ['acc_number']);
            $data['biz_name'] = $borrower->biz_name;
            $data['account_num'] = $account->acc_number;
            $data = (object)$data;

        }
        

        try{
            $result = $file_serv->create_file_from_data_url($file_data);
            $file_path = $result["file_rel_path"];
            $file_name = $result["file_name"];
            $file = flow_storage_path($file_path .DIRECTORY_SEPARATOR. $file_name);
            $contents = file_get_contents($file);
            $extract_serv = new TextExtractionService();
            $nid_data = $extract_serv->extract_text($file_data, $contents);


            $textract = isset($nid_data['processed']) ? $nid_data['processed'] : [];
            $remove_file = false;

            $validate_arr["owner_person"] = $textract;
            $validate_arr["country_code"] = session('country_code');

            FlowValidator::validate($validate_arr, array("owner_person"), __FUNCTION__);
    

            if( array_key_exists("national_id", $textract)) {
                $chk_nat_id = ["id_type" => "national_id", 
                               "national_id" => $textract["national_id"]
                            ];
                $chk_names = [];
                if(isset($textract['last_name']) && isset($textract['first_name'])){
                    $chk_names = [ "first_name" => $textract["first_name"],
                                "last_name" => $textract["last_name"]
                            ];
                }
                if(isset($textract['middle_name'])){
                    $chk_names['middle_name'] = $textract['middle_name'];
                }

                

                if(array_key_exists('person_id', $file_data)){
                    $chk_nat_id['person_id'] = $file_data['person_id'];
                    $chk_names['person_id'] = $file_data['person_id'];
                }

                if(isset($textract['dob'])){
                    $chk_names['dob'] = $textract['dob'];
                }


                $cust_id = null;
                if(array_key_exists('lead_id', $file_data)){
                    $lead_repo = new LeadRepositorySQL();
                    $cust_id = $lead_repo->get_rekyc_lead_cust_id($file_data['lead_id']);
                    if($cust_id){
                        $cust_reg_serv = new CustomerRegService();
                        if(session('ignore_nat_check') == false){
                            $cust_reg_serv->validate_for_rekyc(['national_id' => $textract["national_id"]], $cust_id );
                        }
                    }
                }
                if(session('ignore_nat_check') == false){
                    $this->dup_check_person($chk_nat_id, $cust_id);
                    $this->dup_check_person($chk_names, $cust_id);
                }

                $result['textract'] = $textract;

                
            }else{
                thrw("Check if you have uploaded a valid {$file_data['file_of']}");
            }
            return $result;

        }catch(FlowCustomException $e){
            $err_msg = $e->getMessage();
            return ['err_msg' => $err_msg];

        }catch(\Exception $e){
            $err_msg = $e->getMessage();
            if($file_path){
                $file_serv = new FileService();
                $file_data['file_rel_path'] = $file_path;
                $file_data['file_name'] = $file_name;
                $resp = $file_serv->remove_file($file_data);
            }
            $trace = $e->getTraceAsString();
            if($nid_data != null){
                $this->send_nid_notification_mail($file_data, $err_msg, $nid_data, $trace);
            }

            $acc_num= $data->account_num;
            $biz_name= $data->biz_name;
            
            $type = $file_data['type'] == 'owner_person' ? "Biz Owner" : dd_value($file_data['type']);
           
            $err_nid_msg = "$type \n\t  [$acc_num, $biz_name], \n\t".config('app.national_id_instruction');

            return ['err_msg' => $err_nid_msg];
        }
    }

    public function send_nid_notification_mail($file_data, $err_msg, $nid_data, $trace){

        $lead_repo = new LeadRepositorySQL();
        if(isset($file_data['lead_id'])){
            $mail_data = (array)$lead_repo->get_record_by('id',$file_data['lead_id'], ['account_num', 'biz_name', 'acc_prvdr_code', 'country_code']);
        }else if(isset($file_data['cust_id'])){
            $mail_data = (array)(new BorrowerRepositorySQL())->get_record_by('cust_id',$file_data['cust_id'], ['acc_number', 'biz_name', 'acc_prvdr_code', 'country_code']);
            $mail_data['account_num'] = $mail_data['acc_number'];
        }

        $mail_data['textract'] = $nid_data['processed'];
        $mail_data['raw_data'] = $nid_data['raw'];
        $mail_data['error_msg'] = $err_msg;
        $mail_data['trace'] = $trace;
        
        Mail::to(config('app.app_support_email'))->send(new FlowCustomMail('extract_national_id', $mail_data));
 
    }

    public function file_upload($data){
        try{
            $file_serv = new FileService();
            if(isset($data['rm_photo_pps'])){
                $data['entity_code'] = session('user_person_id');  
              }
            $resp = $file_serv->create_file_from_data_url($data);
            if(isset($data['rm_photo_pps'])){
                (new PersonRepositorySQL)->update_model(["photo_pps" => $resp['file_name'], "id" =>$data['entity_code']]);
              }
            $resp['resp_msg'] = "File uploaded successfully";
            
            return $resp;
        }catch (FlowCustomException $e) {
            throw new FlowCustomException($e->getMessage());
        }
    }

    private function check_duplicate_file($file_arr, $hash){
        $all_file_md5s = Arr::pluck($file_arr, "file_md5");
        if(in_array($hash,$all_file_md5s)){
            thrw("This file was already uploaded.");
        }

    }
    
    public function stmt_upload($data){
        try{
            $data['entity_code'] = $data['lead_id'];
            $lead_repo = new LeadRepositorySQL;
            $file_json = $lead_repo->get_file_json($data['lead_id']);
            $hash = md5($data['file_data']);
            $this->check_duplicate_file($file_json['files'], $hash);
            $resp = $this->file_upload($data);
            $index = $data['index'];
            if(pathinfo($resp['file_name'], PATHINFO_EXTENSION) == 'pdf'){
                $resp['file_rel_path'] = $resp['file_rel_path'].DIRECTORY_SEPARATOR;
            }
            $file_json['files'][$index]['file_path'] = $resp['file_rel_path'];
            $file_json['files'][$index]['file_name'] = $resp['file_name'];
            $file_json['files'][$index]['file_md5'] = $hash;
            $file_json['files'][$index]['file_type'] = $data['file_type'];
            $file_json['files'][$index]['file_err'] = NULL;
            $file_json = json_encode($file_json);
            $lead_repo->update_model(['file_json' => $file_json,'id' => $data['lead_id']]);
           
            return $resp;
        }catch (FlowCustomException $e) {
            throw new FlowCustomException($e->getMessage());
        }
    }
    
    public function stmt_remove($data) {
        
        $lead_repo = new LeadRepositorySQL;
        $file_serv = new FileService();

        $lead_id = $data['lead_id'];
        $lead_data = (array)$lead_repo->get_record_by('id', $lead_id, ['acc_prvdr_code','file_json']);
        $file_json = json_decode($lead_data['file_json'], true); 

        $is_in_json = 0;
        foreach ($file_json['files'] as $index => $file_data) {
            if (
                $file_data['file_of'] == $data['file_of'] &&
                isset($file_data['file_path']) &&
                $file_data['file_path'] == $data['file_path']
                ) 
            {
                $is_in_json = 1;
                break;
            }
        }

        if ($is_in_json) {

            $file_json['files'][$index] = json_decode($this->get_file_upload_tmplt($lead_data['acc_prvdr_code']), true)['files'][$index];
            $file_json = json_encode($file_json);
            $lead_repo->update_model(['file_json' => $file_json,'id' => $lead_id]);

            $file_data['file_rel_path'] = $file_data['file_path'];
            $resp = $file_serv->remove_file($file_data);
        }
        
        $resp['is_in_json'] = $is_in_json;
        return $resp;
    }

    private function upload_file_to_s3($url, $file_path) {
        $body = fopen($file_path, 'r');
        $client = new \GuzzleHttp\Client();
        $resp = $client->request('PUT', $url, ['body' => $body, 'http_errors' => false]);
        return [    
                    'response_code' => $resp->getStatusCode(),
                    'response_msg' => json_decode($resp->getBody(), true)
                ];
    }

    public function upload_to_s3_and_invoke_lambda($lead_data, $file_path, $file_json) {

        $lead_data['lead_id'] = $lead_data['id'];
        $lead_data['acc_number'] = $lead_data['account_num'];
        unset($lead_data['id'], $lead_data['account_num']);
        
        $partner_serv = new PartnerService();
        $stmt_req_record = ($partner_serv)->req_acc_stmt($lead_data);
        
        $flow_req_id = $stmt_req_record['flow_req_id'];
        $stmt_upload_resp = $this->upload_file_to_s3($stmt_req_record['presigned_url'], $file_path);
        if ($stmt_upload_resp['response_code'] == 200) {
            $resp = [
                        'status' => 'success',
                        'flow_req_id' => $flow_req_id,
                        'file_json' => $file_json
                    ];

            $partner_serv->notify_new_acc_stmt($resp);
            return $flow_req_id;
        }      
        thrw('Unable to upload file to S3');
    }

    public function match_n_filter_stmt_group($stmt_groups, $files_uploaded, $files_not_uploaded) {
        // Remove all other groups from not uploaded array if a stmt group is matched
        foreach ($stmt_groups as $index => $stmt_group) {
            $stmts_not_uploaded_in_group = array_diff($stmt_group, $files_uploaded);
            if (empty($stmts_not_uploaded_in_group)) { //Stmt group matched if array_diff is empty
                unset($stmt_groups[$index]);
                $unmatched_stmt_groups = call_user_func_array('array_merge', $stmt_groups);
                $files_not_uploaded = array_diff($files_not_uploaded, $unmatched_stmt_groups);
                break;
            }
        }
        return $files_not_uploaded;
    }

    public function check_uploaded_files($file_json, $acc_prvdr_code) {
        $files = $file_json['files'];
        
        $files_uploaded = array();
        $uploaded_file_paths = array();
        foreach ($files as $file) {
            if(isset($file['file_path'])) {
                $file_directory = $file['file_path'];
                $file_path = $file_directory.$file['file_name'];
                $abs_file_path = flow_storage_path($file_path);
                if(File::exists($abs_file_path)) {
                    $files_uploaded[] = $file['file_of'];
                    $uploaded_file_paths[] = $file_path; 
                }
            }
        }

        $all_files = collect($files)->pluck('file_of')->toArray();   
        $files_not_uploaded = array_diff($all_files, $files_uploaded);

        if ($acc_prvdr_code == 'RBOK') {
            $comm_stmt_groups = [ ['month1_comm_sum', 'month2_comm_sum', 'month3_comm_sum'], ['comm_stmt'] ];
            $files_not_uploaded = $this->match_n_filter_stmt_group($comm_stmt_groups, $files_uploaded, $files_not_uploaded);
        }
        
        foreach ($files as $index => $file){
            $file_of = $file['file_of'];
            if(in_array($file_of, $files_not_uploaded)) {
                $file_json['files'][$index]['file_err'] = "Please upload {$file['file_label']}";
            }
            else{
                $file_json['files'][$index]['file_err'] = NULL;
            }
        }
        
        return [    
                    "files_not_uploaded" => $files_not_uploaded,
                    "uploaded_file_paths" => $uploaded_file_paths,
                    "file_json" => $file_json
                ];
    }

    public function file_process($lead_id){
            
        try {
            DB::beginTransaction(); 
            $result = False;

            $lead_repo = new LeadRepositorySQL;
            $lead_data = (array)$lead_repo->get_record_by('id', $lead_id, ['account_num', 'acc_prvdr_code', 'country_code', 'file_json']);
            $file_json = json_decode($lead_data['file_json'], true); 
            unset($lead_data['file_json']);

            $check_resp = $this->check_uploaded_files($file_json, $lead_data['acc_prvdr_code'], $lead_id);
            $file_json = $check_resp['file_json'];
            (new LeadRepositorySQL)->update_model(['file_json' => json_encode($file_json),'id' => $lead_id]);
            if (!empty($check_resp['files_not_uploaded'])) {
                DB::commit();
                thrw('Please upload the required files');
            }
            
            $uploaded_file_paths = $check_resp['uploaded_file_paths'];

            $zip_name = 'statements.zip';
            $file_rel_path = get_file_path('leads', $lead_id, 'statements')."/$zip_name";
            $zip_file_path = flow_storage_path($file_rel_path);
            create_zip_file($zip_file_path, $uploaded_file_paths);

            $this->upload_to_s3_and_invoke_lambda($lead_data, $zip_file_path, $file_json);
            DB::commit();
            return True;
        } catch (Exception $e) {
            DB::rollback();
            throw new FlowCustomException($e->getMessage());
        }
        finally {
            if(isset($zip_file_path) && File::exists($zip_file_path)) {
                File::delete($zip_file_path);
            }
        }
    }

    public function cancel_schedule($data){
        $field_repo = new FieldVisitRepositorySQL();
        $result = $field_repo->update_model(['sch_status' => 'cancelled','id' => $data['sch_id']]);
        return $result;
    } 
    // public function get_home_data($data){  
    //     [$schedules, $today_schedules]  = $this->get_schedules($data);
    //     [$loan_appl , $tot_fas_pending] = $this->get_loan_appl($data);
    //     $pending_checkout = $this->get_pending_checkout_visits($data['visitor_id']);
    //     $resp = ['today_schedules' => $today_schedules,'tot_fas_pending' => $tot_fas_pending, 'sch_data' => $schedules , 'loan_appl'=> $loan_appl] ;
    //     if($pending_checkout){
    //         $resp['pending_checkout'] = $pending_checkout;
    //     }
    //     return $resp;
    //     }
    
    public function get_home_data($data){
        $this->get_additn_condtn($data);
        $visitor_id = $data['visitor_id'];
        $today = carbon::now();
        $month_start = $today->copy()->startOfMonth();
        $months = $this->get_months();

        $pendingAction = $this->get_pending_action($visitor_id);
        $field_visits = $this->get_field_visits($visitor_id,$today);
        $acquisition = $this->get_acquisition($visitor_id,$month_start,$today);
        $live_tab = $this->get_live_tab($visitor_id,$today);
        $pending_checkout = $this->get_pending_checkout_visits($visitor_id);

        $resp = ['pendingAction' => $pendingAction, 'field_visits' => $field_visits, 'acquisition' => $acquisition, 'live_tab' => $live_tab,'months' => $months];

        if($pending_checkout){
            $resp['pending_checkout'] = $pending_checkout;
        }
        return $resp;
    }
    
    public function get_dashboard_update($data){
        $this->get_additn_condtn($data);
        $visitor_id = $data['visitor_id'];
        if($data['month'] == 30 || $data['month'] == 60 || $data['month'] == 90) {
            $month_start = carbon::now()->subDays($data['month']);
            $month_end = carbon::now();
        }

        else {
            $month_start = carbon::parse($data['month'])->startOfMonth();
            log::warning("$month_start");
            if ($month_start == (carbon::now()->startOfMonth())) {
                $month_end = carbon::now();
            } else {
                $month_end = carbon::parse($data['month'])->endOfMonth();
            }
        }
            log::warning("$month_end".(carbon::now()->format("M")));
        $overdues = $this->get_overdues($visitor_id,$month_start,$month_end);
        $stats = $this->get_stats($visitor_id,$month_start,$month_end);

        return ['overdues' => $overdues, 'stats' => $stats];
    }

    private function get_schedules($data){
        $field_repo = new FieldVisitRepositorySQL;
        $field_names = ['visitor_id','sch_status','sch_date'];
        $today_date = date_db(Carbon::now());

        $field_values = [$data["visitor_id"], 'scheduled',$today_date];
        $field_arr = ['sch_status,sch_slot,sch_purpose,sch_date,cust_id,cust_name,visitor_name,visitor_id','location','id','cust_mobile_num','cust_gps'];

        $addl_sql = "limit 3";
        $schedules = $field_repo->get_records_by_many($field_names ,$field_values, $field_arr,"and",$addl_sql);
        $count = DB::selectOne("select count(1) as count from field_visits where visitor_id = ? and sch_status = ? and sch_date = ? ",$field_values);
        $today_schedules = $count->count;
        return [$schedules,$today_schedules];
    }

    public function get_loan_appl($data){
        $loan_appl_serv = new LoanApplicationService();
        $data['status'] = 'pending_approval';
        $data['loan_approver_id'] = $data['visitor_id'];
        unset($data['visitor_id']);
        $approval_list = $loan_appl_serv->loan_appl_search($data);
        $this->get_cust_photo_and_location($approval_list);        
        $approval_list = array_reverse($approval_list);
        $loan_appl = array_slice($approval_list, 0, 3);
        $tot_fas_pending = count($approval_list);
        return [$loan_appl,$tot_fas_pending];
    }

    public function create_schedule($data){
        
        $person_repo = new PersonRepositorySQL;
        $addr_repo = new AddressInfoRepositorySQL;
        $field_repo = new FieldVisitRepositorySQL;
        $borrower_repo = new BorrowerRepositorySQL;
        $borr_serv = new BorrowerService();
        $data['visitor_id'] = (session('user_person_id')) ? session('user_person_id') : $data['rm_id'];
        $sch_slot = $data['sch_slot'];
        $sch_date = $data['sch_date'];
        $count = DB::selectOne("select count(sch_slot) as count from field_visits where visitor_id = ? and sch_slot = ? and sch_status = 'scheduled' and sch_date = ?", [$data['visitor_id'], $sch_slot, $sch_date]);
        $current_schedule_count = $count->count;
        $schedules_per_slot_limit = config('app.schedules_per_slot_limit');
        if($current_schedule_count < $schedules_per_slot_limit ){
            $is_reg_sch = false;
            if($data['sch_purpose'] == 'new_cust_reg'){
                $sql = "select count(*) as count from field_visits where sch_date = ? and cust_mobile_num = ? and sch_status = 'scheduled'";
                $field_values = [$sch_date,$data['cust_mobile_num']];
                $is_reg_sch = true;
            }else{
                $cust_id = $data['cust_id'];
                $sql = "select count(cust_id) as count from field_visits where sch_date = ? and cust_id = ? and sch_status = 'scheduled'";
                $field_values = [$sch_date,$cust_id];
            }
            $cust_count = DB::selectOne($sql,$field_values );
            $current_cust_count = $cust_count->count;
            if($current_cust_count == 0){
                if($is_reg_sch){
                    $data['sch_purpose'] = json_encode(["new_cust_reg"]);
                    
                }else{
                    $cust_profile = $borr_serv->get_borrower_profile($cust_id,true);
                    $data['cust_name'] = $cust_profile->cust_name;
                    $data['biz_name'] = $cust_profile->biz_name;
                    $data['owner_person_id'] = $cust_profile->owner_person_id;
                    $data['cust_mobile_num'] = $cust_profile->cust_mobile_num;
                    $data['sch_purpose'] = json_encode($data['sch_purpose']);
                    $data['location'] = $cust_profile->location;
                    $data['cust_gps'] = $cust_profile->gps;
                    $data['acc_number'] = $cust_profile->acc_number;
                }
                $data['sch_status'] = 'scheduled';
                $data['country_code'] = session('country_code');
                $data['visitor_name'] = $person_repo->full_name($data['visitor_id']);
                $data['sch_from'] = (isset($data['sch_from'])) ? $data['sch_from'] : Consts::RM;               
                $visit_id = $field_repo->insert_model($data);
                return $visit_id;

            }else{
                $new_format = format_date($sch_date);
                thrw("Already a visit has been scheduled for this customer on $new_format.");
            }
            
        }else{
            thrw("Can not schedule more than {$schedules_per_slot_limit} visits per slot. Already {$current_schedule_count} visits scheduled");

        }

    }

    public function loan_approval($data){
        $la_serv = new LoanApplicationService();
        $res = $la_serv->approval($data);
        $result['loan_appl_status'] = $res['status'];
        if(isset($res['loan'])){
            $result['loan_doc_id'] = $res['loan']->loan_doc_id;
        }
        $person = DB::selectOne("select mobile_num from borrowers,persons where borrowers.owner_person_id = persons.id and cust_id = ?", [$data['cust_id']]);
        $loan_repo = new LoanApplicationRepositorySQL();
        $loan_appl = $loan_repo->find_by_code($data['loan_appl_doc_id'],['loan_principal','currency_code','loan_approver_name']);
        
        if ($person){
            try{
                $serv = new FireBaseService();
                $messenger_token = AppUser::where('mobile_num',$person->mobile_num)->get(['messenger_token'])->pluck("messenger_token")[0];
                
                if($messenger_token){
                    $loan_principal = number_format($loan_appl->loan_principal);
                    $fcm_data['notify_type'] = 'fa_approval';
                    $text = "Your <b>{$loan_principal} {$loan_appl->currency_code}</b> Float Advance application has been <b>{$result['loan_appl_status']}</b>.";
                    if($result['loan_appl_status'] == "approved"){
                        $fcm_data['message'] =  "{$text} It will be credited shortly.";
                    }elseif($result['loan_appl_status'] == "rejected"){
                        $fcm_data['message'] = "{$text} Please contact Relationship Manager <b>{$loan_appl->loan_approver_name}</b> for further info.";
                    }
                    $serv($fcm_data, $messenger_token, false);
                }
            }
            catch(\Exception $e){
                $exp_msg = $e->getMessage();
                $trace = $e->getTraceAsString();
                Log::error($exp_msg);
                Log::error($trace);
            }
        
        }
        return $result;
    }

    public function reschedule_visit($data){

        $field_repo = new FieldVisitRepositorySQL();
        $borr_repo= new BorrowerRepositorySQL();
        $data['visitor_id'] = session('user_person_id');
        $data['sch_status'] = 'scheduled';
        $date = $data['sch_date'];
        $result = $field_repo->find($data['sch_id']);
        if($result->cust_id){
            $borrower = $borr_repo->find_by_code($result->cust_id,['gps']);
            $result->cust_gps = $borrower->gps;
        }
        unset($result->id, $result->created_by, $result->updated_by, $result->created_at, $result->updated_at);
        $result->sch_date = $date;
        $result->sch_slot = $data['sch_slot'];
        try
        {
            DB::beginTransaction();
            $resch_id = $field_repo->insert_model((array)$result);
            $result = $field_repo->update_model(['sch_status' => 'rescheduled','resch_id' => $resch_id,'id' => $data['sch_id']]);
            DB::commit();



        }
        catch (FlowCustomException $e){
             DB::rollback();
             throw new FlowCustomException($e->getMessage());
        };

         return $result;    
    }

    public function get_fas_by_status($data){
        
        $status_arr = ['disb_tdy', 'due_dat', 'due_tmrw', 'due', 'ongoing', 'stalled', 'overdue', 'pre_appr_tdy'];
       
        if(has_any($data,$status_arr)){
            $fields_arr = ['pre_appr_id', 'loan_doc_id', 'loan_appl_id', 'product_name', 'product_id', 'cust_name', 'cust_mobile_num','flow_rel_mgr_id', 'product_name', 'loan_principal', 'due_amount','duration',  'current_os_amount', 'currency_code',  'cust_id', 'credit_score', 'loan_appl_date', 'loan_approved_date', 'loan_approver_name', 'status',  'loan_doc_id', 'provisional_penalty', 'penalty_collected', 'paid_amount', 'due_date', 'biz_name','disbursal_date','flow_fee_type', 'flow_fee_duration', 'flow_fee', 'paid_date', 'approver_role', 'cs_result_code','customer_consent_rcvd','disbursal_status','acc_prvdr_code', 'acc_number', 'loan_purpose','last_visit_date'];

            $loan_serv = new LoanService();

            if(in_array('due',$data)){
                $data['date(due_date)'] = date_db();
                unset($data['status']);
            }else if(in_array('due_tmrw',$data)){
                $data['date(due_date)'] = Carbon::tomorrow()->toDateString();
                unset($data['status']);
            }else if(in_array('due_dat',$data)){
                $data['date(due_date)'] = Carbon::now()->addDay(2)->toDateString();
                unset($data['status']);
            }else if(in_array('disb_tdy',$data)){
                $data['date(disbursal_date)'] = Carbon::now()->toDateString();
                unset($data['status']);
            }else if(in_array('pre_appr_tdy',$data)){
                $data['pre_appr_tdy'] = true;
                unset($data['status']);
            }

            $data['mode'] = "search";
            $loans = $loan_serv->loan_search($data,$fields_arr);
            $results = $loans['results'];
            if(in_array('overdue',$data )){
                foreach($results as $loan){
                    $loan_txn_repo = new LoanTransactionRepositorySQL;
                    $loan->payments  = $loan_txn_repo->get_payments($loan->loan_doc_id);
                }
            }
               
        }else{
            $loan_appl_serv = new LoanApplicationService();
            $results = $loan_appl_serv->loan_appl_search($data);
           
        }
        $borrower_repo  = new BorrowerRepositorySQL;
        $person_repo = new PersonRepositorySQL;
        $this->get_cust_photo_and_location($results);
        
        return $results;
  }


  public function get_fas_by_criteria($criteria){
    $data = [];
    $loan_approve_data = null;
    $data[$criteria] = 'true';
      $fields_arr = ['pre_appr_id','loan_doc_id', 'loan_appl_id', 'product_name', 'product_id', 'cust_name', 'cust_mobile_num','flow_rel_mgr_id', 'product_name', 'loan_principal', 'due_amount','duration',  'current_os_amount', 'currency_code',  'cust_id', 'credit_score', 'loan_appl_date', 'loan_approved_date', 'loan_approver_name', 'status',  'loan_doc_id', 'provisional_penalty', 'penalty_collected', 'paid_amount', 'due_date', 'biz_name','disbursal_date','flow_fee_type', 'flow_fee_duration', 'flow_fee', 'paid_date', 'approver_role', 'cs_result_code','customer_consent_rcvd','disbursal_status','acc_prvdr_code', 'acc_number', 'loan_purpose','last_visit_date'];
      if($criteria == 'pending_w_cust' || $criteria == 'pending_w_prvdr'){
        $loan_serv = new LoanService();
        $data['flow_rel_mgr_id'] = session('user_person_id');
        $data['mode'] = "search";
        if($criteria == 'pending_w_prvdr'){
            $data['disburse_attempt'] = true;
            $data['status'] = "pending_w_prvdr";
        }
        $loans = $loan_serv->loan_search($data,$fields_arr);
        $results = $loans['results'];
    }else{
        $data['loan_approver_id'] = session('user_person_id');
        $loan_appl_serv = new LoanApplicationService();
        $results = $loan_appl_serv->loan_appl_search($data);
        
    }
    $this->get_cust_photo_and_location($results);

    if($criteria == 'pending_w_rm'){
        // $rm_metrics_repo = new RMMetrics();
        // $loan_approve_data = $rm_metrics_repo->get_record_by('rm_id', session('user_person_id'),['30_days_appr_count','appr_count','max_time','avg_time','30_days_avg_time','30_days_max_time']);

    }
    return $results;
    #return ["loans" => $results, "approval_times" => $loan_approve_data];
}

    public function get_cust_photo_and_location(&$loans){
        $borrower_repo  = new BorrowerRepositorySQL;
        $person_repo = new PersonRepositorySQL;
        foreach($loans as $loan){
            $borrower = $borrower_repo->find_by_code($loan->cust_id,['owner_person_id','biz_address_id','last_visit_date','location', 'biz_name' ]);
            $loan->photo_pps_path = get_file_path("persons",$borrower->owner_person_id,"photo_pps"); 
            $person = $person_repo->find($borrower->owner_person_id, ["photo_pps"]);
            $loan->photo_pps = $person ? $person->photo_pps : null ;
            // $loan->photo_pps = $person->photo_pps;
            $loan->last_visit_ago = get_days_ago($borrower->last_visit_date);
            $loan->location = $borrower->location;
            $loan->biz_name = $borrower->biz_name;

             
            if($loan->status == "pending_approval"){
                $start_date =  Carbon::parse($loan->loan_appl_date);
                $end_date = Carbon::now();
                $loan->pending_hours = $start_date->diffInHours($end_date); 
                $loan->allow_pre_approval = $this->should_allow_pre_approval($loan->cust_id);
            }
        }
        
    }

    public function should_allow_pre_approval($cust_id){
		$borr_repo = new BorrowerRepositorySQL;
		$borrower = $borr_repo->find_by_code($cust_id,['category']);
        $category_check = $borrower->category != "Probation" ? true : false;
        $market_check = in_array(session('country_code'), config('app.pre_approval_enabled_markets'));

		return $category_check && $market_check;
	}


  public function get_cal_days($data){ 
    $to_date = Carbon::now()->addDays(config('app.mob_rm_cal_days'))->format('Y-m-d');
    $from_date = Carbon::now()->subDays(config('app.mob_rm_cal_prev_days'))->format('Y-m-d');    
    $period = CarbonPeriod::create($from_date, $to_date);
    $cal_days = [];
    foreach ($period as $date) {
        $days = $date->format('l');
        $date = $date->format('Y-m-d');
        $status_count = DB::selectOne("select sum(sch_status = 'scheduled') as scheduled,sum(sch_status = 'checked_out') as checked_out from field_visits where sch_date = ? and visitor_id = ? ",[$date,$data['visitor_id']]);
        $day['is_holiday'] = in_array($date, Consts::HOLIDAYS[session('country_code')]) || $days == "Sunday" ? true : false;
        $day['date'] = $date;
        $day['scheduled'] = $status_count->scheduled;
        $day['checked_out'] = $status_count->checked_out;       
        $cal_days[] = $day; 
    }
    $result['cal_days'] = $cal_days;
    $result['today'] = Carbon::now()->format('Y-m-d');

    
    return $result;
  }

    public function get_agrmt_to_sign($data){
        $aggr_serv = new AgreementService();
        $results = $aggr_serv->load_aggrs_to_upload($data);
        $results['aggr_file_path'] = $results['master_aggr_folder_rel_path'].'/'.$results['master_agreement']->aggr_doc_id.'.pdf';
        $results = array_merge((array)$results['master_agreement'],$results);
        unset($results['master_agreement']);
        return $results;
    }


    public function list_call_logs($data){
        $call_lists = $data['call_history'];
        $person_repo = new PersonRepositorySQL;
        $borrower_repo = new BorrowerRepositorySQL;
        $call_log_repo = new CallLogRepositorySQL;

        $resp = [];

        foreach ($call_lists as $call){
            $mobile_num = $call['phoneNumber'];
            if(Str::startsWith ($mobile_num,'+' ) ){
                $mobile_num_arr = split_mobile_num($call['phoneNumber']);
                $mobile_num = $mobile_num_arr[0];
            }else if(Str::startsWith($mobile_num, "0")){
                $mobile_num = substr($mobile_num, 1);
            }
            $person = $person_repo->get_records_by_any(["mobile_num","alt_biz_mobile_num_1","alt_biz_mobile_num_2"],[$mobile_num,$mobile_num,$mobile_num],['photo_pps','first_name','middle_name','last_name']);
            if(count($person) == 1){
                $person = $person[0];
                $borrower = $borrower_repo->get_record_by("owner_person_id",$person->id,['cust_id','biz_name']);
                $call['cust_name'] = full_name($person);
                $photo_pps_path = get_file_path("persons",$person->id,"photo_pps"); 
                $call['photo_pps_path'] = $photo_pps_path;
                $call['photo_pps'] = $person->photo_pps;
                if($borrower){
                    $call['biz_name'] = $borrower->biz_name;
                    $call['cust_id'] = $borrower->cust_id;
                    $call_logger_id = session('user_person_id');
                    $field_names = ["cust_id","timestamp","call_logger_id"];
                    $field_values = [$borrower->cust_id,$call['timestamp'],$call_logger_id];
                    $call_log = $call_log_repo->get_records_by_many($field_names,$field_values,['id','remarks','call_purpose']);
                    if(sizeof($call_log) == 1){
                        $call_log = $call_log[0];
                        $call['call_log_id'] = $call_log->id;
                        $call['call_purpose'] = $call_log->call_purpose;
                        $call['remarks'] = $call_log->remarks;
                    }
                } 
            }
                
            $resp['call_history'][] = $call;
        

             
        }   

        return $resp;
    }


    public function do_call_log($data){
       $borrower_repo = new BorrowerRepositorySQL;
       $person_repo = new PersonRepositorySQL;
       $borrower_serv = new BorrowerService();
       $call_start_time =  Carbon::parse($data['dateTime']);
       $log['call_type'] = Str::lower($data['type']);
       $borrower_data = $borrower_serv->get_borrower_profile($data['cust_id']);
       $log['cust_id'] = $data['cust_id'];
       $user_person_id = session('user_person_id');
       $user_person = $person_repo->find($user_person_id);
       $user_name = full_name($user_person);
       $log['cust_name'] =  $borrower_data->cust_name;
       $log['call_logger_name'] = $user_name;
       $log['call_logger_id'] = $user_person_id;  
       $log['call_start_time'] =  $call_start_time->format(Consts::DB_DATETIME_FORMAT);
       $log['time_spent'] = $data["duration"];
       $log['call_end_time'] = $call_start_time->addSeconds($data["duration"]);
       $call_log_repo = new CallLogRepositorySQL;
       $log['time_spent'] = $data['duration'];
       $log['timestamp'] = $data['timestamp'];
       $log['remarks'] = $data['remarks'];
       $log['country_code'] = session('country_code');
       $log['call_purpose'] = json_encode($data['call_purpose']);
       $log_id = $call_log_repo->insert_model($log);
       return $log_id;

   }


    public function sign_agreement($data){
        $aggr_serv = new AgreementService();
        $borr_serv = new BorrowerService();
        $borrower_repo  = new BorrowerRepositorySQL();
        try
        {
            DB::beginTransaction();
            $cust_reg_arr = null;

            if(array_key_exists('lead_id',$data)){
                $lead_repo = new LeadRepositorySQL();
                $lead = $lead_repo->find($data['lead_id'], ['cust_reg_json', 'type']);
                $cust_reg_arr = json_decode($lead->cust_reg_json, true);

                flatten_borrower($cust_reg_arr);
                if($cust_reg_arr['cust_id'] == null ){
                    if($cust_reg_arr['biz_identity']['mobile_num'] == null || $cust_reg_arr['biz_identity']['mobile_num'] == ""){
                        thrw("Please fill in the Mobile Number field");
                    }
                    $cust_reg_arr['cust_id'] = gen_cust_id_frm_mob_num($cust_reg_arr['biz_identity']['mobile_num']);
                    
                }else if($cust_reg_arr['cust_id'] && $lead->type == 'kyc'){
                    $cust_aggr_repo = new CustAgreementRepositorySQL();
                    $active_aggrs = $cust_aggr_repo->get_records_by_many(['cust_id','status'], [$cust_reg_arr['cust_id'],'active'], ['id']);
                    if(sizeof($active_aggrs) > 0){
                        // DB::delete("delete from cust_agreements where cust_id = ? and status = ?", [$cust_reg_arr['cust_id'], 'active']);
                    }
                }
                $data['cust_id'] = $cust_reg_arr['cust_id'];
                $data['account_num'] = $cust_reg_arr['account']['acc_number'];
                
            }else{
                $aggr_serv->inactivate_agreement($data['cust_id']);
                $acc_repo = new AccountRepositorySQL();
                $aggr_repo = new AgreementRepositorySQL();
                $aggr = $aggr_repo->get_record_by('aggr_doc_id', $data['aggr_doc_id'], ['acc_purpose']);
                // $account = $acc_repo->get_record_by_many(['cust_id', 'status', 'acc_purpose'], [$data['cust_id'], 'enabled', $aggr->acc_purpose], ['acc_number']);
                // $data['account_num'] = $account->acc_number;
            }

            $aggr_file_rel_path = $aggr_serv->save_agreement($data, $cust_reg_arr);
           
            DB::commit();
            return ["aggr_file_rel_path" => $aggr_file_rel_path] ;
        
        }catch (\Exception $e) {

            DB::rollback();       
            Log::warning($e->getTraceAsString());
            //Log::warning($e->getMessage());
            if ($e instanceof QueryException){
            throw $e;
            }else{
            thrw($e->getMessage());
            } 
            //throw new Exception($e);
        }
    }

    

    

//     private function update_cust_reg_json_arr(&$reg_arr, $lead_arr,$parent_key = null){
//         foreach($reg_arr as $key => &$value){
//             if(is_array($value)){
//                $this->update_cust_reg_json_arr($value, $lead_arr,$key);
//                 }
//             else{
//                if(in_array($key, array_keys($lead_arr))){
//                 if(($key == "location" || $key == "landmark") && $parent_key != "biz_address"){
//                     continue;
//                 }
//                 $value = $lead_arr[$key];
//                }
//             }
//         }
//    }


    private function set_branch_arr($acc_prvdr_code, &$reg_arr){

        $common_repo = new CommonRepositorySQL();
        $data["country_code"] = session('country_code');
        $data["data_key"] = "{$acc_prvdr_code}_branches";
        $result = $common_repo->get_master_data($data);
        if($result){
            $reg_arr['account']['branch'] =  ['value' => NULL,'type' => 'text','cmts' => []];
        }else{
            unset($reg_arr['account']['branch']);
        }
        
    }

    private function set_alt_acc_num_arr($acc_prvdr_code, &$reg_arr){
        $acc_prvdr_arr = config('app.acc_prvdrs_w_alt_acc_num')[session('country_code')];

        if(in_array($acc_prvdr_code, $acc_prvdr_arr )){
            $reg_arr['account']['alt_acc_num'] =  ['value' => NULL, 'type' => 'text', 'cmts' => []];
        }else{
            unset($reg_arr['account']['alt_acc_num']);
        }
        
    }

    public function update_cust_reg_arr(&$reg_arr, $lead_arr, $checklist_json = []){
        $reg_arr['biz_info']['territory']['value'] = (array_key_exists('territory' , $lead_arr) && $lead_arr['territory']) ? $lead_arr['territory'] : null;
        $reg_arr['biz_identity']['gps']['value'] = (array_key_exists('gps' , $lead_arr) && $lead_arr['gps']) ? $lead_arr['gps'] : null;
        $reg_arr['biz_info']['biz_name']['value'] = (array_key_exists('biz_name' , $checklist_json) && $checklist_json['biz_name']) ? $checklist_json['biz_name'] : ($lead_arr['biz_name'] ?? null);
        $reg_arr['biz_address']['landmark']['value'] = (array_key_exists('landmark' , $lead_arr) && $lead_arr['landmark']) ? $lead_arr['landmark'] : null;
        $reg_arr['biz_address']['location']['value'] = (array_key_exists('location' , $lead_arr) && $lead_arr['location']) ? $lead_arr['location'] : null;
        $reg_arr['biz_identity']['mobile_num']['value'] = (array_key_exists('mobile_num' , $lead_arr) && $lead_arr['mobile_num']) ? $lead_arr['mobile_num'] : null;
        $reg_arr['account']['acc_number']['value'] = (array_key_exists('acc_number' , $checklist_json) && $checklist_json['acc_number']) ? $checklist_json['acc_number'] : ($lead_arr['acc_number'] ?? null);
        $reg_arr['account']['acc_prvdr_code']['value'] = (array_key_exists('acc_prvdr_code' , $checklist_json) && $checklist_json['acc_prvdr_code']) ? $checklist_json['acc_prvdr_code'] : ($lead_arr['acc_prvdr_code'] ?? null);
        
        if(isset($checklist_json['acc_prvdr_code'])){
            $this->set_branch_arr($checklist_json['acc_prvdr_code'],$reg_arr);
            $this->set_alt_acc_num_arr($checklist_json['acc_prvdr_code'], $reg_arr);
        }

        $reg_arr['owner_person']['national_id']['value'] = (array_key_exists('national_id' , $lead_arr) && $lead_arr['national_id']) ? $lead_arr['national_id'] : null;
        $cust_id = (isset($lead_arr['type']) && $lead_arr['type'] == 're_kyc') ? $reg_arr['cust_id'] : null;
        $reg_arr['partner_kyc']['UEZM']['UEZM_MainContent_txtAbbreviationName']['value'] = array_key_exists('UEZM_MainContent_txtAbbreviationName' , $lead_arr) ? $lead_arr['UEZM_MainContent_txtAbbreviationName'] : null;
        $reg_arr['partner_kyc']['UEZM']['UEZM_MainContent_txtCompanyRegistrationNo']['value'] = array_key_exists('UEZM_MainContent_txtCompanyRegistrationNo' , $lead_arr) ? $lead_arr['UEZM_MainContent_txtCompanyRegistrationNo'] : null;
        $reg_arr['partner_kyc']['UEZM']['UEZM_MainContent_ddlNatureOfBusiness']['value'] = array_key_exists('UEZM_MainContent_ddlNatureOfBusiness' , $lead_arr) ? $lead_arr['UEZM_MainContent_ddlNatureOfBusiness'] : null;
        $reg_arr['partner_kyc']['UEZM']['UEZM_MainContent_ddOperatedBy']['value'] = array_key_exists('UEZM_MainContent_ddOperatedBy' , $lead_arr) ? $lead_arr['UEZM_MainContent_ddOperatedBy'] : null;
        $reg_arr['partner_kyc']['UEZM']['UEZM_MainContent_ddWallet']['value'] = array_key_exists('UEZM_MainContent_ddWallet' , $lead_arr) ? $lead_arr['UEZM_MainContent_ddWallet'] : null;
        $reg_arr['partner_kyc']['UEZM']['UEZM_MainContent_txtRecruiterID']['value'] = array_key_exists('UEZM_MainContent_txtRecruiterID' , $lead_arr) ? $lead_arr['UEZM_MainContent_txtRecruiterID'] : null;
        $reg_arr['partner_kyc']['UEZM']['UEZM_MainContent_ddlZone']['value'] = array_key_exists('UEZM_MainContent_ddlZone' , $lead_arr) ? $lead_arr['UEZM_MainContent_ddlZone'] : null;
       
        $reg_arr['register_num_verify'] = config('app.register_num_verify');
        $reg_arr['alternate_num_verify'] = config('app.alternate_num_verify');
        $reg_arr['addl_num_verify'] = config('app.addl_num_verify');

        $reg_arr['agreements'] = $this->get_master_agreements($lead_arr['acc_purpose'], $cust_id);

    }

    private function get_master_agreements($acc_purpose, $cust_id = null){
        $aggr_type = 'probation';
        $borrower = (new BorrowerRepositorySQL)->find_by_code($cust_id, ['category', 'tot_loans']);
        if($borrower){
            $aggr_type = strtolower($borrower->category);
            if(($aggr_type != 'probation' || $borrower->tot_loans >= config('app.default_prob_fas') )   && $aggr_type != 'condonation'){
                $aggr_type = 'onboarded';
            }
        }
        $lender_code = session('lender_code');
        $addl_sql ="";
        if(is_tf_acc($acc_purpose)){
            $addl_sql = "and ((acc_purpose = 'float_advance' and aggr_type = '$aggr_type') or acc_purpose = 'terminal_financing')";
        }else if(is_fa_acc($acc_purpose)){
            $addl_sql = "and acc_purpose = 'float_advance' and aggr_type = '$aggr_type'";
        }
        $agreements = DB::select("select acc_purpose,aggr_doc_id,aggr_type,duration_type,aggr_duration from master_agreements where status = 'enabled' and lender_code = ? and country_code = ? $addl_sql",[$lender_code,session('country_code')]);
        $data_arr = [];
        foreach($agreements as $key => $value){
            $data_arr[] = $value;
            $data_arr[$key]->aggr_file_path = get_file_rel_path_w_file_name($value->aggr_doc_id);
            $data_arr[$key]->status = "draft";
        }

        return $data_arr;
    }

    public function append_visit_id($lead_id,$visit_id){
        $lead_repo = new LeadRepositorySQL();
        $lead_data = $lead_repo->find($lead_id,['visit_ids']);
        $visit_id_arr = json_decode($lead_data->visit_ids,true);
        if(!in_array($visit_id,$visit_id_arr)){
            array_push($visit_id_arr,$visit_id);
        }
        $visit_id_arr = json_encode($visit_id_arr);
        return $visit_id_arr;
        
    }

   
    public function cust_evaluation($data){
        $checklist = $data['cust_eval_checklist'];
        $update_arr = array();
        $lead_repo = new LeadRepositorySQL;
        if($checklist['cust_int']){
            $checklist_json = $checklist['checklist_json'];
            $checklist['checklist_json'] = json_encode($checklist['checklist_json']);
            $this->dup_check_cust(["acc_prvdr_code" => $checklist_json['acc_prvdr_code'],
            "acc_number" =>$checklist_json['acc_number']]); 
            $this->dup_check_cust(["biz_name" => $checklist_json['biz_name']]); 
            $lead_data = $lead_repo->find($checklist['lead_id'],['file_json','lead_json','account_num','acc_prvdr_code','biz_name','acc_purpose','visit_ids']); 
            if($lead_data){
                if($checklist['rm_recommendation']){
                    $lead_data->lead_json = json_decode($lead_data->lead_json);
                    $lead_arr = (array)$lead_data;

                    $cust_reg_arr = get_cust_reg_arr();
                    
                    if(is_tf_acc($lead_data->acc_purpose)){

                        $checklist_json['acc_number'] = "dummy_".strtoupper(uniqid());
                    }
                    $lead_json = (array)$lead_arr['lead_json'];
                    $lead_json['acc_purpose'] = $lead_arr['acc_purpose'];

                    $this->update_cust_reg_arr($cust_reg_arr,$lead_json, $checklist_json);
                    $cust_reg_json = json_encode($cust_reg_arr);
                    $visit_id_arr = $this->append_visit_id($checklist['lead_id'],$checklist['visit_id']);
                    $update_arr += ["status" => Consts::PENDING_DATA_CONSENT, "visit_ids" => $visit_id_arr,"cust_reg_json" => $cust_reg_json,"cust_eval_json" => $checklist['checklist_json'] , "eval_date" =>  datetime_db()];

                    if($checklist_json['biz_name'] != $lead_arr['biz_name'] || isset($lead_arr['account_num']) && $checklist_json['acc_number'] != $lead_arr['account_num'] || $checklist_json['acc_prvdr_code'] != $lead_arr['acc_prvdr_code']){
                        $update_arr += ['biz_name' => $checklist_json['biz_name'], 'account_num' => $checklist_json['acc_number'], 'acc_prvdr_code' => $checklist_json['acc_prvdr_code']];
                        $lead_repo->update_lead_json($checklist_json, $checklist['lead_id']);
                    }
                }else{
                    $update_arr = ["status" => Consts::RM_REJECTED];
                }   
            }        
        }else{
            $checklist['visit_purpose'] = ["create_lead"];
            $checklist['remarks'] = "create_lead";
            $checklist['force_checkout_reason'] = null;
            $resp = $this->cust_reg_checkout($checklist);
            $update_arr = ["status" => Consts::CUST_NOT_INTERESTED];
        }
        $update_arr += ['id' =>$checklist['lead_id']];
        $resp = $lead_repo->update_model($update_arr);
        return $resp;

    }


    public function get_file_upload_tmplt($acc_prvdr_code){
        $acc_prvdr_arr = array_keys(Consts::LEAD_FILE_UPLOAD_TMPLT);
        if(in_array($acc_prvdr_code , $acc_prvdr_arr)){
            $file_arr = Consts::LEAD_FILE_UPLOAD_TMPLT[$acc_prvdr_code];
            return json_encode($file_arr);
        }
    }
       
    
    private function send_gps_notification_email($data){
        $person_repo = new PersonRepositorySQL;
        $borr_repo = new BorrowerRepositorySQL;
        $brwr = $borr_repo->get_record_by("biz_address_id",$data['id'],['cust_id','gps']);
        $data['old_gps'] = ($brwr && $brwr->gps) ? $brwr->gps : "NA";
        $data['cust_id'] = $brwr->cust_id;
        $user_person = $person_repo->find(session("user_person_id"));
        $data['user_name'] = full_name($user_person);
        Mail::to(['praveen@flowglobal.net','kevina@flowglobal.net'])->send(new EmailUpdateGps($data));  

    }
    private function update_address($data){
        try
        {
           DB::beginTransaction();
            $address = $data['address'];
            $field_repo = new FieldVisitRepositorySQL();
            if(!array_key_exists('sch_id',$address) && array_key_exists('cust_id' , $data) ){
                // $sch_data = $field_repo->get_record_by_many(['cust_id','sch_status'] , [$data['cust_id'],'scheduled'],['id']);
                $date = date_db();
                $addl_sql = "and date(sch_date) = '$date'";
                $sch_data = $field_repo->get_record_by_many(['cust_id','sch_status'] , [$data['cust_id'],'scheduled'],['id'] , 'and' , $addl_sql);
                if($sch_data){
                    $address['sch_id'] = $sch_data->id;
                }
            }
            if(array_key_exists('cust_id' , $data) && !array_key_exists('address_id',$address)){
                $borr_repo = new BorrowerRepositorySQL;
                $borrower = $borr_repo->find_by_code($data['cust_id'],['biz_address_id']);
                if($borrower && $borrower->biz_address_id){
                    $address['id'] =  $borrower->biz_address_id;
                }
            }else{
                $address['id'] = $address['address_id'];
            } 
            $addr_repo = new AddressInfoRepositorySQL();
            if(array_key_exists('gps' , $address)  && $address['gps'] && env('APP_ENV') == 'production'){
                $this->send_gps_notification_email($address);
            }
            $resp = $addr_repo->update($address);                            
            if(array_key_exists('sch_id' , $address) && array_key_exists('gps' , $address) &&
                $address['sch_id'] && $address['gps'] ){
                $cust_gps = $address['gps'];
                $id = $address['sch_id'];
                $field_repo->update_model(['cust_gps' => $cust_gps,'id' => $id]);
            }  
            DB::commit();
        }
        catch (Exception $e) {
            DB::rollback() ;
            throw new Exception($e->getMessage());
        }
        return $resp;

    }
    public function update_cust_profile($data){
        $update_fields_arr = ["gps", "email_id", "gender", "biz_type", "ownership", "photo_selfie"];
        
        if(array_key_exists('person' ,$data) && !empty(array_intersect(array_keys($data['person']),$update_fields_arr)) || 
        array_key_exists('address' ,$data) && !empty(array_intersect(array_keys($data['address']),$update_fields_arr))
        || array_key_exists('borrower' ,$data) && !empty(array_intersect(array_keys($data['borrower']),$update_fields_arr)) ){
        
            if(array_key_exists('person' ,$data)){
                $borr_serv = new BorrowerService();
                $person = $data['person'];
                $person['id'] = $person['person_id'];
                $resp = $borr_serv->update_person($person);
            }else if(array_key_exists('address' ,$data)){
                $resp = $this->update_address($data);
        
            }else if(array_key_exists('borrower' ,$data)){
                $borrower = $data['borrower'];
                $borrower['country_code'] = session('country_code');
                $brwr_serv = new BorrowerService();
                $resp = $brwr_serv->update($borrower);
            }
        }else{
            thrw("You can't update this field. Please request auditors to update this field");
        }

        return $resp;
    }
    
    public function get_last_n_fas($data){
        $loan_appl_serv = new LoanService();
        $loans = $loan_appl_serv->loan_search($data);
        $results = $loans['results'];
        $rm_metrics_repo = new RMMetrics;
        $loan_approve_data = $rm_metrics_repo->get_record_by('rm_id',session('user_person_id'),['30_days_appr_count','appr_count','max_time','avg_time','30_days_avg_time','30_days_max_time']);
        return $results;
        #return ["loans" => $results, "approval_times" => $loan_approve_data];
    }


    public function cust_reg_checkin($checkin_req){
        $visitor_id = session('user_person_id');
        $pending_checkout = $this->get_pending_checkout_visits($visitor_id);
        if($pending_checkout === false){

            if(array_key_exists('mobile_num',$checkin_req) &&  array_key_exists('biz_name',$checkin_req)){
                $this->dup_check_person(['mobile_num' => $checkin_req['cust_mobile_num']]); 
                $this->dup_check_cust(["biz_name" => $checkin_req['biz_name']]); 
            }
           
            $checkin_req['visit_start_time'] = datetime_db();
            if(array_key_exists('sch_id',$checkin_req)){
                $resp = $this->schedule_checkin($checkin_req);
            }else{
                $resp = $this->direct_checkin($checkin_req);
                if($checkin_req['shop_status'] == "closed"){
                    $checkout_req['visit_id'] = $resp['visit_id'];
                    $checkout_req['visit_purpose'] = ['shop_closed'];
                    $checkout_req['remarks'] = "shop_closed";
                    $checkout_req['force_checkout'] = false;
                    $checkout_req['force_checkout_reason'] = null;
                    $checkout_req['gps'] = $resp['gps'];
                    $resp = $this->cust_reg_checkout($checkout_req);
                }
            } 
            return $resp;
            
            
        }else{
            return ['pending_checkout' =>$pending_checkout];
        }
        
    }

    public function cust_reg_checkout($checkout_req){
            $field_repo = new FieldVisitRepositorySQL;
            $borrower_repo = new BorrowerRepositorySQL;
            $lead_repo = new LeadRepositorySQL;
            $checkout_req['visit_purpose'] = json_encode($checkout_req['visit_purpose']);
            $checkout_req['visit_end_time'] = datetime_db();
            $visitor_id = session('user_person_id');
            $last_visit_date = $checkout_req['visit_end_time'];
            $sch_status = 'checked_out';
            $update_arr = ['id'=> $checkout_req['visit_id'],'visit_purpose' => $checkout_req['visit_purpose'] ,'remarks' => $checkout_req['remarks'],'visit_end_time' => $checkout_req['visit_end_time'],'sch_status' => $sch_status,"gps" => $checkout_req['gps'],"force_checkout" => $checkout_req['force_checkout'],"force_checkout_reason" => $checkout_req['force_checkout_reason']];
            if(array_key_exists('checkout_distance' ,$checkout_req)){
                $update_arr += ["checkout_distance" => $checkout_req['checkout_distance']];
            }
            if(array_key_exists('early_checkout' ,$checkout_req)){
                $update_arr += ["early_checkout" => $checkout_req['early_checkout']];
            }

            
            try
                {
                    
                    DB::beginTransaction();

                    if(array_key_exists('lead_id',$checkout_req)){
                        $lead_data = $lead_repo->find($checkout_req['lead_id'],['biz_name']);
                        $update_arr += ["biz_name" => $lead_data->biz_name];
                    }
                   
                    if(array_key_exists('cust_id' ,$checkout_req)){
                        $cust_id = $checkout_req['cust_id'];
                        $update_arr += ["cust_id" => $cust_id];
                        $borrower_repo->update_model_by_code(["cust_id" => $cust_id,"last_visit_date" => $last_visit_date]); 
                    }

                     if($checkout_req['force_checkout'] && env('APP_ENV') == 'production' ){
                        $checkout_req['visitor_id'] = $visitor_id;
                        $this->send_force_checkout_email($checkout_req);
                        
                    }

                    $field_repo->update_model($update_arr,"id");

                    DB::commit();
                }


                catch (Exception $e) {
                    DB::rollback() ;
                    throw new Exception($e->getMessage());
                };

            return "The field visit has been registered on Flow App successfully";

    }

    private function schedule_checkin($checkin_req){
        $field_repo = new FieldVisitRepositorySQL();
        $visit_purpose = json_encode(['new_cust_reg']);
        $checkin_req['country_code'] = session('country_code');
        $sch_status = "checked_in";
        $field_repo->update_model(['id'=> $checkin_req['sch_id'],'country_code' => $checkin_req['country_code'], 'visit_purpose' => $visit_purpose,'visit_start_time' => $checkin_req['visit_start_time'],'sch_status' => $sch_status ],"id");
        #$sch_data = $field_repo->find($checkin_req['sch_id'], ["cust_kyc_data"]);
        #$cust_kyc_data =  json_decode($sch_data->cust_kyc_data);
        return ["action" => "cust_reg"];

    }

    private function set_lead_gps($data){
        $lead_repo = new LeadRepositorySQL;
        $lead_data = $lead_repo->find($data['lead_id'],['lead_json']);
        $lead_json = json_decode($lead_data->lead_json,true);
        $lead_json['gps'] = $data['gps'];
        $lead_json = json_encode($lead_json);
        return $lead_json;
    }

    private function direct_checkin($checkin_req){
        $person_repo = new PersonRepositorySQL;
        $checkin_req['sch_purpose'] = json_encode($checkin_req['sch_purpose']);
        $checkin_req['visitor_id'] = session("user_person_id");
        $user_person = $person_repo->find($checkin_req['visitor_id']);
        $checkin_req['visitor_name'] = full_name($user_person);
        $checkin_req['sch_status'] = "checked_in";
        $checkin_req['sch_from'] = Consts::RM;
        $checkin_req['country_code'] = session('country_code');
        $checkin_req['sch_date'] = date_db();
        $visit_start_time_utc =  gmdate(Consts::DB_DATETIME_FORMAT, strtotime($checkin_req['visit_start_time']));
        $hour = Carbon::now()->hour;
        if($hour < 13){
            $checkin_req['sch_slot'] = 'morning';
        }else if($hour >= 13){
            $checkin_req['sch_slot'] = 'post_noon';
        }
        try{
            DB::beginTransaction();
            $field_repo = new FieldVisitRepositorySQL;
            $checkin_req['cust_gps'] = $checkin_req['gps'];
            $visit_id = $field_repo->insert_model($checkin_req);
            if(isset($checkin_req['lead_id'])){
                $lead_json = $this->set_lead_gps($checkin_req);
                $lead_id =  $checkin_req['lead_id'];
                $visit_id_arr = $this->append_visit_id($checkin_req['lead_id'],$visit_id);
                $lead_repo = new LeadRepositorySQL;
                $lead_repo->update_model(['id' => $checkin_req['lead_id'] ,'lead_json' => $lead_json, 'visit_ids' => $visit_id_arr ]);
            }else{
                $lead_id = null;
            }
            mount_entity_file("cust_reg_checkin", $checkin_req, $visit_id, 'photo_visit_selfie');
            DB::commit();
        }
        catch (Exception $e) {
            DB::rollback() ;
            throw new Exception($e->getMessage());
        }
        return ["action" => "checkout", 'lead_id' => $lead_id, 'biz_name' => $checkin_req['biz_name'], 'visit_id' => $visit_id, "visit_start_time" => $checkin_req['visit_start_time'],
        'visit_start_time_utc' => $visit_start_time_utc, 'sch_slot' => $checkin_req['sch_slot'], 'gps' => $checkin_req['gps']];
        
        
    }
    private function send_force_checkout_email($data){
        $person_repo = new PersonRepositorySQL;
        $visit_data = DB::selectOne("select cust_gps,cust_id,visit_start_time as checkin_time, gps as checkin_location from field_visits where visitor_id = ? and id = ?  and visit_end_time is null and sch_status != 'scheduled' order by visit_start_time desc limit 1 " ,[$data['visitor_id'],$data['visit_id']]);
        $start_time = Carbon::parse($visit_data->checkin_time);
        $end_time = Carbon::parse($data['visit_end_time']);
        $duration = $start_time->diff($end_time)->format('%H:%I');
        $user_name = $person_repo->full_name($data['visitor_id']);
        $mail_data = ['checkout_time' => $data['visit_end_time'], 'checkout_location' => $data['gps'],'checkout_distance' => $data['checkout_distance'],'user_name' => $user_name,'duration' => $duration,'cust_id' => $data['cust_id'],"force_checkout_reason" => $data['force_checkout_reason']];
        $mail_data = array_merge((array)$visit_data,$mail_data);
        Mail::to(['praveen@flowglobal.net','kevina@flowglobal.net'])->send(new EmailForceCheckout($mail_data));
    }

    public function get_pending_checkout_visits($visitor_id){
        $pending_checkout =  DB::selectOne("select lead_id,id,biz_name,cust_id,visit_start_time,sch_slot from field_visits where visitor_id = ?  and visit_end_time is null and sch_status = 'checked_in' order by visit_start_time desc limit 1 " ,[$visitor_id]);

        if($pending_checkout){
            $pending_checkout->visit_start_time_utc =  gmdate(Consts::DB_DATETIME_FORMAT, strtotime($pending_checkout->visit_start_time));
            return $pending_checkout;
        }

        return false;
    }
    public function get_months(){
        $month = carbon::now()->startOfMonth();
        $months = [$month->format("M"),$month->subMonth(1)->format("M"),$month->subMonth(1)->format("M")];

        return $months;
    }
    public function get_pending_action($visitor_id){
        $pending_fa = DB::selectOne("select count(*) as pending_fa from loan_applications where loan_approver_id = {$visitor_id} and status='pending_approval' $this->addl_sql $this->addl_sql2" )->pending_fa;
        # need change in the conditions
        $agrmt_renewal = DB::selectOne("select count(*) as agrmt_renewal from borrowers where ((aggr_valid_upto between '" .Carbon::now()->subDays(7). " 'and' " .Carbon::now()->addDays(14)."'  and prob_fas = 0) or (aggr_status = 'inactive' and status ='enabled')) and flow_rel_mgr_id= {$visitor_id} $this->addl_sql $this->addl_sql2")->agrmt_renewal;
        $kyc_pending = DB::selectOne("select count(*) as kyc_pending from leads where status='40_pending_kyc' and flow_rel_mgr_id={$visitor_id} $this->addl_sql $this->addl_sql2")->kyc_pending;
        return ['pending_fa' => $pending_fa, 'agrmt_renewal' => $agrmt_renewal, 'kyc_pending' => $kyc_pending];
    }
    public function get_field_visits($visitor_id,$today){
        $scheduled = DB::selectOne("select count(*) as schedule from field_visits fi,borrowers l where fi.cust_id = l.cust_id and visitor_id = {$visitor_id} and date(sch_date) = date('{$today}') and sch_status = 'scheduled' $this->addl_sql1 $this->addl_sql2")->schedule;
        $scheduled += DB::selectOne("select count(*) as schedule from field_visits fi,leads l where fi.lead_id = l.id and fi.cust_id is null and visitor_id = {$visitor_id} and date(sch_date) = date('{$today}') and sch_status = 'scheduled' $this->addl_sql1 $this->addl_sql2")->schedule;
        $visited = DB::selectOne("select count(*) as visited from field_visits fi,borrowers l where fi.cust_id = l.cust_id and visitor_id = {$visitor_id} and date(sch_date) = date('{$today}') and sch_status = 'checked_out' $this->addl_sql1 $this->addl_sql2")->visited;
        $visited += DB::selectOne("select count(*) as visited from field_visits fi,leads l where fi.lead_id = l.id and fi.cust_id is null and visitor_id = {$visitor_id} and date(sch_date) = date('{$today}') and sch_status = 'checked_out' $this->addl_sql1 $this->addl_sql2")->visited;

        return ['visited' => $visited, 'to_visit' => $scheduled];
    }

    public function  get_acquisition($visitor_id,$month_start,$today){
        $acquired = DB::selectOne("select count(*) as acquired from borrowers where reg_flow_rel_mgr_id = {$visitor_id} and first_loan_date >= '{$month_start}' and first_loan_date <= '{$today}' $this->addl_sql $this->addl_sql2")->acquired;
        // $targets = config('app.rms_monthly_targets');
        $date = new Carbon($month_start);
        // $month = $date->format('Y-m');
        $month = $date->format('M');
        $year = $date->format('Y');
        $rm_targets = DB::selectOne("select json_extract(targets, '$.{$month}') as targets from rm_targets where rel_mgr_id = ? and year = ?",[$visitor_id,$year]);
        
        if($rm_targets && $rm_targets->targets){
            $target = $rm_targets->targets;
        }else{
            $target = 'NA';
        }
       
        return ['acquired' => $acquired, 'target' => $target];
    }

    public function get_overdues($visitor_id,$month_start,$month_end){
        $new_overdues = DB::selectOne("select count(*) as new from loans where flow_rel_mgr_id = {$visitor_id} and paid_date is null and due_date >= '{$month_start}' and due_date <= '{$month_end}' $this->addl_sql $this->addl_sql2")->new;
        $overdues_recovered = DB::selectOne("select count(distinct lt.loan_doc_id) as recovered from loans l,loan_txns lt where l.loan_doc_id = lt.loan_doc_id and flow_rel_mgr_id = {$visitor_id} and (paid_date > due_date or paid_amount > 0) and (txn_date >= '{$month_start}' and txn_date<= '{$month_end}') and txn_date > due_date $this->addl_sql1 $this->addl_sql2")->recovered;
        # shoud it include partial payments

        $overdues_g_5d = DB::selectOne("select count(*) as greater_overdue from loans where flow_rel_mgr_id = {$visitor_id} and  datediff('{$month_end}',due_date) > 5 and paid_date is null and due_date >= '{$month_start}' and due_date <= '{$month_end}' $this->addl_sql $this->addl_sql2")->greater_overdue;

        return ['new_overdues' => $new_overdues, 'overdues_recovered' => $overdues_recovered, 'overdue_g_5G' => $overdues_g_5d];
    }
    public  function get_stats($visitor_id,$month_start,$month_end){
        $cust = DB::select("select count(*) as tot_cust, count(IF(activity_status = 'active', 1, null)) as active_cust, 
                                                                count(IF(activity_status = 'passive', 1, null)) as passive_cust,
                                                                count(IF(activity_status = 'dormant', 1, null)) as dormant_cust 
                                                                from borrowers where flow_rel_mgr_id = {$visitor_id} $this->addl_sql $this->addl_sql2");
        $ontime_perc = DB::selectOne ("select ((count(if(paid_date <= due_date,1,null))/count(if(disbursal_status = 'disbursed',1,null)))*100) as ontime from loans where flow_rel_mgr_id = {$visitor_id} and due_date >= '{$month_start}' and due_date <= '{$month_end}' $this->addl_sql $this->addl_sql2")->ontime;
        $brwrvisit = DB::selectOne("select sum(visits) as visits from (select count(distinct fi.cust_id) as visits from field_visits fi,borrowers b where visitor_id = {$visitor_id} and visit_end_time >= '{$month_start}' and visit_end_time <= '{$month_end}' and fi.cust_id = b.cust_id {$this->addl_sql2} $this->addl_sql3 group by date(visit_end_time))as fv",[])->visits;
        $leadvisits = DB::selectOne("select sum(visits) as visits from (select count(distinct fi.lead_id) as visits from field_visits fi,leads l where visitor_id = {$visitor_id} and visit_end_time >= '{$month_start}' and visit_end_time <= '{$month_end}' and fi.lead_id = l.id $this->addl_sql2 $this->addl_sql1 group by date(visit_end_time))as fv")->visits;
        $visits = $brwrvisit + $leadvisits;
        return ['cust' => $cust, 'ontime' => intval(round($ontime_perc)), 'visits' => $visits];
    }
    public function get_live_tab($visitor_id,$today){
        $par_date = $today->copy()->subDay(60);
        $ongoing = DB::selectOne("select count(*) as ongoing from loans where flow_rel_mgr_id = {$visitor_id} and status in ('ongoing', 'due') $this->addl_sql $this->addl_sql2")->ongoing;
        #what is the meaning of ongoing here

        $par_le_60d = DB::selectOne("select count(*) as par_le from loans where flow_rel_mgr_id = {$visitor_id} and paid_date is null and due_date >= '{$par_date}' and due_date <= '{$today}' $this->addl_sql $this->addl_sql2")->par_le;
        $par_g_60d = DB::selectOne("select count(*) as par_g from loans where flow_rel_mgr_id = {$visitor_id} and paid_date is null and due_date < '{$par_date}' $this->addl_sql $this->addl_sql2")->par_g;

        return ['ongoing' => $ongoing, 'par_le_60d' => $par_le_60d, 'par_g_60d' => $par_g_60d];
    }

    private function get_additn_condtn($data){
        $cc = session('country_code');
        $this->addl_sql = " and country_code = '{$cc}'";
        $this->addl_sql1 = " and l.country_code = '{$cc}'";
        $this->addl_sql3 = " and b.country_code = '{$cc}'";
        $this->addl_sql2 = "";
        if(session('acc_prvdr_code') != 'ALL') {
            $ap_code = session('acc_prvdr_code');
            // $dp_code = $data['acc_prvdr_code'] == "UMTN" ? "UFLO" : $data['acc_prvdr_code'];
            $this->addl_sql2 = "and acc_prvdr_code = '{$ap_code}'";

        }
    }

  
    public function send_otp_to_mobile_num($borrower){

        $borrower_serv = new BorrowerService;
        $sms_serv = new SMSNotificationService();  
        $person_repo = new PersonRepositorySQL();
        $borr_repo = new BorrowerRepositorySQL();

        // $mobile_num_fields = ['mobile_num' => null, 'alt_biz_mobile_num_1' => null, 'alt_biz_mobile_num_2' => null];
        // $key = array_intersect_key($borrower, $mobile_num_fields);
        // $mobile_field = array_keys($key)[0];

        $mobile_field = $this->get_mobile_num_field($borrower);
        
        $borrower['entity_verify_col'] = "verified_".$mobile_field;
        $borrower['entity_update_value'] = 1;

        $borr = $borr_repo->get_record_by('cust_id', $borrower['cust_id'], ['owner_person_id', 'acc_prvdr_code']);
        $person = $person_repo->get_record_by('id', $borr->owner_person_id, ['first_name']);

        $borrower['person_id'] = $borr->owner_person_id;
        $mobile_verify_otp = $borrower_serv->get_cust_reg_otp($borrower,$borrower[$mobile_field]);
        $sms_reply_to = config('app.sms_reply_to')[session('country_code')];

        $sms_serv->notify_welcome_customer(['cust_mobile_num' => $borrower[$mobile_field], 
                                              'country_code' => session('country_code'),
                                              'acc_prvdr_code' => session('acc_prvdr_code'),
                                               'customer_success' => config('app.customer_success_mobile')[$borr->acc_prvdr_code],
                                               'otp_code' => $mobile_verify_otp[0],
                                               'otp_id' => $mobile_verify_otp[1],
                                               'sms_reply_to' => $sms_reply_to,
                                               'cust_id' => $borrower['cust_id'],
                                               'cust_name' => $person->first_name
                                            ]);
        
        $msg = "An OTP has been sent to the mobile number.Please ask customer to send the OTP to {$sms_reply_to} in the format FLOW <OTP> Eg: If OTP is 123456, FLOW 123456";
        return $msg;
    }

    public function send_kyc_otp_to_mobile_num($data){
        $sms_serv = new SMSNotificationService();
        $lead_repo = new LeadRepositorySQL;
        $mobile_field = $this->get_mobile_num_field($data);        
        $data['entity_verify_col'] = "verified_".$mobile_field;
        $data['entity_update_value'] = 1;
        $mobile_verify_otp = $this->get_kyc_otp($data,$data[$mobile_field]);
        $cust_reg_arr = $lead_repo->get_cust_reg_arr($data['lead_id']);
        $sms_reply_to = config('app.sms_reply_to')[session('country_code')];

        
        $data_arr = ['cust_mobile_num' => $data[$mobile_field], 
                     'country_code' => session('country_code'),
                     'otp_code' => $mobile_verify_otp[0],
                     'otp_id' => $mobile_verify_otp[1],
                     'sms_reply_to' => $sms_reply_to,
                     'purpose' => 'otp/cust_kyc',
                     'cust_name' => $cust_reg_arr['owner_person']['first_name']['value']
                    ];

        if(Str::contains($data['entity_verify_col'], 'verified_addl_mobile_num') ){
            
            $index = filter_var($data['entity_verify_col'], FILTER_SANITIZE_NUMBER_INT);            
            $addl_num = $cust_reg_arr['addl_num'][$index];
            $cust_reg_arr['addl_num'][$index][$data['entity_verify_col']] = 0;
            $data_arr['relation'] = $addl_num['relation']['value'];
            $gender = $cust_reg_arr['owner_person']['gender']['value'];
            $data_arr['gender'] = $gender == 'male' ? 'his' : 'her';
            $template = 'ADDL_NUM_OTP_MSG';
        }else{
            $lead_repo = new LeadRepositorySQL;
            $cust_reg_arr['biz_identity'][$data['entity_verify_col']] =  0;
            $template = 'OTP_MSG';

        }

        (new LeadRepositorySQL)->update_cust_reg_json($cust_reg_arr, $data['lead_id']);

        $sms_serv->send_confirmation_message($data_arr, $template);
        
        $msg = "An OTP has been sent to the mobile number.Please ask customer to send the OTP to {$sms_reply_to} in the format FLOW <OTP> Eg: If OTP is 123456, FLOW 123456";
        return $msg;
    }

    public function get_kyc_otp($data,$mob_num){
        $otp_serv = new SMSService();
        $confirm_code = $otp_serv->get_otp_code(['lead_id' => $data['lead_id'],'entity_verify_col' => $data['entity_verify_col'], 'entity_update_value' => $data['entity_update_value'], 'entity' => 'lead', 'entity_id' => $data['lead_id'],
                                        'otp_type' => 'verify_lead_mobile','mobile_num' => $mob_num,'country_code'=>session('country_code')]);
        return $confirm_code;        
    }

    public function list_data_prvdrs($data){
        $data_prvdr_repo = new DataProviderRepositorySQL();
        $data_prvdrs = $data_prvdr_repo->list($data);

        foreach($data_prvdrs as $data_prvdr){
            $data_prvdr->dp_code_path = config('app.data_prvdr_logo')[$data_prvdr->data_prvdr_code];
        }
        return $data_prvdrs;
    }
    
    public function get_mobile_num_field($borrower){
        $mobile_num_fields = ['mobile_num' => null, 'alt_biz_mobile_num_1' => null, 'alt_biz_mobile_num_2' => null,'addl_mobile_num_0' => null, 'addl_mobile_num_1' => null, 'addl_mobile_num_2' => null, 'addl_mobile_num_3' => null, 'addl_mobile_num_4' => null ];
        $key = array_intersect_key($borrower, $mobile_num_fields);
        $mobile_field = array_keys($key)[0];

        return $mobile_field;

    }
    public function verify_mobile_num_field($data){

        $person_repo = new PersonRepositorySQL();
        $mobile_field = $this->get_mobile_num_field($data);
        $check_field = 'verified_' . $mobile_field;
        $person =  $person_repo->get_record_by_many(['id', $mobile_field], [$data['person_id'], $data[$mobile_field]], [$check_field]);

        return ['is_verified' => $person->$check_field];
    }

    public function verify_kyc_mobile_num_field($data){
        $lead_repo = new LeadRepositorySQL();
        $mobile_field = $this->get_mobile_num_field($data);
        $check_field = 'verified_' . $mobile_field;

        $addl_num = false;

        if(Str::contains($check_field, 'verified_addl_mobile_num') ){
            
            $index = filter_var($check_field, FILTER_SANITIZE_NUMBER_INT);
            // $json_condition = ['addl_num' => [$index => ["mobile_num" => ['value' => $data[$mobile_field]] ]]];

            $json_condition = ['addl_num' => ["mobile_num" => ['value' => $data[$mobile_field]] ]];

            $addl_num = true;

        }else{                            
            $json_condition = ['biz_identity' => [$mobile_field => ['value' => $data[$mobile_field] ]]];
        }

        $lead_data = $lead_repo->get_json_by('cust_reg_json', $json_condition, ['cust_reg_json'], $addl_sql = "and profile_status != 'closed'");

        $cust_reg_arr = json_decode($lead_data->cust_reg_json, true);

        if($addl_num){
            $resp_arr = ['is_verified' => $cust_reg_arr['addl_num'][$index][$check_field]];
        }else{
            $resp_arr = ['is_verified' => $cust_reg_arr['biz_identity'][$check_field]];
        }

        return $resp_arr;
    }

    private function mount_kyc_files(&$cust_reg_arr, $lead_id){
        if(array_key_exists('biz_identity', $cust_reg_arr)){
            $biz_identity = &$cust_reg_arr['biz_identity'];
            $biz_identity['photo_biz_lic_full_path']['value'] = move_entity_file("borrowers", "leads", $biz_identity, $lead_id, 'photo_biz_lic');
            $biz_identity['photo_shop_full_path']['value'] = move_entity_file("borrowers", "leads", $biz_identity, $lead_id, 'photo_shop');  
        }
        
        if(array_key_exists('references', $cust_reg_arr)){
            foreach($cust_reg_arr['references'] as $key=>&$value){
                $keys = ['guarantor1_doc', 'guarantor2_doc','lc_doc'];
                $path = $keys[$key]."_full_path";
                $value[$path]['value'] = move_entity_file("borrowers", "leads", $value, $lead_id, $keys[$key]);
            }
        }


        if(array_key_exists('photo_new_acc_letter', $cust_reg_arr['account'])){
            $account = &$cust_reg_arr['account'];
            $account['photo_new_acc_letter_full_path']['value']  = move_entity_file("borrowers", "leads", $account, $lead_id, 'photo_new_acc_letter');
        }

        if(array_key_exists('third_party_owner', $cust_reg_arr)){
            $third_party_owner = &$cust_reg_arr['third_party_owner'];
            $third_party_owner['photo_consent_letter_full_path']['value']  = move_entity_file("borrowers", "leads", $third_party_owner,$lead_id, 'photo_consent_letter');
            $third_party_owner['photo_national_id_full_path']['value'] = move_entity_file("persons", "leads", $third_party_owner,$lead_id, 'photo_national_id');
            $third_party_owner['photo_national_id_back_full_path']['value'] = move_entity_file("persons", "leads", $third_party_owner,$lead_id, 'photo_national_id_back');
        }


        if($cust_reg_arr['same_as_owner_person'] == false){
            foreach($cust_reg_arr['contact_persons'] as $key => &$person){
                $person['photo_national_id_full_path']['value'] = move_entity_file("persons","leads", $person, $lead_id, 'photo_national_id');
                $person['photo_national_id_back_full_path']['value'] = move_entity_file("persons","leads", $person, $lead_id, 'photo_national_id_back');
                $person['photo_selfie_full_path']['value'] = move_entity_file("persons","leads", $person, $lead_id, 'photo_selfie');
                $person['photo_pps_full_path']['value'] = move_entity_file("persons","leads", $person, $lead_id, 'photo_pps');
            }
        }

        if(array_key_exists('owner_person', $cust_reg_arr)){
            $person = &$cust_reg_arr['owner_person'];
            $person['photo_national_id_full_path']['value'] = move_entity_file("persons", "leads", $person,$lead_id, 'photo_national_id');
            $person['photo_national_id_back_full_path']['value'] = move_entity_file("persons", "leads", $person, $lead_id, 'photo_national_id_back');
            $person['photo_selfie_full_path']['value'] = move_entity_file("persons", "leads",$person, $lead_id, 'photo_selfie');
            $person['photo_pps_full_path']['value'] = move_entity_file("persons", "leads",$person, $lead_id, 'photo_pps');
        }
           
       
    }

    private function check_mobile_num_ver_keys($cust_reg_arr, $type, $lead_status){

        if($type == 'reg_mob_num'){
            foreach ($cust_reg_arr['biz_identity'] as $key => $value){
                if(Str::contains($key, 'verified')){
                    $is_otp_verified = $cust_reg_arr['biz_identity'][$key];
                    if(!$is_otp_verified){
                        $lead_status = Consts::PENDING_MOBILE_NUMBER_VER;
                        unset($cust_reg_arr['biz_identity'][$key]);
                    }
                }
            }
        }else if($type == 'addl_mob_num'){
            foreach ($cust_reg_arr['addl_num'] as $key => $value){
                foreach($value as $inner_key => $inner_value){
                    if(Str::contains($inner_key, 'verified')){
                        $is_otp_verified = $cust_reg_arr['addl_num'][$key][$inner_key];
                        if(!$is_otp_verified){
                            $lead_status = Consts::PENDING_MOBILE_NUMBER_VER;
                            unset($cust_reg_arr['addl_num'][$key][$inner_key]);
                        }
                    }
                }

            }
        }

        return $lead_status;
        
    }

    public function check_otp_verification(&$cust_reg_arr, $lead_status){

        if($cust_reg_arr['register_num_verify'] == 'otp'){
           
            $mobile_num_arr = ['verified_mobile_num'];
            $key_arr = array_diff($mobile_num_arr, array_keys($cust_reg_arr['biz_identity']));
            if(sizeof($key_arr) > 0){
                thrw("You must send one time otp from register mobile number");
            }
        } 
        
        if($cust_reg_arr['alternate_num_verify'] == 'otp'){
            $mobile_num_arr = ['verified_alt_biz_mobile_num_1', 'verified_alt_biz_mobile_num_2'];
            $key_arr = array_diff($mobile_num_arr, array_keys($cust_reg_arr['biz_identity']));
            if(sizeof($key_arr) > 0){
                thrw("You must send one time otp from alternate mobile number");
            }
        }

        $lead_status = $this->check_mobile_num_ver_keys($cust_reg_arr, 'reg_mob_num', $lead_status);
        if($lead_status != Consts::PENDING_MOBILE_NUMBER_VER ){
            $lead_status = $this->check_mobile_num_ver_keys($cust_reg_arr, 'addl_mob_num', $lead_status);
        }

        return $lead_status;

    }
   

    public function audit_name($ussd_holder_name_code, $acc_number, $acc_prvdr_code){
        $ussd_code = $code = compile_sms_old($ussd_holder_name_code, ['recipient' => $acc_number], false);
        $mode = config('app.audit_kyc_line')[$acc_prvdr_code];
        if ($mode == "ussd"){
            $agent_id = (new AccountRepositorySQL())->get_account_by(["acc_prvdr_code", "acc_purpose"], [$acc_prvdr_code, $mode], ["acc_number"])->acc_number;
            $ussd_code = substr($ussd_code, 1, -1);
            $loan_serv = new LoanService();
            $queue = $loan_serv->get_queue($agent_id, "mob", $acc_prvdr_code, true);
            $audit_data = ['ussd_code' => $ussd_code, 'acc_prvdr_code' => $acc_prvdr_code, 'acc_number' => $acc_number, 'country_code' => session('country_code'), 'agent_id' => $agent_id];
        }
        if($queue){
            AuditKYC::dispatch($audit_data)->onQueue($queue);
        }
        if(session('channel') == 'web_app'){
            // 12 * 5 = 60 secs
            $wait_secs = 6;
            for($i = 0; $i < $wait_secs; ++$i) {
                // $lead_info = DB::selectOne("select id, cust_reg_json from leads where account_num = ? order by id desc limit 1", [$acc_number]);
                $lead_repo = new LeadRepositorySQL;
                $json_condition = ['account' => ['acc_number' => ['value' => $acc_number]]];
                $lead_info = $lead_repo->get_json_by('cust_reg_json', $json_condition, ['cust_reg_json'], " AND profile_status = 'open'");

                if ($lead_info) {
                    $cust_reg_arr = json_decode($lead_info->cust_reg_json, true);
                    if(isset($cust_reg_arr['account']['holder_name'])){
                        return true;
                    }
                }
                sleep(5);
            }
            return false;
        }
    }

    public function delete_agreement($data){
        $lead_repo = new LeadRepositorySQL;
        $cust_reg_json = $lead_repo->get_cust_reg_arr($data['lead_id']);
        $lead = $lead_repo->find($data['lead_id'],['type','cust_id']);
        $cust_id = null;
        if($lead->type == 're_kyc'){
            $cust_id = $lead->cust_id;
        } 
        $resp = null;
        if(array_key_exists('agreements',$cust_reg_json)){
            foreach($cust_reg_json['agreements'] as $key => $value){
                if($data['aggr_doc_id'] == $value['aggr_doc_id']){
                    unset($cust_reg_json['agreements'][$key]);
                    
                    $master_agreements =  $this->get_master_agreements($data['acc_purpose'], $cust_id);
                    foreach($master_agreements as $agrmnt){
                        if($data['master_aggr_doc_id'] == $agrmnt->aggr_doc_id ){
                            $cust_reg_json['agreements'][$key] = $agrmnt;
                        }
                    }
                    $resp = $lead_repo->update_cust_reg_json($cust_reg_json,$data['lead_id']);
                }
            }
            DB::delete("delete from cust_agreements where aggr_doc_id = ? ", [$data['aggr_doc_id']]);
        }
        return $resp;
	}

    public function prdctList(){
        $products = array();
        $prdctrepo = new LoanProductRepositorySQL();
        $prdct = $prdctrepo->get_records_by_many(['loan_purpose','cs_model_code','status'],['terminal_financing','tf_products','enabled'],['product_code','max_loan_amount','product_json']);
        foreach ($prdct as $key=>$value) {
            $prdctjson = json_decode($prdct[$key]->product_json);
            $i = 0;
            foreach ($prdctjson as $prdctsub){

                $products[] = ['data_code' => $value->product_code.'/'.$i, 'data_value' => round($value->max_loan_amount).' UGX | '.$prdctsub->duration.' '.$prdctsub->duration_type. " @ ".$prdctsub->daily_deductions." UGX "." / day"];
                $i++;
            }
        }
        return $products;
    }

    public function add_location($data){
        $data['location'] = Str::lower($data['location']);
        $conv_location = Str::snake($data['location']);
        $master_data_repo = new MasterDataRepositorySQL();
        $master_data =  $master_data_repo->get_record_by_many(['data_key','data_code'], ['location', $conv_location],['id']);
        if($master_data){
            thrw("Location {$data['location']} already exists.");
        }else{
            $master_data['data_key'] = 'location';
            $master_data['data_code'] = $conv_location;
            $master_data['data_type'] = 'common';
            $master_data['data_value'] = dd_value($data['location']);
            $master_data['country_code'] = session('country_code');
            $resp = $master_data_repo->create($master_data);
            return $resp;
        }    
    }

   
    public function get_cust_by_criteria($criteria){

        $data=[];
        if($criteria == 'visit_suggestion'){
            $data['cust_needs_visit'] = 'true';
            $result = $this->borrower_search($data);
        }
        else if($criteria == 'active_cust_wo_fa'){
            $data = ["active_cust" => "true",
                    "not_have_ongoing_loan" => "true",
                    "not_have_overdue_loan"=> "true",
                    "not_have_pending_loan_appl"=> "true",
                    "profile_status" => "open",
                    "status" => "enabled"];
            $result = $this->borrower_search($data);
        }else if($criteria == 'pre_approval'){
            $data['pre_approved_cust'] = 'true';
            $result = $this->borrower_search($data);
        }
        return $result;
    }
      
    public function get_nearby_cust($data){
        $flow_rel_mgr_id = session('user_person_id');
        $borr_repo = new BorrowerRepositorySQL;
        $lead_repo = new LeadRepositorySQL;
        $borr_data = $borr_repo->get_records_by_many(['flow_rel_mgr_id','profile_status'],[$flow_rel_mgr_id,'open'],['gps','biz_name','cust_id','ongoing_loan_doc_id','is_og_loan_overdue','last_visit_date','next_visit_date']);
        $leads = DB::select("select id,cust_reg_json,status from leads where flow_rel_mgr_id = ? and profile_status = ?",[$flow_rel_mgr_id,'open']);
        $lead_data = [];
        foreach ($leads as $lead){
            if($lead && $lead->cust_reg_json){
                $cust_reg_arr = json_decode($lead->cust_reg_json,true);
                $lead_data[] = ['status' => $lead->status, "gps" => $cust_reg_arr['biz_identity']['gps']['value'], 'biz_name' => $cust_reg_arr['biz_info']['biz_name']['value'],
                                    'lead_id' => $lead->id  ];
                
            }
        }
        return array_merge($borr_data,$lead_data);


    }

    private function get_consent_pdf_file_paths($lead_id,$file_of){
        $pdf_rel_path = get_file_rel_path($lead_id, $file_of,null);
        $pdf_abs_path =  flow_file_path().$pdf_rel_path;
        if(!File::exists($pdf_abs_path)){
            create_dir($pdf_abs_path);
        }

        $file_name = $lead_id.'_'.time().".pdf";
        $pdf_abs_file_path = flow_file_path().separate([$pdf_rel_path, $file_name]);
        $full_path =  separate(["files", $pdf_rel_path]).$file_name;

        return [$full_path,$pdf_abs_file_path];

    }

    private function get_lead_data($lead_id){
        $lead_repo = new LeadRepositorySQL;
        $lead =  $lead_repo->find($lead_id,['cust_eval_json','consent_json','lead_json','flow_rel_mgr_id']);
        $lead->consent_json = json_decode($lead->consent_json,true);
        $lead->lead_json = json_decode($lead->lead_json,true);
        $lead->cust_eval_json = json_decode($lead->cust_eval_json,true);

        return $lead;
            
    }
    public function view_data_consent($data){

        try{

            $lead_repo = new LeadRepositorySQL;
            $person_repo = new PersonRepositorySQL;

            $lead = $this->get_lead_data($data['lead_id']);
            $data_consent_arr = $lead->consent_json;
           
            if($data_consent_arr){
                $full_path = $data_consent_arr['unsigned_consent_path'];
            }else{

                [$pdf_abs_file_path,$full_path] = $this->save_consent_pdf($lead,false);

                if(File::exists($pdf_abs_file_path)){
                    $data_consent_arr['unsigned_consent_path'] =  $full_path;
                    $data_consent_json = json_encode($data_consent_arr);
                    $lead_repo->update_model(['id' => $data['lead_id'],'consent_json' => $data_consent_json]);

                }else{
                    thrw("Unable to view {$full_path}");
                }
            }


        }catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return  $full_path;
    }

    private function save_consent_pdf($lead,$signed_consent = false){
        
        $lead_json = $lead->lead_json;
        $country_code = session('country_code');
        $person_repo = new PersonRepositorySQL;
        $lead_repo = new LeadRepositorySQL;
        $folder_name = $signed_consent ? 'signed_consent' : 'unsigned_consent';
        [$full_path,$pdf_abs_file_path] = $this->get_consent_pdf_file_paths($lead->id,$folder_name);
        $agrmt_file = "agreements.{$country_code}.data_consent";
        if($signed_consent){
            $view_data = ['cust_sign_file_path' => $lead->cust_sign_file_data];
        }
        $view_data['lead']['biz_name'] =  $lead_json['biz_name'];
        $view_data['lead']['cust_name'] = full_name((object)$lead_json);
        $view_data['lead']['cust_mobile_num'] =  $lead_json['mobile_num'];
        $view_data['lead']['curr_date'] = date_ui();
        PDF::loadView($agrmt_file,  $view_data)->setPaper('A4','portrait')->save($pdf_abs_file_path);
        return [$pdf_abs_file_path,$full_path];
    }

    public function sign_data_consent($data){

        try{

        $file_serv = new FileService;
        $person_repo = new PersonRepositorySQL;
        $lead_repo = new LeadRepositorySQL;
        $data['cust_sign_req']['lead_id'] = $data['lead_id'];
        $cust_sign_file_det = $file_serv->create_file_from_data_url($data['cust_sign_req']);        
        $lead = $this->get_lead_data($data['lead_id']);
        $lead->cust_sign_file_data = $data['cust_sign_req']['file_data'];
        [$pdf_abs_file_path,$full_path] = $this->save_consent_pdf($lead,true);
       
        $result = null;
        
        if(File::exists($pdf_abs_file_path)){
            $data_consent_arr = $lead->consent_json;
            $lead_json = $lead->lead_json;
            $eval_json = $lead->cust_eval_json;
            $data_consent_arr['signed_consent_path'] =  $full_path;
            $data_consent_json = json_encode($data_consent_arr);
            
            File::delete(flow_storage_path($data_consent_arr['unsigned_consent_path']));

            if (config('app.allow_cust_stmt_upload')) {
                $file_json = $this->get_file_upload_tmplt($eval_json['acc_prvdr_code']);
                $run_id = (new CustCSFValuesRepositorySQL())->get_run_id($eval_json['acc_number'], $eval_json['acc_prvdr_code']);

                if( $file_json && !$run_id ){
                    $update_arr = [ "status" => Consts::PENDING_DATA_UPLOAD, 'score_status' =>  Consts::SC_PENDING_DATA_UPLOAD, 'file_json' => $file_json ];
                }else{
                    $update_arr = [ "status" => Consts::PENDING_KYC, "run_id" => $run_id ];
                }
            }
            else {
                $update_arr = [ "status" => Consts::PENDING_KYC];
            }
            
            $lead_repo->update_model($update_arr + ['id' => $data['lead_id'],'consent_json' => $data_consent_json, 'consent_signed_date' => datetime_db()]);
            
            $result['consent_signed_date'] = datetime_db();
        }

        }catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return  $result;
   
    }

    public function remove_pre_approval($data){
		try{

			DB::beginTransaction(); 

			$pre_appr_repo = new PreApproval;
			$borr_repo = new BorrowerRepositorySQL();
			$result = $pre_appr_repo->update_model_by_code(['cust_id' => $data['cust_id'],'status' => 'disabled']);
			$borr_repo->update_model_by_code(['cust_id' =>$data['cust_id'], 'pre_appr_count' => null,'pre_appr_exp_date' => null]);
			DB::commit();
			
			return $result;

		}
		catch (Exception $e) {
			DB::rollback();
			throw new FlowCustomException($e->getMessage());
		}
		
	}
    
    public function list_fa_upgrade_requests($data){
        $person_id = session('user_person_id');
        $json_condition = ['person_id' => $person_id];
        $upgrade_reqs = (new FAUpgradeRequest)->get_jsons_by('approval_json', $json_condition, ['*'], $addl_sql = "and status = 'requested'");
        
        $this->get_cust_photo_and_location($upgrade_reqs);
       
        foreach ($upgrade_reqs as $upgrade_req){

            $upgrade_req->last_loan = (new BorrowerRepositorySQL)->get_last_loan($upgrade_req->cust_id, ['loan_principal','disbursal_date']);
           
            $accounts = (new AccountRepositorySQL)->get_account_by(['cust_id', 'status', 'acc_purpose'], [$upgrade_req->cust_id, 'enabled', 'float_advance'], ['acc_prvdr_code', 'acc_number']);
            if($accounts){
                $upgrade_req->acc_number = $accounts->acc_number;
                $upgrade_req->acc_prvdr_code = $accounts->acc_prvdr_code;
                $ap_logo_arr = config('app.acc_prvdr_logo')[session('country_code')];
                $upgrade_req->ap_code_path = $ap_logo_arr[$upgrade_req->acc_prvdr_code];
            }
            
            $upgrade_req->available_amounts = json_decode($upgrade_req->available_amounts);
            $upgrade_req->approval_json = json_decode($upgrade_req->approval_json);


           
        }

        return $upgrade_reqs;
    }

    private function close_exisitng_pre_appr_and_upgrade($cust_id){
       
        $upgrade_req = new FAUpgradeRequest();
        $pre_approval = (new PreApproval)->get_record_by_many(['cust_id', 'status'], [$cust_id, 'enabled'], ['id']);

        if($pre_approval){
            $data['cust_id'] = $cust_id;
            $this->remove_pre_approval($data);
        }
    
        $approved_reqs = $upgrade_req->get_record_by_many(['cust_id', 'status' ], [$cust_id, 'approved'], ['id']);

        if($approved_reqs){
            $upgrade_req->update_model(['id' => $approved_reqs->id, 'status' => 'closed']);
        }
    }

    public function fa_upgrade_approval($data){

        try{

			DB::beginTransaction(); 

            $upgrade_req = new FAUpgradeRequest();

            $person_id = session('user_person_id');
            $update_arr = ['id'=> $data['id']];

            $upgrade_reqs = $upgrade_req->find($data['id'], ['approval_json', 'cust_id']);

            if($data['action'] == 'approve'){

                $approval_json_arr = json_decode($upgrade_reqs->approval_json, true);

                $appr_count = 0;
                foreach ($approval_json_arr as &$approval_json){
                    if($person_id == $approval_json['person_id']){
                        if($approval_json['approved']){
                            thrw("You have already approved this FA upgrade request.");
                        }else{
                            $approval_json['approved'] = true;
                            $approval_json['amount'] = $data['amount'];
                        }
                    }else{
                        if(isset($approval_json['amount']) && $approval_json['approved'] == true){
                            if($approval_json['amount'] > $data['amount'] ){
                                $update_arr['upgrade_amount'] = $data['amount'];
                            }else{
                                $update_arr['upgrade_amount'] = $approval_json['amount'];

                            }
                        }
                    }

                    if($approval_json['approved']){
                        $appr_count = $appr_count+1;
                    }
                }
                $update_arr['approval_json'] = json_encode($approval_json_arr);     
                
                if(sizeof($approval_json_arr) == $appr_count){
                    $update_arr['status'] = 'approved'; 
                    $this->close_exisitng_pre_appr_and_upgrade($upgrade_reqs->cust_id);
                }
                $status = "approved";

            }else if($data['action'] == 'reject'){
                $update_arr['status'] = $status = 'rejected'; 
                (new BorrowerRepositorySQL)->update_model_by_code(['cust_id' =>$upgrade_reqs->cust_id, 'fa_upgrade_id' => null]);

            }
            $resp['is_updated'] = $upgrade_req->update_model($update_arr);

            $resp['status'] = $status;
            
            DB::commit();

            return $resp;

    }
    catch (Exception $e) {
        DB::rollback();
        throw new FlowCustomException($e->getMessage());
    }
    
    }

    
    public function update_third_party_owner($data){

        $lead_repo = new LeadRepositorySQL;
        $cust_reg_arr = $lead_repo->get_cust_reg_arr($data['lead_id']);

        if($data['is_rented_line']){
            $cust_reg_arr['third_party_owner'] = Consts::THIRD_PARTY_OWNER_DETAILS;
            $cust_reg_arr['is_rented_line']  = true;
        }else{
            unset($cust_reg_arr['third_party_owner']);
            $cust_reg_arr['is_rented_line']  = false;
        }

        $result = $lead_repo->update_cust_reg_json($cust_reg_arr,$data['lead_id']);

        return $result;
    
    }

    public function set_updated_cust_reg_arr(&$cust_reg_arr){
        log::warning("cust_reg_details_in");
        log::warning($cust_reg_arr);
       
        $lead_repo = new LeadRepositorySQL;

        if($cust_reg_arr['is_rented_line']){
            if(!array_key_exists('third_party_owner', $cust_reg_arr)){
                $cust_reg_arr['third_party_owner'] = Consts::THIRD_PARTY_OWNER_DETAILS;
            } 
        }       
        else{
            unset($cust_reg_arr['third_party_owner']);
        }

        if($cust_reg_arr['account']['acc_prvdr_code'] && $cust_reg_arr['account']['acc_prvdr_code']['value']){
            if(!array_key_exists('branch', $cust_reg_arr['account'])){
                $this->set_branch_arr($cust_reg_arr['account']['acc_prvdr_code']['value'], $cust_reg_arr);
            }
            if(!array_key_exists('alt_acc_num', $cust_reg_arr['account'])){
                $this->set_alt_acc_num_arr($cust_reg_arr['account']['acc_prvdr_code']['value'], $cust_reg_arr);
            }
        }

    }

    public function rm_cur_location($data){

        $cur_datetime = Carbon::now();
        $gps = $data['gps'];
        $state = $data['state'];
        $rm_id = session('user_person_id');
        $cur_date = $cur_datetime->copy()->format('Y-m-d');
        $cur_time = $cur_datetime->copy()->format('H:i:s');
        
        $addl_sql_condition = "order by id desc limit 1";
        
        $rm_punch_time = (new RMPunchTime) -> get_record_by_many(['rel_mgr_id','date'], [$rm_id,$cur_date], ['punch_out_time','punch_in_time', 'date'],$condition = "and", $addl_sql_condition);
        
        if($rm_punch_time -> punch_in_time != null && $rm_punch_time -> punch_out_time == null){
        
            $rm_locations = (new RMLocation)->get_record_by_many(['rel_mgr_id', 'date'],[$rm_id, $cur_date],['id','date', 'locations']);

            $data_arr = ['l' => $gps, 's'=> $state ];
            
            if(isset($data['cust_id'])){
                $data_arr['c_id'] = $data['cust_id'];
            } 
            if(isset($data['visit_id'])){
                $data_arr['v_id'] = $data['visit_id'];
            }
       
            if(isset($rm_locations) && $rm_locations->date ){
                $location_arr = $rm_locations->locations;
                
                $is_update = true;

                $data_arr['t'] = $cur_time;

                foreach ($location_arr as $location){
                    if( (isset($location->s) && $location->s== 'punch_out') || (isset($location->l) && $location->l  == $gps )|| (isset($location->v_id) && 
                            isset($data_arr['v_id']) && $location->v_id ==  $data_arr['v_id'] && $location->s == $state) ){
                        $is_update = false;
                    }
                }

                $location_arr[] = (object)$data_arr;

                
                if($is_update){
                    (new RMLocation)->update_model(['id' => $rm_locations->id, 'locations' => $location_arr ]);
                }

            }else{
                
                $data_arr['t'] = $rm_punch_time -> punch_in_time;


                $rm_location_data['country_code'] = session('country_code');
                $rm_location_data['rel_mgr_id'] = $rm_id;
                $rm_location_data['date'] = $cur_date;
                $rm_location_data['locations'][] = $data_arr;
                
                (new RMLocation)->insert_model($rm_location_data);
            }
            
        }else if ($rm_punch_time -> punch_out_time != null){

            $resp['tracking_status'] = Consts::STOP_TRACKING_STATUS ;
            return $resp;
        }
    }

    public function punch_in(){

        $cur_datetime = Carbon::now();
        $cur_time = $cur_datetime->copy()->format('H:i:s');
        $cur_date = $cur_datetime->copy()->format('Y-m-d');

        $punch_in['country_code'] = session('country_code');
        $punch_in['rel_mgr_id'] = session('user_person_id');
        
        $punch_in['punch_in_time'] = $cur_time;
        $punch_in['date'] = $cur_date;

        (new RMPunchTime)->insert_model($punch_in);
        $resp = "Punched in successfully";
    
    return $resp;  
    }

    public function punch_out($data){

        try{
            DB::beginTransaction();
            $rm_act = new RMActivityLogs;
            $rm_punch_time = new RMPunchTime;
            $cur_datetime = Carbon::now();
            $cur_time = $cur_datetime->copy()->format('H:i:s');
            $cur_date = $cur_datetime->copy()->format('Y-m-d');

            if($data['punch_out'] == 'manual'){

                $addl_sql_condition = "order by id desc limit 1";

                $rm_act_data = $rm_punch_time->get_record_by_many(['rel_mgr_id', 'date'], 
                                                                    [session('user_person_id'), $cur_date], 
                                                                    ['punch_out_time'],
                                                                    $condition = "and",
                                                                    $addl_sql_condition);
                if($rm_act_data) {
                    if($rm_act_data-> punch_out_time == null){

                        $rm_punch_time->update_model(['punch_out_time' => $cur_time,
                                                        'id' => $rm_act_data->id], 'id' );
                
                        $resp = "You have been punched out successfully";
                    }else{
                        $resp = "You have been punched out already";
                    }
                }else{
                    $resp = "There is no punch in record for today";
                }

            }
            if($data['punch_out'] == 'auto'){
                $rm_act_datas = $rm_punch_time->get_records_by('date', $cur_date, ['punch_out_time','rel_mgr_id']);
                foreach($rm_act_datas as $rm_act_data){
                    if($rm_act_data->punch_out_time == null){
                    
                        $rm_last_activity = $rm_act->get_record_by_many(['rel_mgr_id'],[$rm_act_data->rel_mgr_id], ['activities'], "and", 'order by id desc limit 1')->activities;
                        
                        $rm_locations = (new RMLocation)->get_record_by_many(['rel_mgr_id', 'date'],[$rm_act_data->rel_mgr_id, $cur_date],['locations', 'id']);
        
                        end($rm_last_activity);
                        
                        $last_activity_time = key($rm_last_activity);
                        
                        $rm_punch_time->update_model(['punch_out_time' => $last_activity_time, 'id' => $rm_act_data->id ]);
                        
                        $is_update = false;
                        
                        foreach ($rm_locations->locations as $location){
                            if($location->t >= "$last_activity_time"){
                                $location->s = 'punch_out';
                                $is_update = true;
                            }
                        }

                        if($is_update){
                            (new RMLocation)->update_model(['id' => $rm_locations->id, 'locations' => $rm_locations->locations ]);
                        }
                    }
                }
                $resp = "Punched out Successfully";
            }
            DB::commit();
            return $resp;

        }catch (Exception $e) {
            DB::rollback();
            throw new FlowCustomException($e->getMessage());
        } 
    }

    public function update_addl_num_field($data){

        $action =  $data['action'] ;

        $cust_reg_arr = (new LeadRepositorySQL)->get_cust_reg_arr($data['lead_id']);

        if($action == 'add'){
            $cust_reg_arr['addl_num'][] =  Consts::ADDL_NUM_TMPLT;

        }else if ($action == 'remove'){
            unset($cust_reg_arr['addl_num'][$data['index']]); 
            $cust_reg_arr['addl_num'] = array_values($cust_reg_arr['addl_num']);
        }

        $resp['is_update'] = (new LeadRepositorySQL)->update_cust_reg_json($cust_reg_arr, $data['lead_id']);
        $resp['action'] = $action;
        return $resp;

}

    public function list_task_counts($data){
        
        $person_id = $data['visitor_id'] =   session('user_person_id');

        $counts_arr = [];

        $tasks = DB::select("select count(*) as count, task_type from tasks where country_code = 'UGA' and json_extract(approval_json,'$[0].person_id') = $person_id and json_extract(approval_json,'$[0].approved') = false and status = 'requested' group by task_type");

        foreach ($tasks as $task){
            $counts_arr[$task->task_type] = $task->count;
        }

        $fa_upgrade_req_count = DB::selectOne("select count(*) as fa_upgrade_requests from fa_upgrade_requests where country_code = 'UGA' and json_extract(approval_json,'$[0].person_id') = $person_id and json_extract(approval_json,'$[0].approved') = false and status = 'requested'");

    
        if(isset($fa_upgrade_req_count->fa_upgrade_requests) && $fa_upgrade_req_count->fa_upgrade_requests > 0  ){
            $counts_arr['fa_upgrade_req'] = $fa_upgrade_req_count->fa_upgrade_requests;
        }

        [$loan_appl, $tot_fas_pending] = $this->get_loan_appl($data);

        if($tot_fas_pending){
            $counts_arr['fa_appr'] = $tot_fas_pending;
        }

        $counts_arr['total_counts'] = array_sum(array_values($counts_arr));

        return $counts_arr;
        
    }


    public function get_rm_routes($data){
        $rm_locations = (new RMLocation)->get_record_by_many(['rel_mgr_id', 'date'],[session('user_person_id'), $data['date']],['locations']);
        $count = 0;
        foreach ($rm_locations->locations as $key =>$value){
            if($value->s == 'punch_out'){
                $count++;
                if($count != 1){
                    unset($rm_locations->locations[$key]);
                }
            }
        }
        $rm_locations->locations = array_values($rm_locations->locations);
        $field_names = ['visitor_id'];
        $field_values = [session('user_person_id')];
        $field_arr = ['cust_name', 'biz_name', 'cust_id', 'lead_id', 'visit_start_time', 'visit_end_time', 'visit_purpose', 'force_checkout','force_checkout_reason', 'force_checkin', 'force_checkin_reason', 'checkin_distance', 'checkout_distance'];
        $addl_sql = "and sch_status in ('checked_in', 'checked_out') and date(visit_start_time) = '{$data['date']}'";
        $field_repo = new FieldVisitRepositorySQL();
        $visits = $field_repo->get_records_by_many($field_names, $field_values, $field_arr, "and", $addl_sql); 
       

        foreach ($visits as $visit){
            $visit->v_id = $visit->id;
            $visit->visit_purpose = json_decode($visit->visit_purpose);
            unset($visit->id);
        }

        foreach ($rm_locations->locations as $key =>$value){
            foreach ($visits as $visit){
                if(isset($value->v_id) && isset($visit->v_id) &&  $value->v_id == $visit->v_id){
                    $visit->l  = $value->l;
                }
            }
        }

        
        return ["locations" => $rm_locations->locations, 'cust_info' => $visits];

    }

    public function submit_kyc_for_audit($lead, $cust_reg_arr){

        if(config('app.owner_name_verify') == 'ussd'){

            $audit_name_mode = config('app.audit_kyc_line')[$cust_reg_arr['account']['acc_prvdr_code']['value']];

            $ussd_codes = (new AccProviderRepositorySQL())->get_record_by('acc_prvdr_code', $cust_reg_arr['account']['acc_prvdr_code']['value'], ['mobile_cred_format'])->mobile_cred_format;
            if (isset($ussd_codes->ussd_holder_name_code) && $audit_name_mode == "ussd"){
                // if(isset($cust_reg_arr['account']['branch']['value'])){
                //     if (array_key_exists($cust_reg_arr['account']['branch']['value'], config('app.RMTN_district_accounts'))){
                //         $audit_kyc_line = config('app.RMTN_district_accounts')[$cust_reg_arr['account']['branch']['value']];
                //     }
                // }
                // else if(session('country_code') == 'UGA'){
                //     $audit_kyc_line = config('app.audit_kyc_line')[$cust_reg_arr['account']['acc_prvdr_code']['value']];
                // }
                if (!isset($cust_reg_arr['account']['holder_name'])){
                    $this->audit_name($ussd_codes->ussd_holder_name_code, $cust_reg_arr['account']['acc_number']['value'], $cust_reg_arr['account']['acc_prvdr_code']['value']);
                }
            }

            if ($audit_name_mode == "ussd"){
                $lead_status = (isset($ussd_codes->ussd_holder_name_code) && !isset($cust_reg_arr['account']['holder_name'])) ? Consts::RETRIEVE_HOLDER_NAME : Consts::PENDING_AUDIT;
            }
            // else if (session('country_code') == "RWA" && isset($ussd_codes->ussd_holder_name_code)){
            //     $lead_status = (array_key_exists($cust_reg_arr['account']['branch']['value'], config('app.RMTN_district_accounts')) && !isset($cust_reg_arr['account']['holder_name'])) ? Consts::RETRIEVE_HOLDER_NAME : Consts::PENDING_AUDIT;
            // }
        }
        
    
        if($lead->type == 're_kyc' && $lead->kyc_reason == 'new_account' && !isset($borrower['account']['photo_new_acc_letter'])){
            thrw('Please upload the new account letter photo');
        }

        $lead_status = Consts::PENDING_AUDIT;
       

        
        $whatsapp = new WhatsappWebService();
        $rm_name = DB::selectOne("select first_name, last_name from persons where id = ?",[$lead->flow_rel_mgr_id]);
        $notification = "KYC details for {$cust_reg_arr['owner_person']['first_name']['value']} {$cust_reg_arr['owner_person']['last_name']['value']} - *{$cust_reg_arr['account']['acc_number']['value']}* have been collected by the RM {$rm_name->first_name} {$rm_name->last_name} and is pending audit.";
        if ($lead->audited_by == NULL) {
            $whatsapp->send_message(["body" => $notification, "to" => config('app.whatsapp_group_codes')["auditor"][session('country_code')] , "isd_code"=> "", "session" => config('app.whatsapp_notification_number')]);
        } else {
            $person_repo = new PersonRepositorySQL();
            $auditor_info = $person_repo->find($lead->audited_by, ['whatsapp', 'country_code']);
            $isd_code = DB::selectOne("select isd_code from markets where country_code = '$auditor_info->country_code'")->isd_code;
            $whatsapp->send_message(["body" => $notification, "to" => $auditor_info->whatsapp , "isd_code"=> $isd_code, "session" => config('app.whatsapp_notification_number')]);
        }
    }

//     public function submit_kyc($data){
//         $lead_repo = new LeadRepositorySQL;
//         $cust_reg_arr = $data['cust_reg_json'];
//         $borr_serv = new CustomerRegService();
//         $borrower = $cust_reg_arr;
//         flatten_borrower($borrower);
//         $send_email = false;

//         $lead = $lead_repo->find($data['lead_id'], ['acc_purpose', 'type', 'kyc_reason', 'visit_ids', 'audited_by', 'first_name', 'last_name', 'flow_rel_mgr_id', 'remarks']);

//         // $cust_reg_arr = json_decode($lead->cust_reg_json, true);

// //        $lead->acc_purpose = 'terminal_financing,float_advance';
//         if(env('APP_ENV') == 'production' && !is_tf_acc($lead->acc_purpose)){
//             $borr_serv->get_csf_run_id($borrower['account']);
//         }
//         #run id doesn't exist if function throws the error

//         $this->mount_kyc_files($cust_reg_arr, $data['lead_id']);
     
//         $addl_fields = array();
//         if($cust_reg_arr['is_templ']){
//             $addl_fields += ['rm_kyc_start_date' => datetime_db()];
//             $cust_reg_arr['is_templ'] = false;
//         }
//         if($data['action'] == "draft"){
//             $lead_status = Consts::KYC_INPROGRESS;
//             $this->set_updated_cust_reg_arr($cust_reg_arr);
//             $resp['message'] = "Draft saved successfully";
//         }else if($data['action'] == "submit"){
//             $borr_serv = new CustomerRegService();
//             $borrower['country_code'] = session('country_code');
//             $borrower = array_merge($borrower,$borrower['biz_info']);
//             $borrower['cr_owner_person'] = $borrower['owner_person'];
// 	        $borrower['cr_account'] = $borrower['account'];

//             $lead_status = Consts::PENDING_AUDIT;

//             if(config('app.register_num_verify') != null && config('app.alternate_num_verify') != null && config('app.addl_num_verify') != null ){
//                 $lead_status = $this->check_otp_verification($cust_reg_arr, $lead_status);
//             }


            
//         //     $validation_keys = $borr_serv->get_validation_keys($borrower);
//         //     $check_validate = FlowValidator::validate($borrower, $validation_keys ,__FUNCTION__);
//         //     if(is_array($check_validate))
//         // {
//         //     return $this->respondValidationError($check_validate); 
//         // }



//             if($lead_status == Consts::PENDING_AUDIT ){
//                 $this->submit_kyc_for_audit($data, $lead, $cust_reg_arr);
//                 $resp['message'] = "Lead's KYC has been successfully submited for audit" ;
//                 $remarks = (new LeadService)->combine_remarks($lead->remarks, null, session('user_person_id'), Consts::LEAD_ACTIONS[Consts::LA_KYC_SUBMITTED]);
//             }else if ($lead_status == Consts::PENDING_MOBILE_NUMBER_VER){
//                 $send_email = true;
//                 $resp['message'] = "Lead's KYC has been successfully submited for mobile number verfication" ;
//                 $remarks = (new LeadService)->combine_remarks($lead->remarks, null, session('user_person_id'), Consts::LEAD_ACTIONS[Consts::LA_MOB_NUM_VERF]);
//             }

//             $addl_fields += ['rm_kyc_end_date' => datetime_db(), 'remarks' => $remarks, 'status' => $lead_status];
            
//         }

//         $visit_id_arr = $this->append_visit_id($data['lead_id'], $data['visit_id']);
//         $addl_fields += ['status' => $lead_status,'visit_ids' => $visit_id_arr ];
//         $resp['is_updated'] = $lead_repo->update_cust_reg_json($cust_reg_arr, $data['lead_id'], $addl_fields);
        
//         if($send_email){
//             $this->send_mobile_num_verfication_email($cust_reg_arr, $lead->flow_rel_mgr_id, $data['lead_id']);
//         }
        
//         return $resp;
//     }


    public function submit_kyc($data){
        $lead_repo = new LeadRepositorySQL;
        $cust_reg_arr = $data['cust_reg_json'];
        $borr_serv = new CustomerRegService();
        $borrower = $cust_reg_arr;
        flatten_borrower($borrower);

        $lead = $lead_repo->find($data['lead_id'], ['acc_purpose', 'type', 'kyc_reason', 'visit_ids', 'audited_by', 'first_name', 'last_name', 'flow_rel_mgr_id', 'remarks']);

        // $cust_reg_arr = json_decode($lead->cust_reg_json, true);

    //        $lead->acc_purpose = 'terminal_financing,float_advance';
        if(env('APP_ENV') == 'production' && !is_tf_acc($lead->acc_purpose)){
            $borr_serv->get_csf_run_id($borrower['account']);
        }
        #run id doesn't exist if function throws the error

        $this->mount_kyc_files($cust_reg_arr, $data['lead_id']);
    
        $addl_fields = array();
        if($cust_reg_arr['is_templ']){
            $addl_fields += ['rm_kyc_start_date' => datetime_db()];
            $cust_reg_arr['is_templ'] = false;
        }
        if($data['action'] == "draft"){
            $lead_status = Consts::KYC_INPROGRESS;
            $this->set_updated_cust_reg_arr($cust_reg_arr);
            $message = "Draft saved successfully";
        }else if($data['action'] == "submit"){
            $borr_serv = new CustomerRegService();
            $borrower['country_code'] = session('country_code');
            $borrower = array_merge($borrower,$borrower['biz_info']);
            $borrower['cr_owner_person'] = $borrower['owner_person'];
            $borrower['cr_account'] = $borrower['account'];

        //     $validation_keys = $borr_serv->get_validation_keys($borrower);
        //     $check_validate = FlowValidator::validate($borrower, $validation_keys ,__FUNCTION__);
        //     if(is_array($check_validate))
        // {
        //     return $this->respondValidationError($check_validate); 
        // }

            $audit_name_mode = config('app.audit_kyc_line')[$data['cust_reg_json']['account']['acc_prvdr_code']['value']];

            $ussd_codes = (new AccProviderRepositorySQL())->get_record_by('acc_prvdr_code', $data['cust_reg_json']['account']['acc_prvdr_code']['value'], ['mobile_cred_format'])->mobile_cred_format;
            if (isset($ussd_codes->ussd_holder_name_code) && $audit_name_mode == "ussd"){
                // if(isset($cust_reg_arr['account']['branch']['value'])){
                //     if (array_key_exists($cust_reg_arr['account']['branch']['value'], config('app.RMTN_district_accounts'))){
                //         $audit_kyc_line = config('app.RMTN_district_accounts')[$cust_reg_arr['account']['branch']['value']];
                //     }
                // }
                // else if(session('country_code') == 'UGA'){
                //     $audit_kyc_line = config('app.audit_kyc_line')[$cust_reg_arr['account']['acc_prvdr_code']['value']];
                // }
                if (!isset($cust_reg_arr['account']['holder_name'])){
                    $this->audit_name($ussd_codes->ussd_holder_name_code, $data['cust_reg_json']['account']['acc_number']['value'], $data['cust_reg_json']['account']['acc_prvdr_code']['value']);
                }
            }
            
        
            if($lead->type == 're_kyc' && $lead->kyc_reason == 'new_account' && !isset($borrower['account']['photo_new_acc_letter'])){
                thrw('Please upload the new account letter photo');
            }

            $lead_status = Consts::PENDING_AUDIT;
            if ($audit_name_mode == "ussd"){
                $lead_status = (isset($ussd_codes->ussd_holder_name_code) && !isset($cust_reg_arr['account']['holder_name'])) ? Consts::RETRIEVE_HOLDER_NAME : Consts::PENDING_AUDIT;
            }
            // else if (session('country_code') == "RWA" && isset($ussd_codes->ussd_holder_name_code)){
            //     $lead_status = (array_key_exists($cust_reg_arr['account']['branch']['value'], config('app.RMTN_district_accounts')) && !isset($cust_reg_arr['account']['holder_name'])) ? Consts::RETRIEVE_HOLDER_NAME : Consts::PENDING_AUDIT;
            // }

            $message = "Lead's KYC has been successfully submited for audit" ;
            $remarks = (new LeadService)->combine_remarks($lead->remarks, null, session('user_person_id'), Consts::LEAD_ACTIONS[Consts::LA_KYC_SUBMITTED]);
            $addl_fields += ['rm_kyc_end_date' => datetime_db(), 'remarks' => $remarks];
            $whatsapp = new WhatsappWebService();
            $rm_name = DB::selectOne("select first_name, last_name from persons where id = ?",[$lead->flow_rel_mgr_id]);
            $notification = "KYC details for {$cust_reg_arr['owner_person']['first_name']['value']} {$cust_reg_arr['owner_person']['last_name']['value']} - *{$cust_reg_arr['account']['acc_number']['value']}* have been collected by the RM {$rm_name->first_name} {$rm_name->last_name} and is pending audit.";
            if ($lead->audited_by == NULL) {
                $whatsapp->send_message(["body" => $notification, "to" => config('app.whatsapp_group_codes')["auditor"][session('country_code')] , "isd_code"=> "", "session" => config('app.whatsapp_notification_number')]);
            } else {
                $person_repo = new PersonRepositorySQL();
                $auditor_info = $person_repo->find($lead->audited_by, ['whatsapp', 'country_code']);
                $isd_code = DB::selectOne("select isd_code from markets where country_code = '$auditor_info->country_code'")->isd_code;
                $whatsapp->send_message(["body" => $notification, "to" => $auditor_info->whatsapp , "isd_code"=> $isd_code, "session" => config('app.whatsapp_notification_number')]);
            }
            
            
        }

        $visit_id_arr = $this->append_visit_id($data['lead_id'],$data['visit_id']);
        $addl_fields += ['status' => $lead_status,'visit_ids' => $visit_id_arr ];
        $lead_repo->update_cust_reg_json($cust_reg_arr,$data['lead_id'],$addl_fields);
        return $message;
    }

    private function send_mobile_num_verfication_email($cust_reg_arr, $rm_id, $lead_id){
    
        $first_name =  $cust_reg_arr['owner_person']['first_name']['value'];
        $biz_name =  $cust_reg_arr['biz_info']['biz_name']['value'];
        $acc_num =  $cust_reg_arr['account']['acc_number']['value'];
        $acc_prvdr_code =  $cust_reg_arr['account']['acc_prvdr_code']['value'];
        $mobile_num =  $cust_reg_arr['biz_identity']['mobile_num']['value'];
        $country_code =  session('country_code');
        $rm_name  = (new PersonRepositorySQL)->full_name($rm_id);
        $assigned_date = format_date(datetime_db());

        $mail_data = compact('country_code','first_name', 'biz_name', 'acc_num', 'acc_prvdr_code', 'mobile_num', 'rm_name', 'assigned_date', 'lead_id');

        send_email('pending_mobile_num_ver', [get_csm_email()], $mail_data);

    }

    public function submit_mobile_num_verified_kyc($data){

        $lead_repo = new LeadRepositorySQL();

        $lead = $lead_repo->find($data['lead_id'], ['cust_reg_json', 'acc_purpose', 'type', 'kyc_reason', 'visit_ids', 'audited_by', 'first_name', 'last_name', 'flow_rel_mgr_id', 'remarks']);
        
        $lead_status = Consts::PENDING_AUDIT;

        $cust_reg_arr = json_decode($lead->cust_reg_json, true);
        
        $lead_status = $this->check_otp_verification($cust_reg_arr, $lead_status);

        if($lead_status == Consts::PENDING_MOBILE_NUMBER_VER){
            thrw("Mobile number verfication is not completed. Can you please check all the mobile numbers are verified ?");
        }

        $this->submit_kyc_for_audit($lead, $cust_reg_arr);

        $resp['message'] = "Lead's KYC has been successfully submited for audit" ;
        $remarks = (new LeadService)->combine_remarks($lead->remarks, null, session('user_person_id'), Consts::LEAD_ACTIONS[Consts::LA_KYC_SUBMITTED]);

        $addl_fields = ['remarks' => $remarks, 'status' => $lead_status];

        $resp['is_updated'] = $lead_repo->update_cust_reg_json($cust_reg_arr,$data['lead_id'],$addl_fields);
        return $resp;

    }

    public function reject_call_log($data){

        $cust_reg_arr = (new LeadRepositorySQL())->get_cust_reg_arr($data['lead_id']);

        $mobile_field = $this->get_mobile_num_field($data);
        
        $reject_field = "rejected_".$mobile_field;

        $verified_field = "verified_".$mobile_field;

        if(Str::contains($reject_field, 'rejected_addl_mobile_num') ){
            $index = filter_var($reject_field, FILTER_SANITIZE_NUMBER_INT);
            $cust_reg_arr['addl_num'][$index][$reject_field] = 1;
            unset($cust_reg_arr['addl_num'][$index][$verified_field]);

        }else{
            $cust_reg_arr['biz_identity'][$reject_field] = 1;
            unset($cust_reg_arr['biz_identity'][$verified_field]);

        }
        
        $resp  = (new LeadRepositorySQL())->update_cust_reg_json($cust_reg_arr, $data['lead_id']);

        return "Call log rejected successfully";
    }

    public function submit_call_log($data){

        $cust_reg_arr = (new LeadRepositorySQL())->get_cust_reg_arr($data['lead_id']);
        $mobile_field = $this->get_mobile_num_field($data);
        $check_field = "verified_".$mobile_field;

        if(Str::contains($check_field, 'verified_addl_mobile_num') ){
            $index = filter_var($check_field, FILTER_SANITIZE_NUMBER_INT);
            $name = $cust_reg_arr['addl_num'][$index]['name']['value'];
            $relation = $cust_reg_arr['addl_num'][$index]['relation']['value'];

            if($relation != $data['relation']){
                thrw("As per the lead information collected by the RM the relation mismatches with the owner of the mobile number.");
            }
            $cust_reg_arr['addl_num'][$index]['name']['value'] = $data['name'];
            
            $cust_reg_arr['addl_num'][$index][$check_field] = 1;
            

        }else{
            if(isset($cust_reg_arr['biz_identity'][$check_field]) && $cust_reg_arr['biz_identity'][$check_field]){
                thrw("Customer has already sent the otp. Mobile number verified successfully");
            }else{
                $cust_reg_arr['biz_identity'][$check_field] = 1;
            }
        }
        
        $resp  = (new LeadRepositorySQL())->update_cust_reg_json($cust_reg_arr, $data['lead_id']);

        return "Call log submitted successfully";
    }


    
}
