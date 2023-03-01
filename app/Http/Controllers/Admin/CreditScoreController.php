<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\CSModelWeightages;
use Log;
use Illuminate\Support\Facades\Validator;
use App\Validators\FlowValidator;

class CreditScoreController extends ApiController
{

	protected $credit_score_repo;
	public function __construct(){
			//$this->credit_score_repo = new CreditScoreRepositorySQL();
		$this->cs_model_weightages = new CSModelWeightages;

	}

	public function list_cs_weightages(Request $req){
		$data = $req->data;
		//$cs_weightages = $this->credit_score_repo->list_cs_weightages($data);
		$cs_weightages = $this->cs_model_weightages->list_cs_weightages($data);
		return $this->respondData($cs_weightages);
	}

	public function update_cs_weightages(Request $req){
		$data = $req->data;
		$update_cs_weightages = $this->cs_model_weightages->update_cs_weightages($data['cs_weightages']);

		
		return $this->respondData($update_cs_weightages);

		/*if ($update_cs_weightages) {
            return $this->respondSuccess("CS Weightage updated successfully");
        }else{
            return $this->respondInternalError("Unknown Error");
        }*/
	}

	public function create(Request $req){
		$data = $req->data;

		/*$check_validate = FlowValidator::validate($data, array("cs_model_weightage"),__FUNCTION__);
 
        if(is_array($check_validate))
        {
            return $this->respondValidationError($check_validate); 
            
        }*/
		
        $cs_model_id = $this->cs_model_weightages->create($data['cs_model_weightage']);
         	
        if ($cs_model_id) {
             return $this->respondCreated("New Factor added successfully", $cs_model_id);
        }else{
            return $this->respondInternalError("Unknown Error");

        }
	}

}