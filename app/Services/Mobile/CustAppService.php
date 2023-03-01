<?php
namespace App\Services\Mobile;


use App\Consts;
use App\Mail\FlowCustomMail;
use App\Models\AddressConfig;
use App\Models\CustComplaints;
use App\Models\LoansView;
use App\Models\FAUpgradeRequest;
use App\Models\PaymentAttempt;
use App\Models\CustFeedback;
use App\Models\MasterData;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\AccProviderRepositorySQL;
use App\Repositories\SQL\AddressInfoRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\LoanApplicationRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\LoanTransactionRepositorySQL;
use App\Repositories\SQL\MarketRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Services\BorrowerService;
use App\Services\Factors;
use App\Services\LoanApplicationService;
use App\Services\AgreementService;
use App\Repositories\SQL\CustAgreementRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Services\AccountService;
use App\Services\LoanService;
use App\Services\Mobile\RMService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use function JmesPath\search;
use App\Services\Support\FireBaseService;
use App\Services\Support\SMSNotificationService;
use App\Repositories\SQL\CommonRepositorySQL;

class CustAppService{

    public function get_recent_fas($data)
    {
        $loans = (new LoanService())->loan_search($data,['loan_doc_id','loan_principal','disbursal_date','paid_date','duration','flow_fee','paid_amount','due_date','penalty_days','acc_prvdr_code','acc_number','product_id']);
        $result = $loans['results'];

        return $result;
    }

    public function get_fa_detail($loan_doc_id){
        $prdct_repo = new LoanProductRepositorySQL();
        $loan_txn_repo = new LoanTransactionRepositorySQL();
        $loan = (new LoansView())->find_by_code($loan_doc_id,['disbursal_date','paid_date','due_date','duration','loan_appl_date','product_id','tot_paid','penalty_days','tot_penalty','os_total','paid_amount']);
        $loan->product_penalty = $prdct_repo->get_penalty_amount($loan->product_id);
        $loan->payments = $loan_txn_repo->get_payments($loan_doc_id,['amount','txn_date']);
        unset($loan->product_id);

        return $loan;
    }

    public function get_home_fa($cust_id){
        $loan_repo = new LoanRepositorySQL();
        $loan_appl_repo = new LoanApplicationRepositorySQL();
        $borrower = (new BorrowerRepositorySQL())->get_record_by('cust_id',$cust_id,['ongoing_loan_doc_id','pending_loan_appl_doc_id','last_loan_doc_id']);
        if($borrower->ongoing_loan_doc_id){
            $loan = $loan_repo->get_outstanding_loan($borrower->ongoing_loan_doc_id,['loan_doc_id','loan_principal','due_date','duration','flow_fee','paid_amount','penalty_days','cust_acc_id']);
            $loan_detail = (new LoansView())->find_by_code($borrower->ongoing_loan_doc_id,['os_total']);
            $acc = (new AccountRepositorySQL())->find($loan->cust_acc_id,['acc_number','alt_acc_num','acc_prvdr_code']);
            $loan->isd = (new MarketRepositorySQL())->get_isd_code(session('country_code'))->isd_code;
            $loan->alt_acc_num = $acc->alt_acc_num;
            $loan->acc_number = $acc->acc_number;
            $loan->acc_prvdr_code = $acc->acc_prvdr_code;
            $loan->os_total = $loan_detail->os_total;
            unset($loan->cust_acc_id);
            $result = ['ong_fa' => $loan];
        }
        else if($borrower->pending_loan_appl_doc_id){
            $loan_appl = $loan_appl_repo->get_loan_appl($borrower->pending_loan_appl_doc_id,['loan_principal', 'loan_appl_doc_id', 'loan_appl_date', 'duration', 'flow_fee', 'status']);
            if($loan_appl->status == 'approved') {
                $loan_appl->status = 'pending_disbursal';
            }
            $result = ["fa_appl" => $loan_appl];
        }
        else if($borrower->last_loan_doc_id){
            $loan = (new LoansView())->find_by_code($borrower->last_loan_doc_id, ['loan_doc_id','loan_principal','paid_date','duration','flow_fee','paid_amount','acc_prvdr_code','acc_number','product_id','overdue_days']);
            $loan->settled_day = Carbon::parse($loan->paid_date)->diffInDays(Carbon::today(), false);
            unset($loan->paid_date);
            $rmrks = null;
            if($loan->overdue_days > 0){
                $rmrks = "You have made the repayment {$loan->overdue_days} day(s) late. This will affect your eligibility for your next Float Advance.";
            }
            else{
                $add_rmrk = false;
                $loans = (new LoanRepositorySQL())->get_records_by_many(["cust_id","status"],[$cust_id, "settled"],["overdue_days"],"and"," order by id desc limit 5");
                foreach ($loans as $ln){
                    if($ln->overdue_days > 0){
                        $add_rmrk = true;
                    }
                }
                if($add_rmrk){
                    $rmrks = "Your recent repayment behavior is not good. This will affect your eligibility for your next Float Advance.";
                }
            }
            $result = ['rept_fa' => $loan, 'rmrks' => $rmrks];
        }
        else{
                $result = null;
        }
        $loans = (new LoanRepositorySQL())->get_records_by_many(["cust_id","status"],[$cust_id, "settled"],["overdue_days", "loan_principal", "paid_date", "cs_result_code"],"and"," order by id desc limit 5");
        $result['loan_rprt'] =  $loans;
        return $result;
    }

    public function get_fa_products($data,$prdct_to_upgrade = false)
    {
        $mode = session('channel'); 
            // $cust_id = $data['req_parameter'];
        $borrower = (new BorrowerService())->search_borrower($data['req_parameter']);
        $cust_id = $borrower[0]->cust_id;
        if(!isset($data['acc_number'])){
            $data['acc_number'] = (new AccountRepositorySQL())->get_acc_num($data['account_id']);
        }
        if($mode == "cust_app" && $cust_id != session("cust_id")){
            thrw("This account ({$data['acc_number']}) do not belong to you");
        }
        $requested_amount = 0;
        $fa_upgrade = (new FAUpgradeRequest())->get_record_by_many(['cust_id', 'status'], [$cust_id,Consts::UPGRADE_REQUEST], ['id','requested_amount']);
        if(isset($fa_upgrade->id)){
            $requested_amount = $fa_upgrade->requested_amount;
        }
        $data["req_parameter"] = $data["acc_number"];
        $loan_appl_serv = new LoanApplicationService();
        $products = $loan_appl_serv->product_search($data);
        $fa_prdcts = array();
        $amounts = array_values(array_unique(array_column($products["loan_products"],'max_loan_amount')));
        $max_eligible =$this->get_fa_limit($cust_id);
        if(!$max_eligible){
          $max_eligible = max($amounts);
        }
    
        $upgradable_amount = array();
        foreach ($amounts as $key => $amount){
            if($amount > $max_eligible){
                $upgradable_amount[] = $amount;
                unset($amounts[$key]);
            }
        }
        $amounts = array_values($amounts);
        if($prdct_to_upgrade && $mode = "cust_app"){
            sort($upgradable_amount);
            $upgradable_amount = array_slice($upgradable_amount,0,config('app.fa_upgradable_upto'));
            if(count($upgradable_amount) == 0 && $mode == "cust_app"){
                thrw("You are already eligible for the maximum possible FA amount.");
            }
            return  ["upgradable_amounts" => $upgradable_amount, "crnt_fa_limit" => $max_eligible];
        }
        else{
            if ($mode == "web_app"){
                sort($upgradable_amount);
                $upgradable_amount = array_slice($upgradable_amount,0,config('app.fa_upgradable_upto'));
                foreach ($upgradable_amount as $amt) {
                    $amounts[] = $amt;
                }
                $fa_prdcts['cust_agreement_status'] = $products['cust_agreement_status'];
                $fa_prdcts['borrower'] = $products['borrower'];
                $fa_prdcts['all_csf_values'] = $products['all_csf_values'];
                $fa_prdcts['all_ineligible'] = $products['all_ineligible'];
                $fa_prdcts['category'] = $products['category'];
            }
            foreach ($products["loan_products"] as $prdct) {
                $amount = $prdct->max_loan_amount;
                $index = array_search($amount, $amounts);
                if ($index !== false) {
                    if($mode == "cust_app"){
                        $opt = ['id' => $prdct->id, 'duration' => $prdct->duration, 'flow_fee' => $prdct->flow_fee, 'flow_fee_type' => $prdct->flow_fee_type, 'penalty_amount' => $prdct->penalty_amount];
                        $fa_prdcts[$index]['loan_principal'] = $amount;
                        $fa_prdcts[$index]['prod'][] = $opt;
                    }
                    elseif ($mode == "web_app") {
                        if (($prdct->result_code == "eligible" || $prdct->result_code == "requires_flow_rm_approval") && in_array($amount, $upgradable_amount)){
                            $prdct->result_code = "product upgrade";
                        }
                        $fa_prdcts['loan_products'][] = $prdct;
                    }
                }
            }
        }
        if($mode == "cust_app"){
            return ["fa_prdcts" => $fa_prdcts, 'requested_amount' => $requested_amount, "crnt_fa_limit" => $max_eligible] ;
        }
        elseif ($mode == "web_app"){
            return $fa_prdcts;
        }
    }

    public function get_fa_confirm_data($data)
    {
        $aggr_link = $this->get_aggr_link($data['cust_id']);
        $prdct = (new LoanProductRepositorySQL())->get_loan_product($data['product_id'],['duration','max_loan_amount as loan_principal','flow_fee','penalty_amount']);
        $prdct->due_date = carbon::parse(getDueDate($prdct->duration))->format('Y-m-d H:m:s');


        return ['aggr_link' => $aggr_link, 'prod' => $prdct];

    }

    public function get_repayment_accs($ongoing_loan_doc_id = null){
        $person_repo = new PersonRepositorySQL();
        $acc_details = (new AccountRepositorySQL())->get_record_by_many(['cust_id','status'],[session('cust_id'),'enabled'],['acc_number', 'acc_prvdr_code', 'alt_acc_num', 'branch']);
        if($ongoing_loan_doc_id)
        {
            $acc_prvdr_code = (new LoanRepositorySQL())->get_outstanding_loan($ongoing_loan_doc_id, ['acc_prvdr_code'])->acc_prvdr_code;
        }
        else{
            $acc_prvdr_code = $acc_details->acc_prvdr_code;
            $acc_number = $acc_details->acc_number;
        }
        $branch =  $acc_details->branch;

        if($acc_details->alt_acc_num == null && in_array($acc_details->acc_prvdr_code, config("app.acc_prvdr_support_ussd")[session('country_code')])){

                $person_id =  (new BorrowerRepositorySQL)->get_record_by('cust_id', session('cust_id'), ['owner_person_id', 'flow_rel_mgr_id']);
                $cust_mobile_num = $person_repo->get_person_contacts($person_id->owner_person_id);
                $cust_full_name  = $person_repo->full_name($person_id->owner_person_id);
                $rel_mgr_email = $person_repo->get_contact_rel_mgr($person_id->flow_rel_mgr_id);
            
                Mail::to([get_csm_email(),$rel_mgr_email->email_id])->
                            queue((new FlowCustomMail('alt_acc_num_not_config',['cust_id' => session('cust_id'),
                                                                               'mobile_num' => $cust_mobile_num->mobile_num,
                                                                               'cust_name' => $cust_full_name,
                                                                                'acc_num' => $acc_details->acc_number,
                                                                                'acc_prvdr_code' => $acc_details->acc_prvdr_code,
                                                                                'country_code' => session('country_code'),
                                                                                'app' => 'CUST APP'
                                                                            ]))->onQueue('emails'));

            
                               
        }

        $acc_arr = array();
        $addl_sql = " and cust_id is null";
        if(in_array($acc_prvdr_code, config('app.bank_repayment_exempted_list')[session('country_code')])){
            $addl_sql .= " and type != 'bank' ";
        }
        if(in_array($acc_prvdr_code, config('app.branch_wise_acc_prvdr_list'))){
            if($branch){
                $addl_sql .= " and (branch = '{$branch}' or type = 'bank')";
            }
            else{
                $addl_sql .=" and ( type = 'bank' )";
            }
        }
        $accounts = (new AccountRepositorySQL())->get_accounts_by(['network_prvdr_code','status', 'to_recon'],[$acc_prvdr_code,'enabled', 1],['acc_number','holder_name','acc_prvdr_code','type'],true, $addl_sql);
        foreach($accounts as $acc){
            $acc_arr[$acc->type][$acc->acc_prvdr_code]['accounts'][] = ['acc_number' => $acc->acc_number, 'holder_name' => $acc->holder_name];
        }
        if(isset($acc_details->acc_number)){
            return ['acc_prvdr_code' => $acc_prvdr_code,'acc_number' => $acc_details->acc_number, 'optns' => $acc_arr];
        }
        else{
            return  ['optns' => $acc_arr];
        }

    }

    public function get_cust_profile($cust_id)
    {
        $brwr_repo = new BorrowerRepositorySQL();
        $biz_addr = (new AddressConfig())->get_records_by_many(['country_code','status'], [session('country_code'), 'enabled'], ['field_num']);
        $biz_addr_fields = array_column($biz_addr, 'field_num');
        $biz_addr_fields[] = "country_code";
        $cust = $brwr_repo->view($cust_id,['borrower','owner_person','biz_address'],['borrower' => ['biz_name','photo_shop','tot_loans','biz_type','owner_person_id','biz_address_id','first_loan_date'],'owner_person' => ['first_name','middle_name','last_name','gender','dob','national_id','mobile_num','alt_biz_mobile_num_1','alt_biz_mobile_num_2','whatsapp','email_id'],'biz_address' => $biz_addr_fields]);
        $cust->photo_shop = get_file_path("persons",$cust->owner_person_id,"photo_pps")."/s_".$cust->photo_shop;
        unset($cust->biz_type,$cust->owner_person_id,$cust->biz_address_id,$cust->first_loan_date);
        return $cust;
    }

    public function get_support($cust_id)
    {
        $borrower_repo  = new BorrowerRepositorySQL();
        $person_repo = new PersonRepositorySQL();
        $borrower = $borrower_repo->find_by_code($cust_id,['flow_rel_mgr_id','acc_prvdr_code']);
        $support['customer_sucess']['mobile_num'] = config('app.customer_success')[$borrower->acc_prvdr_code];
        $rm = $person_repo->find($borrower->flow_rel_mgr_id,['email_id','mobile_num']);
        $rm->flow_rel_manager_name = $person_repo->full_name($borrower->flow_rel_mgr_id);
        $support['relational_manager'] = $rm;

        return $support;
    }

    public function get_aggr_link($cust_id){
        $aggr = (new CustAgreementRepositorySQL())->get_active_cust_aggr(['cust_id' => $cust_id]);
        $aggr_link = separate(["files".get_file_rel_path($cust_id,'agreement','pdf'),$aggr->aggr_doc_id]).".pdf";

        return $aggr_link;
    }

    public function get_fa_limit($cust_id){
        $borrower_repo = new BorrowerRepositorySQL();
        $borrower = $borrower_repo->get_record_by('cust_id',$cust_id,['fa_upgrade_id']);
        $loan_amount = null;
        if(isset($borrower->fa_upgrade_id)){
            $fa_upgrade = (new FAUpgradeRequest())->get_record_by_many(['id','cust_id','status'],[$borrower->fa_upgrade_id,$cust_id,'approved'],['upgrade_amount']);
            if(isset($fa_upgrade->upgrade_amount)){
                $loan_amount = $fa_upgrade->upgrade_amount;
            }
        }
        if($loan_amount == null){
            $loan = $borrower_repo->get_last_loan($cust_id,['loan_principal']);
            if(isset($loan->loan_principal)){
                $loan_amount = $loan->loan_principal;
            }
        }
            return $loan_amount;
    }

    public function request_upgrade_status_web($cust_id){
        $fa_upgrade = (new FAUpgradeRequest())->get_record_by_many(['cust_id', 'status'], [$cust_id,Consts::UPGRADE_REQUEST], ['requested_amount', 'id']);
        if(isset($fa_upgrade->requested_amount)){
            return ['requested_amount' => $fa_upgrade->requested_amount];
        }
        return null;
    }

    public function request_fa_upgrade($cust_id, $requested_amount, $acc_number, $acc_prvdr_code)
    {
        $fa_upgrade = (new FAUpgradeRequest())->get_record_by_many(['cust_id', 'status'], [$cust_id,Consts::UPGRADE_REQUEST], ['requested_amount', 'id']);
        if(isset($fa_upgrade->id) && session('channel') == "cust_app"){
            thrw("You already have an Upgrade Request.");
        }
        else if(isset($fa_upgrade->id) && session('channel') == "web_app"){
            return ["requested_amount" => $fa_upgrade->requested_amount];
        }
        $borrower_repo = new BorrowerRepositorySQL();
        $approvers = config('app.fa_upgrade_approvers');
        $upgrades = $this->get_fa_products(["acc_number" => $acc_number, "acc_prvdr_code" => $acc_prvdr_code, "req_parameter" => $cust_id],true);
        $crnt_fa_limit = $upgrades['crnt_fa_limit'];
        $mail_data['elig_fa_limit'] = $crnt_fa_limit;
        $mail_data['upgrade_amt'] = $requested_amount;
        $country_code = session('country_code');
        $approval_data = array();
        foreach ($approvers as $approver){
            if($approver == 'relationship_manager'){
                $approver_id = $borrower_repo->get_flow_rel_mgr_id($cust_id);
                $approver_name =  (new PersonRepositorySQL)->full_name($approver_id);
                $approval_data[] = ['person_id' => $approver_id, 'approved' => false , 'approver_name' => $approver_name];
                $rel_mgr_contact = (new PersonRepositorySQL)->get_contact_rel_mgr($approver_id);
                $mail_recip = $rel_mgr_contact->email_id;
                $data['notify_type'] = 'fa_upgrade_request';
                $this->send_fire_base_notify($approver_id, $data);
                $this->fa_upgrade_request_mail($mail_recip,$cust_id,$mail_data);
            }
            else{
                $app_users = db::select("select person_id from app_users where role_codes = '{$approver}' and status = 'enabled' and country_code = '{$country_code}'");
                foreach ($app_users as $app_user){
                    $approver_id = $app_user->person_id;
                    $approver_name =  (new PersonRepositorySQL)->full_name($approver_id);
                    $approval_data[] = ['person_id' => $approver_id, 'approved' => false, 'approver_name' => $approver_name];
                    $approver_contact = (new PersonRepositorySQL)->get_person_contacts($approver_id);
                    $mail_recip = $approver_contact->email_id;
                    $this->fa_upgrade_request_mail($mail_recip,$cust_id,$mail_data);
                }
            }
        }
        $approval_json = json_encode($approval_data);
        $upgrades = $upgrades['upgradable_amounts'];
        $available_amount= array();
        foreach ($upgrades as $upgrade){
            if($upgrade <= $requested_amount) {
                $available_amount[] = $upgrade;
            }
        }
        $available_amount = json_encode($available_amount);
        $upgrade_id = (new FAUpgradeRequest())->insert_model(['cust_id' => $cust_id,'type' => 'upgrade', 'status' => Consts::UPGRADE_REQUEST, 'requested_amount' => $requested_amount, 'crnt_fa_limit' => $crnt_fa_limit, 'available_amounts' => $available_amount, 'approval_json' => $approval_json, 'country_code' => $country_code, 'acc_prvdr_code' => $acc_prvdr_code]);
        (new BorrowerRepositorySQL())->update_model_by_code(['cust_id' => $cust_id, "fa_upgrade_id" => $upgrade_id]);
    
        return true;
    }

    public function validate_pin($pin){
        $user = auth()->user();
        $mobile_num = $user->mobile_num;
        $credentials = ['mobile_num' => $mobile_num, 'password' => $pin, 'country_code' => session('country_code')];
        $authenticated = auth()->attempt($credentials);
        if(!$authenticated){
            return false;
        }
        else{
            return true;
        }
        }
        
    public function get_FAQs()
    {
        $faqs = \config('data.faqs');

        $faqs_array = array();

        foreach ($faqs as $qstn => $ans){
            $faqs_array[] = ['qstn' => $qstn, 'ans' => $ans];
        }

        return $faqs_array;
    }

    public function get_pay_now_info($ongoing_loan_doc_id)
    {
        $loan = (new LoansView())->find_by_code($ongoing_loan_doc_id,['cust_acc_id', 'os_total']);
        $borrower = (new AccountRepositorySQL())->find($loan->cust_acc_id,['acc_number', 'acc_prvdr_code']);
        $acc_prvdr_code = $borrower->acc_prvdr_code;
        $acc_number = $borrower->acc_number;
        $recipient_acc = (new AccountService())->get_lender_disbursal_account(session('lender_code'), $acc_prvdr_code,$acc_number);
        $payment_ussd = (new USSDService())->get_payment_ussd($acc_prvdr_code, $recipient_acc->acc_number, $loan->os_total, $acc_number);
        $resp = ['frm_acc_num' => $acc_number , 'to_acc_hldr' => $recipient_acc->holder_name, 'to_acc_num' => $recipient_acc->acc_number, 'acc_prvdr_code' => $acc_prvdr_code] + $payment_ussd;

        $id = (new PaymentAttempt())->insert_model(['status' => 'generated', 'loan_doc_id' => $ongoing_loan_doc_id, 'cust_id' => session('cust_id'), 'flow_request' => json_encode($resp), 'country_code' => session('country_code')]);

        return $resp + ['id' => $id];
    }

    public function get_performance($cust_id)
    {
        $loans = (new LoanRepositorySQL())->get_records_by_many(["cust_id"],[$cust_id],["disbursal_date"],"and"," order by id desc limit 10");
        if(count($loans)> 0) {
            $first_date = (new BorrowerRepositorySQL())->find_by_code($cust_id, ["first_loan_date"])->first_loan_date;
            $rcnt_date = end($loans)->disbursal_date;
            $factor = (new Factors($cust_id, $first_date, true));
            $overall_perf = $factor->_ontime_loans_pc();
            $factor = (new Factors($cust_id, $rcnt_date, true));
            $recent_perf = $factor->_ontime_loans_pc();

            return ['overall' => $overall_perf, 'rcnt' => $recent_perf];
        }else{
            return ['overall' => 'nil', 'rcnt' => 'nil'];
        }
    }
  
    public function request_profile_update($section,$cust_cmnts)
    {
        $cust_id = session("cust_id");
        $country_code = session("country_code");
        $data["cust_id"] = session("cust_id");
        $data["cust_name"] = (new PersonRepositorySQL())->full_name(auth()->user()->person_id);
        $data["mobile_num"] = auth()->user()->mobile_num;
        $data['sctn_list'] = implode(", ",$section);
        $data['cust_cmnts'] = $cust_cmnts;
        $data['country_code'] = session("country_code");

        $rm_id = (new BorrowerRepositorySQL())->get_flow_rel_mgr_id($cust_id);
        $emails = DB::select("select email from app_users where ((person_id = {$rm_id}) or role_codes in ('operations_auditor', 'ops_admin')) and status = 'enabled' and country_code = '{$country_code}'");
        $email_array = array_column($emails,'email');

        Mail::to($email_array)->send(new FlowCustomMail('profile_update_request', $data));
    }

    public function get_cust_loan_details($cust_id){

        $loan_details = DB::selectone("select count(*) no_of_loans, sum(loan_principal) as total_amt_disb from loans where cust_id = ? and status != 'voided' and fee_waived = 0 and  country_code = ?",[$cust_id,session('country_code')]);

        $cust_reg = (new BorrowerRepositorySQL)->get_record_by('cust_id', $cust_id, ['reg_date']);
        
        $cust_loan_details['no_of_loans'] = $loan_details->no_of_loans;
        $cust_loan_details['total_amt_disb'] = $loan_details->total_amt_disb;
        $cust_loan_details['reg_date'] = $cust_reg->reg_date;

        return $cust_loan_details;
    }

    public function fa_upgrade_request_mail($mail_recip,$cust_id,$mail_data){

        $borrower = (new BorrowerRepositorySQL)->get_record_by('cust_id', $cust_id, ['owner_person_id','biz_name']);
        $mail_data['cust_id'] = $cust_id;
        $mail_data['biz_name'] = $borrower->biz_name;
        $mail_data['cust_name'] = (new PersonRepositorySQL)->full_name($borrower->owner_person_id);
        
        $cust_contact = (new PersonRepositorySQL)->get_person_contacts($borrower->owner_person_id);
        $mail_data['mobile_num'] = $cust_contact->mobile_num;
        $mail_temp = 'fa_upgrade_request';
        $mail_data['country_code'] = session('country_code');
        Mail::to($mail_recip)->queue((new FlowCustomMail($mail_temp,$mail_data))->onQueue('emails'));

    }

    public function send_fire_base_notify($person_id, $data){
         
        $app_user = DB::selectOne("select messenger_token from app_users where person_id = {$person_id} and status = 'enabled'");
        
        if($app_user && $app_user->messenger_token){
            $serv = new FireBaseService();
            $serv($data, $app_user->messenger_token);
        }
    
    }

    public function cust_feedback($ratings){
        $borrower_repo = new BorrowerRepositorySQL();
        $cust_feedback = new CustFeedback();
        
        $feedback_questions = config('app.feedback_questions');
        $data['cust_id'] = session("cust_id");
        $data['rm_id'] = $borrower_repo->get_flow_rel_mgr_id($data['cust_id']);
        $data['country_code'] = session('country_code');
        $record = $borrower_repo->get_record_by_many(['cust_id', 'status'], [$data['cust_id'], 'enabled'], ['rm_feedback_due']);
        $cust_ratings = DB::selectone("select ratings from cust_feedbacks where cust_id = ? order by id DESC limit 1", [$data['cust_id']]);
        if(is_array($ratings)){ 
            $data['ratings'] = $ratings;
            $data["total_score"] = array_sum($ratings); 
            if($record->rm_feedback_due == true){
                $cust_feedback->insert_model($data);
                $res = $borrower_repo->update_model_by_code(['rm_feedback_due' => false, 'cust_id' => $data['cust_id']]);
                if($res){
                    return ['ratings' => null];
                }
            }else{
                thrw("Sorry for the inconvenience you are unable to rating now");
            } 
        }else{
            if(!empty($cust_ratings)){
                $feedback_rating = json_decode($cust_ratings->ratings);
            }else{
                $feedback_rating = array_fill_keys($feedback_questions, 'NA');
            }
            return ['ratings' => $feedback_rating, 'rm_feedback_due' => $record->rm_feedback_due];
        } 
    }
    public function cust_complaint($data){

        $person_repo = new PersonRepositorySQL();
        $borrower_repo = new BorrowerRepositorySQL();
        $cust_id = session("cust_id");
        $complaint_type = $data["complaint_type"];
        $remarks =$data["remarks"];
        $raised_date = Carbon::now()->format("Y-m-d");

        $inst_cust_complaints = (new CustComplaints)->insert_model(['cust_id' => $cust_id,
                                            'complaint_type' => $complaint_type,
                                            'remarks' => $remarks,
                                            'raised_date' => $raised_date,
                                            'country_code' => session('country_code'),
                                            'status' => 'raised']);
         
        if(isset($inst_cust_complaints)){

            $mail_data['cust_name'] = $person_repo->full_name_by_cust_id($cust_id);
            $mail_data['complaint_type'] = $complaint_type;
            $mail_data['remarks'] = $remarks;
            $mail_data['country_code'] = session('country_code');

            send_email('customer_complaint',[get_ops_admin_email()], $mail_data, true);
        }  
    }

    public function view_cust_complaints($cust_id){


        $from_date = Carbon::now()->subMonthsNoOverflow(5)->startOfMonth()->format('Y-m-d');

        $addtn_condn = "and raised_date >= '$from_date' order by id desc";
        $cust_complaint_list = (new CustComplaints)->get_records_by('cust_id', $cust_id, ['raised_date', 'complaint_type', 'remarks', 'status', 'resolution', 'resolved_date', 'updated_by'],null, $addtn_condn);

        if($cust_complaint_list){
            $data_codes = collect($cust_complaint_list)->pluck('complaint_type')->toarray();
            if(sizeof($data_codes) > 0){
                $data_codes = array_unique($data_codes);
                foreach($data_codes as $data_code){
                    $data_value[$data_code] = $this->get_dd_value($data_code);
                }
            }
            $person_repo = new PersonRepositorySQL();
            foreach($cust_complaint_list as $list){
                
                $list->complaint_type = $data_value[$list->complaint_type]->data_value;
                if(isset($list->updated_by)) {
                    $person = $person_repo->get_person_by_user_id($list->updated_by);
                    $list->resolved_by = $person->first_name . " " . $person->last_name;
                    unset($list->updated_by);
                }
            }
        }
        return $cust_complaint_list;
    }

    public function get_master_data($master_data){

        $country_code = DB::select("select distinct(country_code) from master_data where status = ? and data_key = ?", [$master_data['status'], $master_data['data_key']]);
        if(count($country_code) > 1){
             $master_data['country_code'] = session('country_code');
            $inc_global = true;
        }else{
            $inc_global = false;
        }
        $common_repo = new CommonRepositorySQL();
        $get_data = $common_repo->get_master_data($master_data,$inc_global,['data_code', 'data_value']);
      
        return  $get_data;
    }

    public function get_dd_value($data_code, $country_code = '*'){
        $data_value = DB::selectOne("select data_value from master_data where data_code = ? and country_code = ?", [$data_code,$country_code]);
        return $data_value;
    }

    public function list_complaints($data){

        $country_code=session('country_code');
        $sql="select c.cust_id,c.id,c.complaint_type,c.raised_date,c.remarks,c.status,c.resolution,b.biz_name,p.mobile_num from cust_complaints c,borrowers b,persons p where b.cust_id=c.cust_id and b.owner_person_id= p.id and c.country_code='$country_code'";
        $list_complaints= DB::select($sql);
        foreach ($list_complaints as $list_complaint){
            $list_complaint->resolution = json_decode($list_complaint->resolution);
        }
        return $list_complaints;
    }

    public function  resolved_complaints($data){
        
        $cust_cmplint = new CustComplaints();
        $country_code = session('country_code');
        $resolved_date = Carbon::now()->format("Y-m-d");
        $raised_complaints = $cust_cmplint->update_model(['resolution' => $data['resolution'], 'status' => 'resolved', 'resolved_date' => $resolved_date,'country_code' => $country_code, 'id' => $data['id']]);   
        if(isset($raised_complaints)){

            //Send Email, Notification, SMS to customer           
            $person_repo = new PersonRepositorySQL();
            $cmplnt_details = $cust_cmplint->get_record_by('id', $data['id'], ['cust_id', 'complaint_type', 'created_at']);
            $person_id = (new BorrowerRepositorySQL)->get_record_by('cust_id', $cmplnt_details->cust_id, ['owner_person_id'])->owner_person_id;
            $cust_details = $person_repo->get_record_by('id', $person_id, ['first_name', 'mobile_num']);

            $sms_data['country_code'] = $country_code;
            $sms_data['cust_name'] = $cust_details->first_name;
            $sms_data['cust_mobile_num'] = $cust_details->mobile_num;
            $sms_data['raised_date'] = Carbon::parse($cmplnt_details->created_at)->format('d-M-Y');
            $sms_template = 'CUST_RESOLVED_COMPLAINT_MSG';
            
            $email_data['cust_name'] = $person_repo->full_name_by_cust_id($cmplnt_details->cust_id);
            $email_data['date_of_complaint'] = $data['raised_date'];
            $email_data['complaint_type'] = $this->get_dd_value($data['complaint_about'])->data_value;
            $email_data['remarks'] = $data['remarks'];
            $email_data['resolution'] = $data['resolution'];
            $email_data['country_code'] = $country_code;

            $notify_data['notify_type'] = 'resolved_complaints'; 
            $notify_data['complaint_type'] = $this->get_dd_value($data['complaint_about'])->data_value;

            send_email('customer_resolved_complaint', [get_market_admin_email(), get_super_admin_email()], $email_data, true);
            $this->send_fire_base_notify($person_id, $notify_data);
            (new SMSNotificationService)->send_notification_message($sms_data, $sms_template);

        }
        return $raised_complaints;

    }


    public function  view_customer_complaints($data){
      
        m_array_filter($data);
        $person_repo = new PersonRepositorySQL();
        $borrower_repo = new BorrowerRepositorySQL();
        $req= $data['borrower_search_criteria'] ?? null;
        unset($data['borrower_search_criteria']);
        $field_names = array_keys($data);
        $field_values = array_values($data);
        
        
        if($req){

            $borrower_serv = new BorrowerService($data['country_code']);
            $borrowers = $borrower_serv->borrower_search($req,['cust_id','lender_code','acc_prvdr_code','flow_rel_mgr_id','dp_rel_mgr_id','reg_flow_rel_mgr_id','owner_person_id']);
            $cust_id= $borrowers['results'][0]->cust_id;
           
            $results= $borrower_repo ->get_record_by('cust_id',$cust_id);
            array_push($field_names,'cust_id');
            array_push($field_values,$cust_id);
            

           if(!isset($results)){
        
               thrw("Please enter a valid search criteria");
           }
        }
          
            $fields_arr = ['raised_date', 'complaint_type','cust_id', 'remarks', 'status', 'resolution', 'resolved_date','id'];
            $results= (new CustComplaints)->get_records_by_many($field_names, $field_values, $fields_arr);
        
         foreach ($results as $result){
           
            $borrower= $borrower_repo-> get_record_by('cust_id', $result->cust_id,['biz_name', 'owner_person_id']);
            $person =$person_repo->get_record_by('id', $borrower->owner_person_id,['mobile_num']);

            $result->biz_name= $borrower ->biz_name;
            $result->mobile_num =$person->mobile_num;
         }
        
         return $results;
            
    }

    public function rm_visit_request($req){
        
        $data['cust_id'] = session('cust_id');        
        $rm_id = (new BorrowerRepositorySQL())->get_flow_rel_mgr_id($data['cust_id']);
        $data['rm_id'] = $rm_id;
        $data['sch_from'] = Consts::CUSTOMER;
        $data['sch_slot'] = $req['sch_slot'];
        $data['sch_date'] = $req['sch_date'];
        $data['sch_purpose'] = $req['sch_purpose'];
        $data['biz_name'] = (new BorrowerRepositorySQL())->get_record_by('cust_id', $data['cust_id'], ['biz_name'])->biz_name;
        $result = (new RMService())->create_schedule($data);
        if($result){
            [$email, $messenger_token] = (new PersonRepositorySQL())->get_email_n_msgr_token($data['rm_id']);
            if(isset($messenger_token)){
                $data['notify_type'] = 'rm_visit_request';
                $data['rm_visit_request']['biz_name'] = $data['biz_name'];
                $data['rm_visit_request']['sch_date'] = $data['sch_date'];

                unset($data['cust_id'], $data['sch_from'], $data['rm_id'], $data['sch_purpose']);
                $data['rm_visit_request'] = json_encode($data['rm_visit_request']);
                try {
                    $serv = new FireBaseService();
                    $serv($data, $messenger_token);
                }catch(\Exception $e){
                    send_notification_failed_mail($e, $rm_id, $data['notify_type']);
                }
            }
        }
        return $result;
    }
    public function rm_visit_list($data){
       
        $data['visitor_id'] = (new BorrowerRepositorySQL())->get_flow_rel_mgr_id($data['cust_id']);
        $data['sch_from'] = Consts::CUSTOMER;
        $field_arr = ['id','sch_slot','sch_date','sch_status','resch_id', 'visitor_id','visit_start_time','visit_end_time','remarks','sch_purpose', 'visit_purpose'];
        $addl_sql = "order by id desc limit 10";
        $visit_list = (new RMService())->get_visits_schedules($data, $field_arr, false, $addl_sql);
        $morning_slot = $post_noon_slot = []; 
        if(isset($visit_list['slots']['morning'])){
            $morning_slot = $visit_list['slots']['morning'];
        }
        if(isset($visit_list['slots']['post_noon'])){
            $post_noon_slot = $visit_list['slots']['post_noon'];
        }   
        $unorder_data = array_merge($morning_slot, $post_noon_slot);
        $results = collect($unorder_data)->sortByDesc('sch_id')->values()->all();
        foreach($results as $result){
            $result->sch_purpose = collect($result->sch_purpose)->map(function ($data_code, $key){
                                        return $this->get_dd_value($data_code)->data_value;
                                    })->all();
        }
        return $results;
        
    }  

    public function get_cust_gps($data){
        $loan = new LoanRepositorySQL();
        $ongoing_loan = (new BorrowerRepositorySQL())->get_current_loan(session('cust_id'),['id']);
        $resp = NULL;
        if($ongoing_loan){
            $date_time = datetime_db();
            $cust_data[$date_time] = $data['gps'];
            $cust_gps = json_encode($cust_data);
            $resp = $loan->update_model(['id' => $ongoing_loan->id , 'cust_gps' => $cust_gps ]);
        }
        return $resp;
    }

}