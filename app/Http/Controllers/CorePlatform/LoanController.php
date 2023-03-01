<?php

namespace App\Http\Controllers\CorePlatform;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Consts;
use App\Services\LoanService;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Services\Schedule\BackgroundService;
use Illuminate\Support\Facades\Log;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\LoanCommentRepositorySQL;
use Illuminate\Support\Facades\Validator;
use App\Validators\FlowValidator;
class LoanController extends ApiController
{
	 public function __construct()
    {
         $this->loan_service = new LoanService();
    }
  	
	public function getproductsummary(Request $req)
	{
	        $data = $req->data;
	        $product_summary = $this->loan_service->getproductsummary($data['product_id']);
	        return $this->respondData($product_summary);  
	}
	public function loan_search(Request $req)
    {
        $data = $req->data;
        $loans["results"] = $this->loan_service->get_loan_search($data['cust_id']); 
        return $this->respondData($loans);     
    }

 public function get_loan(Request $req)
    {
        $data = $req->data;
        $loan["loan"] = $this->loan_service->get_loan($data);
        return $this->respondData($loan);  
    }
    public function current_loan_search(Request $req)
    {
        $data = $req->data;
        $loans["results"] = $this->loan_service->get_current_loan_search($data); 
        return $this->respondData($loans);
    }
    public function getloanproduct(Request $req)
    {
         $data = $req->data;
        $loans["results"] = $this->loan_service->getloanproduct($data['product_id']); 
        return $this->respondData($loans);
    }
}