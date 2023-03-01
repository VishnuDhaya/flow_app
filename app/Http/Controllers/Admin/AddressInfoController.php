<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Repositories\SQL\AddressInfoRepositorySQL;
use Illuminate\Support\Facades\Validator;
use App\Validators\FlowValidator;
use App\Repositories\SQL\CommonRepositorySQL;
use Log;

class AddressInfoController extends ApiController
{

	protected $addr_info_repo;
	
   /* public function __construct(){

    	$this->addr_info_repo = new AddressInfoRepositorySQL();
    }*/

       public function update(Request $req){
        $data = $req->data;
        //Log::warning($data['country_code']);
        $addr_info_repo = new AddressInfoRepositorySQL();
        $address_info_id = $addr_info_repo->update($data['address']);
        if ($address_info_id) {
            return $this->respondSuccess('Address Info Updated successfully');
        }else{
            return $this->respondInternalError("Unknown Error");

        }

    }
    public function get_addr_config(Request $req){
        $data = $req->data;

        $addr_info_repo = new AddressInfoRepositorySQL();
        $result["addr_config"] = $addr_info_repo->get_addr_config_list(false, $data);
        if ($result) {
            return $this->respondData($result);
        }else{ 
            return $this->respondInternalError("Unknown Error");
        }
    }

      public function get_dropdown(Request $req){
            $data = $req->data;
            
            $data["data_key"] = $data["field_code"] ;
            unset($data["field_code"]);
            
            $common_repo = new CommonRepositorySQL();
            $response["list"] = $common_repo->get_master_data($data);
            //$response["list"] = cachee($data['country_code'], $data['field_code']);
            return $this->respondData($response);
        }
    }
