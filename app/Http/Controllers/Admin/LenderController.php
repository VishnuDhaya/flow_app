<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Repositories\SQL\LenderRepositorySQL;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\AccountStmtRepositorySQL;
use App\Services\AccountService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Validators\FlowValidator;
use Illuminate\Support\Facades\Log;

class LenderController extends ApiController
{

    public function create(Request $req){

    	$data = $req->data;
    	
        $check_validate = FlowValidator::validate($data, array("lender","org","contact_person", "reg_address"),__FUNCTION__);
 
        if(is_array($check_validate))
        {
            return $this->respondValidationError($check_validate); 
            
        }

        $lender_repo = new LenderRepositorySQL();
        $lender_id = $lender_repo->create($data['lender']);
         	
        if ($lender_id) {
             return $this->respondCreated("New lender created successfully", $lender_id);
        }else{
            return $this->respondInternalError("Unknown Error");

        }
      
    }

    public function list(Request $req){
       
        $data = $req->data;
        $lender_repo = new LenderRepositorySQL();
        $lenders = $lender_repo->list($data);

        return $this->respondData($lenders);

    }

    public function get_name_list(Request $req){

       
    //$lender_repo = new LenderRepositorySQL();

        //  $lender_repo = new LenderRepositorySQL();

        //  $name_list = $lender_repo->get_name_list($data['country_code'],["lender_code", "name"], null, $data['status']);

        $data = $req->data;
        $lender_repo = new LenderRepositorySQL();
       # $response = $lender_repo->get_name_list($data['country_code']);
         $response = $lender_repo->get_name_list(["lender_code", "name"], null, $data['status']);
         /*$lender_repo = new LenderRepositorySQL();
         $name_list = $lender_repo->get_name_list($data['country_code'],["lender_code", "name"], null, $data['status']);

         
        // foreach ($name_list as $lender) {

        //    if(strpos($lender->id,'FLW') == true)
        //    {
        //      $lender->selected = true;
        //    }else{
        //      $lender->selected = false;
        //    }


        }*/
        return $this->respondData($response);
    }


    public function view(Request $req){
        $data = $req->data;
        $lender_repo = new LenderRepositorySQL();
        $lender = $lender_repo->view($data['lender_code']);
        return $this->respondData($lender);
    }

    public function add_account(Request $req){
        $data = $req->data;
        $check_validate = FlowValidator::validate($data, array("lender_account"),"create");
        if(is_array($check_validate))
        {
            return $this->respondValidationError($check_validate); 
        }
        $acc_repo = new AccountRepositorySQL();
        $lender_account = $acc_repo->create($data['lender_account']);
        return $this->respondCreated('Account added to lender successfully');
    }


    public function update(Request $req){
        $data = $req->data;
        $check_validate = FlowValidator::validate($data, array("lender"),__FUNCTION__);
        if(is_array($check_validate))
        {
            return $this->respondValidationError($check_validate); 
        }
        $lender_repo = new LenderRepositorySQL();
        $lender = $lender_repo->update($data['lender']);
        
        if($lender){
            return $this->respondSuccess("Lender updated successfully");
        }else{
            return $this->respondInternalError("Unknown Error");
        }
           
    }

     public function get_acc_stmts(Request $req){
        $data = $req->data;
        
        $acc_serv = new AccountService();
        $acc_stmts = $acc_serv->get_acc_stmts($data);
        
    
        return $this->respondData($acc_stmts);   
         

    }

    public function add_acc_stmts(Request $req){
        $data = $req->data;
        $acc_serv = new AccountService();
        $acc_stmts = $acc_serv->add_acc_stmts($data);
        if($acc_stmts){
            return $this->respondSuccess(" Statement transaction captured successfully");
        }else{
            return $this->respondInternalError("Unknown Error");
        }    }
    
    
}
