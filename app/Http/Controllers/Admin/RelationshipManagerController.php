<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Repositories\SQL\RelationshipManagerRepositorySQL;
use Illuminate\Support\Facades\Validator;
use App\Validators\FlowValidator;
use Log;
class RelationshipManagerController extends ApiController
{
  

    public function create(Request $req){
        $data = $req->data;
        $check_validate = FlowValidator::validate($data, array("relationship_manager"), __FUNCTION__);
        if(is_array($check_validate))
        {
            return $this->respondValidationError($check_validate); 
            
        }
        $relationship_manager_repo = new RelationshipManagerRepositorySQL();
        $is_created = $relationship_manager_repo->create($data['relationship_manager']);

        if ($is_created) {
            return $this->respondSuccess('Relationship manager created successfully');
        }else{
            return $this->respondInternalError("Unknown Error");
        }
    }
    public function list(Request $req){
        $request = $req->data;

        $relationship_manager_repo = new RelationshipManagerRepositorySQL();
        $relationship_managers = $relationship_manager_repo->show_list($request);
        return $this->respondData($relationship_managers);
    }

    public function get_name_list(Request $req){
        $request = $req->data;
        $relationship_manager_repo = new RelationshipManagerRepositorySQL();
        // $response= $relationship_manager_repo->show_name_list($request);
        $response= $relationship_manager_repo->get_flow_rel_name(session('country_code'),$request['associated_with']);
        return $this->respondData($response);
    }

    public function view(Request $request){
        $data = $request->data;
        $relationship_manager_repo = new RelationshipManagerRepositorySQL();
        $relationship_manager = $relationship_manager_repo->view($data['relationshipManager_id'], $data['country_code']);
        return $this->respondData($relationship_manager);

   }

    public function update(Request $request){
        $data = $request->data;
        $check_validate = FlowValidator::validate($data, array("rel_mgr"),__FUNCTION__);
        if(is_array($check_validate))
        {
            return $this->respondValidationError($check_validate); 
        }
        $relationship_manager_repo = new RelationshipManagerRepositorySQL();
        $update_status = $relationship_manager_repo->update($data['rel_mgr']);
        if($update_status){
            return $this->respondSuccess("Relationship manager updated successfully");
        }else{
            return $this->respondInternalError("Unknown Error");
        }
   }
    public function get_partner_rm_dropdown(Request $request){
        $data = $request->data;
        $data["status"] = "enabled";
        $data["associated_with"] = "acc_prvdr";
        $data["associated_entity_code"] = session("acc_prvdr_code");
        $data["country_code"] = session("country_code");
        $relationship_manager_repo = new RelationshipManagerRepositorySQL();
        $response= $relationship_manager_repo->show_name_list($data);
        return $this->respondData($response);
    }
}
