<?php

//error_reporting(E_ALL);

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Repositories\SQL\AccProviderRepositorySQL;
use Illuminate\Support\Facades\Validator;
use App\Validators\FlowValidator;


class AccProviderController extends ApiController
{

   #
   public function create(Request $req){

   	  $data = $req->data;

      $check_validate = FlowValidator::validate($data, array("acc_provider","org","reg_address"),__FUNCTION__);

      if(is_array($check_validate))
      {
         return $this->respondValidationError($check_validate); 
      }

      $acc_prvdr_repo = new AccProviderRepositorySQL();
      $is_created = $acc_prvdr_repo->create($data['acc_provider']);
          
      if ($is_created) {
         return $this->respondCreated("New account provider created successfully");
      }else{
         return $this->respondInternalError("Unknown Error");
      }


   }

  public function list(Request $req){

        $data = $req->data;
        $acc_prvdr_repo = new AccProviderRepositorySQL();
        $resp["list"] = $acc_prvdr_repo->list($data);

        return $this->respondData($resp);
    
    }

   public function update_acc_types(Request $req){

      $data = $req->data;

      $check_validate = FlowValidator::validate($data, array("acc_provider"),"update");
      if(is_array($check_validate))
      {
          return $this->respondValidationError($check_validate);   
      }

      $acc_prvdr_repo = new AccProviderRepositorySQL();
      $is_updated = $acc_prvdr_repo->update($data['acc_provider']);

      if ($is_updated) {
         return $this->respondSuccess("New account types added successfully");
      }else{
         return $this->respondInternalError("Unknown Error");
      }
       
   }

   public function get_acc_prvdr_name(Request $req)
   {
   
        $data = $req->data;
        $acc_prvdr_repo = new AccProviderRepositorySQL();
        $acc_prvdr_name = $acc_prvdr_repo->get_acc_prvdr_name($data['acc_prvdr_code']);

        return $this->respondData($acc_prvdr_name);

   }
    
   public function get_name_list(Request $req){
      $data = $req->data;    
      $acc_prvdr_repo = new AccProviderRepositorySQL();
      $acc_prvdrs = $acc_prvdr_repo->get_name_list(["acc_prvdr_code", "name"], null, $data['status']);
      
      return $this->respondData($acc_prvdrs);
   }
  

}
