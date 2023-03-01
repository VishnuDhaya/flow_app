<?php

namespace App\Http\Controllers\FlowApp;

use App\Models\LoanRecovery;
use App\Services\LoanApplicationService;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Consts;
use App\Exceptions\FlowCustomException;
use App\Services\LoanService;
use App\Services\RepaymentService;
use App\Services\ReconService;
use App\Services\Mobile\RMService;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\AccountStmtRepositorySQL;
use App\Services\Schedule\BackgroundService;
use Illuminate\Support\Facades\Log;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\LoanCommentRepositorySQL;
use Illuminate\Support\Facades\Validator;
use App\Validators\FlowValidator;
use App\Models\LoanTransaction;
use App\Repositories\SQL\LoanTransactionRepositorySQL;


class LoanController extends ApiController
{   
	    //$loan_service = null;
    public function __construct()
    {
         $this->loan_service = new LoanService();
    }
  	

    public function loan_search(Request $req)
    {

        $data = $req->data;

        #$loans["results"] = $this->loan_service->loan_search($data['loan_search_criteria']); 
        #return $this->respondData($loans);     
        try{
            $response = $this->loan_service->loan_search($data['loan_search_criteria']);
            return $this->respondData($response);
        }catch(FlowCustomException $e){
            if(array_key_exists('alert', $data['loan_search_criteria']) && !$data['loan_search_criteria']['alert'] && $e->err_code == "no_results"){
                return  $this->respondData(['results' => []]);
            }
            else{
                thrw($e->getMessage());
            }
        }
        catch(\Exception $e){
            thrw($e->getMessage());

        }
    }

    public function held_loan_search(Request $req)
    {

        $data = $req->data;

        try{
            $response = $this->loan_service->loan_search($data['loan_search_criteria']);
            return $this->respondData($response);
        }catch(FlowCustomException $e){
            if($e->err_code == "no_results"){
                return  $this->respondData(['results' => []]);
            }
            else{
                thrw($e->getMessage());
            }
        }
        catch(\Exception $e){
            thrw($e->getMessage());
        }

    }


    public function get_loan(Request $req)
    {
        $data = $req->data;

        $loan["loan"] = $this->loan_service->get_loan($data);

        return $this->respondData($loan);  

    }

    public function cancel_loan(Request $req)
    {
        $data = $req->data;
        
        
        $response = $this->loan_service->cancel_loan($data['cancel_loan']);
       	return $this->respondSuccess("Cancel Loan Application"); 
    }
    public function list(Request $req)
    {

        $data = $req->data;

        $loan_list = $this->loan_service->list($data);

        return $this->respondData($loan_list);  

    }

    public function release_loan(Request $req)
    {
    //    $this->bckg_service = new BackgroundService();

        $data = $req->data;
       // Log::warning($data);
        $loan_update = $this->loan_service->release_loan($data['loan_doc_id']);

        return $this->respondData($loan_update);  
    }


    public function create_comment(Request $req)
    {
        $data = $req->data;

        $check_validate = FlowValidator::validate($data , array("loan_comments"));

        if(is_array($check_validate))
        {
        return $this->respondValidationError($check_validate); 
        }
        $loan_comment_repo = new LoanCommentRepositorySQL();
        //Log::warning($data);
        $comment_id = $loan_comment_repo->create($data['loan_comments']);
        if ($comment_id) {
             return $this->respondCreated("New comment created successfully", $comment_id);
        }else{
            return $this->respondInternalError("Unknown Error");
        }

    }

    public function list_comments(Request $req)
    {

            $data = $req->data;

            $loan_comment_repo = new LoanCommentRepositorySQL();
            
            $comment['list'] = $loan_comment_repo->list($data['loan_doc_id']);
            $comment['logged_in_user_id'] = session('user_id');
            return $this->respondData($comment);  
    }

    public function list_disbursers(Request $req)
    {
        $data = $req->data;
        $disbursers = $this->loan_service->list_disbursers($data);
        $disbursers_result["list"] = $disbursers;
        return $this->respondData($disbursers_result);     
    }

    public function assign_list(Request $req)
    {
            $data = $req->data;
            $person_repo = new PersonRepositorySQL(); 
            $assign_list = $person_repo->get_person_contacts($data['person_id']);
            return $this->respondData($assign_list);  

    }
    public function getproductsummary(Request $req)
    {
            $data = $req->data;
            $product_summary = $this->loan_service->getproductsummary($data['product_id']);
            return $this->respondData($product_summary);  

    }
    public function get_payment_summary(Request $req){
        $data = $req->data;
        $repay_service = new RepaymentService();
        if(array_key_exists('txn_id', $data) && array_key_exists('acc_id', $data)){
            $req_data = ['mode' => 'credit', 'txn_id' => $data['txn_id'], 'acc_id' => $data['acc_id'], 'loan_doc_id' => $data['loan_doc_id'], 'txn_date' => $data['txn_date']];
            $req_data['is_skip_trans_id_check'] = array_key_exists('is_skip_trans_id_check', $data) ? $data['is_skip_trans_id_check'] : null;
            $response = $this->loan_service->check_txn_id_exists($req_data);
        
            if(isset($response) && array_key_exists('message', $response)){
                thrw($response['message']);
            }
        }
        
        $loan = $repay_service->get_payment_summary($data);
        $loan->os_principal = $loan->loan_principal - $loan->paid_principal;
        $loan->os_fee = $loan->flow_fee - $loan->paid_fee - $loan->fee_waived;

        if(array_key_exists('is_part_payment' ,$data) && $data["is_part_payment"] == false  && $data["amount"] < ($loan->os_principal + $loan->os_fee)){
            thrw("You can not make part payment");
        }

        if($loan->os_principal == 0){
            $last_payment_txn = (new LoanTransactionRepositorySQL)->get_record_by_many(['loan_doc_id', 'txn_type'], [$data['loan_doc_id'], 'payment'], ['txn_date'], 'and', 'order by id desc limit 1');
            $diff = parse_date($last_payment_txn->txn_date)->floatDiffInHours(parse_date($loan->disbursal_date));
            $loan->can_waive_fee = $diff <= config('app.waive_fee_validity');
        }

        return $this->respondData($loan);


    }
    public function create_capture_payment(Request $req){
        $data = $req->data;
        // if( ((array_key_exists('to_ac_id' , $data)) && ($data['to_ac_id'] == 1783  || $data['to_ac_id'] == 2895 )) &&  session('user_id') != 10){
        //     thrw('Can not manually capture payment for accounts that is reconciled. Use "Review & Sync" button under recon menu to capture payments made to this account');
        // }
            
        $capture_service = new RepaymentService();
        #$resp = $capture_service->capture_payment($data);
        $resp = $capture_service->review_n_sync($data);
        return $this->respondData($resp);
    }

    public function create_unlink_payment(Request $req){
        $data = $req->data;
        $recon = new ReconService();
        $resp = $recon->unlink_payment($data);


        return $this->respondWithMessage($resp);
    }
    public function allow_partial_payment(Request $req){
       $data = $req->data;
       $resp = $this->loan_service->allow_partial_payment($data);
       Log::warning($resp);
       return $this->respondData($resp);
    }



     public function retry_disbursal (Request $req) {
        $data = $req->data;
        $serv = new LoanService();
        $serv->retry_disbursal($data['loan_doc_id'], $data['int_type']);
        return $this->respondSuccess("Sent to Disbursal Queue");
     }

     public function get_disbursal_attempt(Request $req) {
         $data = $req->data;
         $serv = new LoanService();
         $result = $serv->get_disbursal_attempt($data['loan_doc_id']);
         return $this->respondData($result);
         
     }

     public function change_disbursal_status(Request $req) 
     {
        $data = $req->data;
        $serv = new LoanService();
        $result = $serv->change_disbursal_status($data);
        return $this->respondSuccess($result);
     }

     public function bypass_cust_confirm(Request $req) {
         $data = $req->data;
         $repo = new LoanRepositorySQL();
         $loan = $repo->get_record_by('loan_doc_id',$data['loan_doc_id'],['status','customer_consent_rcvd','disbursal_status']);
         if(!$loan->customer_consent_rcvd && $loan->status == Consts::LOAN_PNDNG_DSBRSL && $loan->disbursal_status == null) {
            $serv = new LoanService();
            $serv->send_to_disbursal_queue($data['loan_doc_id'], null, 'bypassed');
            return $this->respondSuccess("Bypassed customer confirmation and sent message to disbursal queue");
         }
         Log::warning("----------------------BYPASS CUST CONFIRM FAILURE------------------------");
         Log::warning((array)$loan);
         thrw("Unable to Disburse. Check logs for details.");
        }

        public function check_txn_id_exists(Request $req)
        {
            $data = $req->data;
            $loan_serv = new LoanService();
            $result = $loan_serv->check_txn_id_exists($data);
            return $this->respondData($result);
        }

        public function update_waiver(Request $req){
            $data = $req->data;
            $capture_service = new RepaymentService();
            $resp = $capture_service->update_waiver($data);
            return $this->respondData($resp);
        }
        public function reverse_payment(Request $req){
            $data = $req->data;
            $repay_serv = new RepaymentService();
            $result = $repay_serv->reverse_payment($data);
            return $this->respondData($result);
        }
        public function capture_excess_reversal(Request $req){
            $data = $req->data;
            $capture_service = new RepaymentService();
            $resp = $capture_service->capture_excess_reversal($data);
            if($resp){
                return $this->respondWithMessage("Excess captured successfully");
            }else{
                return $this->respondWithError("Can't capture the excess payment");
            }
        }
    public function cancel_capture_disbursal(Request $req) 
    {
        $data = $req->data;
        $serv = new LoanService();
        $result = $serv->cancel_capture_disbursal($data);
        return $this->respondSuccess($result);
    }

    public function list_fa_upgrade_requests(Request $req){
        $data = $req->data;
        $serv = new RMService();
        $result = $serv->list_fa_upgrade_requests($data);
        return $this->respondData($result);
    }

    public function fa_upgrade_approval(Request $request){
        $data = $request->data;
        $rm_serv = new RMService();
        $result = $rm_serv->fa_upgrade_approval($data);
        if($result){
            return $this->respondSuccess("Upgrade FA request {$result['status']} successfully");  
        }else{
            return $this->respondWithError("unable to {$result['status']} FA upgrade request");
        }  
        return $result;


    }


    public function update_recon_status(Request $request){
        $data = $request->data;
        $loan_serv = new LoanService();
        $result = $loan_serv->update_recon_status($data);
        if($result){
            return $this->respondSuccess("Manual recon for this transaction is successfully");  
        }else{
            return $this->respondWithError("unable to do Manual Recon for this transaction");
        }  

    }

    public function reinitiate_recon(Request $request){
        $data = $request->data;
        $loan_serv = new LoanService();
        $result = $loan_serv->reinitiate_recon($data);
        if($result){
            return $this->respondSuccess("Recon transaction reinitiated successfully");  
        }else{
            return $this->respondWithError("unable to do reinitate recon");
        }  
    }
    
    public function remove_disbursal(Request $req){
        $data = $req->data;
        $loan_serv = new LoanService();
        $result = $loan_serv->remove_disbursal($data);
        return $this->respondData($result);

    }

    public function manual_capture_reports(Request $req){
        $data = $req->data;
        $serv = new LoanService();
        m_array_filter($data);
        $result = $serv->list_manual_capture($data);
        return $this->respondData($result);
    }
}
