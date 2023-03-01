<?php

namespace App\Services;
use App\Models\FlowApp\AppUser;
use App\Models\Loan;
use App\Models\LoanRecovery;
use App\Models\FundUtilizationReport;
use App\Repositories\SQL\FieldVisitRepositorySQL;
use App\Repositories\SQL\OtpRepositorySQL;
use DateTime;
use Exception;
use App\Consts;
use Carbon\Carbon;
use App\SMSTemplate;
use App\Services\BorrowerService;
use App\Services\RepaymentService;
use Illuminate\Support\Facades\DB;
use App\Jobs\InstantDisbursalQueue;
use Illuminate\Support\Facades\Log;
use App\Services\Support\SMSService;
use App\Exceptions\FlowCustomException;
use Illuminate\Database\QueryException;
use App\Repositories\SQL\BaseRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\LenderRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Services\Support\SMSNotificationService;
use App\Repositories\SQL\AddressInfoRepositorySQL;
use App\Repositories\SQL\CapitalFundRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Repositories\SQL\LoanApplicationRepositorySQL;
use App\Repositories\SQL\LoanTransactionRepositorySQL;
use App\Repositories\SQL\ProbationPeriodRepositorySQL;
use App\Repositories\SQL\DisbursalAttemptRepositorySQL;
use App\Repositories\SQL\AcctDataPrvdrCommRepositorySQL;
use App\Repositories\SQL\AccountStmtRepositorySQL;
use App\Repositories\SQL\AccProviderRepositorySQL;
use App\Models\StatementImport;
use App\Mail\FlowCustomMail;
use App\Models\PreApproval;
use Mail;
use Illuminate\Support\Str;



class LoanService {
    public function get_loan($data)
    {
    	$loan_repo = new LoanRepositorySQL();
        $loan_txn_repo = new LoanTransactionRepositorySQL();
    	$borrow_repo = new BorrowerRepositorySQL();
    	$address_repo = new AddressInfoRepositorySQL();
    	$lender_repo = new LenderRepositorySQL();
    	$person_repo = new PersonRepositorySQL();
    	$account_repo = new AccountRepositorySQL();
        $acc_prvdr_repo = new AccProviderRepositorySQL();
        $loan = $loan_repo->find_by_code($data['loan_doc_id']);

        if(!session()->has('google_sheet_import')){
            if($loan->due_date && $loan->due_date < Carbon::now()
                    && $loan->status != Consts::LOAN_SETTLED
                    && $loan->status != Consts::LOAN_OVERDUE){
                $chk_penalty_days = getPenaltyDate($loan->due_date);
                if($chk_penalty_days > 0){
                    $this->process_overdue($loan);
                    $loan = $loan_repo->find_by_code($data['loan_doc_id']);
                }
            }
        }

        $borrower = $borrow_repo->find_by_code($loan->cust_id, ["id", "biz_name", "acc_number", "biz_type","owner_person_id","cust_id","pending_loan_appl_doc_id","ongoing_loan_doc_id","last_loan_doc_id","acc_purpose", "location"]);
        // $loan->repeat_fa = true;
        $loan->last_loan_doc_id = $borrower->last_loan_doc_id;
        $loan->pending_loan_appl_doc_id = $borrower->pending_loan_appl_doc_id;
        $loan->ongoing_loan_doc_id = $borrower->ongoing_loan_doc_id;
        $loan->biz_name = $borrower->biz_name;
        $loan->acc_number = $borrower->acc_number;
        $loan->biz_type = $borrower->biz_type;
        $loan->owner_person_id = $borrower->owner_person_id;
        $loan->cust_id = $borrower->cust_id;
        $loan->location = $borrower->location;
        $acc_prvdr = $acc_prvdr_repo->find_by_code($loan->acc_prvdr_code, ['name']);
        $loan->acc_prvdr_name = $acc_prvdr->name;
        $lenders = $lender_repo->find_by_code($loan->lender_code, ['name']);
        $loan->lender_name = $lenders->name;
        $flow_rel_mgr = $person_repo->find($loan->flow_rel_mgr_id,  ["id", "first_name", "last_name", "mobile_num"]);
        $loan->flow_rel_mgr_id = $flow_rel_mgr->id;
        $loan->flow_rel_mgr_mobile_num = $flow_rel_mgr->mobile_num;
        $loan->flow_rel_mgr_first_name = $flow_rel_mgr->first_name;
        $loan->flow_rel_mgr_last_name = $flow_rel_mgr->last_name;
        $loan->flow_rel_mgr_name = full_name($flow_rel_mgr);
        $loan->photo_pps_path = get_file_path("persons", $loan->owner_person_id,"photo_pps");
        $person = $person_repo->find( $loan->owner_person_id , ["photo_pps"]);
        $loan->photo_pps = $person->photo_pps;

        $dp_rel_mgr = $person_repo->find($loan->dp_rel_mgr_id,  ["id", "first_name", "last_name", "mobile_num"]);
        $loan->photo_pps_path = get_file_path("persons", $loan->owner_person_id,"photo_pps");
        $person = $person_repo->find( $loan->owner_person_id , ["photo_pps"]);
        $loan->photo_pps= $person->photo_pps;
       
         if($dp_rel_mgr)
          {
            $loan->dp_rel_mgr_id = $dp_rel_mgr->id;
            $loan->dp_rel_mgr_mobile_num = $dp_rel_mgr->mobile_num;
            $loan->dp_rel_mgr_first_name = $dp_rel_mgr->first_name;
            $loan->dp_rel_mgr_last_name = $dp_rel_mgr->last_name;
            $loan->dp_rel_mgr_name = full_name($dp_rel_mgr);
           }
        // $account = $account_repo->find($loan->cust_acc_id);
        // $loan->account_type = $account->type;
        // $loan->acc_number = $account->acc_number;
        // $loan->acc_provider_name = $account->acc_prvdr_name;
        $repayment_date = $loan_txn_repo->get_payment_date($data['loan_doc_id']);
        $loan->repayment_date = $repayment_date;

        $loan->overdue_days = get_od_days($loan->due_date, $loan->paid_date,$loan->status);
        $loan->write_off_od_days = config('app.write_off_overdue_days');
        
        if($loan->status == Consts::LOAN_HOLD){
            if($loan->disbursal_status == Consts::DSBRSL_IN_PROGRESS){
                $disb_attempt_repo = new DisbursalAttemptRepositorySQL();
                $disb_attempt = $disb_attempt_repo->get_last_disburse_attempt($data['loan_doc_id'], ['id','created_at']);
                if(Carbon::now() > Carbon::parse($disb_attempt->created_at)->addMinutes(5)){
                    $loan_repo->update_record_status(Consts::DSBRSL_UNKNOWN, $loan->id,'disbursal_status');
                    $disb_attempt_repo->update_model(['status' => Consts::DSBRSL_UNKNOWN, 'id' => $disb_attempt->id]);
                }
            }
        }

        if($loan->status == Consts::LOAN_OVERDUE){
            //Log::warning(Carbon::now()->toDateTimeString());
            //Log::Warning($loan->due_date);

            //parse_date($loan->due_date, Consts::DB_DATETIME_FORMAT)->diffForHumans();
            $this->add_overdue_details($loan);
        }

        if($loan->status == Consts::LOAN_PNDNG_DSBRSL){
            if(!$loan->customer_consent_rcvd){
                $loan->otp_status = $this->get_last_otp_status($data['loan_doc_id']);
                $loan->confirm_code_info = confirm_code_alert(session("country_code"));
            }
            $loan->due_date = format_date(getDueDate($loan->duration));
            $acc_repo = new AccountRepositorySQL();
            if($loan->cust_acc_id){
                $loan->to_acc_num = $acc_repo->get_acc_num($loan->cust_acc_id);
            }

        }
       return $loan;
    }

    public function add_overdue_details(&$loan){

        $due_date = $loan->due_date;
        $due_date = Carbon::parse($due_date);
        $due_date =$due_date->startOfDay();
        $loan->days_overdue = Carbon::parse($due_date)->diffInDays();
        $loan->penalty_days = getPenaltyDate($loan->due_date);
        $loan->provisional_penalty = $loan->provisional_penalty * $loan->penalty_days;

        $loan->balance_amount = $loan->due_amount - $loan->paid_amount;
        $loan->os_amt_w_prv_pnlty = $loan->current_os_amount + $loan->provisional_penalty;
        $sms_serv = new SMSNotificationService();  
        $loan->mobile_num = config("app.customer_success_mobile")[$loan->acc_prvdr_code];
        $message = $sms_serv->get_sms($loan, 'REGULAR_OVERDUE_MSG');
        $loan->message = $message;
        return $loan;
    }

    public function loan_search($criteria_array,  $fields_arr = ['pre_appr_id','loan_doc_id', 'loan_appl_id', 'product_name', 'product_id', 'cust_name', 'cust_mobile_num','flow_rel_mgr_id', 'product_name', 'loan_principal', 'due_amount','duration',  'current_os_amount', 'currency_code',  'cust_id', 'credit_score', 'loan_appl_date', 'loan_approved_date', 'loan_approver_name', 'status',  'loan_doc_id', 'provisional_penalty', 'penalty_collected', 'paid_amount', 'due_date', 'biz_name','disbursal_date','flow_fee_type', 'flow_fee_duration', 'flow_fee', 'paid_date', 'approver_role', 'cs_result_code','customer_consent_rcvd','disbursal_status','acc_prvdr_code', 'acc_number', 'loan_purpose', 'manual_disb_user_id']){

        if(isset($criteria_array['disburse_attempt'])){
            $disburse_attempt = $criteria_array['disburse_attempt'];
            unset($criteria_array['disburse_attempt']);
        }
        $last_visit_date = false;
        if(in_array("last_visit_date",$fields_arr)){
            $last_visit_date = true;
            unset($fields_arr[array_search("last_visit_date", $fields_arr)]);
        }
        if(array_key_exists('voided_fa', $criteria_array)){
            $voided_fa_status = $criteria_array['voided_fa'];
            unset($criteria_array['voided_fa']);
        }

        m_array_filter($criteria_array);
        $mode = $criteria_array['mode'];
        unset($criteria_array['mode']);

        $otp_count = false;
        if(array_key_exists('pending_w_cust', $criteria_array)) {
            $otp_count = true;
        }
        $addl_sql_condn = null;
        $status_cond = " ";
        if(array_key_exists('req_parameter',  $criteria_array)){
            $req_pram = $criteria_array['req_parameter'];
            $borrower_service = new BorrowerService(session('country_code'));
            $borrowers = $borrower_service->search_borrower($req_pram);
            if($voided_fa_status == "false"){
                $status_cond = "and status != 'voided'";
            }
            if(sizeof($borrowers) == 1){
                $criteria_array["cust_id"] = $borrowers[0]->cust_id;
                unset($criteria_array['req_parameter']);
            }else if (sizeof($borrowers) == 0){
                $criteria_array['loan_doc_id'] = $criteria_array['req_parameter'];
                unset($criteria_array['req_parameter']);
            }else if (sizeof($borrowers) > 1){
                thrw("Please refine your search", 8001);
            }
        }
        
        $addl_sql_condn = $this->get_addl_search_criteria($criteria_array);

        $loan_repo = new LoanRepositorySQL();
        $disbursal_condn = $this->handle_loan_status($criteria_array);

        $loans = $loan_repo->get_records_by_many(array_keys($criteria_array),  array_values($criteria_array),  $fields_arr, " and", $disbursal_condn .$status_cond. $addl_sql_condn);


        $person_repo = new PersonRepositorySQL();
        $borrower_repo = new BorrowerRepositorySQL();
        foreach ($loans as $loan) {
            if(isset($loan->flow_rel_mgr_id)){
                $flow_rel_mgr = $person_repo->find($loan->flow_rel_mgr_id, ["first_name"]);
                if($flow_rel_mgr){
                    $loan->flow_rel_mgr_name = $flow_rel_mgr->first_name;
                }
                if($last_visit_date) {
                    $borrower = $borrower_repo->find_by_code($loan->cust_id, ['last_visit_date']);
                    if ($borrower->last_visit_date) {
                        $loan->last_visit_date = $borrower->last_visit_date;
                    }
                }
            }
            if(isset($loan->manual_disb_user_id)){
                $loan->manual_disb_user_name = $person_repo->full_name($loan->manual_disb_user_id);
            }
            $loan->is_manual_disb_ap_code = (!in_array($loan->acc_prvdr_code,config('app.auto_disbursal_acc_providers'))) ? true : false;
            if(isset($disburse_attempt) && $disburse_attempt){
                $this->get_disb_attempt_info($loan);
            }


           

            if(isset($otp_count) && $otp_count){
                $otp_repo = new OtpRepositorySQL();
                $otps = $otp_repo->get_records_by('entity_id', $loan->loan_doc_id, ['id']);
                $loan->otp_count = sizeof($otps);
            }
            if(isset($loan->status)){
                $loan->overdue_days = get_od_days($loan->due_date, $loan->paid_date,$loan->status);
            }





            }


        if(empty($loans)){
            thrw("No results found for your search!", 8001, "no_results");
        }

            if($mode == 'view'){

                if(count($loans) == 1)
                {
                    $loan_doc_id = $loans[0]->loan_doc_id;

                    return ["loan_doc_id" => $loan_doc_id, 'mode' => 'view'];
                }
                else if(count($loans)>1)
                {
                    if(sizeof($borrowers) == 1){

                        if($borrowers[0]->ongoing_loan_doc_id){

                            return ["loan_doc_id" => $borrowers[0]->ongoing_loan_doc_id, 'mode' => 'view'];
                        }else if($loans[0]->status == 'pending_disbursal') {
                            return ["loan_doc_id" => $loans[0]->loan_doc_id, 'mode' => 'view'];
                        }else{
                            return ["results" => $loans, 'mode' => 'search'];
                        }

                    }

                }
            }else{

                return ["results" => $loans, 'mode' => 'search'];
            }

        return  $loans;

    }

    private function get_disb_attempt_info(&$loan){
        $disb_attempt_repo = new DisbursalAttemptRepositorySQL();
               
        $first_disb_attempt = $disb_attempt_repo->get_first_disburse_attempt($loan->loan_doc_id, ['created_at']);
    
        if($first_disb_attempt){
            $loan->first_attempt_time = $first_disb_attempt->created_at;
        }
        $disb_attempt = $disb_attempt_repo->get_last_disburse_attempt($loan->loan_doc_id, ['id','created_at','count', 'flow_request', 'updated_at']);
        if($disb_attempt){
            $loan->attempt_time = $disb_attempt->created_at;
            $loan->attempt_id = $disb_attempt->id;
            $loan->attempts = $disb_attempt->count;
            $loan->allow_status_change = false;
            $attempt_request = json_decode($disb_attempt->flow_request, true);
            if($attempt_request == null || !isset($attempt_request['from_acc'])){
                $loan->allow_status_change = true;
            }
            else{
                $disb_acc_id = $attempt_request['from_acc']['id'];
                $end_time = "null";
                if(isset($disb_attempt->updated_at)){

                    $ussd_timeout_sec = config('app.ussd_disbursal_timeout_secs');
                    $updated_at = Carbon::parse("{$disb_attempt->updated_at}")->addSeconds($ussd_timeout_sec+10);
                    $end_time = "'{$updated_at}'";
                }
                $imports = (new StatementImport)->get_records_by_many(['account_id', 'status'], [$disb_acc_id, 'imported'], ["id"], "and", " and start_time > $end_time");
                if(sizeof($imports) > 0){
                    $loan->allow_status_change = true;
                }
            }

        }

        
        $loan->otp_status = $this->get_last_otp_status($loan->loan_doc_id);
    }

    private function get_addl_search_criteria(&$criteria_array){
        $addl_sql_condition_arr = array();
        $conditions = [
            "pending_w_cust" => "status = 'pending_disbursal' and customer_consent_rcvd is false",
            "pending_w_prvdr" => "status in ('hold', 'pending_disbursal') and customer_consent_rcvd is true",
        ];

        if(session('role_codes') == 'recovery_specialist') {
            $addl_sql_condition_arr[] = "datediff(curdate(), due_date) > 15";
        }
        if(array_key_exists('exclude_wo_fa',  $criteria_array)){
            if($criteria_array['exclude_wo_fa'] == 'true'){
                $addl_sql_condition_arr[] = "write_off_status is null";
            }
            unset($criteria_array['exclude_wo_fa']);
        }
        $addl_sql_condition = get_sql_condition($criteria_array, $conditions, $addl_sql_condition_arr);

        $valid_criterias = ['status', 'last_n_fas', 'flow_rel_mgr_id', 'cust_id', 'loan_doc_id','pre_appr_tdy'];
        if($addl_sql_condition == ""){
            if(sizeof($criteria_array) == 0 || sizeof(array_intersect(array_keys($criteria_array), $valid_criterias)) == 0){
                thrw("Please enter a valid search criteria");
            }
        }

        $order_condn = "order by status, disbursal_date desc";

        $limit_str ='';
        if(array_key_exists('last_n_fas',  $criteria_array)){
            $limit_str = " limit ".$criteria_array['last_n_fas'];
            $addl_sql_condition = "and status not in ('due','overdue','ongoing')";

            unset($criteria_array['last_n_fas']);
        }

        if(array_key_exists('pre_appr_tdy', $criteria_array)){
            $today = date_db();
            $addl_sql_condition = "and date(loan_appl_date) = '{$today}' and pre_appr_id is not null"; //loan_approved_date is null on pre-approval.
            unset($criteria_array['pre_appr_tdy']);
        }
        if(array_key_exists('include_setld_fa',  $criteria_array)){
            if($criteria_array['include_setld_fa'] == 'true'){
                $addl_sql_condition = " and provisional_penalty > 0";
                unset($criteria_array['status']);
            }
            unset($criteria_array['include_setld_fa']);     
        }

        $addl_sql_condition .= " ".$order_condn." ".$limit_str;
        return $addl_sql_condition;
    }



    private function handle_loan_status(&$criteria_array){
        if(isset($criteria_array['status']) && $criteria_array['status'] == "disbursed"){
           unset($criteria_array['status']);
           return "and status not in ('pending_disbursal','pending_mnl_dsbrsl','voided', 'hold')";
       }else if(isset($criteria_array['status']) && $criteria_array['status'] == "partially_paid"){
           unset($criteria_array['status']);
           return "and status not in ('pending_disbursal','pending_mnl_dsbrsl','voided', 'hold', 'settled') and paid_amount > 0";
       }else if(isset($criteria_array['status']) && $criteria_array['status'] == "outstanding"){
           unset($criteria_array['status']);
           return "and status in ('ongoing', 'due', 'overdue')";
       }
       else if(isset($criteria_array['status']) && $criteria_array['status'] == "stalled") {
           unset($criteria_array['status']);
           $disb_status = Consts::DSBRSL_CPTR_FAILED;
           return "and status in ('hold', 'pending_disbursal') and (disbursal_status != '$disb_status' or disbursal_status is null) and customer_consent_rcvd is true";
       }
       else if(isset($criteria_array['status']) && $criteria_array['status'] == "pending_disb_capture"){
            unset($criteria_array['status']);
            $disb_status = Consts::DSBRSL_CPTR_FAILED;
            return " status in ('hold', 'pending_mnl_dsbrsl') and (disbursal_status = '$disb_status' or disbursal_status is null)";
        }
       else if(isset($criteria_array['status']) && $criteria_array['status'] == "ongoing") {
            unset($criteria_array['status']);
            $disb_status = [Consts::DSBRSL_CPTR_FAILED, Consts::DSBRSL_SUCCESS];
            $disb_status_csv = csv($disb_status);
            return "and ( (status in ('ongoing', 'due') and customer_consent_rcvd is true) or (status = 'hold' and disbursal_status  in ($disb_status_csv)) )";
        }
        else if(isset($criteria_array['status']) && $criteria_array['status'] == "pending_w_prvdr") {
            unset($criteria_array['status']);
            $disb_status= Consts::DSBRSL_CPTR_FAILED;
            return "and disbursal_status != '$disb_status' and status in ('hold', 'pending_disbursal', 'pending_mnl_dsbrsl') and customer_consent_rcvd is true";
        }
    }

    	private function allocate_fund($cust_id, $fund_code){
		$brwr_repo = new BorrowerRepositorySQL();
		$fund_repo = new CapitalFundRepositorySQL();
		$brwr_repo->update_model_by_code(['fund_code' => $fund_code, 'cust_id' => $cust_id]);
    	$fund_repo->increment_by_code('current_alloc_cust', $fund_code);

	}



	private function get_appl_fund_code($cust_id, $lender_code, $cust_fund_code, $loan_principal){

        Log::warning("get_appl_fund_code");
        Log::warning("cust_id: $cust_id, lender_code: $lender_code, cust_fund_code: $cust_fund_code, loan_principal: $loan_principal");
		$fund_repo = new CapitalFundRepositorySQL();
		$default_fund_code = $fund_repo->get_default_fund($lender_code); #FLOW-INT
        $appl_fund_code = $default_fund_code;
        Log::warning("default_fund_code: $default_fund_code");
        if($cust_fund_code == $default_fund_code){
            $min_used_fund = DB::selectOne("select fund_code, current_amount from fund_utilization_reports where date = curdate() and current_amount >= $loan_principal and country_code = ? order by util_perc limit 1", [session('country_code')]);
		    $min_used_fund_code = isset($min_used_fund) ? $min_used_fund->fund_code : null;
            Log::warning("min_used_fund_code: $min_used_fund_code");
            if ($min_used_fund_code != null){
                $fund = $fund_repo->get_fund_details($min_used_fund_code);

                if($fund->os_amount + $loan_principal <= $fund->alloc_amount) {

                    if ($fund->os_amount + $loan_principal <= (0.80 * $fund->alloc_amount)) {
                        $this->allocate_fund($cust_id, $min_used_fund_code);
                    }
                    $appl_fund_code = $min_used_fund_code;
                }

            }

        }elseif($cust_fund_code){
            $fund = $fund_repo->get_fund_details($cust_fund_code);
			if($fund->os_amount + $loan_principal <= $fund->alloc_amount){
        		$appl_fund_code = $cust_fund_code;
    		}
        }


        if($appl_fund_code != $default_fund_code){
            $fund_util_repo = new FundUtilizationReport();
            $fund_ongoing_data =  DB::selectOne("select id, current_amount from fund_utilization_reports where fund_code = '$appl_fund_code' and current_amount >= $loan_principal and date = curdate()");
            if($fund_ongoing_data){
                $new_amount = $fund_ongoing_data->current_amount - $loan_principal;
                $appl_fund = $fund_repo->get_fund_details($appl_fund_code);
                $new_util_perc = ($appl_fund->alloc_amount - $new_amount)/($appl_fund->alloc_amount);
                $fund_util_repo->update_model(['current_amount' => $new_amount, 'id' => $fund_ongoing_data->id, 'util_perc' => number_format($new_util_perc,2)]);
            }
        }

    Log::warning("appl_fund_code: $appl_fund_code");
		return $appl_fund_code;
	}


    public function disburse(array $loan_txn, $with_txn = true, $allow_overlap = false, $for_add_txn = false)
    {
        $loan_repo = new LoanRepositorySQL();
        $loan_txn_repo = new LoanTransactionRepositorySQL();
        $disb_serv = new DisbursalService();
        try
        {

            #$result = ["sms_sent" => false];
            $flow_disb_result = ['disb_status' => 'unknown'];

            if($loan_txn["amount"] <= 0)
            {
                thrw("Please enter a valid amount");
            }

            if(array_key_exists('txn_date', $loan_txn)){
                //$txn_date = $loan_txn['txn_date'];
                $disbursal_date = parse_date($loan_txn['txn_date'], Consts::DATETIME_FORMAT);
            }else{
                //$txn_date = datetime_db();

                $disbursal_date = Carbon::now(); //datetime_db();
                $loan_txn['txn_date'] = $disbursal_date;
            }
            
            $loan = $loan_repo->find_by_code($loan_txn["loan_doc_id"], ['id', "country_code","loan_principal", "status", "duration", 'currency_code', 'cust_id', 'cust_name', 'cust_mobile_num', 'due_date', 'current_os_amount','due_amount', 'flow_fee', 'flow_fee_duration', 'flow_fee_type', 'acc_prvdr_code','acc_number','fund_code','customer_consent_rcvd','manual_disb_user_id']);

            if(!$loan->customer_consent_rcvd){
                thrw("Can not disburse. Customer's confirmation SMS has not been received yet");
            }
            if((!isset($loan_txn['capture_only']) && $loan->status != Consts::LOAN_PNDNG_DSBRSL)
                || (isset($loan_txn['capture_only']) && !in_array($loan->status, [Consts::LOAN_HOLD, Consts::LOAN_PNDNG_DSBRSL, Consts::PNDNG_DSBRSL_MANUAL]))){
                thrw("Float Advance is on {$loan->status} status. Can not disburse at this status");
            }
            if($loan_txn['amount'] > config("app.flow_disbursal_limit")){
                thrw("Cannot disburse more than the limit : {config('app.flow_disbursal_limit')}");
            }
            if($loan->loan_principal != $loan_txn["amount"]){
                thrw("Can not disburse an amount other than Float {$loan->loan_principal} requested in the application");
            }

            if(array_key_exists('txn_id' ,$loan_txn)){
                $dup_loans = $loan_txn_repo->get_loan_doc_id($loan_txn['txn_id']);
                if(sizeof($dup_loans) > 0){
                    thrw("Unable to capture this disbursement because this transaction ID ({$loan_txn['txn_id']}) has already been captured for another FA {$dup_loans[0]->loan_doc_id}", 5003);
                }
            }

            if (array_key_exists('due_date', $loan_txn)){
                $due_date = parse_date($loan_txn['due_date'], Consts::DATETIME_FORMAT);
                $due_date = $due_date->endOfDay();
            }
            else{
                if(array_key_exists('txn_date', $loan_txn)){
                    $due_date = getDueDate($loan->duration, $loan_txn['txn_date']);
                }else{
                    $due_date = getDueDate($loan->duration);
                }
            }
            $loan_txn['due_date'] = $due_date->format('Y-m-d H:i:s');;

            /*$result = $this->get_loan_apprvd_n_disbursed_date($loan_txn['loan_doc_id'], $loan->country_code);

            if($result['approved_date'] > $loan_txn['txn_date']){
                thrw("Transaction Date must be greater than or equal to approved date ( {$result['approved_date']} ).");
            }*/



            if($allow_overlap == false){
                $overlap_loan = $this->check_date_overlap($loan->cust_id, $disbursal_date->copy(), $due_date->copy());
                if($overlap_loan){
                    if($overlap_loan->ref_row_id){
                        thrw("Can not disburse overlapped FA. Already an FA {$overlap_loan->loan_doc_id} exists for the period ".format_date($disbursal_date)." to ".format_date($due_date)."the ref row_id is :{$overlap_loan->ref_row_id}" );
                    }
                    else{
                         thrw("Can not disburse overlapped FA. Already an FA {$overlap_loan->loan_doc_id} exists for the period ".format_date($disbursal_date)." to ".format_date($due_date));
                    }
                }
            }

            $fund_repo = new CapitalFundRepositorySQL();

            // $loan_event_repo = new LoanEventRepositorySQL();
            $acc_serv = new AccountService();


            $brwr_repo = new BorrowerRepositorySQL();
            $instant_disbursal = array_key_exists('instant_disbursal', $loan_txn) && $loan_txn['instant_disbursal'] ;
            if($instant_disbursal){

                $disb_acc = $acc_serv->get_lender_disbursal_account($loan_txn['lender_code'], $loan_txn['acc_prvdr_code'], $loan->acc_number);

                $loan_txn['txn_mode'] = "instant_disbursal";

                $loan_txn['txn_exec_by'] = session('user_id');

                $loan_txn['from_ac_id'] = $disb_acc->id;
                $loan_repo->update_record_status(Consts::LOAN_HOLD, $loan->id);
                $succ_atmpt_start_time = datetime_db();
                $flow_disb_result = $this->make_instant_disbursal($loan_txn, $disb_acc);
                $flow_disb_result['flow_request'] = ['loan_txn' => $loan_txn, 'from_acc' => $disb_acc];
                $flow_disb_result['flow_request']['from_acc']->api_cred = '*********';

                if($flow_disb_result['disb_status'] == Consts::DSBRSL_SUCCESS || $flow_disb_result['disb_status'] == Consts::DSBRSL_CPTR_FAILED)  {
                    $loan_repo->update_loan_event('success_atmpt_start_time',$loan_txn["loan_doc_id"],$succ_atmpt_start_time);
                   }

                if($flow_disb_result['disb_status'] != Consts::DSBRSL_SUCCESS){
                    $chk_loan_status = $loan_repo->find($loan->id, ['status'])->status;
                    if ( !in_array( $chk_loan_status, Consts::DISBURSED_LOAN_STATUS ) ) {
                        $loan_repo->update_record_status($flow_disb_result['loan_status'], $loan->id);
                    }
                    #thrw($flow_disb_result['exp_msg']);
                    return $flow_disb_result;
                }
              

                $loan_txn['txn_data'] = $flow_disb_result['partner_combined_resp']['txn_data'];

                $loan_txn['txn_id'] = $flow_disb_result['txn_id'];

            }else if(!array_key_exists('txn_id', $loan_txn)
                        || !array_key_exists('txn_mode', $loan_txn)
                        // || !array_key_exists('txn_exec_by', $loan_txn)
                        || !array_key_exists('from_ac_id', $loan_txn)
                    ){
                thrw("Unable to capture disbursal due to missing keys");
            }

            $loan_txn['txn_type'] = "disbursal";

            $flow_fee = $this->calc_fee($loan);
            
            
            $loan->disbursal_status = Consts::DSBRSL_SUCCESS;
            // $loan->due_date = $due_date;
            $loan->due_date = $loan_txn['due_date'];
            $loan->disbursal_date = $disbursal_date;
            $loan->due_amount = $loan_txn['amount'] + $flow_fee;
            $loan->current_os_amount = $loan_txn['amount'] + $flow_fee;
            $loan->loan_doc_id = $loan_txn["loan_doc_id"];
                
            // $loan->due_date = $due_date;
            $loan->current_os_amount = $loan_txn['amount'] + $flow_fee;

            $flow_disb_result['loan_txn_id'] = $this->capture_disbursal($loan_txn,(array)$loan,$with_txn);
            $flow_disb_result['disb_status'] = Consts::DSBRSL_SUCCESS;
            $flow_disb_result['manual_disb_id'] = $loan->manual_disb_user_id;

            if($flow_disb_result['manual_disb_id'] != null){
                $loan_repo->update_loan_event('manual_disb_end_time',$loan->loan_doc_id);
                $disb_serv->update_event_durations($loan->loan_doc_id);
              }

        }

        catch (\Exception $e) {

            $with_txn ? DB::rollback() : null;

            if($for_add_txn){
                thrw($e->getMessage());
            }

            Log::warning($e->getTraceAsString());
            $flow_disb_result['exp_msg'] = $e->getMessage();
            if($flow_disb_result['disb_status'] == Consts::DSBRSL_SUCCESS){
                $flow_disb_result['disb_status'] = Consts::DSBRSL_CPTR_FAILED;
            }

        }

        if($loan_txn['send_sms'] && $flow_disb_result['disb_status'] == Consts::DSBRSL_SUCCESS){
                $sms_serv = new SMSNotificationService();
                $flow_disb_result["sms_sent"] = $sms_serv->notify_loan($loan, 'DISBURSEMENT_MSG');
        }

        return  $flow_disb_result;

   }

public function capture_disbursal($loan_txn, $loan, $with_txn =true){
        $with_txn ? DB::beginTransaction() : null;
        $brwr_repo = new BorrowerRepositorySQL();
        $loan_repo = new LoanRepositorySQL();
        $loan_txn_repo = new LoanTransactionRepositorySQL();

        $borrower = $brwr_repo->find_by_code($loan['cust_id'], ['tot_loans', 'prob_fas', 'first_loan_date', 'lender_code', 'fund_code']);

        $loan['fund_code'] = $this->get_appl_fund_code($loan['cust_id'], $borrower->lender_code, $borrower->fund_code, $loan['loan_principal']);
        $loan['status'] = Consts::LOAN_ONGOING;
        $loan_repo->update_model_by_code($loan);

        $brwr_repo->update_model_by_code(
                    ['cust_id' => $loan['cust_id'],
                    'pending_loan_appl_doc_id' => null,
                    'ongoing_loan_doc_id' => $loan_txn["loan_doc_id"],
                    'last_loan_date' => date_db(),
                    'tot_loans' => $borrower->tot_loans + 1
                    ]);



        #$acc_serv->create_platform_acc_txn($loan_txn['from_ac_id'], 'debit',$loan_txn['amount'], $loan_txn["loan_doc_id"], 'disbursal',$disbursal_date);

        [$on_prob_cond_period, $is_first_loan, $prob_fas] = $this->check_new_cust($borrower->prob_fas, $borrower->first_loan_date);

        if ($on_prob_cond_period){
            $brwr_repo->increment_by_code("prob_fas", $loan['cust_id'], -1);


            if($prob_fas - 1 == 0){
                $prob_period_repo = new ProbationPeriodRepositorySQL();
                $cust_prob = $prob_period_repo->get_record_by_many(['cust_id', 'status'],[$loan['cust_id'], 'active'], ['id']);
                    if($cust_prob){
                        $prob_period_repo->complete_probation($cust_prob->id, $loan['cust_id']);
                    }
            }

        }

        if ($is_first_loan){
            $cust_comm = array_key_exists('cust_comm', $loan_txn) ? $loan_txn['cust_comm'] : null;

            $brwr_repo->update_first_loan_date($loan['cust_id'], $loan['disbursal_date']);
            // $this->add_commission('borrower', $loan->acc_prvdr_code, $loan->cust_id, $disbursal_date, $cust_comm);
        }


            #TODO update loan_txn & acc_txn's txn_id to the above $txn_id
            $loan_txn['country_code'] = session("country_code");


        if(isset($loan_txn['photo_disbursal_proof'] )){
            mount_entity_file("loan_txns", $loan_txn,null, 'photo_disbursal_proof');
            $loan_txn['photo_proof']['photo_disbursal_proof'] = $loan_txn['photo_disbursal_proof'];
            $loan_txn['photo_proof'] = json_encode($loan_txn['photo_proof']);
        }


        if(array_key_exists('txn_data', $loan_txn) && sizeof($loan_txn['txn_data']) > 0){

            foreach($loan_txn['txn_data'] as $txn_data ){
                $loan_txn['txn_id'] = $txn_data['txn_id'];
                $loan_txn['amount'] = $txn_data['amount'];
                $loan_txn_id = $loan_txn_repo->create($loan_txn, true, Consts::LOAN_EVNT_DISBURSED);
            }
        }else{
            $loan_txn_id = $loan_txn_repo->create($loan_txn, true, Consts::LOAN_EVNT_DISBURSED);
        }

        $acc_stmt_repo = new AccountStmtRepositorySQL();
        $acc_stmt_obj = $acc_stmt_repo->get_record_by_many(['stmt_txn_id','account_id'], [$loan_txn['txn_id'],$loan_txn['from_ac_id']], ['id', 'dr_amt', 'stmt_txn_date', 'loan_doc_id']);

        if($acc_stmt_obj){
            $txn_type = 'debit';
            $acc_stmt_id = (new RepaymentService)->validate_for_recon($loan_txn, $acc_stmt_obj, $txn_type);
            if($acc_stmt_id){
                $acc_stmt_repo->update_model(["recon_status" => "80_recon_done","id" => $acc_stmt_id, "loan_doc_id" => $loan_txn["loan_doc_id"]]);
            }    
        }
    
        $fund_repo = new CapitalFundRepositorySQL();

        $fund_repo->increment_by_code('os_amount', $loan['fund_code'], $loan['loan_principal']);
        $fund_repo->increment_by_code('tot_disb_amount', $loan['fund_code'], $loan['loan_principal']);

        $with_txn ? DB::commit() : null;
        return $loan_txn_id;
}

public function check_new_cust($prob_fas, $first_loan_date){
    $on_prob_cond_period = false;
    $first_loan = false;

    if ($prob_fas == null){
        $prob_fas = 0;
    }
    if ($prob_fas > 0 ){
        $on_prob_cond_period =  true;
    }

    if ($first_loan_date == null){
        $first_loan = true;
    }

    return [$on_prob_cond_period, $first_loan, $prob_fas];
}



    public function make_instant_disbursal($loan_txn, $disb_acc){
        $UNKNOWN_MSG = "Last disbursal attempt resulted in UNKNOWN status.\n FA is put on HOLD. You can not try again";

        $disb_serv = new DisbursalService();
        $partner_combined_resp = $disb_serv->make_instant_disbursal($loan_txn, $disb_acc);
        $hold_loan = false;
        $disb_status = Consts::DSBRSL_UNKNOWN;
        $loan_status = $exp_msg = $txn_id = null;

        if($partner_combined_resp && $partner_combined_resp['status'] == 'success' && $partner_combined_resp['amount'] == $loan_txn['amount']){
            if($partner_combined_resp['txn_ids'] == 'ussd' || $partner_combined_resp['txn_ids'] == 'rbok_transfer'){
                $disb_status = Consts::DSBRSL_CPTR_FAILED;
                $hold_loan = true;
                $exp_msg_type = $partner_combined_resp['txn_ids'] == 'ussd' ? 'USSD' : 'Instant';
                $exp_msg = "$exp_msg_type disbursal was successful. Unable to capture since Txn ID is not available";
            }else{
                $txn_id = $partner_combined_resp['txn_ids'];
                $disb_status = Consts::DSBRSL_SUCCESS;
            }
        }else if($partner_combined_resp['amount'] > 0 && $partner_combined_resp['amount'] != $loan_txn['amount']){
            $hold_loan = true;
            $remaining = $loan_txn['amount'] - $partner_combined_resp['amount'];
            $exp_msg = "Partially disbursed only {$partner_combined_resp['amount']}. Please disburse the remaining {$remaining} manually. FA is put on HOLD\n\n {$partner_combined_resp['message']}";
        }else if($partner_combined_resp['status'] == 'unknown'){
           $hold_loan = true;
            $exp_msg = $UNKNOWN_MSG ."\n". $partner_combined_resp["message"];
        }else if($partner_combined_resp['status'] == 'failure' || $partner_combined_resp['status'] == 'FAILED' || $partner_combined_resp['status'] == 'failed'){
            $disb_status = Consts::DSBRSL_FAILED;
            $hold_loan = true;
            $exp_msg = $partner_combined_resp["message"];
        }
        else{
            $hold_loan = true;

            $exp_msg = "UNKNOWN STATUS";
        }


        if($hold_loan){
            $loan_status = Consts::LOAN_HOLD;
        }

        return ['txn_id' => $txn_id , 'loan_status' => $loan_status, 'disb_status' => $disb_status,
        'exp_msg' => $exp_msg, 'partner_combined_resp' => $partner_combined_resp];
        #thrw($exp_msg)
    }

    public function release_loan($loan_doc_id){
        $loan_repo = new LoanRepositorySQL();
        return $loan_repo->update_model_by_code(['status' => Consts::LOAN_PNDNG_DSBRSL, 'loan_doc_id' => $loan_doc_id]);
    }

   private function check_date_overlap($cust_id, $disbursal_date, $due_date){
           # disbursal_date,paid_date,ref_row_id
         $result = DB::selectOne('select loan_doc_id,ref_row_id from loans where cust_id = ? and country_code = ? and ((date(disbursal_date) <= ? and date(paid_date) > ?) OR (date(disbursal_date) < ? and date(paid_date) >= ?)) order by disbursal_date desc', [$cust_id, session('country_code'), $disbursal_date->toDateString(), $disbursal_date->toDateString(), $due_date->toDateString(), $due_date->toDateString()]);

         return $result? $result : null;
   }
    public function add_commission($type, $acc_prvdr_code, $entity_type_id,$txn_date = null,$commission = null){
        // $dp_repo = new DataProviderRepositorySQL();
        $ap_repo = new AccProviderRepositorySQL();

        if ($commission == null){
            $acc_prvdr = $ap_repo->find_by_code($acc_prvdr_code, ['cust_comm', 'repay_comm']);
        }
        if($type == 'borrower'){
            if ($commission != null){
                $comm = $commission;
            }else{
                $comm = $acc_prvdr->cust_comm;    
            }
            $acc_txn_type = 'new_cust_comm';
        }elseif ($type == 'repay'){
            if ($commission != null){
                $comm = $commission;
            }else{
                $comm = $acc_prvdr->repay_comm;    
            }

            $acc_txn_type = 'repay_comm';
        }

        // if($comm){
        //    $ap_acc =  (new AccountRepositorySQL())->get_commission_account($acc_prvdr_code);
        //    if($ap_acc == null){
        //         thrw("Please configure Commission A/C for Data Provider", 9999);
        //    }

        //     (new AccountService())->create_platform_acc_txn($ap_acc->id, 'credit', $comm, $entity_type_id,$acc_txn_type,$txn_date);
           
        // }

    }


    private function calc_fee($product){

        $flow_fee = $product->flow_fee;
        if($product->flow_fee_type == "Percent"){
            $flow_fee = $this->calc_fee_from_percent($product);
        }
        if($this->is_per_day_loan($product->flow_fee_duration)){
            $flow_fee = $flow_fee * $product->duration;
        }
        return $flow_fee;
    }

private function calc_fee_from_percent($product){
    return $product->loan_principal * ($product->flow_fee / 100);
}

private function is_per_day_loan($flow_fee_duration){
    if($flow_fee_duration == "each_day" || $flow_fee_duration == "1"){
        return true;
    }
    else{
         return false;
    }
}

    private function get_loan_apprvd_n_disbursed_date($loan_doc_id){

        //$loan_appl_doc_id = "APPL-".$loan_doc_id;
        $loan_appl_repo = new LoanRepositorySQL();

        $date_n_time = $loan_appl_repo->get_record_by('loan_doc_id',$loan_doc_id,['loan_approved_date','disbursal_date']);

        $approved_date  = $date_n_time->loan_approved_date;
        $disbursed_date = $date_n_time->disbursal_date;

        return ["approved_date" => $approved_date,"disbursed_date" => $disbursed_date];
    }

    private function validate_recon($loan_txn){

        $acc_stmt_txn_id = $loan_txn['txn_id'];

        $acc_stmt_repo = new AccountStmtRepositorySQL();
        $acc_stmt_obj = $acc_stmt_repo->get_record_by('stmt_txn_id', $acc_stmt_txn_id, ['id', 'cr_amt', 'stmt_txn_date']);

        if($acc_stmt_obj){
            if($acc_stmt_obj->cr_amt != $loan_txn['amount']){
                thrw("Cannot capture. For txn ID {$acc_stmt_txn_id}, amount in account statement is {$acc_stmt_obj->cr_amt}");
            }
            if($loan_txn['txn_date'] == datetime_db($loan_txn['txn_date'])){
                $acc_stmt_obj->stmt_txn_date = format_date($acc_stmt_obj->stmt_txn_date, 'Y-m-d');
            }


            if($acc_stmt_obj->stmt_txn_date != $loan_txn['txn_date']){
                thrw("Cannot capture. For txn ID {$acc_stmt_txn_id}, txn date in account statement is {$acc_stmt_obj->stmt_txn_date}");
            }
            return $acc_stmt_obj->id;
        }

    }

    private function payment_validation(&$loan, &$loan_txn){


        $result = $this->get_loan_apprvd_n_disbursed_date($loan_txn["loan_doc_id"]);

        $disbursed_date = $result['disbursed_date'];

        if(parse_date($disbursed_date, Consts::DB_DATETIME_FORMAT) > parse_date($loan_txn['txn_date'])){
            thrw("Payment date can not be before disbursed date ({$disbursed_date}).", 5004);
        }
        if(!in_array($loan->status, Consts::DISBURSED_LOAN_STATUS)){
        #if($loan->status != Consts::LOAN_ONGOING && $loan->status != Consts::LOAN_DUE && $loan->status != Consts::LOAN_OVERDUE ){
                thrw("Float Advance is in '{$loan->status}' status. Can not make payment at this status", 5005);
        }

        # To be removed once excess payment warning is shown on UI
        if($loan_txn["amount"] > $loan->current_os_amount){
            thrw("Cannot capture payment for an amount higher than Current Outstanding Amount {$loan->current_os_amount}", 5006);

        }

       if($loan_txn["is_part_payment"] == false  && $loan_txn["amount"] < $loan->current_os_amount){
             thrw("You can not make part payment", 5007);
        }

        $acc_stmt_id = $this->validate_recon($loan_txn);

        if($loan->provisional_penalty > 0 ){

            if(!array_key_exists('penalty_collected', $loan_txn) || $loan_txn['penalty_collected'] == NULL ){
                thrw("FA is overdue. Please collected penalty.", 5008);

            }
            $loan->penalty_days = getPenaltyDate($loan->due_date);
            $loan->provisional_penalty = $loan->provisional_penalty * $loan->penalty_days;

            //$loan_txn['penalty_collected'] = array_key_exists('penalty_collected', $loan_txn) ? $loan_txn['penalty_collected'] : 0;

            // if(isset($loan_txn["penalty_collected"])) {

                if($loan->penalty_collected + $loan_txn["penalty_collected"] > $loan->provisional_penalty ){
                    thrw("Cannot collect more penalty than Provisional Penalty {$loan->provisional_penalty}", 5009);
                }
            //}

        }
        return $acc_stmt_id;
    }


    public function get_cust_category($borrower){
        if($borrower->prob_fas > 0 && $borrower->cond_count > 0){
            return "Condonation";
        }
        else if($borrower->prob_fas > 0) {
            return "Probation";
        }

        $factors = new Factors($borrower->cust_id, $borrower->perf_eff_date);
        $ontime_pc =  $factors-> _ontime_loans_pc();

        if($borrower->tot_loans > 15 ){
            if($ontime_pc > 80){
                return "Silver";
            }
            else if($borrower->tot_loans > 20 ){
                if($ontime_pc > 85){
                return "Gold";
                }
                else
                if($borrower->tot_loans > 30 ){
                    if($ontime_pc > 90){
                    return "Platinum";
                    }
                }
            }
        }

        return "Regular";
    }

    private function late_settlement_handler($borrower, $late_days, $txn_date){
        $late_fields = array();
        //$this->process_condonation($late_days, $borrower->cust_id, $txn_date);

        $ld_field = "late_{$late_days}_day_loans";

        if($late_days > 3){
            $ld_field = 'late_3_day_plus_loans';
        }

        $late_fields[$ld_field] = $borrower->{$ld_field} + 1;
        $late_fields['late_loans'] = $borrower->late_loans + 1;
        return $late_fields;

    }
    public function process_overdue(&$loan){
        $loan_repo = new LoanRepositorySQL();
        // $loan_event_repo = new LoanEventRepositorySQL();
        $brwr_repo = new BorrowerRepositorySQL();
        $loan_product_repo = new LoanProductRepositorySQL();
        $pre_appr_repo = new PreApproval;

        $loan->provisional_penalty = $loan_product_repo->get_penalty_amount($loan->product_id);

        $loan_repo->update_model(['status' => Consts::LOAN_OVERDUE, 'provisional_penalty'=> $loan->provisional_penalty, 'overdue_days' => $loan->overdue_days, 'penalty_days' => $loan->penalty_days, 'id' => $loan->id]);

        $pre_appr_id = $pre_appr_repo->get_record_by_many(['cust_id','status'], [$loan->cust_id, 'enabled'], ['status']);
       
        if($pre_appr_id &&  $loan->overdue_days > Consts::PRE_APPR_RMVE_OD_DAYS_THRESHOLD){
            $pre_appr_repo -> update_model(['status' => 'disabled', 'id'=> $pre_appr_id->id ]);
            $brwr_repo->update_model_by_code(['pre_appr_count' => null, 'pre_appr_exp_date' => null, 'cust_id'=> $loan->cust_id]);

        }

        #$loan_repo->update_record_status(Consts::LOAN_OVERDUE,$loan->id);

        $brwr_repo->update_model_by_code(['ongoing_loan_doc_id' => $loan->loan_doc_id ,'is_og_loan_overdue'  => true, 'cust_id' => $loan->cust_id]);

        // $loan_event_repo->create_event($loan->loan_doc_id, Consts::LOAN_OVERDUE);

    }

    private function get_principal_in_payment($loan, $loan_txn){

        if($loan->paid_amount >= $loan->loan_principal){
            return 0;
        }else if($loan->loan_principal - $loan->paid_amount >= $loan_txn['amount']){
            return $loan_txn['amount'];
        }else if($loan->loan_principal - $loan->paid_amount < $loan_txn['amount']){
            return $loan->loan_principal - $loan->paid_amount ;
        }else {
            return 0;
        }
    }

   private function get_paid_amounts($loan, $loan_txn){
        $serv = new RepaymentService();
        $amount = $loan_txn['penalty_collected'] + $loan_txn['amount'];
        $to_capture = $serv->get_amounts_to_capture($loan, $amount); 

       $paid_principal = $this->get_principal_in_payment($loan, $loan_txn);

        $paid_fee = $loan_txn['amount'] - $paid_principal;

        $paid_excess = null;

        if($loan->paid_fee + $paid_fee > $loan->flow_fee){
            $this_paid_fee = $loan->flow_fee - $loan->paid_fee;
            $paid_excess = $loan->paid_fee + $paid_fee - $loan->flow_fee;
            $paid_fee = $this_paid_fee;
        }

        return [
            'paid_principal' => $loan->paid_principal + $to_capture['principal'],
            'paid_fee' => $loan->paid_fee + $to_capture['fee'],
            'paid_excess' => $loan->paid_excess + $to_capture['excess'],
            'penalty_collected' => $loan->penalty_collected + $to_capture['penalty'],
        ]; 

    //    return [
    //             'paid_principal' => $loan->paid_principal + $paid_principal,
    //             'paid_fee' => $loan->paid_fee + $paid_fee,
    //             'paid_excess' => $paid_excess,
    //         ];

   }

     public function capture_repayment(array $loan_txn, $with_txn = true,$fwd_to_float_ac = false)
    {

        $result = ["sms_sent" => false];
        $loan_repo = new LoanRepositorySQL();
        // $loan_event_repo = new LoanEventRepositorySQL();
        $brwr_repo = new BorrowerRepositorySQL();
        $person_repo = new PersonRepositorySQL();
        $loan_txn_repo = new LoanTransactionRepositorySQL();

        $br_data = array();
        #$txn_date = array_key_exists('txn_date', $loan_txn) ? $loan_txn['txn_date'] : null;

        $loan = $loan_repo->find_by_code($loan_txn["loan_doc_id"], ['id','loan_doc_id', "country_code","loan_principal",
        "flow_rel_mgr_id", "status", 'currency_code', 'lender_code' , 'cust_id', 'cust_name', 'cust_mobile_num',
        'due_amount', 'due_date', 'current_os_amount','paid_amount','acc_prvdr_code','acc_number','penalty_collected',
        'provisional_penalty', 'product_id','paid_fee','paid_principal','flow_fee','fund_code','penalty_waived','paid_excess']);

        $borrower = $brwr_repo->find_by_code($loan->cust_id, ['ongoing_loan_doc_id', 'is_og_loan_overdue',
        'category', 'late_1_day_loans', 'late_2_day_loans', 'late_3_day_loans', 'late_3_day_plus_loans',
        'late_loans','tot_loans', 'prob_fas', 'cond_count', 'perf_eff_date']);


        $due_date = parse_date($loan->due_date, Consts::DB_DATETIME_FORMAT);
        $paid_date = array_key_exists('paid_date', $loan_txn) ? parse_date($loan_txn['paid_date']) : Carbon::now();

        $due_date->endOfDay();
        $paid_date->endOfDay();
        $late_days = 0;

        $dup_loans = $loan_txn_repo->get_loan_doc_id($loan_txn['txn_id']);

        if(sizeof($dup_loans) > 0){
            thrw("Unable to capture payment because this transaction ID ({$loan_txn['txn_id']}) already captured on for another FA {$dup_loans[0]->loan_doc_id}", 5003);
        }

        try
        {
            $with_txn ? DB::beginTransaction() : null;


            # $loan passed by reference. Provisional penalty will be multiplied by no.of days in this func.
            $this->payment_validation($loan, $loan_txn);

            $loan_txn_repo = new LoanTransactionRepositorySQL();

            $category = $borrower->category;
            $SMS_TEMPLATE = null;
            $payment = $loan_txn['amount'];
            $loan_txn['txn_type'] = "payment";

            $paid_by = 0;


            if(session('user_id')){
                $person = $person_repo->get_person_by_user_id(session('user_id'));
                if($person){
                    $paid_by = $person->id;
                }
            }

            if(!array_key_exists('txn_exec_by', $loan_txn)){
                $loan_txn['txn_exec_by'] = $paid_by;
            }

            $result["loan_txn_id"] = $loan_txn_repo->create($loan_txn);

            $new_status = $loan->status;

            $ongoing_loan_doc_id = $borrower->ongoing_loan_doc_id;
            $is_og_loan_overdue = $borrower->is_og_loan_overdue;
            $loan_txn['penalty_collected'] = array_key_exists('penalty_collected', $loan_txn) ? $loan_txn['penalty_collected'] : 0;

            $new_loan_data = [
                'loan_doc_id' => $loan_txn["loan_doc_id"],
                'current_os_amount' => $loan->current_os_amount -  $loan_txn['amount'],
                'paid_amount' => $loan->paid_amount +  $loan_txn['amount'],
                'penalty_collected' => $loan->penalty_collected + $loan_txn['penalty_collected'],
                'paid_by' => $paid_by
           ];

            $late_fields = array();

            if($new_loan_data['paid_amount'] >= $loan->due_amount){
                $new_status = Consts::LOAN_SETTLED;
                $event_type = Consts::LOAN_SETTLED;
                $SMS_TEMPLATE = SMSTemplate::REPAYMENT_SETTLED_MSG;
                $br_data['last_loan_doc_id'] = $loan->loan_doc_id;

                if($ongoing_loan_doc_id == $loan_txn["loan_doc_id"]){
                    $ongoing_loan_doc_id = null;
                    if($is_og_loan_overdue){
                        $is_og_loan_overdue = false;
                    }
                }

                $repay_comm = array_key_exists('repay_comm', $loan_txn) ? $loan_txn['repay_comm'] : null;
                // $this->add_commission('repay', $loan->acc_prvdr_code, $loan_txn["loan_doc_id"],$loan_txn['txn_date'],$repay_comm);
                
                $borrower->cust_id = $loan->cust_id;
                $br_data['category'] = $this->get_cust_category($borrower);
                if($loan->status == Consts::LOAN_OVERDUE || $due_date < $paid_date){

                    // if($loan->provisional_penalty > 0 && $loan_txn['penalty_collected'] == 0){
                    //     $loan_event_repo->create_event($loan_txn["loan_doc_id"], Consts::PENALTY_WAIVER, "{\"provisional_penalty\": $loan->provisional_penalty}",null,$loan_txn["txn_date"]);
                    // }

                    if($loan_txn['penalty_collected'] > 0){
                        $penalty_txn = $loan_txn;
                        $penalty_txn['txn_type'] = Consts::LOAN_PENALTY_PAYMENT;
                        $penalty_txn['amount'] = $loan_txn['penalty_collected'];
                        $loan_txn_repo->create($penalty_txn, true, Consts::LOAN_PENALTY_PAYMENT, "{\"penalty_collected\": {$loan_txn['penalty_collected']}}");
                    }

                    $late_days = $paid_date->diffInDays($loan->due_date);
                    if($late_days > 0){

                        $borrower->cust_id = $loan->cust_id;
                        $late_fields = $this->late_settlement_handler($borrower, $late_days, $loan_txn['txn_date']);
                        $br_data += $late_fields;

                        $borr = $brwr_repo->get_record_by('cust_id', $loan->cust_id, ['prob_fas', 'cond_count', 'perf_eff_date', 'cust_id', 'tot_loans']);
                        $br_data['category'] = $this->get_cust_category($borr);
                    }

                }
                // $br_data['category'] = $this->get_cust_category($borrower);

            }
            else{
                $event_type = Consts::LOAN_EVENT_PART_PYMNT;
                $SMS_TEMPLATE = 'REPAYMENT_PENDING_MSG';

                $ongoing_loan_doc_id = $loan_txn["loan_doc_id"];
                if($loan->status == Consts::LOAN_OVERDUE || $due_date < $paid_date){
                    $is_og_loan_overdue = true;
                }
                $loan->new_os_amount = $loan->current_os_amount - $payment;
            }

            $paid_amounts = $this->get_paid_amounts($loan, $loan_txn);

            $fund_repo = new CapitalFundRepositorySQL();
            $fund_repo->increment_by_code('os_amount', $loan->fund_code, -1 * $paid_amounts['paid_principal']);
            $fund_repo->increment_by_code('earned_fee', $loan->fund_code, $paid_amounts['paid_fee']);

            $new_loan_data += $paid_amounts;

            $new_loan_data['status'] = $new_status;

            $new_loan_data['paid_date'] = $new_status == Consts::LOAN_SETTLED ? $loan_txn["txn_date"]: null;

            if(array_key_exists('is_part_payment', $loan_txn) && $loan_txn['is_part_payment'] == true){
                $new_loan_data['paid_date'] = null;
            }

            $loan_repo->update_model_by_code($new_loan_data);

            $br_data += ['cust_id' => $loan->cust_id,
                        'ongoing_loan_doc_id' => $ongoing_loan_doc_id,
                        'is_og_loan_overdue'  => $is_og_loan_overdue,

                        ];

            $brwr_repo->update_model_by_code($br_data);



            // $loan_event_repo->create_event($loan_txn["loan_doc_id"],$event_type,null,null,$loan_txn["txn_date"]);

            $loan->mobile_num = config("app.customer_success")[$loan->acc_prvdr_code];
            $loan->sms_reply_to = config('app.sms_reply_to')[session('country_code')];
            $loan->payment = $payment;

           if($loan_txn['send_sms']){
                $sms_serv = new SMSNotificationService();
                $result["sms_sent"] = $sms_serv->notify_loan($loan, $SMS_TEMPLATE);
            }
            $with_txn ? DB::commit() : null;
            $result["status"] = "success";
        }
        catch (FlowCustomException $e) {
            $with_txn ? DB::rollback() : null;

            thrw($e->getMessage(), $e->response_code);
        }
        catch (\Exception $e) {
            $with_txn ? DB::rollback() : null;
            Log::warning($e->getTraceAsString());
            if ($e instanceof QueryException){
                    throw $e;
                }else{
                thrw($e->getMessage());
                }
        }
        return $result;
    }

    private function process_condonation($late_days, $cust_id, $txn_date){

        $condonation_overdue_days = config('app.auto_condonation_overdue_days');

        if($late_days > $condonation_overdue_days){

            $prob_period_repo = new ProbationPeriodRepositorySQL();

            $cust_prob = $prob_period_repo->get_record_by_many(['cust_id', 'status','type'],[$cust_id, 'active','condonation'], ['id']);
            if($cust_prob){
                $prob_period_repo->complete_probation($cust_prob->id, $cust_id);

                /*$condonation_delay = config('app.condonation_punishment_delay');
                $start_date = Carbon::now()->addDays($condonation_delay)->startOfDay();

                $prob_period_repo->update_model(['id' => $cust_prob->id,'start_date' => $start_date]);

                $borrow_repo->update_model_by_code(['cust_id' => $cust_id, 'perf_eff_date' => $start_date]);*/
            }

            $borr_serv = new BorrowerService();
            $category = $borr_serv->allow_condonation($cust_id, false, $txn_date, true);

            return $category;

        }
    }
    public function list_disbursers($data)
    {
        $data['priv_code'] = Consts::PRIV_CODE_DISBURSAL;
        $data['select_login_user'] = true;
        $common_serv = new CommonService();

        return $common_serv->get_users($data);
    }

    public function getproductsummary($product_id)
    {
        $loan_product_repo =  new LoanProductRepositorySQL();
        $loan_product = $loan_product_repo->get_loan_product($product_id);
        $date = Carbon::now();
        $current_date =  $date->format('Y-m-d');
        $loan = getDueDate($loan_product->duration,$current_date);
        $borrower_repo = new BorrowerRepositorySQL();
        $borrower = $borrower_repo->get_customer("UEZM-503539");
        return ['loan_product' => $loan_product, 'loan' => $loan,'disbursal_date'=>$current_date,'borrower'=>$borrower];
    }
    public function get_loan_search($cust_id){
         $cust_id = $this->loan_search(['req_parameter' => $cust_id]);
        return  $cust_id;
    }
    public function get_current_loan_search($data)
    {
        $loan_doc_id = $this->get_loan($data);
        return  $loan_doc_id;
    }
    public function getloanproduct($product_id)
    {
        $loan_product_repo = new LoanProductRepositorySQL();
        $product = $loan_product_repo->getloanproduct($product_id);
        return $product;
    }

    public function cancel_loan($data)
    {
        try{

            DB::beginTransaction();
            $loan_repo = new LoanRepositorySQL();
            $loan_appl_repo = new LoanApplicationRepositorySQL();
            $borrow_repo = new BorrowerRepositorySQL();
            // $loan_event_repo = new LoanEventRepositorySQL();
            // Log::warning($data['loan_doc_id']);

            $loan_doc_id = $data['loan_doc_id'];

            $loan = $loan_repo->find_by_code($loan_doc_id);
            if(!array_key_exists('ignore_consent_check', $data) && $loan->customer_consent_rcvd){
                thrw("Unable to cancel. Loan is being processed for disbursal");
            }
            $borrower = $borrow_repo->find_by_code($loan->cust_id, ['tot_loan_appls', 'tot_loans']);

            // $loan_repo->update_model(['status' => Consts::LOAN_CANCELLED,'loan_doc_id' => $loan_doc_id]);
            $loan_repo->update_model_by_code(['loan_doc_id' => $loan_doc_id, 'status' => Consts::LOAN_CANCELLED]);
            $loan_appl_repo->update_model(['status' => Consts::LOAN_CANCELLED,'id' => $loan->loan_appl_id]);


            $borrow_repo->update_model_by_code(['cust_id' => $loan->cust_id ,
            'tot_loan_appls'  => $borrower->tot_loan_appls - 1, 'ongoing_loan_doc_id' => null]);

            // $loan_event_repo->create_event($loan_doc_id,'Cancel');
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            Log::warning($e->getTraceAsString());
            if($e instanceof QueryException){
                thrw($e);
            }else{
                thrw($e->getMessage());
            }
        }
    }

    public function allow_partial_payment($data){

        $loan_repo = new LoanRepositorySQL();
        $allow_pp = $data['allow_pp'] ? "enabled" : "disabled";
        $loan_repo->update_model_by_code(['loan_doc_id' => $data['loan_doc_id'], 'allow_pp' => $allow_pp]);

        return ['allow_pp' => $allow_pp];
    }

    public function get_queue($acc_number, $disb_int_type, $acc_prvdr_code, $is_kyc_line = false){

        if($disb_int_type == null){
            return null;
        }

        if($is_kyc_line){

            return "USSD_{$acc_number}";
        }

        if($disb_int_type == 'web' && is_single_session_ap($acc_prvdr_code)){

            $queue_prefix = 'DISB_STMT';            
        }
        else{
            $queue_prefix = 'DISB';
        }

        $queue = "{$queue_prefix}_{$acc_prvdr_code}_{$acc_number}_{$disb_int_type}";

        return $queue;

    }

    public function send_to_disbursal_queue($loan_doc_id, $int_type = null, $conf_channel = null, $otp_id = null){

        $repo = new LoanRepositorySQL();
        $disb_attempt_repo = new DisbursalAttemptRepositorySQL;
        $loan = $repo->get_record_by('loan_doc_id',$loan_doc_id,['lender_code','acc_prvdr_code','loan_principal','cust_id','cust_acc_id',]);
        if(isset($conf_channel)){
            if($conf_channel == 'cust_otp'){
                (new BorrowerRepositorySQL())->update_model_by_code(['cust_id' => $loan->cust_id, 'temp_first_conf_code_sent' => true]);
            }
            $repo->update_model_by_code(['loan_doc_id' => $loan_doc_id, 'customer_consent_rcvd' => true, 'cust_conf_channel' => $conf_channel, 'conf_otp_id' => $otp_id]);
        }
        if(!in_array($loan->acc_prvdr_code,config('app.auto_disbursal_acc_providers')) || env('APP_ENV') == 'local' ){
            $repo->update_model_by_code(['loan_doc_id' => $loan_doc_id, 'status' => Consts::PNDNG_DSBRSL_MANUAL, 'disbursal_status' => null]);
            return;
        }
        $acc_repo = new AccountRepositorySQL;
        $acc_num = $acc_repo->get_acc_num($loan->cust_acc_id);
        $loan_id = $repo->get_id($loan_doc_id);

        $data = ['lender_code' => $loan->lender_code, 'acc_prvdr_code' => $loan->acc_prvdr_code,
                                           'amount' => $loan->loan_principal, 'loan_doc_id' => $loan_doc_id,
                                           'cust_id' => $loan->cust_id, 'to_ac_id' => $loan->cust_acc_id,
                                           'to_acc_num' => $acc_num, 'send_sms' => true, 'instant_disbursal' => true,
                                           'country_code' => session('country_code'),'loan_id'=>$loan_id];


        if (isset($int_type)){
            $data['int_type'] = $int_type;
        }
        
        $first_q_inst_time = datetime_db();
        $disb_count = $disb_attempt_repo->get_records_by('loan_doc_id',$loan_doc_id,['id']);

        $from_acc = (new AccountService)->get_lender_disbursal_account($loan->lender_code, $loan->acc_prvdr_code, $acc_num);
       
        $queue = $this->get_queue($from_acc->acc_number, $from_acc->disb_int_type, $loan->acc_prvdr_code);

        $update_arr['loan_doc_id'] = $loan_doc_id;
        if($queue){
            InstantDisbursalQueue::dispatch($data)->onQueue($queue);
            $update_arr['disbursal_status'] = Consts::DSBRSL_PROCESSING;
            if(count($disb_count) == 0){
                $repo->update_loan_event('first_queue_insert_time',$loan_doc_id,$first_q_inst_time);
            }
            
        }else{
            $update_arr['disbursal_status'] = null;
            $update_arr['status'] = Consts::PNDNG_DSBRSL_MANUAL;

        }
        
        $repo->update_model_by_code($update_arr);

    }

    public function retry_disbursal ($loan_doc_id, $int_type) {
        $loan_repo = new LoanRepositorySQL();
        $loan = $loan_repo->get_record_by('loan_doc_id',$loan_doc_id,['disbursal_status']);
        if($loan->disbursal_status !== Consts::DSBRSL_FAILED){
            thrw("Unable to retry. Disbursal status has changed");
        }
        try{
            DB::beginTransaction();
            $this->release_loan($loan_doc_id);
            $this->send_to_disbursal_queue($loan_doc_id, $int_type);
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            if ($e instanceof QueryException){
                throw $e;
            }else{
            thrw($e->getMessage());
            }
        }
    }


    public function get_disbursal_attempt ($loan_doc_id) {
        $attempt = DB::selectOne("select flow_request, partner_combined_response from disbursal_attempts where loan_doc_id = ? order by id desc limit 1", [$loan_doc_id]);
        //Log::warning($attempt);
        if(!isset($attempt->flow_request)){
            return ['from_acc_num' => "n/a", 'to_acc_num' => 'n/a', 'disbursed_amount' => 'n/a',
            'from_acc_id' => 'n/a', 'to_acc_id' => 'n/a'];
        }
        $flow_req = json_decode($attempt->flow_request);
        $from_acc_num = $flow_req->from_acc->acc_number;
        $to_acc_num = $flow_req->loan_txn->to_acc_num;
        $from_acc_id = $flow_req->from_acc->id;
        $to_acc_id = $flow_req->loan_txn->to_ac_id;
        $disb_amount = $flow_req->loan_txn->amount;
        return ['from_acc_num' => $from_acc_num, 'to_acc_num' => $to_acc_num, 'disbursed_amount' => $disb_amount,
                'from_acc_id' => $from_acc_id, 'to_acc_id' => $to_acc_id];

    }

    public function change_disbursal_status($data){
        
        $loan_repo = new LoanRepositorySQL();

        $loan_repo -> update_loan_event('manual_disb_start_time', $data['loan_doc_id']);

        $loan = $loan_repo->get_record_by('loan_doc_id',$data['loan_doc_id'],['disbursal_status','manual_disb_user_id']);

        if($loan->disbursal_status !== Consts::DSBRSL_UNKNOWN && ($data['loan_status'] !== Consts::PNDNG_DSBRSL_MANUAL)){
            thrw("Current disbursal status is {$loan->disbursal_status}. Refresh the page to see the latest status.");
        }
        if($loan->manual_disb_user_id !== null && isset($data['manual_disb_user_id'])){
            if($loan->manual_disb_user_id == $data['manual_disb_user_id']){
                thrw("Manual disbursement already initiated by you.");
            }
            else{
                $person_repo = new PersonRepositorySQL();
                $manual_disb_user_name = $person_repo->full_name($loan->manual_disb_user_id);
                thrw("Manual disbursement initiated by {$manual_disb_user_name}. You are not able to initiate or capture this manual disbursement");
            }
        }
        if(isset($data['loan_status']) && $data['loan_status'] == Consts::PNDNG_DSBRSL_MANUAL){
            $result = $loan_repo->update_model_by_code(['loan_doc_id'=> $data['loan_doc_id'],
            'status' => Consts::PNDNG_DSBRSL_MANUAL, 'disbursal_status' => null, 'manual_disb_user_id' => $data['manual_disb_user_id']]);
        }else{
            $disb_attempt = (new DisbursalAttemptRepositorySQL)->get_last_disburse_attempt($data['loan_doc_id'], ['status', 'partner_response']);
            $success_disb_statuses = [Consts::DSBRSL_CPTR_FAILED, Consts::DSBRSL_SUCCESS];
            if($data['disbursal_status'] == Consts::DSBRSL_FAILED && in_array($disb_attempt->status, $success_disb_statuses)){
                thrw("Current disbursal status is {$disb_attempt->status}. Refresh the page to see the latest status.");
            }
            else{
                $result = $loan_repo->update_model_by_code(['loan_doc_id' => $data['loan_doc_id'], 'disbursal_status' => $data['disbursal_status']]);
            }
        }
        return $result;

    }

    public function cancel_capture_disbursal($data){
        $loan_repo = new LoanRepositorySQL();

        $loan = $loan_repo->get_record_by('loan_doc_id',$data['loan_doc_id'],['status']);

        if($loan->status == Consts::PNDNG_DSBRSL_MANUAL){
            $loan_repo->update_model_by_code(['loan_doc_id'=> $data['loan_doc_id'], 'manual_disb_user_id' => null]);
        }
        
    }



    public function get_fa_applier_email($doc_id, $appl = true){
        $repo = $appl ? new LoanApplicationRepositorySQL : new LoanRepositorySQL;
        $record = $repo->find_by_code($doc_id,['loan_applied_by']);
		$cs_user_id = $record->loan_applied_by;
		$cs_users = AppUser::where('id',$cs_user_id)->get(['email']);
		return $cs_users->isEmpty() ? null : $cs_users[0]->email;
    }

    public function get_last_otp_status($loan_doc_id){
        $otp = DB::selectOne("select status from otps where entity_id='$loan_doc_id' order by id desc");
        return isset($otp) ? $otp->status : null;
    }

    public function check_txn_id_exists($data)
    {
        

        // return ['ignore_check' => true];
        $acc_repo = new AccountRepositorySQL();
        // $acc_stmt_repo = new AccountStmtRepositorySQL();
        // $loan_txn_repo = new LoanTransactionRepositorySQL();

        
        $acc_ids = str_getcsv($data['acc_id']);

        $acc_stmt_records = $result = $message =  array();
        foreach ($acc_ids as $acc_id){
        
            if(Str::contains($acc_id,'_')){
                $arr = explode('_',$acc_id);
                $acc_id = $data['acc_id'] = $arr[1];
                $data['mode'] = $arr[0];
            }

            $amount_type = get_amt_field($data['mode']);


            $account = $acc_repo->find($acc_id, ['to_recon', 'acc_prvdr_code']);
        
            $skip_txn_id_check = config('app.skip_txn_id_check');

            if($account){
                try{
                    if(array_key_exists('is_skip_trans_id_check', $data) && $data['is_skip_trans_id_check']){
                        // if($account->to_recon == 0){
                            return ['ignore_check' => true];
                        // }else{
                        //     thrw("This transaction ID check cannot be skipped.");
                        // }

                    }
                    else 
                    if($account->to_recon || $account->id == 4292){
                        $txn_ids = str_getcsv($data['txn_id']);
                        $total_amount = null; 
                        

                        foreach($txn_ids as $txn_id){  
                            $data['loan_doc_id'] = isset($data['loan_doc_id']) ? $data['loan_doc_id'] : null;
                            $acc_stmt_record = $this->get_acc_stmt_details($txn_id, $data, $data['loan_doc_id']);
                            array_push($acc_stmt_records, $acc_stmt_record);
                            $total_amount += $acc_stmt_record->$amount_type; 

                        }

                        if(array_key_exists('message', $result)){
                            $result += ['acc_stmt_records' => $acc_stmt_records, 'total_amount' => $total_amount];
                        }else{
                            $result = ['acc_stmt_records' => $acc_stmt_records, 'total_amount' => $total_amount];

                        }

                        if(sizeof($acc_stmt_records) > 1 && $data['type'] == 'payment_capture'){
                            unset($result['acc_stmt_records']);

                            $acc_stmt =  DB::selectOne("select id from account_stmts where recon_status != '31_paid_to_different_acc' and stmt_txn_type = 'credit' and stmt_txn_id = ? and country_code = ?", [$data['txn_id'], session('country_code')]);

                            if($acc_stmt){
                                (new AccountStmtRepositorySQL)->update_model(['id' => $acc_stmt->id, 'recon_status' => '31_paid_to_different_acc' , 'updated_at' => Carbon::now()] );
                            }

                            thrw("You cannot capture the repayment here. \n Already the repayment against this FA has paid to the different account.\n Ask app support to capture this transaction.");

                        }

                    }
                    else if(in_array($account->acc_prvdr_code, $skip_txn_id_check)){
                  $result +=  ['ignore_check' => true, 'message' => 'Transaction import is disabled for this account, Please proceed with transaction proof.'];
                    }
                    else{
                        thrw("Ask app support to capture this transaction.");
                    }
                }catch(\Exception $e){
                    // thrw($e->getMessage());
                    if(empty($message)){
                        array_push($message, $e->getMessage());
                    }


                }   
            }
            else{
                thrw("No such account exist {$data['acc_id']}");
            }
        }
       
        if(!empty($message) && !isset($result['acc_stmt_records'])){
            $result['message'] = $message;
        }

        return $result;

    }
    private function get_acc_stmt_details($txn_id, $data,$loan_doc_id = null){
        $acc_stmt_repo = new AccountStmtRepositorySQL();
        $loan_txn_repo = new LoanTransactionRepositorySQL();
       
        $loan_txn = $loan_txn_repo->get_loan_doc_id($txn_id);
        $amount_type = get_amt_field($data['mode']);
        
        if(sizeof($loan_txn) == 0 || $loan_txn[0]->reversed_date != null){
            $acc_stmt_record = $acc_stmt_repo->get_record_by_many(['BINARY stmt_txn_id', 'stmt_txn_type'], [$txn_id, $data['mode']], ['account_id', $amount_type, 'stmt_txn_id', 'stmt_txn_date', 'descr', 'stmt_txn_type', 'loan_doc_id']);
            
            if($acc_stmt_record){
                if(isset ($data['txn_date']) && $data['txn_date'] > carbon::now()->format('Y-m-d')){
                    thrw("You cannot capture a transaction with future date");
                }else if(session('role_codes') != "app_support" && format_date($acc_stmt_record->stmt_txn_date) < format_date(datetime_db())){    
                    thrw("You cannot capture a transaction for past date. Report to App support to capture the transaction");
                }elseif($acc_stmt_record->stmt_txn_type != $data['mode']){
                    thrw("This txn id {$txn_id} exist but it is a {$acc_stmt_record->stmt_txn_type} transaction. But you are trying to capture it for a {$data['mode']} transaction");
                }else if($acc_stmt_record->account_id != $data['acc_id'] && $data['acc_id'] != 4292){
                    thrw("No {$data['mode']} record found in the respective account statement");
                }else if($loan_doc_id && $acc_stmt_record->loan_doc_id != null){
                    thrw("The transaction ID {$txn_id} is already matched with the FA {$acc_stmt_record->loan_doc_id}. You can not manually capture the repayment here. Under the 'Recon Float A/C' menu search the transaction ID {$txn_id} and use the 'Review & Sync' button.");
                }else{
                    return $acc_stmt_record;
                }
            }
            else{
                #thrw("No {$data['mode']} record found in the respective account statement");
                thrw("No such transaction exist with above details. If you think, this was missed in the import or import is failed or not configured, you can decide to add the transaction first under Flow Float A/C > Add Transaction menu.");

            }

        }else {
            thrw("This txn id {$txn_id} is already linked the FA {$loan_txn[0]->loan_doc_id}");
        }

    }


    public function get_req_data_for_capture($data){

        return [
                 "mode"      => 'recon',
                 "loan_doc_id" => $data['loan_doc_id'],
                 "txn_date" => $data['txn_date'],
                 "paid_date" => $data['txn_date'], 
                 'remarks' => array_key_exists('remarks' ,$data) ? $data['remarks'] : null,
                 "txn_id" => $data['txn_id'],
                 "amount" => $data['cr_amount'],
                 "to_ac_id" => $data['to_acc_id'],
                 "txn_mode" => "wallet_transfer",
                 "send_sms" => true,
                 "waive_penalty" => false,
                 "is_part_payment" => false,
 
             ];
 
 
     }

    private function get_addl_sql($data){

        $recon_reason = $data['recon_reason'];
        
        if($recon_reason == 'paid_to_diff_acc' || $recon_reason == 'duplicate_payment' ){
            $acc_stmt_ids = $data['acc_stmt_ids'];
            $loan_doc_id = $data['loan_doc_id'];
            $loan_repo = new LoanRepositorySQL;
            $loan = $loan_repo->find_by_code($loan_doc_id, ['cust_id']);
            $acc_stmt_ids = implode(", ", $acc_stmt_ids);
            $addl_sql = "and id in ($acc_stmt_ids)";
            $update_fields = ", loan_doc_id = '{$loan_doc_id}' , acc_txn_type = 'fa', cust_id = '$loan->cust_id'";

        }else if($recon_reason == 'redemption' || $recon_reason == 'investment'){
            $addl_sql = "and id  = '{$data['acc_stmt_id']}'";
            $update_fields = ", acc_txn_type = '{$recon_reason}'";
        }
        
        return [$addl_sql, $update_fields];

    }

    public function update_recon_status($data){
         
        try{
            
            $addl_sql = $update_fields =  "";

            DB::beginTransaction(); 

            $country_code = session('country_code');
            $recon_reason = $data['recon_reason'];
            $loan_txn['country_code'] = session('country_code');
            $loan_txn['txn_mode'] = 'wallet_transfer';

            if($recon_reason == 'paid_to_diff_acc'){
                $loan_txn_repo  = new LoanTransactionRepositorySQL;
                $loan_repo = new LoanRepositorySQL;
                $loan = $loan_repo->find_by_code($data['loan_doc_id'], ['cust_id']);
                
                $loan_txn['loan_doc_id'] = $data['loan_doc_id'];
                $loan_txn['txn_type'] = Consts::LOAN_TXN_PAYMENT_DIFF_ACC;;
                $loan_txn['to_ac_id'] = $data['from_acc_id'];
                $loan_txn['txn_id'] = $data['stmt_txn_id'];
                $loan_txn['txn_date'] = $data['stmt_txn_date'];
                $loan_txn['amount'] = $data['cr_amount'];

                
                $loan_txn_repo->create($loan_txn);
                
                $loan_txn['txn_type'] = Consts::LOAN_TXN_PAYMENT_DIFF_ACC_INT_TRANS;
                $loan_txn['txn_id'] = $data['txn_id'];
                $loan_txn['from_ac_id'] = $data['from_acc_id'];
                $loan_txn['to_ac_id'] = $data['to_acc_id'];
                $loan_txn['txn_date'] = $data['txn_date'];

                $loan_txn_repo->create($loan_txn);

                $loan_txn['txn_type'] = "payment";
                
                $req_data = $this->get_req_data_for_capture($data);
                
                (new RepaymentService)->capture_repayment($req_data);

                [$addl_sql, $update_fields] = $this->get_addl_sql($data);
                
                $is_update = true;

            }else if($recon_reason == 'duplicate_payment'){
                $loan_txn_repo  = new LoanTransactionRepositorySQL;
                $loan_repo = new LoanRepositorySQL;
                $loan = $loan_repo->find_by_code($data['loan_doc_id'], ['cust_id']);
                $loan_txn['loan_doc_id'] = $data['loan_doc_id'];
                $loan_txn['txn_type'] = Consts::LOAN_TXN_DUP_PAYMENT;
                $loan_txn['txn_id'] = $data['stmt_txn_id'];
                $loan_txn['amount'] = $data['cr_amount'];
                $loan_txn['to_ac_id'] = $data['account_id'];
                $loan_txn['txn_date'] = $data['stmt_txn_date'];
                
                $loan_txn_repo->create($loan_txn);

                $loan_txn['txn_type'] = Consts::LOAN_TXN_DUP_PAYMENT_REVERSAL;
                $loan_txn['txn_id'] = $data['txn_id'];
                $loan_txn['from_ac_id'] = $data['from_acc_id'];
                $loan_txn['to_ac_id'] = null;
                $loan_txn['amount'] = $data['reversal_amount'];
                $loan_txn['txn_date'] = $data['txn_date'];

                $loan_txn_repo->create($loan_txn);

                [$addl_sql, $update_fields] = $this->get_addl_sql($data);

                $is_update = true;

            }else if($recon_reason == 'redemption' || $recon_reason == 'investment'){
                $acc_details = (new AccountRepositorySQL)->find($data['account_id'],['acc_number','acc_prvdr_code']);
                $is_update = true;
                [$addl_sql, $update_fields] = $this->get_addl_sql($data);
                $currency_code = (new CommonRepositorySQL())->get_currency_code($country_code)->currency_code;

                if($recon_reason == 'redemption' ){
                    $mail_data = ['currency_code' => $currency_code, 
                                  'amount' => number_format($data['db_amount']), 
                                  'txn_id' => $data['stmt_txn_id'], 
                                  'stmt_txn_date' => format_date($data['stmt_txn_date']),
                                  'acc_number' => $acc_details->acc_number,
                                  'acc_prvdr' => $acc_details->acc_prvdr_code,
                                  'country_code' => session('country_code')];
                                
                    Mail::to([get_ops_admin_email(), get_market_admin_email(), get_l3_email()])->queue((new FlowCustomMail('redemption_notification', $mail_data))->onQueue('emails'));
                }

            }else if($recon_reason == 'excess_reversal'){
                
                $is_update = false;
                $data['amount'] = $data['db_amount'];
                $data['txn_id'] = $data['stmt_txn_id'];
                $data['txn_date'] = $data['stmt_txn_date'];
                $data['from_ac_id'] = $data['from_acc_id'];
                $data['txn_mode'] = $loan_txn['txn_mode'];
                $loan = (new LoanRepositorySQL)->find_by_code($data['loan_doc_id'], ['cust_id']);

                $data['cust_id'] = $loan->cust_id;
                $resp = (new RepaymentService)->capture_excess_reversal($data);

            }else if($recon_reason == 'redemption_int_transfer' || $recon_reason == 'charges' || $recon_reason == 'interest'){
                
                $is_update = false;
                $resp = (new AccountStmtRepositorySQL())->update_model(['id' => $data['acc_stmt_id'], 'recon_status' => '80_recon_done', 'acc_txn_type' => $recon_reason]);
            }else if($recon_reason == 'testing'){
                
                $is_update = false;

                $update_arrs = [$data['stmt_txn_id'] => "outward (test)", $data['txn_id'] => "outward_reversed (test)"];

                foreach($update_arrs as $key => $value){
                    $resp = (new AccountStmtRepositorySQL())->update_model_by_code(['stmt_txn_id' => "$key", 'recon_status' => '80_recon_done', 'acc_txn_type' => "$value"]);
                }       

            }

            if($is_update){
                $resp = DB::update("update account_stmts set recon_status = '80_recon_done' $update_fields where country_code = '{$country_code}' $addl_sql" );
            }

            DB::commit();

            return $resp;
         }catch (Exception $e) {
             DB::rollback();
             throw new FlowCustomException($e->getMessage());
         }


    }
    public function reinitiate_recon($data){
       
        $acc_stmt_id = $data['acc_stmt_id'];
        $resp = null;

        try{
            DB::beginTransaction();

            $resp =  DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = ?" , [$acc_stmt_id]);
            
            $loan_txns = (new LoanTransactionRepositorySQL)->get_loan_doc_id($data['txn_id']);
            
            
            if(sizeof($loan_txns) > 0 ){
                $resp =  DB::update("update loan_txns set recon_amount = recon_amount - ? where txn_id = ?", [$data['amount'], $data['txn_id']]);
            }
            
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            if ($e instanceof QueryException){
                throw $e;
            }else{
                thrw($e->getMessage());
            }
    }
        return $resp;
    }


    public function remove_disbursal($data){
        $loan_txn_repo = new LoanTransactionRepositorySQL();
        $loan_repo = new LoanRepositorySQL();
        $fund_repo = new CapitalFundRepositorySQL();
        $borr_repo = new BorrowerRepositorySQL();
        $acc_stmt_repo =  new AccountStmtRepositorySQL();

        $loan_txn = $loan_txn_repo->find($data['loan_txn_id'], ['loan_doc_id', 'from_ac_id', 'to_ac_id', 'amount', 'txn_type', 'txn_id', 'txn_date']);

        $loan = $loan_repo->get_record_by('loan_doc_id', $loan_txn->loan_doc_id, ['fund_code', 'cust_id','status']);

        try{
            DB::beginTransaction();


            $today_date = Carbon::now();
            
            DB::delete("delete from loan_txns where id = ?", [$data['loan_txn_id']]);

            $data = ['loan_doc_id' => $loan_txn->loan_doc_id,
                    'status' => 'hold',
                    'disbursal_status' => 'unknown',
                    'due_amount' => 0,
                    'current_os_amount' => 0,
                    'disbursal_date' => null,
                    'due_date' => null

                    ];

            $loan_repo->update_model_by_code($data);

            $borrower = $borr_repo->find_by_code($loan->cust_id, ['fund_code', 'prob_fas', 'tot_loans', 'category']);


            $update_arr = ['cust_id' => $loan->cust_id, 'ongoing_loan_doc_id' => null,'tot_loans' => $borrower->tot_loans -1];
            
            if($borrower->tot_loans <= config('app.default_prob_fas')){
                $update_arr['prob_fas'] = $borrower->prob_fas +1;
            }

            
            $borr_repo->update_model_by_code($update_arr);

            $fund_repo->increment_by_code('os_amount', $loan->fund_code, -1 * $loan_txn->amount);
            $fund_repo->increment_by_code('tot_disb_amount', $loan->fund_code,-1 * $loan_txn->amount);

            $acc_stmt_repo->update_model_by_code(['stmt_txn_id' => $loan_txn->txn_id, 'loan_doc_id' => null, 'recon_status' => null, 'recon_desc' => null, 'cust_id' => null]);
            
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            if ($e instanceof QueryException){
                throw $e;
            }else{
            thrw($e->getMessage());
            }
        }
        return ['message' => 'success'];

}


    public function get_disb_amt_to_reverse($loan_doc_id){
        $dup_disb_type = Consts::LOAN_TXN_DUPLICATE_DISBURSAL;
        $dup_disb_rvrsl_type = Consts::LOAN_TXN_DUPLICATE_DISBURSAL_RVRSL;
        $dup_disb = DB::selectOne("select sum(amount) amt from loan_txns where loan_doc_id = ? and txn_type = ?", [$loan_doc_id, $dup_disb_type]);
        $dup_disb_rvrsd = DB::selectOne("select sum(amount) amt from loan_txns where loan_doc_id = ? and txn_type = ?", [$loan_doc_id, $dup_disb_rvrsl_type]);
        return $dup_disb->amt - $dup_disb_rvrsd->amt;
    }

    public function capture_dup_disb_n_reversal($loan_txn, $stmt_txn_type, $with_txn = true, $matching_recon_status = Consts::MATCHING_RECON_STATUS){
        try{    
            $with_txn ? DB::beginTransaction() : null;
            $loan_txn['country_code'] = session('country_code');
            $loan_txn['txn_type'] = $stmt_txn_type == "debit" ? Consts::LOAN_TXN_DUPLICATE_DISBURSAL : Consts::LOAN_TXN_DUPLICATE_DISBURSAL_RVRSL;
            (new LoanTransactionRepositorySQL)->create($loan_txn);
    
            $this->complete_recon_loan_txn($loan_txn, $stmt_txn_type, $matching_recon_status);
            $mail_view = $stmt_txn_type == "debit" ? "duplicate_disbursal" : "dup_disb_reversed";
            Mail::to(config('app.app_support_email'))->queue((new FlowCustomMail($mail_view, $loan_txn)));
            $with_txn ? DB::commit() : null;
        }catch(\Exception $e){
            $with_txn ? DB::rollback() : null;
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
        
    }


    public function complete_recon_loan_txn($loan_txn, $txn_type, $matching_recon_status = Consts::MATCHING_RECON_STATUS){
        $account_id = $txn_type == 'debit' ? $loan_txn['from_ac_id'] : $loan_txn['to_ac_id'];
        $acc_stmt_repo = new AccountStmtRepositorySQL();
        $acc_stmt_obj = $acc_stmt_repo->get_record_by_many(['stmt_txn_id','account_id'], [$loan_txn['txn_id'],$account_id], ['id', 'cr_amt', 'dr_amt', 'stmt_txn_date', 'loan_doc_id']);

        if($acc_stmt_obj){
            $acc_stmt_id = (new RepaymentService)->validate_for_recon($loan_txn, $acc_stmt_obj, $txn_type);
            if($acc_stmt_id){
                $acc_stmt_repo->update_model(["recon_status" => $matching_recon_status,"id" => $acc_stmt_id, "loan_doc_id" => $loan_txn["loan_doc_id"]]);
            }    
        }
    }

    public function list_manual_capture($data){
   
        $data = $data['manl_capture_search'];
    
        $country_code = session('country_code');
    
        $addl_sql_condn = '';
    
        if(array_key_exists('txn_id', $data)) {
            $txn_id = $data['txn_id'];
            $addl_sql_condn .= "AND txn_id = '$txn_id' ";
        }
    
        if(array_key_exists('loan_doc_id', $data)) {
            $loan_doc_id = $data['loan_doc_id'];
            $addl_sql_condn .= "AND loan_doc_id = '$loan_doc_id' ";
        }
    
        if(isset($data['txn_date__from']) && isset($data['txn_date__to'])) {            
    
            $addl_sql_condn .= "AND date(created_at) >= '{$data['txn_date__from']}' AND date(created_at) <= '{$data['txn_date__to']}' ";
            if(array_key_exists('stmt_txn_type', $data)) {            
                if($data['stmt_txn_type'] == "credit") {
                    $stmt_txn_type = "payment";
                }elseif($data['stmt_txn_type'] == "debit"){
                    $stmt_txn_type = "disbursal";
                }
                $addl_sql_condn .= "AND txn_type = '$stmt_txn_type' ";
            }
               
        }
          
        $fa_lists = DB::select("select country_code, loan_doc_id, amount, txn_type, txn_id, txn_mode, txn_exec_by, txn_date, reason_for_skip from loan_txns where (reason_for_skip is not null or txn_mode = 'wallet_transfer') and country_code = '{$country_code}' $addl_sql_condn ");
        
        if($fa_lists){
          foreach($fa_lists as $fa_list){
           
            if($fa_list->txn_exec_by){
                $full_name = (new personRepositorySQL)->full_name($fa_list->txn_exec_by);
                if($full_name){
                    $fa_list->txn_exec_by =  $full_name;
                }
            }
            if($fa_list->txn_type == "payment") {
                $fa_list->txn_type = "Credit";
            }elseif($fa_list->txn_type == "disbursal") {
                $fa_list->txn_type = "Debit";
            }
          }
        }    
        
            return $fa_lists;
      }


}
