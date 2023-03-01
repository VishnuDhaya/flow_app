<?php
namespace App\Services\Vendors\Partners\YoUganda;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Services\LoanService;
use Log;
#include './YoAPI.php';

class YoService {
	public function __construct(){
        
	}
    
    public function receive_payment_notification($req)
    {	
        Log::warning('$req');
        Log::warning($req);
        Log::warning('$_POST');
        Log::warning($_POST);
        $yo_api = new YoAPI("100272380571", "xkm5-ibmw-yaOq-WnZS-l7FK-K2cG-X2Kv-z4Iq");
        session()->put('country_code' ,'UGA');
        session()->put('user_id' ,'UGA');
        setPHPTimeZone('EAT');
    	$response = $yo_api->receive_payment_notification();
    
    	if($response['is_verified']){
    	
            $loan_serv = new LoanService();
            Log::warning('$response');
            Log::warning($response);
            #$txn_date = mb_substr($response['date_time'], 0, 10);
            $txn_date = $response['date_time'];
            $payer = $response['payer_names'];
            $msisdn = $response['msisdn'];
            
            
            $loan_doc_id = str_replace('217 payment for ', "", $response['narrative']);
            Log::warning($loan_doc_id);
            $yo_uganda_ac_id = $this->get_payment_ac_id($loan_doc_id);
            
            $loan_txn = [
                    "loan_doc_id" => $loan_doc_id,
                    "txn_date" => $txn_date,
                    "paid_date" => $txn_date, 
                    'txn_exec_by' => $payer,
                    'remarks' => $msisdn,
                    "txn_id" => $response['external_ref'],
                    "amount" => $response['amount'],
                    "to_ac_id" => $yo_uganda_ac_id,
                    "txn_mode" => "payment_gateway",
                    "send_sms" => true,
                    "is_part_payment" => false,
                    "waive_penalty" => true
                ];
                
            Log::warning('$loan_txn');
            Log::warning($loan_txn);
            $loan_serv->capture_repayment($loan_txn);
            
            
    		// Update your transaction status in the db where the external_ref = $response['external_ref']
    	}else{
    	    thrw("Error capturing the payment notification");
    	}
	    
        return true;
    }
    
    function get_payment_ac_id($loan_doc_id){
        $loan_repo = new LoanRepositorySQL();
                
        $loan = $loan_repo->get_loan($loan_doc_id);
        
        #Log::warning($loan);
        
        $account_repo = new AccountRepositorySQL();
        if($loan){
            $keys = ["lender_code", 'status', 'lender_data_prvdr_code', 'acc_prvdr_code'];
            $values = [$loan->lender_code, 'enabled', $loan->data_prvdr_code, 'UYOU'];
            
            $lender_accounts = $account_repo->get_accounts_by($keys, $values, ['id']);
             /*$accounts = $acc_serv->get_lender_accounts();*/
            if(empty($lender_accounts) ){
            thrw("Yo Uganda Payment account not configured for the lender : {$loan->lender_code}");
            }
            if(sizeof($lender_accounts) > 1 ){
                thrw("More than one Yo Uganda payment account configured for the lender : {$loan->lender_code}");
            }
            return $lender_accounts[0]->id;
            
        }else{
            thrw("No valid FA ID exist in the narrative");
        }
       
        
    }
    
  
}