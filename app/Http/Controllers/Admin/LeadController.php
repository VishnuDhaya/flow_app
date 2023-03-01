<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Repositories\SQL\LeadRepositorySQL;
use App\Services\LeadService;
use App\Services\Mobile\RMService;
use App\Validators\FlowValidator;
use Illuminate\Support\Facades\Log;


class LeadController extends ApiController
{
    public function create_lead(Request $req){
        $data = $req->data;
        if(array_key_exists('gps',$data['lead'])){
            $gps_arr = explode(",",$data['lead']['gps']);
            if(count($gps_arr) > 1){
                $data['lead']['latitude'] = $gps_arr[0];
                $data['lead']['longitude'] = $gps_arr[1];
            }
        }
        $check_validate = FlowValidator::validate($data, array("lead"), __FUNCTION__);
        if(is_array($check_validate)){
            return $this->respondValidationError($check_validate);
        }
        
        $lead_serv  = new LeadService();
        $lead_data = $lead_serv->create_lead($data);
        return $this->respondData($lead_data,"Lead created on Flowapp successfully");
    }

    public function view_lead(Request $req){
        $data = $req->data;
        $lead_serv  = new LeadService();
        $lead_data = $lead_serv->view_lead($data);
        return $this->respondData($lead_data);
    }

    public function update(Request $req){
        $data = $req->data;
        $lead_serv  = new LeadService();
        $result = $lead_serv->update($data);
        if($result){
            return $this->respondSuccess("Lead updated successfully");
        }else{
            return $this->respondInternalError("Unknown Error");
        }
    }

    public function bypass_holder_name_audit(Request $req){
        $data = $req->data;
        $lead_serv = new LeadService();
        $lead_serv->bypass_holder_name_audit($data);
        return $this->respondData('');
    }

    public function search_lead(Request $req){
        $data = $req->data;
        $lead_serv  = new LeadService();
        $result = $lead_serv->search_lead($data['lead_search_criteria']);
        return $this->respondData($result);
        
    }

    public function delete_lead(Request $req){
        $data = $req->data;
        $lead_serv  = new LeadService();
        $result = $lead_serv->delete_lead($data);
        if($result){
            return $this->respondSuccess("The Lead has been deleted successfully");
        }else{
            return $this->respondInternalError("Unable to delete the Lead");
        }
        
    }

    public function audit_name(Request $req){
        $data = $req->data;
        $lead_serv  = new LeadService();
        $result = $lead_serv->audit_name($data['acc_number'], session('acc_prvdr_code'), $data['branch']);
        return $this->respondData($result);
    }

    public function update_status(Request $req){
        $data = $req->data;
        $lead_serv  = new LeadService();
        $result = $lead_serv->update_status($data);
        return $this->respondSuccess("The KYC has been submitted to RM Successfully");
        
    }

    public function email_holder_name_proof_to_app_support(Request $req){
        $data = $req->data;
        $lead_serv = new LeadService();
        $result = $lead_serv->email_holder_name_proof_to_app_support($data);
        return $this->respondData('');

    }

    public function close_lead(Request $req){
        $data = $req->data;
        $lead_serv  = new LeadService();
        $result = $lead_serv->close_lead($data);
        return $this->respondSuccess("Lead has been closed Successfully");
    }

    public function allow_manual_capture(Request $req){
        $data = $req->data;
        $lead_serv = new LeadService();
        $result = $lead_serv->allow_manual_capture($data);
        return $this->respondWithMessage("RM is now allowed to capture the National ID manually for this lead.");
    }

    public function update_audited_by(Request $req){
        $data = $req->data;
        $lead_serv = new LeadService();
        $lead_id = $data['id'];
        $result = $lead_serv->update_audited_by($lead_id);
        return $this->respondWithMessage("Auditor name has been updated successfully");
    }

    public function reject_kyc(Request $req){
        $data = $req->data;
        $lead_serv  = new LeadService();
        $result = $lead_serv->reject_kyc($data);
        return $this->respondSuccess("Lead has been closed Successfully");
    }

    public function view_remarks(Request $req){
        $data = $req->data;
        $lead_serv = new LeadService();
        $lead_id = $data['id'];
        $result = $lead_serv->view_remarks($lead_id);
        return $this->respondData($result);
    }
    
    public function add_remarks(Request $req){
        $data = $req->data;
        $lead_serv = new LeadService();
        $result = $lead_serv->add_remarks($data);
        return $this->respondData($result);
    }

    public function stmt_upload(Request $request){
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

        if($result){
            return $this->respondWithMessage("File process started...");
        }else{
            return $this->respondWithError("Unable to upload file for processing");
        }
    }

    public function get_auditor_name_list(Request $req){
        $data = $req->data;
        $lead_serv = new LeadService;
        $result = $lead_serv->get_auditor_name_list();
        return $this->respondData($result);
    }

    public function assign_auditor(Request $req){
        $data = $req->data;
        $lead_serv = new LeadService;
        $result = $lead_serv->assign_auditor($data);
        return $this->respondWithMessage($result);
    }

    public function send_kyc_otp_mobile_num(Request $req){
        $data = $req->data;
        $rm_serv = new RMService() ;
        $result = $rm_serv->send_kyc_otp_to_mobile_num($data);
        return $this->respondWithMessage($result);

    }

    public function submit_call_log(Request $req){
        $data = $req->data;
        $rm_serv = new RMService() ;
        $result = $rm_serv->submit_call_log($data);
        return $this->respondWithMessage($result);

    }

    public function reject_call_log(Request $req){
        $data = $req->data;
        $rm_serv = new RMService() ;
        $result = $rm_serv->reject_call_log($data);
        return $this->respondWithMessage($result);

    }

    public function submit_kyc_for_audit(Request $req){
        $data = $req->data;
        $rm_serv = new RMService() ;
        $resp = $rm_serv->submit_mobile_num_verified_kyc($data);
        
        if(isset($resp['is_updated'])){
            return $this->respondWithMessage($resp['message']);
        }else{
            return $this->respondWithError("Unable to Submit KYC");
        }
    }

    


}
