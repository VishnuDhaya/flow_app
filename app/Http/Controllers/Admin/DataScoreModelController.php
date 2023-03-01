<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Services\DataScoreModelService;
use Log;

class DataScoreModelController extends ApiController
{
	public function __construct(){
		$this->score_model_serv = new DataScoreModelService();
	}
    public function create(Request $req){
        $data = $req->data;
        $score_model_id = $this->score_model_serv->create_score_model($data);
        if ($score_model_id) {
             return $this->respondCreated("New Score Model created successfully", $score_model_id);
        }else{
            return $this->respondInternalError("Unknown Error");
        }
    }


    public function list(Request $req){

        $data = $req->data;
        $score_models = $this->score_model_serv->list($data);
        return $this->respondData($score_models);
      
    }

	public function create_cs_results(Request $req){
		$data = $req->data;
        $cs_result_config_id = $this->score_model_serv->create_cs_results($data['cs_result']);
        return $this->respondData($cs_result_config_id);
	}

    public function get_cs_result_config(Request $req){
        $data = $req->data;
        $result['cs_result_config_details'] = $this->score_model_serv->get_cs_result_config($data);
        return $this->respondData($result);
    }

    public function create_cs_factor(Request $req){
        $data = $req->data;  
        $cs_model_id = $this->score_model_serv->create_cs_factor($data['cs_model_weightage']);
        if ($cs_model_id) {
             return $this->respondCreated("New Factor added successfully", $cs_model_id);
        }else{
            return $this->respondInternalError("Unknown Error");
        }
    }

    public function list_cs_weightages(Request $req){
        $data = $req->data;
        $cs_weightages = $this->score_model_serv->list_cs_weightages($data);
        return $this->respondData($cs_weightages);
    }

    public function update_cs_weightages(Request $req){
        $data = $req->data;
        $update_cs_weightages = $this->score_model_serv->update_cs_weightages($data['cs_weightages']);
        return $this->respondData($update_cs_weightages);
    }

    public function get_filtered_csfs(Request $req){
        $data = $req->data;   
        $filtered_csfs = $this->score_model_serv->get_filtered_csfs($data);
        return $this->respondData($filtered_csfs);
    }
    public function upload_cust_txns(Request $req)
    {
        $data = $req->data;
        $score_factor_id = $this->score_model_serv->upload_cust_txns($data['customer_factor'], session('acc_prvdr_code'));

        if ($score_factor_id) {
             return $this->respondCreated("Success", $score_factor_id);
        }else{
            return $this->respondInternalError("Unknown Error");
        }
    }

    public function create_cust_csf_values(Request $req){
        $data = $req->data;
      
        $csf_values_id = $this->score_model_serv->create_cust_csf_values($data);
        
        if($csf_values_id) {
             return $this->respondCreated("Score Factors saved successfully", $csf_values_id);
        }else{
            return $this->respondInternalError("Unknown Error");
        }
    }

    public function get_score_eligibility(Request $req){
        $data = $req->data;
        $result = $this->score_model_serv->get_score_eligibility($data);
        return $this->respondData($result);
    }

    public function get_cs_model_factor_info(Request $req){
        $data = $req->data;
        $result = $this->score_model_serv->get_cs_model_factor_info($data['cs_model_code']);
        return $this->respondData($result);
    }

    public function get_scoring_model(Request $req){
        $data = $req->data;
        $result = $this->score_model_serv->get_scoring_model($data['acc_number']);
        return $this->respondData($result);
    }

    public function calculate_score_and_insert_csf_values(Request $req){
        $data = $req->data;
        $result = $this->score_model_serv->calculate_score_and_insert_csf_values($data);
        return $this->respondData($result);
    }
}
