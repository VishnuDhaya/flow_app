<?php

namespace App\Http\Controllers\Mobile;

use App\Models\Lead;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\AddressInfoRepositorySQL;
use App\Repositories\SQL\RelationshipManagerRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\AccProviderRepositorySQL;
use App\Services\FieldVisitService;
use App\Services\TaskService;
use App\Services\BorrowerService;
use App\Services\BorrowerServiceV2;
use App\Services\CustomerRegService;
use App\Services\Mobile\RMService;
use App\Services\AgreementService;
use App\Services\LoanApplicationService;
use App\Services\LoanService;
use App\Services\FileService;
use App\Models\Borrower;
use App\Services\LeadService;
use App\Validators\FlowValidator;
use Carbon\Carbon;
use App\Models\FlowApp\AppUser;
use App\Repositories\SQL\LeadRepositorySQL;
use App\Services\RMManagementService;
use Illuminate\Support\Facades\Log;
use PDO;

class RMController extends ApiController
{

    public function borrower_search(Request $req)
    {
        $data = $req->data;
        $rm_serv = new RMService();
        $borrowers['results'] = $rm_serv->borrower_search($data['req']);
        return $this->respondData($borrowers);
    }

    public function view_borrower(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $borrower = $rm_serv->view_borrower($data['req']);
        return $this->respondData($borrower);
    }

    public function reg_cust(Request $request)
    {
        $data = $request->data;
        $lead_repo = new LeadRepositorySQL;
        $lead_data = $lead_repo->find($data['lead_id'], ['cust_reg_json', 'flow_rel_mgr_id', 'product_group', 'id']);
        $cust_reg_json = json_decode($lead_data->cust_reg_json, true);
        $borrower = $cust_reg_json;
        $borr_serv = new CustomerRegService();
        flatten_borrower($borrower);
        $validation_keys = $borr_serv->get_validation_keys($borrower);
        $borr_serv->append_borrower_obj($borrower, $lead_data);
        $check_validate = FlowValidator::validate($borrower, $validation_keys, __FUNCTION__);

        if (is_array($check_validate)) {
            return $this->respondValidationError($check_validate);
        }
        $borrower_id = $borr_serv->create($borrower, true, $cust_reg_json);
        if ($borrower_id) {
            return $this->respondCreated("New borrower created successfully", $borrower_id);
        } else {
            return $this->respondInternalError("Unknown Error");
        }
    }

    public function dup_check_cust(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $dup_check_person = ['mobile_num', 'national_id', 'first_name', 'middle_name', 'last_name'];
        $dup_check_cust = ['biz_name', 'acc_number'];
        $result = [];

        $cust_id = null;
        if (isset($data['lead_id'])) {
            $lead_repo = new LeadRepositorySQL();
            $cust_id = $lead_repo->get_rekyc_lead_cust_id($data['lead_id']);
        }

        if (has_any($dup_check_cust, array_keys($data['req']))) {
            $result = $rm_serv->dup_check_cust($data['req'], $cust_id);
        } else if (has_any($dup_check_person, array_keys($data['req']))) {
            $result = $rm_serv->dup_check_person($data['req'], $cust_id);
        }
        return $this->respondData($result);
    }


    public function get_visit_schedules(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $data['visitor_id'] = session('user_person_id');
        $schedules = $rm_serv->get_visits_schedules($data);
        return $this->respondData($schedules);
    }

    public function extract_text(Request $request)
    {
        $data = $request->data;
        $data["file_data_type"] = "data-url";
        $rm_serv = new RMService();

        $result = $rm_serv->extract_text_details_from_card($data);

        if (isset($result) && array_key_exists("err_msg", $result)) {
            return $this->respondWithError($result['err_msg']);
        } else {
            return $this->respondData($result);
        }
    }

    public function remove_file(Request $request)
    {
        $data = $request->data;
        $file_serv = new FileService();
        if(isset($data['rm_photo_pps'])){
            $data['entity_id'] = session('user_person_id');
            $data['entity_type'] = 'persons'; 
        }
        $resp = $file_serv->remove_file($data);
        if ($resp['is_in_folder'] == 1 || $resp['is_in_table'] == 1) {
            return $this->respondWithMessage("File removed successfully");
        } else {
            return $this->respondWithError("No such file exists.");
        }
    }

    public function file_upload(Request $request)
    {
        $data = $request->data;
        $data["file_data_type"] = "data-url";
        $rm_serv = new RMService();
        $result = $rm_serv->file_upload($data);
        return $this->respondData($result);
    }

    public function stmt_upload(Request $request)
    {
        $data = $request->data;
        $data["file_data_type"] = "data-url";
        $rm_serv = new RMService();
        $result = $rm_serv->stmt_upload($data);
        return $this->respondData($result);
    }


    public function stmt_remove(Request $request) {
        $data = $request->data;
        $rm_serv = new RMService();
        $resp = $rm_serv->stmt_remove($data);
        if($resp['is_in_json' ] == 1 && $resp['is_in_folder'] == 1) {
            return $this->respondWithMessage("File removed successfully");
        } else {
            return $this->respondWithError("No such file exists.");
        }
    }

    public function file_process(Request $request){
        $data = $request->data;
        $rm_serv = new RMService();
        $result = $rm_serv->file_process($data['lead_id']);

        if ($result) {
            return $this->respondWithMessage("File process started...");
        } else {
            return $this->respondWithError("Unable to upload file for processing");
        }
    }

    public function get_address_config(Request $request)
    {
        $data['country_code'] = $request->country_code;
        $addr_info_repo = new AddressInfoRepositorySQL();
        $result["addr_config"] = $addr_info_repo->get_addr_config_list(false, $data);
        return $this->respondData($result);
    }

    public function reschedule_visit(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $result = $rm_serv->reschedule_visit($data);
        $message = "Visit rescheduled successfully";
        return $this->respondData($result, $message);
    }

    public function checkin(Request $request)
    {
        $data = $request->data;
        $field_serv = new FieldVisitService();
        $sch_data['checkin_req'] = $data;
        $resp = $field_serv->submit_checkin($sch_data);
        return $this->respondData($resp);
    }


    public function get_home_data(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $data['visitor_id'] = session('user_person_id');
        $result = $rm_serv->get_home_data($data);
        return $this->respondData($result);
    }

    public function get_dashboard_data(Request $request, $month_num)
    {
        Log::warning(array($request->month_num));
        $data = $request->data;
        $data['month'] = $month_num;
        $rm_serv = new RMService();
        $data['visitor_id'] = session('user_person_id');
        $result = $rm_serv->get_dashboard_update($data);
        return $this->respondData($result);
    }

    public function visit_checkout(Request $request)
    {
        $data['checkout_req'] = $request->data;
        $field_serv = new FieldVisitService();
        $result = $field_serv->submit_checkout($data);
        $data['checkout_req']['sch_status'] = "checked_out";
        if ($result['action'] == "to_success") {
            return $this->respondData($data['checkout_req'], $result['message']);
        } else if ($result['action'] == "show_error") {
            return $this->respondWithError("Please ensure all the visit activities are performed before you checkout. You have checked-in just {$result["min_diff"]} minutes ago");
        }
    }

    public function cancel_schedule(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $result = $rm_serv->cancel_schedule($data);
        return $this->respondData($result);
    }


    public function create_schedule(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $result = $rm_serv->create_schedule($data);
        return $this->respondData($result);
    }

    public function get_fas_by_status(Request $request)
    {

        $data['status'] = $request->status;
        $data['flow_rel_mgr_id'] = session('user_person_id');
        $rm_serv = new RMService();
        $result = $rm_serv->get_fas_by_status($data);
        return $this->respondData($result);
    }
    public function get_fas_by_criteria(Request $request)
    {
        $criteria = $request->criteria;
        $rm_serv = new RMService();
        $result = $rm_serv->get_fas_by_criteria($criteria);
        return $this->respondData($result);
    }

    public function get_cust_by_criteria(Request $request)
    {
        $criteria = $request->criteria;
        $rm_serv = new RMService();
        $result = $rm_serv->get_cust_by_criteria($criteria);
        return $this->respondData($result);
    }


    public function loan_approval(Request $request)
    {
        $data = $request->data;
        $data['credit_score'] = 0;
        $rm_serv = new RMService();
        $result = $rm_serv->loan_approval($data);
        return $this->respondData($result);
    }

    public function condone_customer(Request $request)
    {
        $data = $request->data;
        $borr_serv = new BorrowerService();
        $result = $borr_serv->allow_condonation($data);
        return $this->respondData($result);
    }

    public function get_address_dropdown(Request $request)
    {
        $data = $request->data;
        $common_repo = new CommonRepositorySQL();
        $data["country_code"] = session("country_code");
        $response = $common_repo->get_master_data($data);
        return $this->respondData($response);
    }

    public function get_partner_rm_dropdown(Request $request)
    {
        $data = $request->data;
        $data["status"] = "enabled";
        $data["associated_with"] = "acc_prvdr";
        $data["associated_entity_code"] = session("acc_prvdr_code");
        $data["country_code"] = session("country_code");
        $relationship_manager_repo = new RelationshipManagerRepositorySQL();
        $response = $relationship_manager_repo->show_name_list($data);
        return $this->respondData($response);
    }

    public function get_cal_days(Request $request)
    {
        $data = $request->data;
        $data['visitor_id'] = session('user_person_id');
        $rm_serv = new RMService();
        $resp = $rm_serv->get_cal_days($data);
        return $this->respondData($resp);
    }

    public function sign_agreement(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $resp = $rm_serv->sign_agreement($data);
        $message = "Agreement signed successfully";
        return $this->respondData($resp, $message);
    }

    public function get_agrmt_to_sign(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $resp = $rm_serv->get_agrmt_to_sign($data);
        return $this->respondData($resp);
    }


    public function list_call_logs(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $resp = $rm_serv->list_call_logs($data);
        return $this->respondData($resp);
    }
    public function do_call_log(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $resp = $rm_serv->do_call_log($data);
        return $this->respondData($resp);
    }
    public function cust_evaluation(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $resp = $rm_serv->cust_evaluation($data);
        return $this->respondData($resp);
    }

    public function update_cust_profile(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $resp = $rm_serv->update_cust_profile($data);
        if ($resp) {
            return $this->respondSuccess("Borrower updated successfully");
        } else {
            return $this->respondInternalError("Unknown Error");
        }
    }
    public function get_acc_prvdr(Request $request)
    {
        $data = $request->data;
        $data["status"] = "enabled";
        $data['biz_account'] = true;
        $acc_prvdr_repo = new AccProviderRepositorySQL();
        $resp = $acc_prvdr_repo->list($data);
        return $this->respondData($resp);
    }

    public function list_data_prvdrs(Request $request)
    {
        $data = $request->data;
        $data["status"] = "enabled";
        $rm_serv = new RMService();
        $resp = $rm_serv->list_data_prvdrs($data);
        // $data_prvdr_repo = new DataProviderRepositorySQL();
        // $resp = $data_prvdr_repo->list($data);

        return $this->respondData($resp);
    }

    public function get_last_n_fas(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $data['flow_rel_mgr_id'] = session('user_person_id');
        $data['mode'] = "search";
        $data['last_n_fas'] = 5;
        $result = $rm_serv->get_last_n_fas($data);
        return $this->respondData($result);
    }

    public function cust_reg_checkin(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $result = $rm_serv->cust_reg_checkin($data);
        return $this->respondData($result);
    }

    public function cust_reg_checkout(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $result = $rm_serv->cust_reg_checkout($data);
        return $this->respondWithMessage($result);
    }
    public function create_lead(Request $request)
    {
        $data = $request->data;
        $lead_serv = new LeadService();
        m_array_filter($data['lead']);
        $data['lead']['flow_rel_mgr_id'] = session('user_person_id');
        $check_validate = FlowValidator::validate($data, array("lead"), __FUNCTION__);
        if (is_array($check_validate)) {
            return $this->respondValidationError($check_validate);
        }
        $lead_id = $lead_serv->create_lead($data);
        if ($lead_id) {
            return $this->respondCreated("Lead created successfully", $lead_id);
        } else {
            return $this->respondInternalError("Unknown Error");
        }
    }

    public function search_lead(Request $request)
    {
        $data = $request->data;
        $lead_serv = new LeadService();
        $data['flow_rel_mgr_id'] = session('user_person_id');
        $result = $lead_serv->search_lead($data);
        return $this->respondData($result);
    }

    public function view_lead(Request $request)
    {
        $data = $request->data;
        $lead_serv = new LeadService();
        $result = $lead_serv->view_lead($data);
        return $this->respondData($result);
    }

    public function update_lead(Request $request)
    {
        $data = $request->data;
        $lead_serv = new LeadService();
        $result = $lead_serv->update($data);
        if ($result) {
            return $this->respondSuccess("Lead updated successfully");
        } else {
            return $this->respondInternalError("Unknown Error");
        }
    }




    public function send_otp_to_mobile_num(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $result = $rm_serv->send_otp_to_mobile_num($data);
        return $this->respondWithMessage($result);
    }

    public function send_kyc_otp_to_mobile_num(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $result = $rm_serv->send_kyc_otp_to_mobile_num($data);
        return $this->respondWithMessage($result);
    }

    public function verify_mobile_num_field(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $result = $rm_serv->verify_mobile_num_field($data);
        return $this->respondData($result);
    }


    public function verify_kyc_mobile_num_field(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $result = $rm_serv->verify_kyc_mobile_num_field($data);
        return $this->respondData($result);
    }

    public function get_tf_products()
    {
        $rm_serv = new RMService();
        $result = $rm_serv->prdctList();
        return $this->respondData($result);
    }


    // public function submit_kyc(Request $request)
    // {
    //     $data = $request->data;
    //    if($data['action'] == "submit"){
    //     $cust_reg_serv = new CustomerRegService();
    //     $cust_reg_arr = $data['cust_reg_json'];
    //     $borrower = $cust_reg_arr;
    //     flatten_borrower($borrower);

       

    //     $borrower['country_code'] = session('country_code');
    //     $borrower = array_merge($borrower,$borrower['biz_info']);
    //     $borrower['cr_owner_person'] = $borrower['owner_person'];
    //     $borrower['cr_account'] = $borrower['account'];
        
    //     $validation_keys = $cust_reg_serv->get_validation_keys($borrower);

    //     $check_validate = FlowValidator::validate($borrower, $validation_keys ,__FUNCTION__);

    //     foreach($borrower['addl_num'] as $addl_num){
    //         $new_arr['cr_addl_num'] = $addl_num;
    //         $new_arr['country_code'] = session("country_code");
    //         $check_validate .= FlowValidator::validate($new_arr, ['cr_addl_num'] ,__FUNCTION__);
    //     }


    //     if(is_array($check_validate))
    //     {
    //         return $this->respondValidationError($check_validate); 
    //     }
    //     }
    //     $rm_serv = new RMService();
    //     $resp = $rm_serv->submit_kyc($data);

    //     if(isset($resp['is_updated'])){
    //         return $this->respondWithMessage($resp['message']);
    //     }else{
    //         return $this->respondWithError("Unable to update KYC");
    //     }
    // }


    public function submit_kyc(Request $request)
    {
        $data = $request->data;
        if($data['action'] == "submit"){
            $cust_reg_serv = new CustomerRegService();
            $cust_reg_arr = $data['cust_reg_json'];
            $borrower = $cust_reg_arr;
            flatten_borrower($borrower);

            $borrower['country_code'] = session('country_code');
            $borrower = array_merge($borrower,$borrower['biz_info']);
            $borrower['cr_owner_person'] = $borrower['owner_person'];
            $borrower['cr_account'] = $borrower['account'];
            
            $validation_keys = $cust_reg_serv->get_validation_keys($borrower);
            $check_validate = FlowValidator::validate($borrower, $validation_keys ,__FUNCTION__);
            if(is_array($check_validate)){
                return $this->respondValidationError($check_validate); 
            }
        }
        $rm_serv = new RMService();
        $message = $rm_serv->submit_kyc($data);
        return $this->respondWithMessage($message);
    }
    

    public function close_lead(Request $req)
    {
        $data = $req->data;
        return $this->respondWithMessage("Please request the operations team/COO to close the lead");
        // $lead_serv  = new LeadService();
        // $result = $lead_serv->close_lead($data);
        // if($result){
        //     return $this->respondSuccess("This Lead profile has been closed successfully");
        // }else{
        //     return $this->respondInternalError("Unable to close the Lead Profile");
        // }
    }

    public function delete_agreement(Request $req)
    {
        $data = $req->data;
        $rm_serv = new RMService();
        $result = $rm_serv->delete_agreement($data);
        if ($result) {
            return $this->respondSuccess("Agreement has been deleted successfully");
        } else {
            return $this->respondInternalError("Unable to delete the Agreement");
        }
    }

    public function update_status(Request $request)
    {
        $data = $request->data;
        $lead_serv = new LeadService();
        $message = $lead_serv->update_pos_status($data);
        return $this->respondWithMessage($message);
    }

    public function add_location(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $result = $rm_serv->add_location($data);
        if ($result) {
            return $this->respondSuccess("Location has been added successfully");
        } else {
            return $this->respondInternalError("Unable to add the location");
        }
    }

    public function get_nearby_cust(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $result = $rm_serv->get_nearby_cust($data);
        return $this->respondData($result);
    }

    public function view_data_consent(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $result = $rm_serv->view_data_consent($data);
        return $this->respondData($result);
    }

    public function sign_data_consent(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $result = $rm_serv->sign_data_consent($data);
        if ($result) {
            return $this->respondSuccess("Data consent agreement created successfully.");
        } else {
            return $this->respondInternalError("Unable to Sign the Data Consent");
        }
    }

    public function allow_pre_approval(Request $request)
    {
        $data = $request->data;
        $loan_appl_serv = new LoanApplicationService();
        $result = $loan_appl_serv->allow_cust_pre_approval($data);
        if ($result) {
            return $this->respondSuccess("Pre-approval allowed for this customer successfully.");
        } else {
            return $this->respondWithError("Pre-approval not allowed");
        }
        return $result;
    }

    public function remove_pre_approval(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $result = $rm_serv->remove_pre_approval($data);
        if ($result) {
            return $this->respondSuccess("Pre-approval removed successfully.");
        } else {
            return $this->respondWithError("Unable to remove pre-approval");
        }
        return $result;
    }



    public function fa_upgrade_approval(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $result = $rm_serv->fa_upgrade_approval($data);
        if ($result) {
            return $this->respondSuccess("Upgrade FA request {$result['status']} successfully");
        } else {
            return $this->respondWithError("unable to {$result['status']} FA upgrade request");
        }
        return $result;
    }

    public function list_fa_upgrade_requests(Request $request)
    {
        $data = $request->data;
        $rm_serv = new RMService();
        $result = $rm_serv->list_fa_upgrade_requests($data);

        return $this->respondData($result);
    }


    public function view_rm_target(Request $request){
        $data  = $request->data;
        session()->put('acc_prvdr_code','ALL');
        $rm_serv = new RMManagementService();
        $result = $rm_serv->view_targets($data);
        return $this->respondData($result);  
    }

    public function update_rm_target(Request $request){
       
        $data = $request->data;
        $rm_serv = new RMManagementService;
        $result = $rm_serv->assign_target($data['data']);
        return $this->respondData($result);
    }
    public function add_remarks(Request $req){
        $data = $req->data;
        $lead_serv = new LeadService();
        $result = $lead_serv->add_remarks($data);
        return $this->respondData($result);
    }

    public function update_third_party_owner(Request $request){
        $data = $request->data;
        $rm_serv = new RMService();
        $result = $rm_serv->update_third_party_owner($data);
       
        return $this->respondData($result);    
    }

    public function list_tasks(Request $request){
        $data = $request->data;
        $task_serv = new TaskService();
        $result = $task_serv->list_tasks($data);
        return $this->respondData($result);    
    }

    public function list_task_counts(Request $request){
        $data = $request->data;
        $rm_serv = new RMService();
        $result = $rm_serv->list_task_counts($data);
        return $this->respondData($result);    
    }

    public function task_approval(Request $request){
        $data = $request->data;
        $task_serv = new TaskService();
        $result = $task_serv->task_approval($data);
       
        if($result){
            return $this->respondSuccess(dd_value($data['task_type'])." {$result['status']} successfully");  
        }else{
            return $this->respondWithError("unable to {$result['status']} ".dd_value($data['task_type']));
        }    
    }

    public function rm_cur_location(Request $req){
        $data = $req->data;
        $serv = new RMService;
        $resp = $serv-> rm_cur_location($data);
        return $this->respondData($resp);
    }

    public function punch_out(Request $req){
        $data = $req->data;
        $serv = new RMService;
        $resp = $serv->punch_out($data);
        return $this->respondData($resp);
    }

    public function update_addl_num_field(Request $req){
        $data = $req->data;
        $serv = new RMService;
        $resp = $serv->update_addl_num_field($data);
        if ($resp['is_update']) {
            return $this->respondSuccess("Additional number {$resp['action']} successfully.");
        }else {
            return $this->respondWithError("Unable to {$resp['action']} additional number");
        }   
    }

    public function get_rm_routes(Request $req){
        $data = $req->data;
        $serv = new RMService;
        $resp = $serv->get_rm_routes($data);
        return $this->respondData($resp);
    }
}
