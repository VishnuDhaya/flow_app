<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Services\AccountService;
use Illuminate\Support\Facades\Validator;
use App\Validators\FlowValidator;
use Log;

class AccountController extends ApiController
{

	protected $acc_repo;
	
    /*public function __construct(AccountRepositorySQL $acc_repo){

    	$this->acc_repo = $acc_repo;
    }*/


    public function create(Request $req){

    $data = $req->data;
    $check_validate = FlowValidator::validate($data , array("account"), __FUNCTION__);
       
    if(is_array($check_validate))
    {
        return $this->respondValidationError($check_validate); 
    }       
   
    $acc_serv = new AccountService($data['country_code']);
    $account_id = $acc_serv->create($data['account']);
    $account_result['account_id'] = $account_id;
    if ($account_result) {
        if($data['account']['entity'] == 'customer'){
            $message = 'A new account has been created in disabled status. Successful ReKYC process will enable the account automatically.';
        }
        else{
            $message = 'New Account created successfully';
        }
        return $this->respondData($account_result, $message);
    }else{ 
        return $this->respondInternalError("Unknown Error");

         }
    }

    public function list(Request $req){
        $data = $req->data;
        $acc_repo = new AccountRepositorySQL();
        $accounts = $acc_repo->list($data['account']);

        return $this->respondData($accounts);
    
    }

    public function get_ref_accounts(Request $req){

        $data = $req->data;
        $acc_serv = new AccountService($data['country_code']);
        $accounts = $acc_serv->get_ref_accounts($data['account']);

        return $this->respondData($accounts);
    }

    public function update_account_status(Request $req){

        $data = $req->data;
        $acc_repo = new AccountRepositorySQL();
        $account_status_change = $acc_repo->update_account_status($data['account']);
   
        if ($account_status_change === true) {
             return $this->respondSuccess('Account status updated successfully');
        }else{
            return $this->respondInternalError("Unknown Error");

        }
    }

    public function view(Request $req){

        $data = $req->data;
       
        $acc_repo = new AccountRepositorySQL();
        $account = $acc_repo->find($data['account_id']);
        $account_result['account'] = $account;
        return $this->respondData($account_result);

    }

    public function make_primary(Request $req){

        $data = $req->data;
        $acc_serv = new AccountService($data['country_code']);
        $change_primary = $acc_serv->make_primary($data['account']);

        if ($change_primary === true) {
         return $this->respondSuccess("Primary account updated successfully");
        }else{
         return $this->respondInternalError("Unknown Error");
       }


    }

    public function update(Request $req){
        $data = $req->data;
        $acc_serv = new AccountService();
        m_array_filter($data);
        if(isset($data['person']) && sizeof($data['person']) >0){

            $check_validate = FlowValidator::validate($data, array("person"), __FUNCTION__);

            if(is_array($check_validate))
            {
                return $this->respondValidationError($check_validate); 
                
            }
        }
        $account = $acc_serv->update($data);
        
        if($account){
            return $this->respondSuccess("Account Details updated successfully");
        }else{
            return $this->respondInternalError("Unknown Error");
        }
           
    }

     public function list_acc_txns(Request $req){


        $data = $req->data;
        $acc_serv = new AccountService($data['country_code']);
        $account_txns = $acc_serv->list_acc_txns($data['account']);
        
        return $this->respondData($account_txns);
    
    }

    // public function create_acc_txn(Request $req){

    //     $data = $req->data;
    //     $check_validate = FlowValidator::validate($data, array("acc_txn"),__FUNCTION__);
    //     if(is_array($check_validate))
    //     {
    //         return $this->respondValidationError($check_validate); 
    //     }
    //     $acc_repo = new AccountService($data['country_code']);
    //     $account_txn_id = $acc_repo->process_acc_txn_req($data);
        
    //     if($account_txn_id) {
    //         return $this->respondData('Transaction done successfully');
    //     }else{ 
    //         return $this->respondInternalError("Unknown Error");

    //          }
    //     }
    public function get_acc_txns(Request $req)
    {
        $data = $req->data;
        $acc_repo = new AccountRepositorySQL($data['country_code']);
        
        $account_txns = $acc_repo->get_acc_txns($data['account_txn']);
        return $this->respondData($account_txns);
    }

   

}
