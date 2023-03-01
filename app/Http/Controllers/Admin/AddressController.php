<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Repositories\SQL\AddressRepositorySQL;
use Illuminate\Support\Facades\Validator;
use App\Validators\FlowValidator;


class AddressController extends ApiController
{


    public function update(Request $req){

        $data = $req->data;
    
        $check_validate = FlowValidator::validate($data, array("address"), __FUNCTION__);

        if(is_array($check_validate))
        {
            return $this->respondValidationError($check_validate); 
            
        }

        $addr_repo = new AddressRepositorySQL();
        $is_updated = $addr_repo->update($data['address']);

        if ($is_updated) {
            return $this->respondSuccess("Address updated successfully");
        }else{
            return $this->respondInternalError("Unknown Error");
        }
        

      
           
    }
        
}
