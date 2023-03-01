<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Repositories\SQL\MasterDataKeyRepositorySQL;
use App\Repositories\SQL\MasterDataRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Validators\FlowValidator;

class MasterDataController extends ApiController
{

	protected $master_data_key_repo;
	public function __construct(){
			//$this->master_data_key_repo = new MasterDataKeyRepositorySQL();

	}

	public function create_master_data(Request $req){

		$data = $req->data;

        $check_validate = FlowValidator::validate($data , array("master_data"));

        if(is_array($check_validate))
        {
        return $this->respondValidationError($check_validate); 
        }

		$master_data = (new MasterDataRepositorySQL())->create($data['master_data']);
        
        if ($master_data) {
             return $this->respondCreated("Master Data added successfully", $master_data);
        }else{
            return $this->respondInternalError("Unknown Error");

        }

	}

 	public function get_all_data_keys(Request $req){

        $data = $req->data;

        $first_field_array = array();

        if($data['add_no_parent'] === true){
            //Log::warning("no parent data key");
        	$first_field_array = ["" , "No Parent"];
        }

        // $response["list"] = $this->master_data_key_repo->get_name_list_1($data['country_code'], ["data_key", "data_key"],  $first_field_array, $data['status']);
        $master_data_key_repo = new MasterDataKeyRepositorySQL();
       $response["list"] = $master_data_key_repo->get_names($data, ["data_key", "data_key"],  $first_field_array, $data['status']);
    

        return $this->respondData($response);

    }


    public function get_new_data_keys(Request $req){

        $data = $req->data;
        $master_data_key_repo = new MasterDataKeyRepositorySQL();
        $response["list"] = $master_data_key_repo->get_new_data_keys($data['country_code']);

        return $this->respondData($response);

    }


     public function create_data_key(Request $req){

        $data = $req->data;
        $master_data_key_repo = new MasterDataKeyRepositorySQL();
        $check_validate = FlowValidator::validate($data , array("master_data_key"));

        if(is_array($check_validate))
        {
        return $this->respondValidationError($check_validate); 
        }
        $master_data_key = $master_data_key_repo->create($data['master_data_key']);
        //dd($master_data_keys);
        if ($master_data_key) {
             return $this->respondCreated("Master Data Key saved successfully", $master_data_key);
        }else{
            return $this->respondInternalError("Unknown Error");

        }

    }

    public function list_data_key(Request $req){
       
        $data = $req->data;

        $master_data_key_repo = new MasterDataKeyRepositorySQL();
        $master_data_keys = $master_data_key_repo->list($data['country_code']);

        return $this->respondData($master_data_keys);

    }

    public function update_data_key_status(Request $req){

        $data = $req->data;
        $master_data_key_repo = new MasterDataKeyRepositorySQL(); 
        $status_change = $master_data_key_repo->update_status($data['master_data_key']);

        if ($status_change) {
             return $this->respondSuccess('Master Data Key status updated successfully');
        }else{
            return $this->respondInternalError("Unknown Error");

        }
       }


       public function get_parent_data_codes(Request $req){
    
       	$data = $req->data;
        $master_data_key_repo = new MasterDataKeyRepositorySQL(); 
       	$parent_data_key = $master_data_key_repo->get_parent_data_key($data);

       	$data["data_key"] = $parent_data_key;
        $common_repo = new CommonRepositorySQL();
        $common_repo->class = \App\Models\MasterData::class;

        $field_values = [$data['data_key'],$data['status'],$data['country_code'],"*"];
        $addl_sql_condition = " and country_code in (?, ?)";
       	$response["list"] = $common_repo->get_records_by_many(['data_key','status'],$field_values,['data_value','data_code'], "and ", $addl_sql_condition, false);

        
       	$response["parent_data_key"] = $parent_data_key;

		return $this->respondData($response);

       }

       public function get_cs_model_code(Request $req){
            $data = $req->data;
            $master_data_repo = new MasterDataRepositorySQL();
            $cs_model_code = $master_data_repo->get_cs_model_code($data);
            return $this->respondData($cs_model_code);
       }

       public function get_score_factors(Request $req){
            $data = $req->data;
            $master_data_repo = new MasterDataRepositorySQL();
            $score_factors = $master_data_repo->get_score_factors($data);
            return $this->respondData($score_factors);
       }

}