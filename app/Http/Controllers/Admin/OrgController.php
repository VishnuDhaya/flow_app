<?php

namespace App\Http\Controllers\Admin;
use Exception;

use App\Repositories\SQL\OrgRepositorySQL;
use App\Repositories\SQL\AddressInfoRepositorySQL;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Model\Org;
use App\Http\Requests;
use App\Validators\FlowValidator;
use JWTAuth;
use Response;
use Log;

//use Illuminate\Http\Response as Res;
use Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class OrgController extends ApiController
{
    
    public function update(Request $req){

        $data = $req->data;
    
        $check_validate = FlowValidator::validate($data, array("org"), __FUNCTION__);

        if(is_array($check_validate))
        {
            return $this->respondValidationError($check_validate); 
            
        }

        $org_repo = new OrgRepositorySQL();
        $is_updated = $org_repo->update($data['org']);

        if ($is_updated) {
            return $this->respondSuccess("Organisation updated successfully");
        }else{
            return $this->respondInternalError("Unknown Error");
        }
        
           
    }

     public function get_name_list(Request $req){

        $data = $req->data;
        $org_repo = new OrgRepositorySQL();
        $response = $org_repo->get_name_list();
        //$response["list"] = $this->orgRepo->get_org_name(null);

        return $this->respondData($response);

    }

     public function get_org_details(Request $req){

        $request = $req->data;
        $org_repo = new OrgRepositorySQL();
        $org_details = $org_repo->find($request['id']);

        if(isset($org_details->reg_address_id)){
            $addr_repo = new AddressInfoRepositorySQL();
            $addr_details = $addr_repo->find($org_details->reg_address_id);
            $org_details->reg_address = $addr_details;
        }
        
        return $this->respondData($org_details);

    }

}
