<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\CSResultConfig;
use Log;

class CSResultConfigController extends ApiController
{


	public function __construct(){
		$this->cs_result_config = new CSResultConfig;

	}

	public function create(Request $req){

		$data = $req->data;
        $cs_result_config_id = $this->cs_result_config->create($data['cs_result']);
         	
        if ($cs_result_config_id) {
             return $this->respondCreated("New CS Result Score saved successfully", $cs_result_config_id);
        }else{
            return $this->respondInternalError("Unknown Error");

        }
	}

}