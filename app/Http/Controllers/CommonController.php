<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Services\CommonService;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\LoanProvisioningRepositorySQL;
use App\Repositories\SQL\MarketRepositorySQL;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use App\Services\AccountService;
use App\Services\Support\SMSService;
//use Log;
use URL;
use view;


class CommonController extends ApiController
{
    
    public function get_master_data(Request $req){

         
         $data = $req->data;
        // Log::warning($data['master_data']);
         $common_repo = new CommonRepositorySQL();
    	 $get_data = $common_repo->get_master_data($data['master_data']);
       
    	 return $this->respondData($get_data);
    }

    public function get_country_list(Request $req){

        $data = $req->data;
        $common_repo = new CommonRepositorySQL();
        $response["list"] = $common_repo->get_country_name_list();

        return $this->respondData($response);
    }
    
     public function get_currency_list(Request $req){

        $data = $req->data;
        $common_repo = new CommonRepositorySQL();
         $response["list"]  = $common_repo->get_currency_list();

        return $this->respondData($response);
    }

    public function get_currency_code(Request $req){

        $data = $req->data;
        $common_repo = new CommonRepositorySQL();
        $get_data = $common_repo->get_currency_code($data['country_code']);
    

        return $this->respondData($get_data);
    }

    public function get_loan_search_criteria(Request $req){

        $data = $req->All();
        if (array_key_exists('screen',$data)){
            if ($data['screen'] == "loan_search"){
                $acc_prvdr_code = $req->acc_prvdr_code;
                $data['acc_prvdr_code'] = $acc_prvdr_code;
            }else{
                $data = $req->data;
            }
        }else{
            $data = $req->data;
        }
       
        $get_data = (new CommonService())->get_loan_search_criteria($data);

        return $this->respondData($get_data);

    }

    public function get_customer_accounts(Request $req)
    {

        $data = $req->data;
        $acc_serv = new AccountService();

        $cust_account["list"] = $acc_serv->get_customer_accounts($data['customer_account']);

        return $this->respondData($cust_account);

    }

    public function get_lender_accounts(Request $req)
    {
          $data = $req->data;
          $acc_serv = new AccountService($data['country_code']);
          $lender_account["list"] = $acc_serv->get_lender_accounts($data['lender_account']);
          return $this->respondData($lender_account);

    }

    public function send_sms(Request $req){

        $data = $req->data;
        $market_repo = new MarketRepositorySQL();
        $isd_code = $market_repo->get_isd_code($data['country_code']);

        $sms_serv = new SMSService();
        $sms_status = $sms_serv($data['recipient'], $data['message'], $isd_code->isd_code);

        if($sms_status){
            return $this->respondWithMessage("SMS Sent successfully");
        }
        else{
            return $this->respondWithError("Unable to send SMS now.");   
        }
    }
    
    public function update_status(Request $req){
        $role_codes = array("operations_manager","market_admin", 'super_admin', 'ops_admin', 'it_admin');

        $data = $req->data;
        if(key($data) == 'accounts'){
            $acc_serv = new AccountService();
            $acc_serv->check_account_w_kyc($data['accounts']);
        }
        if(in_array(auth()->user()->role_codes,$role_codes)){
            $common_repo = new CommonRepositorySQL();
            $update_status = $common_repo->update_status($data);
            return $this->respondData($update_status);
        }
        else{
            thrw("You cannot change the status");
        }
    }
    public function list_users_by_priv(Request $req){
        $data = $req->data;
    
        $serv = new CommonService(); 
        $priv_users = $serv->list_users_by_priv($data,$data['priv_code']);
        $priv_users_list['list'] = $priv_users;
        return $this->respondData($priv_users_list);   
         
    }
    public function get_app_users(Request $req){

        $data = $req->data;
        $cmnServ = new CommonService();
        $app_users['list'] = $cmnServ->get_users($data);
        
        return $this->respondData($app_users);
    }
   /* public function get_acc_txn_type(Request $req)
    {
        $data = $req->data;
        $cmnServ = new CommonService();
        $account_txn_type['list'] = $cmnServ->get_acc_txn_type($data);
        return $this->respondData($account_txn_type);
    }*/

     public function get_record_audits(Request $req){
        $data = $req->data;
        $serv = new CommonService();
        $result = $serv->get_audit_changes($data );
        return $this->respondData($result);
    }

    public function check_for_valid_otp(Request $req){
        $data = $req->data;
        $serv = new SMSService();
        $result = $serv->check_for_valid_otp($data['otp_type'],$data['entity_id']);
        return $this->respondData($result);
    }

    public function resend_otp (Request $req) {
        $serv = new SMSService();
        $data = $req->data;
        $serv->resend_otp($data);
        return $this->respondSuccess("The confirmation code has been sent again to the customer's registered mobile number");
    }

    public function list_stmt_imports(Request $req){
        $data = $req->data;
        $serv = new CommonService();
        $result = $serv->list_stmt_imports($data);
        return $this->respondData($result);
    }

    public function search_stmt_imports(Request $req){
        $data = $req->data;
        $serv = new CommonService();
        $result = $serv->search_stmt_imports($data);
        return $this->respondData($result);
    }

}
