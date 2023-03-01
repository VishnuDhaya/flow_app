<?php

namespace App\Http\Controllers\FlowApp;

use DB;
use Log;
use App\Consts;
use Illuminate\Http\Request;
use App\Services\LoanService;
use App\Services\AccountService;
use App\Validators\FlowValidator;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\LoanTransactionRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Services\DisbursalService;

class LoanTransactionController extends ApiController
{
     public function __construct()
    {
         $this->loan_service = new LoanService();
    }
  
  
  public function disburse(Request $req)
  {
    $data = $req->data;
    $check_validate = FlowValidator::validate($data , array("disbursal_txn"), __FUNCTION__);
      $disbursal_txn = $data['disbursal_txn'];
      $loan_service = new LoanService();
       
        if(array_key_exists('txn_id', $disbursal_txn) && array_key_exists('from_ac_id', $disbursal_txn)){
          
          $req_data = ['mode' => 'debit', 'txn_id' => $disbursal_txn['txn_id'], 'acc_id' => $disbursal_txn['from_ac_id'], 'loan_doc_id' => null,'txn_date'=>$disbursal_txn['txn_date']];
          $req_data['is_skip_trans_id_check'] = array_key_exists('is_skip_trans_id_check', $disbursal_txn) ? $disbursal_txn['is_skip_trans_id_check'] : null;
          
          $response = $this->loan_service->check_txn_id_exists($req_data);
          
          if(isset($response) && array_key_exists('message', $response)){
            thrw($response['message']);
          }
        }
      $result = $loan_service->disburse($data['disbursal_txn']);

    if ($result['disb_status'] == Consts::DSBRSL_SUCCESS) {
     
        $message = "Float Advance Disbursed successfully";

        if($data['disbursal_txn']['send_sms'] == true && !$result["sms_sent"]){

          $message = $message . "\n Info : Unable to send SMS notification to customer";

        }
        return $this->respondData($message);
    }else{ 
        return $this->respondInternalError($result['exp_msg']);

         }
  }
  
  public function instant_disburse(Request $req)
  {
    $data = $req->data;
    $check_validate = FlowValidator::validate($data , array("instant_disbursal_txn"), __FUNCTION__);
    $loan_service = new LoanService();
    $result = $loan_service->disburse($data['instant_disbursal_txn']);
  
    if ($result['status'] == "success") {

        $message = "Float advance disbursed successfully";
      
        if($data['instant_disbursal_txn']['send_sms'] == true && !$result["sms_sent"]){

          $message = $message . "\n Info : Unable to send SMS notification to customer";

        }
        return $this->respondData($message);
    }else{ 
        return $this->respondInternalError("Unknown Error");

         }
  }

    public function get_disbursal_accounts(Request $req)
    {
        $data = $req->data;
        $acc_serv = new AccountService($data['country_code']);
        $account_details = $acc_serv->get_disbursal_accounts($data['account']);
        return $this->respondData($account_details);
    }

 public function capture_repayment(Request $req)
  {

    $data = $req->data;
    Log::warning($data);
    $check_validate = FlowValidator::validate($data , array("repayment_txn"), __FUNCTION__);
    $loan_service = new LoanService();
    
    $result = $loan_service->capture_repayment($data['repayment_txn']);
    if ($result['status'] == "success") {
        $message = "Capture Repayment successfully";
        if($data['repayment_txn']['send_sms'] == true && !$result["sms_sent"]){

          $message = $message . "\n Info : Unable to send SMS notification to customer";

        }
        return $this->respondData($message);
    }else{ 
        return $this->respondInternalError("Unknown Error");
         }

  }


public function check_lender_account(Request $req)
{
     $loan_service = new LoanService(); 
    $data = $req->data;
    $loan_agreement =  $loan_service ->check_lender_account($data, $req->getHttpHost());

    return $this->respondData($loan_agreement);
}


public function list(Request $req){
      $data = $req->data;
      //Log::warning($data);
      $loan_txn_repo = new LoanTransactionRepositorySQL();
      $loan_txns = $loan_txn_repo->list($data);

      return $this->respondData($loan_txns);

}

}
