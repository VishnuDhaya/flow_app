<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Repositories\SQL\CreditScoreRepositorySQL;
use Illuminate\Support\Facades\Validator;
use App\Validators\FlowValidator;
use Log;
class LoanProductController extends ApiController
{

    public function create(Request $req){

        $data = $req->data;

        $check_validate = FlowValidator::validate($data, array("loan_product"), __FUNCTION__);

        if(is_array($check_validate))
        {
            return $this->respondValidationError($check_validate); 
            
        }

        $loan_product_repo = new LoanProductRepositorySQL();
        $is_created = $loan_product_repo->create($data['loan_product']);


        if ($is_created) {
            return $this->respondSuccess('Float Advance Product created successfully');
        }else{
            return $this->respondInternalError("Unknown Error");

        }


    }



    public function list(Request $req){
 
        $request = $req->data;
        if(isset($req->acc_prvdr_code)){
            $request['acc_prvdr_code'] = $req->acc_prvdr_code;
        }

        $loan_product_repo = new LoanProductRepositorySQL();
        $products = $loan_product_repo->show_list($request);
    
        return $this->respondData($products);
    
    }

    public function view(Request $request){
  
        $data = $request->data;
        $loan_product_repo = new LoanProductRepositorySQL();
        $relationship_manager = $loan_product_repo->find($data['product_id']);

        return $this->respondData($relationship_manager);

   }

    public function update(Request $request){
        $data = $request->data;
        //dd($data);
        $check_validate = FlowValidator::validate($data, array("loan_product"),__FUNCTION__);

        if(is_array($check_validate))
        {
            return $this->respondValidationError($check_validate); 
            
        }
        $loan_product_repo = new LoanProductRepositorySQL();
        $update = $loan_product_repo->update($data['loan_product']);
        
        if($update){
            return $this->respondSuccess("Loan Product Details updated successfully");
        }else{

            return $this->respondInternalError("Unknown Error");
        }

   }
  
  public function get_score_model(Request $request)
  {
         $request = $request->data;
        $credit_score_repo = new CreditScoreRepositorySQL();
        $credit_score['list'] = $credit_score_repo->get_credit_score($request);
        return $this->respondData($credit_score);
  }
}
