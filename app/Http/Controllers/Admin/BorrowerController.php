<?php
namespace App\Http\Controllers\Admin;
use App\Repositories\SQL\LeadRepositorySQL;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Repositories\SQL\BorrowerRepositorySQL;
use Illuminate\Support\Facades\Validator;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\CustAgreementRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Services\BorrowerService;
use App\Services\CustomerRegService;
use App\Services\Mobile\RMService;
use App\Services\CommonService;
use App\Repositories\SQL\PersonRepositorySQL;
//use App\Http\Requests\BorrowerValidation;
use App\Models\Borrower;
use App\Validators\FlowValidator;
use App\Consts;
use Illuminate\Support\Facades\Log;
use App\Mail\FlowCustomMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class BorrowerController extends ApiController
{
	public function create(Request $req){
		$data = $req->data;
		#$data['borrower']['biz_type'] = $data['borrower']['borrower_type'];
        $data['borrower']['biz_type'] = "individual";
		$this->borrower_service = new BorrowerService($data['country_code']);

		$validation_keys = $this->borrower_service->get_validation_keys($data['borrower']);
		$check_validate = FlowValidator::validate($data, $validation_keys ,__FUNCTION__);
		if(is_array($check_validate))
		{
			return $this->respondValidationError($check_validate); 
		}
		$borrower_id = $this->borrower_service->create($data['borrower']);
         	
        if ($borrower_id) {
             return $this->respondCreated("New borrower created successfully", $borrower_id);
        }else{
            return $this->respondInternalError("Unknown Error");
        }
	}

    public function reg_cust(Request $request)
    {
        $data = $request->data;
        $lead_repo = new LeadRepositorySQL;
        $lead_data = $lead_repo->find($data['lead_id'],['cust_reg_json','flow_rel_mgr_id','acc_purpose','id', 'type', 'cust_id','kyc_reason','audited_by']);
        if($lead_data->audited_by != session('user_person_id')){
            $audited_name = (new PersonRepositorySQL)->full_name($lead_data->audited_by);
            $resp = thrw("You are not able to do any actions for this lead because this lead has already been initiated to {$audited_name}");
        }
        $cust_reg_json = json_decode($lead_data->cust_reg_json,true);
        $borrower = $cust_reg_json;
        $borrower['holder_name_mismatch_reason'] = $data['holder_name_mismatch_reason'] ?? null;
        $borr_serv = new CustomerRegService();
        flatten_borrower($borrower);
        $validation_keys = $borr_serv->get_validation_keys($borrower);
        $borr_serv->append_borrower_obj($borrower, $lead_data);
        $check_validate = FlowValidator::validate($borrower, $validation_keys ,__FUNCTION__);
        if(is_array($check_validate))
        {
            return $this->respondValidationError($check_validate); 
        }
        if($cust_reg_json && isset($cust_reg_json['account']['holder_name'])){
            (new CustomerRegService())->holder_name_evidence_verification_mail($cust_reg_json, $data['lead_id'], isset($borrower['third_party_owner']));
        }
        if($lead_data->type == 're_kyc'){
            $borrower['cust_id'] = $lead_data->cust_id;
            $holder_name = isset($cust_reg_json['account']['holder_name']) ? $cust_reg_json['account']['holder_name'] : null;
            $borr_serv->sync_rekyc_with_cust_profile($borrower,$lead_data->kyc_reason, true, $holder_name);
            return $this->respondSuccess("Details in current KYC process have been synced successfully with the customer profile");
        }
        else{
            $borrower_id = $borr_serv->create($borrower,true,$cust_reg_json);
            if($borrower_id)
            {
                return $this->respondCreated("A new customer profile has been created successfully based on the KYC details", $borrower_id);
            }
            else
            {
               return $this->respondInternalError("Unknown Error");
            }
        }
    }

	public function view(Request $request){
        $data = $request->data;
        $borrower_service = new BorrowerService($data['country_code']);
        $borrower = $borrower_service->view($data);
        return $this->respondData($borrower);
    }
    public function get_third_party_details(Request $request){
        $data=$request->data;
        $borrower_service = new BorrowerService();
        $thirdparty_details=$borrower_service->get_third_party_details($data);
        return $this->respondData($thirdparty_details);

    }

    public function update(Request $request){
        $data = $request->data;
        
		$check_validate = FlowValidator::validate($data, array("borrower"),__FUNCTION__);
        if(is_array($check_validate))
        {
            return $this->respondValidationError($check_validate);        
        }
        $borrower_service = new BorrowerService($data['country_code']);
        $update_status = $borrower_service->update($data['borrower']);
        //$brwr_repo = new BorrowerRepositorySQL();
        //$update_status = $brwr_repo->update($data['borrower']);
        if($update_status){
            return $this->respondSuccess("Borrower updated successfully");
        }else{

            return $this->respondInternalError("Unknown Error");
        }
   }

   /*public function list(Request $req){
        $data = $req->data;
        $brwr_repo = new BorrowerRepositorySQL();
        $borrowers = $brwr_repo->list($data);
        return $this->respondData($borrowers);
    }*/



	public function add_account(Request $req)
	{
		$data = $req->data;
		$check_validate = FlowValidator::validate($data, array("borrower_account"),"create");
		if(is_array($check_validate))
		{
			return $check_validate;	
		}
		$acc_repo = new AccountRepositorySQL();
		$res = $acc_repo->create($data['borrower_account']);
		return $this->respond([
			"status" => "Success",
			"status_code" => $this->getStatusCode(),
			"message" => "Account Created successfully"
		]);		
	}


	


    public function borrower_search(Request $req)
    {
       $data = $req->data;
       
       $borrower_serv = new BorrowerService($data['country_code']);
    //    $borrowers['results'] = $borrower_serv->borrower_search($data['borrower_search_criteria']);
        $borrowers = $borrower_serv->borrower_search($data['borrower_search_criteria']);
       return $this->respondData($borrowers);
    }

    public function bring_to_probation(Request $req)
    {
    	$data = $req->data;
    	$borrower_serv = new BorrowerService($data['country_code']);
       	$borrower_serv->allow_condonation($data['cust_id']);
        $condonation_delay = config('app.condonation_punishment_delay');
        return $this->respondSuccess("Customer has been condonated and after a waiting period of {$condonation_delay} days, FA can be applied for restricted FA products ");

    }

	public function update_status(Request $req){
        
		$data = $req->data;
       
		if($data['status'] == 'enabled'){
			$serv = new BorrowerService();
			$serv->check_kyc_status($data);
		}        
        $role_codes = array("operations_manager","market_admin", 'super_admin', 'ops_admin', 'it_admin', 'operations_auditor');
        if(in_array(auth()->user()->role_codes,$role_codes)){  
            $borrower_serv = new BorrowerService();
            $update_status = $borrower_serv->update_status($data);
            return $this->respondData($update_status);
        }
        else{
            thrw("You cannot change the status");
        }
    }

    public function validate_customer(Request $req){
        
    	$data = $req->data;
        
    	$serv = new BorrowerService();
    	$result  = $serv->validate_customer($data);
    	return $this->respondData($result);
    }
   
    public function get_borrower_profile(Request $req){
        $data = $req->data;
        $serv = new BorrowerService();
        $incl_addr = false;
        $incl_rel_mgr = false;
        $incl_fa = false;
        if(array_key_exists('incl_addr', $data)){
            $incl_addr = $data['incl_addr'];
        }
        if(array_key_exists('incl_rel_mgr', $data)){
            $incl_rel_mgr = $data['incl_rel_mgr'];
        }
        if(array_key_exists('incl_fa', $data)){
            $incl_fa = $data['incl_fa'];
        }

        $result  = $serv->get_borrower_profile($data, $incl_addr, $incl_rel_mgr,$incl_fa);
        return $this->respondData($result);


    }

    public function close_profile(Request $req){
        $data = $req->data;
        
    	$borr_serv = new BorrowerService();
    	$result  = $borr_serv->close_profile($data);
        return $this->respondWithMessage("Customer Profile closed successfully");	
    }

   public function set_cust_app_access(Request $request){
        $data = $request->data;

       $borr_serv = new BorrowerService();
       $resp = $borr_serv->set_cust_app_access($data['cust_id'],$data['status']);
        return $this->respondWithMessage($resp);
   }

   public function list_pre_appr_customers(Request $req)
   {
       $data = $req->data;
       $borr_serv = new BorrowerService();
       $result = $borr_serv->list_pre_appr_customers($data);
       return $this->respondData($result);
   }

   public function remove_pre_approval(Request $request){
       $data = $request->data;
       $rm_serv = new RMService();
       $result = $rm_serv->remove_pre_approval($data);
       if($result){
           return $this->respondSuccess("Pre-approval removed successfully.");  
       }else{
           return $this->respondWithError("Unable to remove pre-approval");
       }  
       return $result;
   }

   public function allow_manual_capture(Request $req)
   {
       $data = $req->data;
       $borr_serv = new BorrowerService();
       $result = $borr_serv->allow_manual_capture($data);
       return $this->respondWithMessage("National ID Manual Capture allowed.");
    }
    
}

