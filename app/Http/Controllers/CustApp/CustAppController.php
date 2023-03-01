<?php

namespace App\Http\Controllers\CustApp;

use App\Http\Controllers\ApiController;
use App\Models\PaymentAttempt;
use App\Services\AccountService;
use App\Services\LoanApplicationService;
use App\Services\Mobile\CustAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Repositories\SQL\AccProviderRepositorySQL;



class CustAppController extends ApiController
{

    public function get_recent_fas(Request $request)
    {
        $data['cust_id'] = session('cust_id');
        $data['status'] = 'settled';
        $data['mode'] = "search";
        $data['last_n_fas'] = 10;
        $custServ = new CustAppService();
        $result = $custServ->get_recent_fas($data);

        return $this->respondData($result);
    }

    public function get_home_fa(Request $request){
        $cust_id = session('cust_id');
        $custServ = new CustAppService();
        $result = $custServ->get_home_fa($cust_id);
        return $this->respondData($result);
    }

    public function get_fa_detail(Request $request)
    {
        $loan_doc_id = $request->data['loan_doc_id'];
        $custServ = new CustAppService();
        $result = $custServ->get_fa_detail($loan_doc_id);

        return $this->respondData($result);
    }

    public function get_customer_accounts(){
        $acc_serv = new AccountService();
        $data['status'] = 'enabled';
        $data['cust_id'] =  session('cust_id');
        $data['acc_purpose'] = 'float_advance';
        $result = $acc_serv->get_customer_accounts($data,true);
        return $this->respondData($result);
     }

     public function repeat_fa(Request $request){
         $pin = $request->data['pin'];
         $authenticated = $this->validate_pin($pin);
         if($authenticated) {
             $loan_appl_serv = new LoanApplicationService();
             $data = $request->data;
             $data['cust_id'] = session('cust_id');
             $result = $loan_appl_serv->repeat_fa($data);
             return $this->respondWithMessage('Applied Successfully');
         }
         else{
             return  $this->respondWithError("Invalid PIN number");
         }
        
    }

    public function apply_fa(Request $request){
        $pin = $request->data['pin'];
        $authenticated = $this->validate_pin($pin);
        if($authenticated) {
            $loan_appl_serv = new LoanApplicationService();
            $data = $request->data;
            $cust_id = session('cust_id');
            $gps = null;
            if(array_key_exists('gps', $data)){
                $gps = $data['gps'];
            }
            $result = $loan_appl_serv->apply_fa_by_product($cust_id, $data['product_id'], $data['account_id'],$gps);
            return $this->respondWithMessage('Applied Successfully');
        }
        else{
            return  $this->respondWithError("Invalid PIN number");
        }
        
    }

    public function list_products(Request $request)
    {
        $data = $request->data;
        $data['req_parameter'] = session("cust_id");
        $prdct_to_upgrade = $data['prdct_to_upgrade'] ?? false;
        $custServ = new CustAppService();
        $result = $custServ->get_fa_products($data,$prdct_to_upgrade);
        return $this->respondData($result);
    }

    public function get_fa_appl_summary(Request $request)
    {
        $data = $request->data;
        $data['cust_id'] = session('cust_id');
        $product_id = $request->data['product_id'];
        $custServ = new CustAppService();
        $result = $custServ->get_fa_confirm_data($data);

        return $this->respondData($result);
    }

    public function get_repayment_accs(Request $request){
        $custServ = new CustAppService();
        if(isset($request->data['loan_doc_id'])){
            $loan_doc_id = $request->data['loan_doc_id'];
        }else{
            $loan_doc_id = null;
        }
        $result = $custServ->get_repayment_accs($loan_doc_id);

        return $this->respondData($result);
    }

    public function get_cust_profile(Request $request)
    {
        $data = $request->data;
        $cust_id = session('cust_id');
        $custServ = new CustAppService();
        $result = $custServ->get_cust_profile($cust_id);

        return $this->respondData($result);
    }

    public function get_support()
    {
        $cust_id = session('cust_id');
        $custServ = new CustAppService();
        $result = $custServ->get_support($cust_id);

        return $this->respondData($result);
    }

    public function get_aggr_link()
    {
        $cust_id = session('cust_id');
        $result = (new CustAppService())->get_aggr_link($cust_id);

        return $this->respondData($result);
    }

    public function request_fa_upgrade(Request $request)
    {
        $cust_id = session('cust_id');
        $amount = $request->data['amount'];
        $acc_number = $request->data['acc_number'];
        $acc_prvdr_code = $request->data['acc_prvdr_code'];
        $result = (new CustAppService())->request_fa_upgrade($cust_id, $amount, $acc_number, $acc_prvdr_code);

        return $this->respondWithMessage("FA Upgrade request has been submitted successfully");
    }

    private function validate_pin($pin){
        return (new CustAppService())->validate_pin($pin);
        }

    public function get_FAQs()
    {
        $resp = (new CustAppService())->get_FAQs();

        return $this->respondData($resp);
    }

    public function get_pay_now_info(Request $request)
    {
        $ongoing_loan_doc_id = $request->data['loan_doc_id'];
        $resp = (new CustAppService())->get_pay_now_info($ongoing_loan_doc_id);

        return $this->respondData($resp);
    }

    public function update_payment_status(Request $request)
    {
        $id = $request->data['id'];
        $status = $request->data['status'];
        (new PaymentAttempt())->update_record_status($status,$id);

        return $this->respondData(true);
    }

    public function request_profile_update(Request $request)
    {
        $sections = $request->data["sctns"];
        $cust_cmnts = $request->data["cust_cmnts"];

        (new CustAppService())->request_profile_update($sections, $cust_cmnts);

        return $this->respondWithMessage("Profile Update Request Received");
    }

    public function get_acc_prvdr(Request $request){
        $data['country_code'] = session('country_code');
        $data["status"] = "enabled";
        $data['biz_account'] = true;
        $acc_prvdr_repo = new AccProviderRepositorySQL();
        $resp = $acc_prvdr_repo->list($data,['acc_prvdr_code', 'name']);
        return $this->respondData($resp);
 
    }

    public function cust_feedback(Request $request){
        $custServ = new CustAppService();
        $result = $custServ->cust_feedback($request->ratings);
        if(isset($result['ratings'])){
            return $this->respondData($result);
        }else{ 
            return $this->respondWithMessage('Thank you for your valuable ratings');
        }
    }
    
    public function list_complaints(Request $request){
       
        $data=$request->data;
        $custServ = new CustAppService();
        $result =$custServ->list_complaints($data);
        return $this->respondData($result);

    }

    public function resolved_complaints(Request $request){
        
        $custServ = new CustAppService();
        $result = $custServ->resolved_complaints($request->data);
        return $this->respondSuccess("Complaint Resolved Successfully");

    }

    public function view_customer_complaints(Request $request){

        $data=$request->data;
        $custServ = new CustAppService();
        $result =$custServ->view_customer_complaints($data);
        return $this->respondData($result);

    }


    public function cust_complaint(Request $request){
        $data = $request;
        $custServ = new CustAppService();
        $result = $custServ->cust_complaint($data);
        return $this->respondData($result);
    }

    public function view_cust_complaints(Request $request){

        $cust_id = session('cust_id');
        $custServ = new CustAppService();
        $result =$custServ->view_cust_complaints($cust_id);
        return $this->respondData($result);
    }

    public function get_master_data(Request $request){
        $data['data_key'] = $request->data_key;
        $data['status'] = 'enabled';
        $custServ = new CustAppService();
        $result =$custServ->get_master_data($data);
        return $this->respondData($result);
    
    }

    public function rm_visit_request(Request $req){
        $req['cust_id'] = session('cust_id');
        $result = (new CustAppService())->rm_visit_request($req->data);
        return $this->respondSuccess("New visit scheduled successfully.");
    }

    public function rm_visit_list(Request $request){
        $data['cust_id'] = session("cust_id");
        $result = (new CustAppService())->rm_visit_list($data);
        return $this->respondData($result);
    }

    public function get_cust_gps(Request $request){
        $data=$request->data; 
        $result = (new CustAppService())->get_cust_gps($data);
        if($result){
            return $this->respondWithMessage('Customer gps added successfully');
        }
        else{
            return  $this->respondWithError("Unable to add customer gps");
        } 
    }

}
