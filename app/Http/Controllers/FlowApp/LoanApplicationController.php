<?php

namespace App\Http\Controllers\FlowApp;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Services\LoanApplicationService;
use App\Services\Mobile\CustAppService;
use App\Services\Mobile\RMService;

use App\Repositories\SQL\LoanApplicationRepositorySQL;
use Illuminate\Support\Facades\Validator;
use App\Models\LoanApplication;
use App\Validators\FlowValidator;
use App\Consts;
use App\Repositories\SQL\AccountRepositorySQL;
use Illuminate\Support\Facades\Log;
use Psy\Readline\Hoa\Console;

class LoanApplicationController extends ApiController
{   
    //$loan_appl_service = null;
    public function __construct()
    {
         $this->loan_appl_service = new LoanApplicationService();
    }
  
    public function validate_appl(Request $req)
    {
        $data = $req->data;
        $loan_agreement =  $this->loan_appl_service ->validate_appl($data);
        return $this->respondData($loan_agreement);
    }

    public function apply_loan(Request $req){
        
        $data = $req->data;
        $loan_appl =$data['loan_application'];
        
        // $check_validate = FlowValidator::validate($data , array("loan_application"));
        // if(is_array($check_validate))
        // {
        //     return $this->respondValidationError($check_validate); 
        // }
        $result = $this->loan_appl_service->apply_fa_by_product($loan_appl['cust_id'],$loan_appl['product_id'],$loan_appl['cust_acc_id']);
        return $this->respondData($result,$result['resp_msg']);

    }

    public function repeat_fa(Request $req){

        $data = $req->data;
        $result = $this->loan_appl_service->repeat_fa($data);
        return $this->respondData($result,$result['resp_msg']);

    }

    // public function product_search(Request $req){

    //     $data = $req->data;
    //     $products = $this->loan_appl_service->product_search($data);

    //     if(empty($products)){
    //             return $this->respondWithError("No Customer record found for the data entered");
    //     }else if(array_key_exists('borrower', $products) && $products["borrower"] && $products["loan_products"] == null){
    //             return $this->respondWithError("Customer is not entitled for a float advance product");
    //     }

    //     return $this->respondData($products);
    // }

    public function product_search(Request $req){

        $data = $req->data;
        $cust_app_serv = new CustAppService();
        $products = $cust_app_serv->get_fa_products($data);
        if(empty($products)){
                return $this->respondWithError("No Customer record found for the data entered");
        }else if(array_key_exists('borrower', $products) && $products["borrower"] && $products["loan_products"] == null){
                return $this->respondWithError("Customer is not entitled for a float advance product");
        }

        return $this->respondData($products);
    }

    public function request_fa_upgrade(Request $req){
        $data = $req->data;
        $cust_app_serv = new CustAppService();
        $response = $cust_app_serv->request_fa_upgrade($data['cust_id'], $data['requested_amount'], $data['acc_number'], session('acc_prvdr_code'));
        return $this->respondData($response);
    }

    public function request_fa_upgrade_status_web(Request $req){
        $data = $req->data;
        $cust_app_serv = new CustAppService();
        $response = $cust_app_serv->request_upgrade_status_web($data['cust_id']);
        return $this->respondData($response);
    }

    public function loan_appl_search(Request $req)
    {
        $data = $req->data;
       
        $loan_applications = $this->loan_appl_service->loan_appl_search($data['loan_appl_search_criteria']);
        return $this->respondData($loan_applications);
    }

     public function list_approvers(Request $req)
    {
        $data = $req->data;
        $approvers = $this->loan_appl_service->list_approvers($data);
        $approvers_result["list"] = $approvers;
        return $this->respondData($approvers_result);
    }

    public function get_application(Request $req)
    {
        $data = $req->data;

        $application = $this->loan_appl_service->get_application($data);
        return $this->respondData($application);
    }

   


     public function approval(Request $req){
        $data = $req->data;

        if($data['loan_request']['action'] == "approve"){
            $check_validate = FlowValidator::validate($data, array("loan_request"));

        }else if($data['loan_request']['action'] == "reject"){
            $data["loan_reject"] = $data['loan_request'];
            $check_validate = FlowValidator::validate($data, array("loan_reject"),"appl");
        }else{
            $data["loan_cancel"] = $data['loan_request'];
            $check_validate = FlowValidator::validate($data, array("loan_cancel"),"appl");
        }
        if(is_array($check_validate))
        {
            return $this->respondValidationError($check_validate);
        }
        $result = $this->loan_appl_service->approval($data['loan_request']);
        if ($result['status'] == 'approved') {
            return $this->respondData($result, "Float Advance successfully approved.\n".confirm_code_alert(session('country_code'))); 
        }
        else if($result['status'] == 'rejected'){ 
            return $this->respondData($result,"Float Advance successfully rejected");
        }
        else if($result['status'] == 'cancelled'){ 
            return $this->respondData($result,"Float Advance successfully cancelled");
        }
        else if($result['status'] == 'error'){
            return $this->respondWithError($result['status_message']);            
        }    
     }

}