<?php
namespace App\Services;

use App\Consts;
use App\Repositories\SQL\CustAgreementRepositorySQL;
use App\Services\Mobile\RMService;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\FieldVisitRepositorySQL;
use App\Repositories\SQL\LeadRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\AccProviderRepositorySQL;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mail;
use App\Mail\FlowCustomMail;
use App\Repositories\SQL\AddressInfoRepositorySQL;
use App\Services\Support\SMSNotificationService;
use App\Services\Support\FireBaseService;
use App\SMSTemplate;
use App\Models\FlowApp\AppUser;
use App\Services\Vendors\Whatsapp\WhatsappWebService;
use App\Services\PartnerService;
use App\Jobs\AuditKYC;
use App\Repositories\SQL\RelationshipManagerRepositorySQL;
use App\Services\LoanService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LeadService{
    
    public function __construct()
    {
        $this->json_fields_arr = ["first_name","last_name","mobile_num","location","territory","acc_prvdr_code","account_num","gps","landmark","biz_name","national_id"];
        $this->tbl_fields_arr = ["first_name","last_name","mobile_num","location","territory","acc_purpose","acc_prvdr_code","account_num","flow_rel_mgr_id","biz_name",'status', 'cust_id', 'type',"product","update_data_json","tf_status","channel","profile_status", "national_id","audited_by"];
    }

    public function validate_tf_acc($data){
        if(isset($data['acc_purpose']) && is_tf_acc($data['acc_purpose'])) {
            if (!in_array($data['acc_prvdr_code'], config('app.tf_acc_prvdrs')[session('country_code')])) {
                thrw("Selected account provider does not support terminal financing");
            }

            if(is_fa_acc($data['acc_purpose'])){
                thrw("Terminal Financing account cannot have Float Advance purpose");
            }
        }
    }



    public function create_lead($data,$tf = false){
        $req_data = $data;
        $data = $data['lead'];
        $this->validate_tf_acc($data);
        $this->dup_lead_check($data);
        if(array_key_exists('acc_purpose', $data) && in_array('assessment',$data['acc_purpose'])){
            thrw('You can not create a lead for Assessment account');
        }
        $lead_repo = new LeadRepositorySQL;
        $lead_json = [];
        if($tf){
            array_push($this->json_fields_arr,"UEZM_MainContent_txtAbbreviationName","UEZM_MainContent_txtCompanyRegistrationNo","UEZM_MainContent_ddlNatureOfBusiness","UEZM_MainContent_ddOperatedBy","UEZM_MainContent_ddWallet","UEZM_MainContent_txtRecruiterID","UEZM_MainContent_ddlZone");
        }
        foreach($data as $key => $value){
            if(in_array($key,$this->json_fields_arr)){
                $lead_json[$key] =  $value;
            }
        }
        if(array_key_exists('visit_id',$req_data)){
            $data['visit_ids'] = json_encode([$req_data['visit_id']]);
            $field_repo = new FieldVisitRepositorySQL();
            $lead_json['gps'] = $field_repo->find($req_data['visit_id'],['gps'])->gps;
        }

        $data['lead_json'] = json_encode($lead_json);
        $data['lead_date'] = datetime_db();
        $data['country_code'] = session('country_code');
        $data['channel'] = session('channel');
        

        if(!array_key_exists("status",$data) || $data['status'] == null){
            if(isset($data['flow_rel_mgr_id'])){
                if(is_tf_acc($data['acc_purpose'])){
                    if(!array_key_exists('product',$data)){
                        $data['status'] = Consts::PENDING_PRODUCT_SEL;
                        $data['tf_status'] = Consts::TF_PENDING_PRODUCT_SEL;

                    }elseif(array_key_exists('product',$data)){
                        $data['status'] = Consts::PENDING_DOWNPAYMENT;
                        $data['tf_status'] = Consts::TF_PENDING_DOWNPAYMENT;
                    }
                }else{
                    $data['status'] = Consts::PENDING_RM_EVAL;
                }
            }else{
                if(isset($data['acc_purpose']) && is_tf_acc($data['acc_purpose'])){
                    thrw("Please select the Flow RM.");
                }
                else{
                    $data['status'] = Consts::PENDING_RM_ALLOC;
                }
            }
        }
        if(!array_key_exists("type",$data) || $data['type'] == null){
            $data['type'] = 'kyc';
        }
        try{

            DB::beginTransaction();

            $lead_id = $lead_repo->insert_model($data);
            if($lead_id){
                if(isset($data['mobile_num'])){
                    $this->send_welcome_msg($data);   
                }
                if(array_key_exists('visit_id',$req_data)){
                    $field_repo->update_model(['id' => $req_data['visit_id'], 'lead_id' => $lead_id]);
                }

            }
            
            DB::commit();

        }
        catch (Exception $e){
            DB::rollback();
            throw new Exception($e->getMessage());
       };
        return $lead_id;
    }

    public function dup_lead_check($data){
        if(!array_key_exists("id" ,$data)){
            $data['id'] = "";
        }
        if(!array_key_exists("cust_id", $data)){
            $data['cust_id'] = "";
        }
        $lead_repo = new LeadRepositorySQL;
        $person_repo = new PersonRepositorySQL;
        $borrower_repo = new BorrowerRepositorySQL;
        if(array_key_exists('mobile_num',$data)){
            $lead_data = DB::select("select id from leads where profile_status = 'open' and mobile_num = ? and id != ?" ,[$data['mobile_num'],$data['id']]);

            $persons = $person_repo->get_records_by("mobile_num",$data['mobile_num'],['id']);
            if($lead_data ){
                thrw("Already a lead exist with the same mobile number");
            }elseif($persons){
                foreach($persons as $person){
                    $borrower = $borrower_repo->get_record_by("owner_person_id",$person->id,['profile_status', 'cust_id']);
                    if($borrower->profile_status == "open" && $borrower->cust_id != $data['cust_id']){
                        thrw("Already a customer exist with the same mobile number");
                    }

                }
            }
        } 
        if(array_key_exists('account_num',$data) && array_key_exists('acc_prvdr_code',$data)){
            $lead_data = DB::select("select id from leads where profile_status = 'open' and account_num = ? and acc_prvdr_code = ? and id != ?" ,[$data['account_num'],$data['acc_prvdr_code'],$data['id']]);
            if($lead_data){
                thrw("Already a lead exist with the same account number");
            }
        }
        if(array_key_exists('national_id',$data)){

            $lead_data = DB::select("select lead_json from leads where profile_status = 'open' and id != ? and json_contains(lead_json,'{\"national_id\" : \"{$data['national_id']}\"}')",[$data['id']]);
            $persons = $person_repo->get_records_by("national_id",$data['national_id'],['id']);
            if($lead_data ){
                thrw("Already a lead exist with the same national id");
            }elseif($persons){
                foreach($persons as $person){
                    $borrower = $borrower_repo->get_record_by("owner_person_id",$person->id,['profile_status', 'cust_id']);
                    if($borrower->profile_status == "open" && $borrower->cust_id != $data['cust_id']){
                        thrw("Already a customer exist with the same national id");
                    }
                }
            }
        }
    }

    public function include_month_values($acc_prvdr_code) {

        $data = [];
        if ($acc_prvdr_code == 'RBOK') {
            $today = now()->day;        
            // If past 10 days [3, 2, 1] else [4, 3, 2]
            $month_maps = ($today > 10) ?
                ['month1' => 3, 'month2' => 2, 'month3' => 1]
            :
                ['month1' => 4, 'month2' => 3, 'month3' => 2];

            foreach($month_maps as $month_label => $sub_by) {
                $data[$month_label] = now()->subMonth($sub_by)->format('F');
            }
        }
        return $data;
    }

    public function view_lead($data){
        $lead_repo = new LeadRepositorySQL;
        $person_repo = new PersonRepositorySQL;
        $field_visit_repo = new FieldVisitRepositorySQL;
        if($data['id'] == "" || $data['id'] == null){
            thrw("Search the Lead again and check");
        }
        $lead_data = $lead_repo->find($data['id']);
        $ap_logo_arr =  config('app.acc_prvdr_logo')[session('country_code')];
        if(isset($lead_data->acc_prvdr_code)){
            $lead_data->ap_logo_path = $ap_logo_arr[$lead_data->acc_prvdr_code];
        }
        $lead_data->lead_json = json_decode($lead_data->lead_json);
        $lead_data->cust_reg_json = json_decode($lead_data->cust_reg_json);
        $lead_data->consent_json = json_decode($lead_data->consent_json);
        

        $visit_id_arr = json_decode($lead_data->visit_ids,true);
        if(sizeof($visit_id_arr)>0){
            $visit_id = max($visit_id_arr);
            if($visit_id){
                $visit_data = $field_visit_repo->find($visit_id,['photo_visit_selfie']);
                $lead_data->photo_visit_selfie = $visit_data->photo_visit_selfie;
                $lead_data->photo_visit_selfie_path = get_file_path("cust_reg_checkin",$visit_id,"photo_visit_selfie");
            }
        }
        $lead_data = array_merge((array)$lead_data->lead_json,(array)$lead_data);
        if($lead_data['status'] >= Consts::PENDING_AUDIT){
            $biz_info = $lead_data['cust_reg_json']->biz_info;
            $dp_rel_mgr_id = $biz_info->dp_rel_mgr_id->value;
            $biz_info->dp_rel_mgr_name  = $person_repo->full_name($dp_rel_mgr_id);
            if(isset($lead_data['audited_by'])){
                $audited_by = $lead_data['audited_by'];
                $lead_data['auditor_name'] = $person_repo->full_name($lead_data['audited_by']);
            }
           
        }

        if($lead_data['status'] >= Consts::PENDING_RM_EVAL){
            $lead_data['rm_name']  = $person_repo->full_name($lead_data['flow_rel_mgr_id']);
        }
        unset($lead_data['lead_json']);
        
        $lead_data = array_merge($lead_data, $this->include_month_values($lead_data['acc_prvdr_code']));
        return $lead_data;
    }

    public function audit_name($acc_number, $acc_prvdr_code, $branch){
        $ussd_codes = (new AccProviderRepositorySQL())->get_record_by('acc_prvdr_code', $acc_prvdr_code, ['mobile_cred_format'])->mobile_cred_format;
        if (isset($ussd_codes->ussd_holder_name_code)){
            // $audit_kyc_line = null;
            // if(isset($branch)){
            //     if (array_key_exists($branch, config('app.RMTN_district_accounts'))){
            //         $audit_kyc_line = config('app.RMTN_district_accounts')[$branch];
            //     }
            // }
            return (new RMService())->audit_name($ussd_codes->ussd_holder_name_code, $acc_number, $acc_prvdr_code);
        }
    }

    public function email_holder_name_proof_to_app_support($data){
        try{
            DB::beginTransaction(); 
            $lead_repo = new LeadRepositorySQL();
            $cust_reg_arr = $lead_repo->get_cust_reg_arr($data['lead_id']);
            $cust_reg_arr['account']['holder_name'] = $data['holder_name'];
            $lead_repo->update_cust_reg_json($cust_reg_arr, $data['lead_id']);
            $data['full_path'] = Storage::path(get_file_path("leads", $data['lead_id'],  "account_holder_name_proof").'/'.$data['account_holder_name_proof']);
            $data['ops_admin_email'] = get_ops_admin_email();
            $data['acc_num'] = $cust_reg_arr['account']['acc_number']['value'];
            Mail::to(config('app.app_support_email'))->queue((new FlowCustomMail('holder_name_evidence_verification', $data))->onQueue('emails'));
            DB::commit();
            return;
        } catch (Exception $e) {
            DB::rollback();
            thrw($e->getMessage());
        }
    }

    public function bypass_holder_name_audit($data){
        $data['status'] = Consts::PENDING_AUDIT;
        $lead_repo = new LeadRepositorySQL();
        $lead_repo->update_model($data);
    }

    public function update($data,$tf=false){
        $lead_repo = new LeadRepositorySQL();
        $lead = $lead_repo->find($data['lead']['id'], ['acc_purpose','status', 'type', 'cust_reg_json']);
        $lead_data = $data['lead'];
        $lead_data['type'] = $lead->type;
        $this->validate_tf_acc($lead_data);
        unset($data['status']);
        $lead_repo = new LeadRepositorySQL;
        if(!isset($data['lead']['cust_id'])){
            $data['lead']['cust_id'] = $lead_repo->get_rekyc_lead_cust_id($data['lead']['id']);
        }
        m_array_filter($data['lead']);
        $this->dup_lead_check($data['lead']);

       if(array_key_exists('acc_purpose', $data['lead']) && in_array('assessment',$data['lead']['acc_purpose'])){
            thrw('You can not update the account purpose to "Assessment"');

        }
        $addl_sql = "";
        $lead_json =array();
        if($tf){
            array_push($this->json_fields_arr,"UEZM_MainContent_txtAbbreviationName","UEZM_MainContent_txtCompanyRegistrationNo","UEZM_MainContent_ddlNatureOfBusiness","UEZM_MainContent_ddOperatedBy","UEZM_MainContent_ddWallet","UEZM_MainContent_txtRecruiterID","UEZM_MainContent_ddlZone");
        }
        if(array_key_exists('flow_rel_mgr_id', $data['lead']) && $lead->status == Consts::PENDING_RM_ALLOC){
            $addl_sql = sprintf( "status = '%s'",Consts::PENDING_RM_EVAL);
            if( is_tf_acc($lead->acc_purpose)){
                $addl_sql .= sprintf( ", tf_status = '%s'",Consts::TF_PENDING_KYC);
            }
        } 
        if(array_key_exists('product',$data['lead']) && !isset($data['status'])){
            if($addl_sql != ""){
                $addl_sql .= ",";
            }
            $addl_sql .= sprintf( "status = '%s'", Consts::PENDING_DOWNPAYMENT);
            $addl_sql .= sprintf( ", tf_status = '%s'", Consts::TF_PENDING_DOWNPAYMENT);

        }
        foreach($data['lead'] as $key => $value){
            if(in_array($key,$this->tbl_fields_arr)){
                if($addl_sql != ""){
                    $addl_sql .= ",";
                }
                if($key == 'acc_purpose'){
                    $value = json_encode($data['lead'][$key]);
                    $addl_sql .= "$key = '$value'";
                }
                else{
                    $addl_sql .= "$key = '{$data['lead'][$key]}'";
                }
            }
            if(in_array($key,$this->json_fields_arr)){
                $lead_json[$key] =  $value;
            }
        }
        if(count($lead_json) > 0){
            if($addl_sql != ""){
                $addl_sql .= ",";
            }
            $lead_json = json_encode($lead_json);
            $addl_sql = "$addl_sql lead_json = '$lead_json'";
        }
        if(array_key_exists('visit_id',$data['lead'])){
            $rm_serv = new RMService();
            $visit_id_arr = $rm_serv->append_visit_id($data['lead']['id'],$data['lead']['visit_id']);
            $addl_sql = "$addl_sql visit_ids = '$visit_id_arr'";
        }
       
        
        $update_status = DB::update("update leads set $addl_sql where id = ?",[$data['lead']['id']]);
        return $update_status;
    }

    public function remove_otp_verf_key(&$cust_reg_arr){
        
        foreach ($cust_reg_arr['biz_identity'] as $key => $value){
            if(Str::contains($key, 'verified' || Str::contains($key, 'rejected'))){
                $is_otp_verified = $cust_reg_arr['biz_identity'][$key];
                if(!$is_otp_verified){
                    unset($cust_reg_arr['biz_identity'][$key]);
                }
            }
        }

        foreach ($cust_reg_arr['addl_num'] as $key => $value){
            foreach($value as $inner_key => $inner_value){
                if(Str::contains($inner_key, 'verified') || Str::contains($inner_key, 'rejected')){
                    $is_otp_verified = $cust_reg_arr['addl_num'][$key][$inner_key];
                    if(!$is_otp_verified){
                        unset($cust_reg_arr['addl_num'][$key][$inner_key]);
                    }
                }
            }

        }

    }

   
    
    public function search_lead($data,$tf = null){
        
        m_array_filter($data);
        
        $addl_sql = $this->get_lead_search_addl_sql($data);
        
        $country_code = session('country_code');
        if($tf){
            $arr = implode(',',$tf);
            $results = DB::select("select {$arr} from leads where  $addl_sql and country_code = '{$data['country_code']}' order by profile_status,created_at desc" );
            return $results;
        }
        else {
            $results = DB::select("select * from leads where  $addl_sql and country_code = '{$country_code}' order by profile_status,created_at desc");
            $result_arr = [];
            foreach($results as $result){

                $person_repo = new PersonRepositorySQL();
                if($result->audited_by){
                    $result->auditor_name = $person_repo->full_name($result->audited_by);
                }
                $result->photo_visit_selfie = 	get_visit_selfie_path($result->visit_ids);
                $result->lead_json = json_decode($result->lead_json);
                $result->cust_reg_json = json_decode($result->cust_reg_json);
                $res = array_merge((array)$result->lead_json,(array)$result);
                $person = $person_repo->get_person_name($result->flow_rel_mgr_id);
                $res['flow_rel_mgr_name'] = $person ? $person->first_name." ".$person->last_name : null;
                unset($res['lead_json']);
                $result_arr[] = $res;
            }
        }
        return $result_arr;
    
        
    }

    private function get_lead_search_addl_sql($data){
        $addl_sql = "";
        $dates_arr = ['created_from',"created_to"];
        $tf_arr = ['tf_status','account_num'];
        $tf_arr_json = ['UEZM_MainContent_txtRecruiterID'];

        foreach($data as $key => $value){
            if(in_array($key,$this->tbl_fields_arr)){
                if($addl_sql !="" ){
                    $addl_sql .= " and ";
                }
                if($key == "biz_name"){
                    $addl_sql .= get_addl_sql_like_name($key,$data[$key]);
                }
                elseif($key=="audited_by"){
                    $addl_sql.= "$key = '{$data[$key]}'";
                }
                elseif($key == "acc_purpose"){
                    $addl_sql .= "JSON_CONTAINS(acc_purpose, JSON_ARRAY('{$data[$key]}'))";
                }
                elseif($key == 'cust_id'){
                        $addl_sql .= "json_contains(cust_reg_json,'{\"cust_id\" : \"{$data[$key]}\"}')";
                }
                elseif($key == 'national_id'){
                    $addl_sql .= "json_contains(lead_json,'{\"national_id\" : \"{$value}\"}')";
                }
                elseif($key == "profile_status"){
                    if($data['profile_status'] != 'closed'){
                        $addl_sql.= "profile_status = 'open'";
                    }
                    else{
                        $addl_sql.= "profile_status in ('open','closed')";
                    }
                }
                elseif($key == "status" && $value == 'pending_name_n_audit'){
                    $addl_sql .= "$key in ('42_retrieve_holder_name', '50_pending_audit') and profile_status = 'open'";
                }
                else{
                    $addl_sql .= "$key = '{$data[$key]}'";
                }
            }
            if(in_array($key,$tf_arr)){
                if($addl_sql !=""){
                    $addl_sql .= " and ";
                }
                $addl_sql .= "$key = '{$value}'";
            }

            if(in_array($key,$dates_arr)){
                if($addl_sql !=""){
                    $addl_sql .= " and ";
                }
                if($key == "created_from"){
                    $addl_sql .= "date(created_at) >= '{$data[$key]}'";
                }
                elseif($key == "created_to"){
                    $addl_sql .= "date(created_at) <= '{$data[$key]}'";
                }
            }

            if(in_array($key,$tf_arr_json)){
                if($addl_sql !=""){
                    $addl_sql .= " and ";
                }
                if($key == "UEZM_MainContent_txtRecruiterID"){
                    $addl_sql .= "json_contains(lead_json,'{\"UEZM_MainContent_txtRecruiterID\" : \"{$value}\"}')";
                }
            }
        }

        return $addl_sql;
    }

    private function send_reassign_lead_notification($data, $user_email){

        Mail::to($user_email)->send(new FlowCustomMail('reassign_lead_notify', $data));
    }


    // public function update_status($data){
        
    //     $lead_repo = new LeadRepositorySQL;
    //     $lead_data = $lead_repo->find($data['lead_id'],['biz_name', 'flow_rel_mgr_id', 'first_name', 'last_name', 'cust_reg_json']);
    //     $data['biz_name'] = $lead_data->biz_name;
    //     // $lead_data = $lead_repo->find($data['lead_id'],['biz_name', 'flow_rel_mgr_id', 'first_name', 'last_name']);
    //     // $data['biz_name'] = $lead_data->biz_name;
    //     $lead_id = $data['lead_id'];
    //     $lead_data = $lead_repo->find($lead_id,['biz_name', 'flow_rel_mgr_id', 'remarks', 'first_name', 'last_name', 'audited_by']);

    //     $audited_name = (new PersonRepositorySQL)->full_name($lead_data->audited_by);

        
    //     if($lead_data->audited_by != session('user_person_id')){
    //         $resp = thrw("You are not able to do any actions for this lead because this lead has already been initiated to {$audited_name}");
    //     }


    //     $data['biz_name'] = $lead_data->biz_name;
        
    //     [$email , $messenger_token]  = (new PersonRepositorySQL ())->get_email_n_msgr_token($lead_data->flow_rel_mgr_id);

    //     $whatsapp = new WhatsappWebService();
    //     $rm_info = (new PersonRepositorySQL)->find($lead_data->flow_rel_mgr_id, ['whatsapp', 'country_code']);
    //     $isd_code = DB::selectOne("select isd_code from markets where country_code = '$rm_info->country_code'")->isd_code;
    //     $notification = "The KYC details for the lead profile of {$lead_data->first_name} {$lead_data->last_name} has been rejected by the auditor. Please make the corrections as mentioned by the auditor.";
    //     $whatsapp->send_message(["body" => $notification, "to" => $rm_info->whatsapp , "isd_code"=> $isd_code, "session" => config('app.whatsapp_notification_number')]);

    //     $this->send_reassign_lead_notification($data, $email);

    //     $notify_type = 'reassign_kyc';
        
    //     $this->send_push_notification($data['lead_id'], $messenger_token, $notify_type);
    //     $audited_by = session('user_person_id');
    //     $status = ( $data['reassign_reason'] == 'incorrect_data_consent' ) ? Consts::PENDING_DATA_CONSENT : Consts::KYC_INPROGRESS;
        
    //      if(isset($data['remarks'])){
    //        $remarks = $this->combine_remarks($lead_data->remarks, $data['remarks'], $audited_by);
    //        $remarks = $this->combine_remarks($remarks, dd_value($data['reassign_reason']), $audited_by, Consts::LEAD_ACTIONS[Consts::LA_KYC_REASSIGNED]);
    //      }else{
    //         $remarks = $this->combine_remarks($lead_data->remarks, $data['reassign_reason'], $audited_by);
    //     }


    //     $cust_reg_arr = json_decode($lead_data->cust_reg_json, true);
        
    //     $this->remove_otp_verf_key($cust_reg_arr);

    //     $cust_reg_json = json_encode($cust_reg_arr);
        
    //     $resp = $lead_repo->update_model(['cust_reg_json' => $cust_reg_json, 'status'=> $status,'id'=> $lead_id,'reassign_reason'=> $data['reassign_reason'], 'remarks' => $remarks]);
	// 	return $resp;
	// }

    public function update_status($data){
        
        $lead_repo = new LeadRepositorySQL;
        $lead_data = $lead_repo->find($data['lead_id'],['biz_name', 'flow_rel_mgr_id', 'first_name', 'last_name']);
        $data['biz_name'] = $lead_data->biz_name;
        $lead_id = $data['lead_id'];
        $lead_data = $lead_repo->find($lead_id,['biz_name', 'flow_rel_mgr_id', 'remarks', 'first_name', 'last_name']);

        $data['biz_name'] = $lead_data->biz_name;
        
        [$email , $messenger_token]  = (new PersonRepositorySQL ())->get_email_n_msgr_token($lead_data->flow_rel_mgr_id);

        $whatsapp = new WhatsappWebService();
        $rm_info = (new PersonRepositorySQL)->find($lead_data->flow_rel_mgr_id, ['whatsapp', 'country_code']);
        $isd_code = DB::selectOne("select isd_code from markets where country_code = '$rm_info->country_code'")->isd_code;
        $notification = "The KYC details for the lead profile of {$lead_data->first_name} {$lead_data->last_name} has been rejected by the auditor. Please make the corrections as mentioned by the auditor.";
        $whatsapp->send_message(["body" => $notification, "to" => $rm_info->whatsapp , "isd_code"=> $isd_code, "session" => config('app.whatsapp_notification_number')]);

        $this->send_reassign_lead_notification($data, $email);

        $notify_type = 'reassign_kyc';
        
        // $this->send_push_notification($data['lead_id'], $messenger_token, $notify_type);
        $audited_by = session('user_person_id');
        $status = ( $data['reassign_reason'] == 'incorrect_data_consent' ) ? Consts::PENDING_DATA_CONSENT : Consts::KYC_INPROGRESS;
        
        $remarks = $this->combine_remarks($lead_data->remarks, $data['remarks'], $audited_by);
        $remarks = $this->combine_remarks($remarks, dd_value($data['reassign_reason']), $audited_by, Consts::LEAD_ACTIONS[Consts::LA_KYC_REASSIGNED]);
        $resp = $lead_repo->update_model([ 'status'=> $status,'id'=> $lead_id,'reassign_reason'=> $data['reassign_reason'],'remarks' => $remarks ]);
		return $resp;
	}

    public function close_lead($data){
        
        $lead_repo = new LeadRepositorySQL;
        if($data['reason'] != null){
            
            $lead = $lead_repo->find($data['lead_id'],['status', 'remarks']);
            if(array_key_exists('lead_id', $data) && $lead->status <= Consts::PENDING_AUDIT){
                $update_arr = [ 'id'=>$data['lead_id'], 'profile_status'=>'closed', 'close_reason'=>$data['reason']];
                if (isset($data['status'], $data['remarks'])) {
                    $update_arr['status'] = $data['status'];
                    
                    $audited_by = session('user_person_id');
                    $remarks = $this->combine_remarks($lead->remarks, $data['remarks'], $audited_by);
                    $remarks = $this->combine_remarks($remarks, dd_value($data['reason']), $audited_by, Consts::LEAD_ACTIONS[Consts::LA_KYC_REJECTED]);
                    $update_arr['remarks'] = $remarks;
                }

                $result = $lead_repo->update_model($update_arr);                
                return $result;
            }else{
                thrw("Can not close this lead profile. Status {$lead->status}");
            }	  

        }else{
            thrw("Please choose a reason to close the profile");
        }
            
	}

    public function initiate_rekyc_for_new_account(array $account)
    {
        $this->check_lead_exists($account['cust_id']);
        $brwr_repo = new BorrowerRepositorySQL();
        $lead_repo = new LeadRepositorySQL();
        $person_repo = new PersonRepositorySQL();
        $addr_repo = new AddressInfoRepositorySQL();

        $borrower = $brwr_repo->find_by_code($account['cust_id']);
        $person = $person_repo->find($borrower->owner_person_id, ['mobile_num']);
        $address = $addr_repo->find($borrower->biz_address_id);

        $lead['acc_prvdr_code'] = $account['acc_prvdr_code'];
        $lead['account_num'] = $account['acc_number'];
        $lead['biz_name'] = $borrower->biz_name;
        $lead['flow_rel_mgr_id'] = $borrower->flow_rel_mgr_id;
        $lead['type'] = "re_kyc";
        $lead['cust_id'] = $account['cust_id'];
        if(!is_tf_acc($account['acc_purpose'])){
            $lead['status'] = Consts::PENDING_KYC;
        }
        $lead['kyc_reason'] = 'new_account';
        $lead['acc_purpose'] = $account['acc_purpose'];
        $lead['mobile_num'] = $person->mobile_num;
        $lead['landmark'] = $address['landmark'] ?? null;

        $data = ['lead' => $lead];
        $lead_id = $this->create_lead($data);

        $lead_data = $lead_repo->find($lead_id,['lead_json']);
        $lead_arr = (array)json_decode($lead_data->lead_json);
        $lead_arr['acc_purpose'] = $lead['acc_purpose'];
        $lead_arr['acc_number'] = $account['acc_number'];
        $lead_arr['type'] = 're_kyc';
        $cust_reg_arr = get_cust_reg_arr();
        $cust_reg_arr['cust_id'] = $account['cust_id'];
        $cust_reg_arr['account']['id'] = $account['id'];
        (new RMService)->update_cust_reg_arr($cust_reg_arr,$lead_arr);
        $cust_reg_json = json_encode($cust_reg_arr);

        $lead_repo->update_model(['id' => $lead_id, 'cust_reg_json' => $cust_reg_json]);

        $cust_aggr_repo = new CustAgreementRepositorySQL();
        $cust_aggr_repo->inactivate_agreement($account['cust_id'], false);
        $rm = $person_repo->find($lead['flow_rel_mgr_id'], ['email_id']);
        $lead['country_code'] = session('country_code');
        Mail::to($rm->email_id)->queue((new FlowCustomMail('repeat_kyc_notify', $lead))->onQueue('emails'));
        return $lead_id;
    }

    private function check_lead_exists($cust_id)
    {
        $onboarded_status = Consts::CUSTOMER_ONBOARDED;
        $leads = DB::select("select id from leads where cust_id = '$cust_id' and status!='$onboarded_status' and profile_status != 'closed'");
        if(sizeof($leads) > 0){
            thrw("A repeat KYC is already pending for this customer");
        }
    }

    public function update_pos_status($data){
        $lead_repo = new LeadRepositorySQL;
        $lead_data = $lead_repo->find($data['lead_id'],['tf_status']);
        if($data['action'] == 'pos_to_rm' && $lead_data->tf_status == Consts::TF_PENDING_POS_TO_RM){
            $update_fields = sprintf( "tf_status = '%s'",Consts::TF_PENDING_POS_TO_CUST);
            $json_field = json_encode(['rm_handover' => ['date' => date_db()]]);
            $msg = "POS handed over to RM successfully";
        }else if($data['action'] == 'pos_to_cust' && $lead_data->tf_status == Consts::TF_PENDING_POS_TO_CUST){
            $update_fields = sprintf( "tf_status = '%s',status = '%s',onboarded_date = '%s',profile_status = '%s',close_reason = '%s'",Consts::TF_PENDING_REPAY_CYCLE,Consts::CUSTOMER_ONBOARDED,datetime_db(),'closed','customer_onboarded');
            $json_field = json_encode(['cust_handover' => ['date' => date_db()]]);
            $msg = "POS handed over to Customer successfully";
        }else{
            thrw("Cannot update when lead is in {$lead_data->tf_status} status");
        }
        $update_arr['id'] = $data['lead_id'];
        DB::update("update leads set $update_fields,update_data_json = json_merge_patch(update_data_json,'$json_field') where id = {$data['lead_id']}");
		return $msg;
	}

    public function send_welcome_msg($data){

        $mobile_num = $data['mobile_num'];
        
        $data['cust_mobile_num'] = $mobile_num;  
        unset($data['mobile_num']); 
          
        $template = null;
        
        $notify_serv = new SMSNotificationService();
        $person_repo = new PersonRepositorySQL();
        $lead_repo   = new LeadRepositorySQL();

		if (isset($data['flow_rel_mgr_id'])){
			
			$flow_rel_mgr_id = $data['flow_rel_mgr_id'];
            
            $person_id= session('user_person_id');
            	
            $data['rm_mobile_num']  = $person_repo->get_mobile_num($flow_rel_mgr_id);
 
            $data['rm_name'] = $person_repo->full_name($flow_rel_mgr_id);

            $rm_gender = $person_repo->get_gender($flow_rel_mgr_id);
            if($rm_gender == 'female'){
                $rm_gender_pronoun = 'her';
            }else{
                $rm_gender_pronoun = 'him';
            }

            $data['rm_gender_pronoun'] = $rm_gender_pronoun;
         
			if($flow_rel_mgr_id == $person_id){

                $template = 'LEAD_MSG_BY_RM';  
                  
            }else{
               
                $template = 'LEAD_MSG_WITH_RM_ASSIGNED';	
			}

        }else{

            $acc_prvdr = (isset($data['acc_prvdr_code']) || $data['acc_prvdr_code'] != 'other') ? $data['acc_prvdr_code'] : 'XXX';
            $cs_number = config('app.customer_success_mobile')["$acc_prvdr"];
            $data['cs_num'] = $cs_number;

            $template = 'LEAD_MSG_WITHOUT_RM_ASSIGNED';
        }
        // unset($data['acc_purpose']);
        
        $notify_welcome_lead = $notify_serv->send_notification_message($data,$template);  
    }

    public function reject_kyc($data){

        $lead_id = $data['lead_id'];
        $lead_data = ['lead_id' => $lead_id, 'status' => Consts::KYC_FAILED , 'reason' => $data['reason'], 'remarks' => $data['remarks']];
        
        $this->close_lead($lead_data);
       
        $lead  = (new LeadRepositorySQL)->find($lead_id,['acc_prvdr_code', 'account_num', 'flow_rel_mgr_id', 'biz_name', 'mobile_num', 'audited_by']);
        
        [$email , $messenger_token]  = (new PersonRepositorySQL())->get_email_n_msgr_token($lead->flow_rel_mgr_id);
    
        $notify_type = 'kyc_rejected';

        $this->send_push_notification($lead_id, $messenger_token, $notify_type);
        $this->send_email_notification_for_reject_kyc($lead, $data['reason'], $email);
        (new PartnerService)->notify_lead_status( $lead_id );
	}

    private function send_email_notification_for_reject_kyc($lead, $reason, $rm_email){

        $person_repo = new PersonRepositorySQL;
        $rm_data = $person_repo->find($lead->flow_rel_mgr_id,['first_name', 'mobile_num']);
        $auditor_name = $person_repo->full_name($lead->audited_by);
	    $mail_data = ['country_code' => session('country_code'), 'cust_mobile_num' => $lead->mobile_num, 'rm_mbl_num' => $rm_data->mobile_num,'rm_name' => $rm_data->first_name, 
        'biz_name' => $lead->biz_name, 'acc_prvdr_code' =>$lead->acc_prvdr_code, 'account_num' => $lead->account_num, 'auditor_name' => $auditor_name, 'reason' => $reason];
		
        Mail::to([get_ops_admin_email(),$rm_email])->queue((new FlowCustomMail('reject_kyc_notification', $mail_data))->onQueue('emails'));
        
    }

    private function send_push_notification($lead_id, $messenger_token, $notify_type){

        $lead_repo  = new LeadRepositorySQL;
        $serv = new FireBaseService();
        $data['notify_type'] = $notify_type;
        $data['lead_id'] = "$lead_id";
		$serv($data, $messenger_token);

    }

    public function allow_manual_capture($data){

        $lead_id = $data['lead_id'];
        $lead_repo  = new LeadRepositorySQL;
        $cust_reg_arr = $lead_repo->get_cust_reg_arr($lead_id);
        $result = null;
      
        if($cust_reg_arr['allow_biz_owner_manual_id_capture'] == false && $data['type'] == 'biz_owner'){
            $cust_reg_arr['allow_biz_owner_manual_id_capture'] = true;
        }else if ($cust_reg_arr['allow_tp_ac_owner_manual_id_capture'] == false && $data['type'] == 'third_party_owner'){
            $cust_reg_arr['allow_tp_ac_owner_manual_id_capture'] = true;
        }else{
            thrw('RM is already allowed to do capture National ID for this lead.');
        }
        
        $result = $lead_repo->update_cust_reg_json($cust_reg_arr,$lead_id);
        $this->send_manual_capture_notification($lead_id);

		return $result;

    }

    public function get_person_id_n_msgr_token($lead_id) {
        
        $lead_repo  = new LeadRepositorySQL;
        $person_repo = new PersonRepositorySQL();

        $lead  = $lead_repo->find($lead_id,['flow_rel_mgr_id']);
        $person_id = $lead->flow_rel_mgr_id;
        [$email, $messenger_token] = $person_repo->get_email_n_msgr_token($person_id);
        return [$person_id, $messenger_token];
    }
    private function send_manual_capture_notification($lead_id){
        
        $serv = new FireBaseService();
        [$person_id, $messenger_token] = $this->get_person_id_n_msgr_token($lead_id);
        $data['notify_type'] = 'allow_national_id_manual_capture';
		$serv($data, $messenger_token);
    }

    public function send_file_process_notification($lead_id){
        
        $serv = new FireBaseService();
        [$rm_id, $messenger_token] = $this->get_person_id_n_msgr_token($lead_id);
        $data['lead_id'] = "$lead_id";
        $data['notify_type'] = 'fp_success';
        try {
		    $serv($data, $messenger_token);
        }catch(\Exception $e){
            send_notification_failed_mail($e, $rm_id, $data['notify_type']);
        }
    }

    public function update_audited_by($lead_id){
        $lead_repo  = new LeadRepositorySQL;
        $audited_by = session('user_person_id');

        $lead_data = $lead_repo->find($lead_id, ['remarks']);
        $remarks = $this->combine_remarks($lead_data->remarks, null, $audited_by, Consts::LEAD_ACTIONS[Consts::LA_AUDIT_INITIATED]);

        $lead_repo->update_model(['id'=>$lead_id, 'audited_by' => $audited_by, 'remarks' => $remarks]);
    }

    public function view_remarks($lead_id) {

        $lead_repo  = new LeadRepositorySQL;
        $lead_data = $lead_repo->find($lead_id, ['remarks']);
        return $lead_data->remarks;
    }

    public function combine_remarks($existing_remarks, $cmt, $cmtr_id, $action=null, $is_pvt=false) {
                
        $person_repo = new PersonRepositorySQL();
        $cmtr_name = $person_repo->get_first_name($cmtr_id);

        $action = ( $action === null ) ? null : str_replace(':cmtr_name', $cmtr_name, $action);
        $new_remarks = [
            "cmt" => $cmt,
            "action" => $action,
            "cmtr_id" => $cmtr_id,
            "cmtr_name" => $cmtr_name,
            "cmtr_time" => now()->format(Consts::DB_DATETIME_FORMAT),
            "is_pvt" => $is_pvt
        ];

        $remarks = $existing_remarks;
        $remarks[] = $new_remarks;
        return $remarks;
    }

    public function add_remarks($data) {

        $lead_id = $data['id'];
        $lead_repo  = new LeadRepositorySQL;
        $remarks = $lead_repo->find($lead_id, ['remarks'])->remarks;
        $remarks = $this->combine_remarks($remarks, $data['cmt'], session('user_person_id'));
        
        $lead_repo->update_model(['id' => $lead_id, 'remarks' => $remarks]);
        return $remarks;
    }

    public function get_auditor_name_list(){

        $auditor_lists = DB::select("select person_id from app_users where role_codes = 'operations_auditor' and country_code = ?",[session('country_code')]);
       
        foreach($auditor_lists as $auditor){
            $auditor_name = (new PersonRepositorySQL)->full_name($auditor->person_id);
            $auditor_id = $auditor->person_id;
            $list_data[] = ['id'=>$auditor_id, 'name' => $auditor_name];
        }
        return $list_data ;
    }

    public function assign_auditor($data){
       
        $lead_repo = new LeadRepositorySQL;
        $auditor_name = (new PersonRepositorySQL)->full_name($data['auditor_id']);
        $assign_auditor = DB::update('update leads set audited_by = ? where id= ?',[$data['auditor_id'], $data['lead_id']]);
        $existing_remarks = $lead_repo->get_record_by('id', $data['lead_id'], ['remarks'])->remarks;
        
        if($assign_auditor){
            $message = "This Lead has been reassigned to {$auditor_name}";
            $cmtr_id = $data['auditor_id'];
            $remarks = $this->combine_remarks($existing_remarks, null, $cmtr_id, Consts::LEAD_ACTIONS[Consts::LA_REASSIGN_AUDIT]);
            $lead_repo->update_model(['id'=>$data['lead_id'], 'remarks' => $remarks]);

            return $message;
        }else{
            thrw("You are not able to reassign this lead to the same auditor");
        }
        
    }
}
