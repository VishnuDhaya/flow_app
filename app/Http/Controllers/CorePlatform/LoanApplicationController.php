<?php

namespace App\Http\Controllers\CorePlatform;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Services\LoanApplicationService;
use App\Repositories\SQL\LoanApplicationRepositorySQL;
use Illuminate\Support\Facades\Validator;
use App\Models\LoanApplication;
use App\Validators\FlowValidator;
use App\Consts;
use App\Repositories\SQL\AccountRepositorySQL;
use Illuminate\Support\Facades\Log;
class LoanApplicationController extends ApiController
{
	public function __construct()
	{
	$this->loan_appl_service = new LoanApplicationService();
	}
  
   public function product_search(Request $req){
        $data = $req->data;
        $products = $this->loan_appl_service->product_search($data);
        if(empty($products)){
                return $this->respondWithError("No Customer record found for the data entered");
        }else if(array_key_exists('borrower', $products) && $products["borrower"] && $products["loan_products"] == null){
                return $this->respondWithError("Customer is not entitled for a float advance product");            
        }

        return $this->respondData($products);
    }

    public function apply_loan(Request $req){
        $data = $req->data;
        // $check_validate = FlowValidator::validate($data , array("loan_application"));
        // if(is_array($check_validate))
        // {
        //     return $this->respondValidationError($check_validate); 
        // }
        $loan_application = $this->loan_appl_service->apply_loan($data['loan_application'], true);
        $result['loan_application'] = $loan_application;
        if ($result) {
            return $this->respondData($result, 'Float Advance application submitted successfully');
        }else{ 
            return $this->respondInternalError("Unknown Error");
        }
    }
    public function get_current_application(Request $req)
    {
        $data = $req->data;
        $application = $this->loan_appl_service->get_application($data);
        return $this->respondData($application);  
    }

}