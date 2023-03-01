<?php

namespace App\Services\Schedule;

use App\Jobs\StatementImportJob;
use App\Mail\FlowCustomMail;
use App\Models\StatementImport;
use App\Repositories\SQL\MarketRepositorySQL;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use App\SMSTemplate;
use App\Services\Support\SMSService;
use App\Services\LoanService;
use App\Services\BorrowerService;
use App\Services\Schedule\SMSScheduleService;
use App\Services\Mobile\CustAppService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Consts;
use App\Repositories\SQL\AccountStmtRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\DisbursalAttemptRepositorySQL;
use App\Repositories\SQL\LoanApplicationRepositorySQL;
use Symfony\Component\Process\Process;
use App\Services\AutoCapturePaymentService;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Services\RepaymentService;
use App\Services\Vendors\Whatsapp\WhatsappWebService;
use App\Repositories\SQL\LoanTransactionRepositorySQL;
use App\Repositories\SQL\ProbationPeriodRepositorySQL;
use App\Models\RMPunchTime;
use App\Models\FlowApp\AppUser;
use App\Models\Vendor;
use App\Services\Vendors\SMS\AitSMSService;
use App\Services\Vendors\File\GoogleService;

class ScheduleService
{

    public function get_markets_to_schedule()
    {
        return DB::select("select country_code,time_zone, isd_code from markets where status = 'enabled'");
    }


    private	function call_import_scripts($acc_prvdr_code, $acc_id, $recon_id)
    {

        $params = array_merge(constant("Consts::{$acc_prvdr_code}_STMT_LOGIN"),  [$acc_id, $recon_id]);

        $resp = run_py_script("vendors/stmts/{$acc_prvdr_code}_stmt_import.py", $params);

        if (Str::contains($resp, 'success')) {

            return true;
        } else {
            return false;
        }
    }

    public function recon_all_acc_stmts($country_code){

        session()->put('country_code', $country_code);
        $recon_id = 123;
        $acc_repo = new AccountRepositorySQL();


        $accounts_to_recon = (new AccountRepositorySQL)->get_records_by_in('id',[1783, 2895, 4161] ,['id', 'acc_prvdr_code', 'country_code','web_cred' ,'acc_number']);


        foreach ($accounts_to_recon as $account_to_recon) {
            // $status = $this->call_import_scripts($account_to_recon->acc_prvdr_code, $account_to_recon->id, $recon_id);			$status = true;

            $this->recon_debit_txns($account_to_recon->id, $account_to_recon->acc_prvdr_code, $recon_id);
            $this->recon_credit_txns($account_to_recon->id, $account_to_recon->acc_prvdr_code, $recon_id);

            #$auto_serv = new AutoCapturePaymentService();
            #$auto_serv->auto_capture();

        }
    }

    public function recon_rbok_debit_txns($descr, $id){

        // $descr = 'Momo Outward transfer / 250787808115 34456 ft'; 	// MOMO Account Transfer Description
        // $descr = 'IB Account transfer / 34456 ft';					// BK Account Transfer Description
        // $descr = 'Momo Outward transfer / 250791519171 MTN Payment'; // Internal Transfer to MOMO account

        $descr_array = explode(' / ', $descr);
        $identifier = $descr_array[1];
        $disbursal_id = null;
        $remarks = explode(' ', $identifier);

        if(strpos($descr_array[0], "Momo Outward transfer") !== false){
            $rmtn_disb_array = config("app.RMTN_district_accounts");
            foreach ($rmtn_disb_array as $rmtn_disb_line) {
                if(strpos($descr_array[1], "250".$rmtn_disb_line) !== false){
                    (new AccountStmtRepositorySQL())->update_model(['id' => $id, 'recon_status' => Consts::REDEMPTION_INT_TRANSFER]);
                    return Consts::REDEMPTION_INT_TRANSFER;
                }
            }
        }

        if($descr_array[0] == 'IB Account transfer'){
            $disbursal_id = $remarks[0];
        }elseif(count($remarks) >= 2){
            $disbursal_id = $remarks[1];
        }

        return $disbursal_id;
    }

    public function capture_disbursal($stmt_txn, $from_acc_id, $acc_prvdr_code, $matching_recon_status = Consts::MATCHING_RECON_STATUS){
        if($acc_prvdr_code == Consts::RMTN_AP_CODE || $acc_prvdr_code == Consts::RATL_AP_CODE){

            $acc_number = ($acc_prvdr_code == Consts::RMTN_AP_CODE) ? substr($stmt_txn->ref_account_num, 3) : $stmt_txn->ref_account_num;
            $account = (new AccountRepositorySQL)->find_by_code($acc_number, ['cust_id']);
            if(!isset($account)){
                return false;
            }
            // $loan = (new LoanRepositorySQL)->get_record_by_many(['cust_id', 'status', 'disbursal_status'], [$account->cust_id, Consts::LOAN_HOLD, Consts::DSBRSL_CPTR_FAILED], ['loan_principal', 'cust_id', 'loan_doc_id']);
            $loan = DB::selectOne("select loan_principal, cust_id, loan_doc_id from loans where cust_id = ? and status in ('hold','pending_disbursal','ongoing') and date(loan_appl_date) <= date(?)", [$account->cust_id, $stmt_txn->stmt_txn_date]);
            $loan_doc_id = isset($loan) ? $loan->loan_doc_id : null;

        }else{
            if($acc_prvdr_code == Consts::BOK_AP_CODE){
                $disbursal_id = $this->recon_rbok_debit_txns($stmt_txn->descr, $stmt_txn->id);
            }else{
                $disbursal_id = explode('/', $stmt_txn->descr)[0];
            }

            if(!is_numeric($disbursal_id)){
                if($disbursal_id == Consts::REDEMPTION_INT_TRANSFER){
                    return true;
                }
                return false;
            }
            $attempt = (new DisbursalAttemptRepositorySQL)->find($disbursal_id, ['loan_doc_id', 'status', 'flow_request']);
            if(!$attempt){
                return false;
            }
            $loan_doc_id = $attempt->loan_doc_id;
            $loan = DB::selectOne("select loan_principal, cust_id from loans where loan_doc_id = ? and status in ('hold','pending_disbursal', 'ongoing') and date(loan_appl_date) <= date(?)", [$loan_doc_id, $stmt_txn->stmt_txn_date]);
        }

        try{
            if(!$loan){
                in_array($acc_prvdr_code, config('app.aps_without_disb_id_in_remarks')) && isset($account) ? thrw("No Pending FA found for Cust ID {$account->cust_id}") : thrw("No pending FA found for the disbursal attempt ID {$disbursal_id}");
            }
            $to_ac_id = in_array($acc_prvdr_code, config('app.aps_without_disb_id_in_remarks')) && isset($account) ? $account->id : json_decode($attempt->flow_request, true)['loan_txn']['to_ac_id'];
            $disbursal_txn = ['amount' => $stmt_txn->dr_amt, 'txn_date' => $stmt_txn->stmt_txn_date,
                'txn_mode' => 'instant_disbursal', 'from_ac_id' => $from_acc_id,
                'to_ac_id' => $to_ac_id, 'txn_id' => $stmt_txn->stmt_txn_id,
                'loan_doc_id' => $loan_doc_id];
            $disb_txns = (new LoanTransactionRepositorySQL)->get_records_by_many(['loan_doc_id', 'txn_type'], [$loan_doc_id, 'disbursal'], ['id']);
            if(sizeof($disb_txns) > 0){
                (new LoanService)->capture_dup_disb_n_reversal($disbursal_txn, 'debit', false, $matching_recon_status);
                return true;
            }
            elseif($loan->loan_principal != $stmt_txn->dr_amt){
                thrw("Incorrect amount disbursed for FA {$loan_doc_id}");
            }

            $disbursal_txn['capture_only'] = true;
            $disbursal_txn['send_sms'] = true;
            DB::beginTransaction();
            $disb_result = (new LoanService)->disburse($disbursal_txn, false);
            if (!array_key_exists('disb_status', $disb_result) || $disb_result['disb_status'] != Consts::DSBRSL_SUCCESS) {
                thrw($disb_result['exp_msg']);
            }
            $this->mark_recon_done($loan_doc_id, $loan->cust_id, $stmt_txn->id, $disb_result['loan_txn_id'], $stmt_txn->dr_amt, $matching_recon_status);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            $exp_msg = $e->getMessage();
            $trace = $e->getTraceAsString();
            Log::error($exp_msg);
            Log::error($trace);
            $cust_id = isset($loan) ? $loan->cust_id : null;
            $disb_id = isset($disbursal_id) ? $disbursal_id : null;
            $acc_number = isset($acc_number) ? $acc_number : null;
            $data = ['account_id' => $from_acc_id, 'acc_prvdr_code' => $acc_prvdr_code,
                'loan_doc_id' => $loan_doc_id, 'cust_id' => $cust_id,
                'txn_id' => $stmt_txn->stmt_txn_id, 'exp_msg' => $exp_msg, 'disb_id' => $disb_id, 'acc_number' => $acc_number,
                'exp_trace' => $trace, 'country_code' => session('country_code')];
            Mail::to(config('app.app_support_email'))->queue(new FlowCustomMail('ussd_capture_failed', $data));
            if($matching_recon_status == Consts::PENDING_STMT_IMPORT) thrw($exp_msg);
            return false;
        }
    }

    public function get_account_ids_for_txn_check($account_to_recon){
        $account_ids = [$account_to_recon];
        if($account_to_recon == 4182){
            $account_ids = [4182, 4292];
        }
        if($account_to_recon == 4161 || $account_to_recon == 3074 || $account_to_recon == 3153){
            $account_ids = [4161, 3074, 3153];
        }
        return implode(',',$account_ids);
    }


    public function recon_debit_txns($account_to_recon, $acc_prvdr_code, $recon_id, $acc_stmt_id = null){

        $addl_sql = "";

        if($acc_stmt_id){
            $addl_sql = "and id = $acc_stmt_id";
        }

        $recon_status = "('30_no_matching_fa', '80_recon_done', '75_internal_transfer')";
        $stmt_txns = DB::select("select stmt_txn_id, account_id, ref_account_num, dr_amt, descr, stmt_txn_date, id from account_stmts where loan_doc_id is null and stmt_txn_type = ? and account_id = ? and date(stmt_txn_date) >= ? and (recon_status is null or recon_status not in $recon_status)  $addl_sql order by stmt_txn_date" , ['debit', $account_to_recon, config('app.recon_scr_strt_date')]);

        $this->loan_repo = new LoanRepositorySQL();

        foreach ($stmt_txns as $stmt_txn){

            try{
                $stmt_txn_id = $stmt_txn->stmt_txn_id;
                $account_ids = $this->get_account_ids_for_txn_check($account_to_recon);
                $txn_types = "('disbursal', 'excess_reversal', 'duplicate_disbursal','duplicate_payment_reversal')";
                $loan_txns = collect(DB::select("select id,recon_amount,amount,loan_doc_id from loan_txns where txn_id like ? and txn_type in $txn_types and from_ac_id in ($account_ids)", ["%{$stmt_txn_id}%"]));

                $matching_recon_status = (isset($acc_stmt_id)) ? Consts::PENDING_STMT_IMPORT : Consts::MATCHING_RECON_STATUS;

                if(count($loan_txns) == 1){
                    #if($stmt_txn->dr_amt == $loan_txns[0]->amount){
                    $loan_txn = $loan_txns[0];
                    if($stmt_txn->dr_amt + $loan_txn->recon_amount <= $loan_txn->amount){
                        $loan_doc_id = $loan_txn->loan_doc_id;
                        $loans = $this->loan_repo->get_records_by('loan_doc_id',$loan_doc_id,['cust_id','acc_number']);
                        $this->mark_recon_done($loan_doc_id, $loans[0]->cust_id, $stmt_txn->id, $loan_txn->id, $stmt_txn->dr_amt, $matching_recon_status);

                    }else{
                        DB::update("update account_stmts set recon_status = '70_incorrect_amount_in_fa', loan_doc_id = ?, updated_at = ? where id = ?" , [$loan_txns[0]->loan_doc_id, Carbon::now(), $stmt_txn->id]);
                        Log::warning("Amount not matching Acc Stmt Amt {$stmt_txn->dr_amt}. Loan Txn amount {$loan_txns[0]->amount}");
                    }
                }else if(sizeof($loan_txns) == 0){
                    $recon_status = false;
                    $account = (new AccountRepositorySQL)->find($account_to_recon, ['acc_purpose']);
                    if(is_disb_capture_frm_stmt_reqrd($acc_prvdr_code) && in_array('disbursement', $account->acc_purpose)){

                        $recon_status = $this->capture_disbursal($stmt_txn, $account_to_recon, $acc_prvdr_code, $matching_recon_status);
                    }
                    if(!$recon_status && !isset($acc_stmt_id)){
                        $recon_descr = "loan_txns.txn_id = $stmt_txn_id";
                        DB::update("update account_stmts set recon_status = '30_no_matching_fa', recon_desc = ?, updated_at = ? where id = ?" , [$recon_descr, Carbon::now(), $stmt_txn->id]);
                        Log::warning("No transaction found in loan_txns table for {$stmt_txn_id}");
                    }
                }else if(sizeof($loan_txns) > 1){
                    $loan_doc_ids = collect($loan_txns)->pluck("loan_doc_id")->toArray();
                    DB::update("update account_stmts set recon_status = '50_multiple_fas_matched', recon_desc = ?, updated_at = ? where id = ?" , [implode(', ', $loan_doc_ids), Carbon::now(), $stmt_txn->id]);
                    Log::warning("More than one transaction found in loan_txns table");
                }

            }
            catch(\Exception $e){

                $this->send_recon_exception_email((array)$stmt_txn, $e, 'debit');
                if($acc_stmt_id) thrw($e->getMessage());

            }
        }
    }
    public function recon_credit_txns($account_to_recon, $acc_prvdr_code, $recon_id, $acc_stmt_id = null){
        #$stmt_txns = DB::select("select stmt_txn_id, id, descr, stmt_txn_date, cr_amt from account_stmts where (recon_status is null or recon_status = '10_capture_payment_pending') and loan_doc_id is null and stmt_txn_type = ? and account_id = ? order by stmt_txn_date", ['credit', $account_to_recon]);

        $addl_sql = "";

        if($acc_stmt_id){
            $addl_sql = "and id = $acc_stmt_id";
        }

        $stmt_txns = DB::select("select stmt_txn_id, id, account_id, ref_account_num, descr, stmt_txn_date, cr_amt from account_stmts 
					where ((recon_status is null and loan_doc_id is null) 
							or recon_status = '10_capture_payment_pending')  
					 and stmt_txn_type = ? and account_id = ? and date(stmt_txn_date) >= ? $addl_sql
					 order by stmt_txn_date", ['credit', $account_to_recon, config('app.recon_scr_strt_date')]);

        $this->loan_repo = new LoanRepositorySQL();

        foreach ($stmt_txns as $stmt_txn){

            try{
                $stmt_txn_id = $stmt_txn->stmt_txn_id;
                $account_ids = $this->get_account_ids_for_txn_check($account_to_recon);
                $txn_types = "('payment', 'dup_disb_rvrsl','duplicate_payment')";
                $loan_txns = DB::select("select id, loan_doc_id, amount, recon_amount from loan_txns where txn_id like ? and txn_type in $txn_types and to_ac_id in ($account_ids)" , ["%{$stmt_txn_id}%"]);

                if(sizeof($loan_txns) == 1){ # Already payment captured manually

                    $loan_txn = $loan_txns[0];
                    if($stmt_txn->cr_amt + $loan_txn->recon_amount <= $loan_txn->amount){
                        $loan_doc_id = $loan_txn->loan_doc_id;

                        $loans = $this->loan_repo->get_records_by('loan_doc_id',$loan_doc_id,['cust_id','acc_number']);
                        $this->mark_recon_done($loan_doc_id, $loans[0]->cust_id, $stmt_txn->id, $loan_txn->id, $stmt_txn->cr_amt);

                    }else{
                        $amt = "There : {$stmt_txn->cr_amt} | Here : {$loan_txn->amount}";
                        DB::update("update account_stmts set recon_status = '70_incorrect_amount_in_fa', loan_doc_id = ? , recon_desc= ? where id = ?",[ $loan_txn->loan_doc_id,$amt,$stmt_txn->id]);
                        Log::warning("Amount not matching Acc Stmt Amt {$stmt_txn->cr_amt}. Loan Txn amount {$loan_txn->amount}");
                        #TODO send email alert
                    }
                }
                else if (sizeof($loan_txns) > 1) {
                    $loan_doc_ids = collect($loan_txns)->pluck("loan_doc_id")->toArray();
                    $desc = implode(',',$loan_doc_ids);
                    #$desc = $loan_doc_ids[0]->loan_doc_id.','.$loan_doc_ids[1]->loan_doc_id;

                    DB::update("update account_stmts set recon_status = '05_multiple_fas_captured', recon_desc= ?  where id = ?",[$desc, $stmt_txn->id]);
                }
                else{
                    if($acc_prvdr_code == Consts::EZEEMONEY_AP_CODE){
                        $this->process_ezm_stmt_txn($stmt_txn);
                    }else if($acc_prvdr_code == Consts::CHAP_CHAP_AP_CODE){
                        $this->process_cca_stmt_txn($stmt_txn);
                    }else if($acc_prvdr_code == Consts::MTN_AP_CODE || $acc_prvdr_code == Consts::RMTN_AP_CODE){
                        $this->process_mtn_stmt_txn($stmt_txn, $acc_prvdr_code);
                    }else if($acc_prvdr_code == Consts::BOK_AP_CODE){
                        $this->process_bok_stmt_txn($stmt_txn);
                    }else if($acc_prvdr_code == Consts::RATL_AP_CODE){
                        $this->process_atl_stmt_txn($stmt_txn);
                    }
                }
            }
            catch(\Exception $e){

                $this->send_recon_exception_email((array)$stmt_txn, $e, 'credit');
                if($acc_stmt_id) thrw($e->getMessage());

            }
        }
    }

    private function send_recon_exception_email($stmt_txn, $exception, $txn_type){

        $account = (new AccountRepositorySQL)->get_account_by(['id'], [$stmt_txn['account_id']], ['acc_prvdr_code', 'acc_number', 'country_code']);

        $currency_code = (new CommonRepositorySQL())->get_currency_code($account->country_code);

        $stmt_txn['failed_at'] = Carbon::now();
        $stmt_txn['exception'] = $exception->getMessage();
        $stmt_txn['trace'] = $exception->getTraceAsString();
        $stmt_txn['txn_type'] = $txn_type;
        $stmt_txn['amount'] = ($txn_type == 'debit') ? ($stmt_txn['dr_amt']) : ($txn_type == 'credit' ? $stmt_txn['cr_amt'] : $stmt_txn['amount']);
        $stmt_txn['country_code'] = $account->country_code;
        $stmt_txn['currency_code'] = $currency_code->currency_code;
        $stmt_txn['acc_prvdr_code'] = $account->acc_prvdr_code;
        $stmt_txn['acc_number'] = $account->acc_number;

        Mail::to(config('app.app_support_email'))->queue((new FlowCustomMail('reconciliation_failure', $stmt_txn))->onQueue('emails'));

    }

    public function mark_recon_done($loan_doc_id, $cust_id, $stmt_id, $loan_txn_id, $amount, $matching_recon_status = Consts::MATCHING_RECON_STATUS){
        DB::update("update account_stmts set recon_status = ?, loan_doc_id = ?,cust_id = ? where id = ?" , [$matching_recon_status, $loan_doc_id, $cust_id, $stmt_id]);
        DB::update("update loan_txns set recon_amount = recon_amount + ? where id = ? ", [$amount, $loan_txn_id]);
    }

    private function get_ezm_cust($mobile_num, $acc_number, $cust_name)
    {
        $borrower_service = new BorrowerService();
        $borr_repo = new BorrowerRepositorySQL;
        $acc_repo = new AccountRepositorySQL;

        $cust_ids_by_mob[] = $borr_repo->get_cust_id_from_mobile_num($mobile_num, true);

        // $cust_ids_by_acc_num = collect($borrower_service->search_borrower($acc_number, ['cust_id'], true))->pluck('cust_id')->toArray();
        $cust_ids_by_acc_num = $this->get_cust_id_by_acc_number($acc_number, false);

        $cust_ids_by_cust_name = [];

        if ($cust_ids_by_mob[0] == null && empty($cust_ids_by_acc_num)) {

            $name_arr = explode(' ', $cust_name);
            if (sizeof($name_arr) > 1) {
                $cust_ids_by_cust_name = $this->get_cust_id($cust_name);
            }
        }

        $cust_ids = [];

        $cust_ids_by_mob_acc_num_cust_name  = array_unique(array_merge($cust_ids_by_mob, $cust_ids_by_acc_num, $cust_ids_by_cust_name));

        if (sizeof($cust_ids_by_mob_acc_num_cust_name) > 1) {
            return $cust_ids_by_mob_acc_num_cust_name;
        }

        if (sizeof($cust_ids_by_mob) == 1 && strlen($acc_number) > 3) {
            $cust_id = $cust_ids_by_mob[0];
            $accounts = $acc_repo->get_accounts_by(['cust_id', 'status', 'acc_prvdr_code'], [$cust_id, Consts::ENABLED, Consts::EZEEMONEY_AP_CODE], ['acc_number']);
            foreach ($accounts as $account) {
                if ($account && Str::startsWith($account->acc_number, $acc_number)) {
                    $cust_ids[] = $cust_id;
                }
            }
        } else if (sizeof($cust_ids_by_acc_num) == 1) {
            $cust_ids[] = $cust_ids_by_acc_num[0];
        } else {
            $cust_ids = $cust_ids_by_mob_acc_num_cust_name;
        }
        return $cust_ids;
    }

    private function process_cca_stmt_txn($stmt_txn){

        $descr = $stmt_txn->descr;

        $match_string = ["Stock sent from", "Float received from"];
        $cust_name = null;

        foreach($match_string as $str){
            if(Str::startsWith($descr, $str)){
                $cust_name = Str::replaceFirst($str." ", "", $descr);
                break;
            }
        }

        if($cust_name){
            $cust_ids = $this->get_cust_id($cust_name);
            $this->process_stmt_txn_by_cust_id($stmt_txn, $cust_ids, "b.biz_name or p.first_name + p.last_name = $cust_name");
        }
        else{
            (new AccountStmtRepositorySQL)->update_model(['recon_status' => '60_non_fa_credit', 'id' => $stmt_txn->id]);
            $this->report_recon_nonmatch("60_non_fa_credit", $stmt_txn, null, null);
        }
    }

    #Col/FLOW/0701647578/LUZZE SIMON PETER/54
    public function process_ezm_stmt_txn($stmt_txn)
    {
        $descr = $stmt_txn->descr;
        $mobile_num = $acc_number = $cust_name = null;
        if (Str::startsWith($descr, "Col/FLOW")) {
            $descr_array = explode('/', $descr);
            $cust_name = $descr_array[3];
            #$mobile_num = ltrim($decr_array[2], '0');
            if (Str::startsWith($descr_array[2], "0")) {
                $mobile_num = substr($descr_array[2], 1);
            }
            // $borrower_service = new BorrowerService();

            // $customers = $borrower_service->search_borrower($mobile_num, ['cust_id'], false);
            // $cust_ids = collect($customers)->pluck('cust_id')->toArray();
            if (count($descr_array) == 5) {
                $acc_number = $descr_array[4];
            }

            $cust_ids = $this->get_ezm_cust($mobile_num, $acc_number, $cust_name);
            $this->process_stmt_txn_by_cust_id($stmt_txn, $cust_ids, "p.mobile_num = $mobile_num join owner_person_id = p.id");

            /*$loan_doc_ids = $this->loan_repo->get_os_loan_doc_id_by('cust_mobile_num', "%{$mobile_num}", 'like');
            $this->process_stmt_txn_by_loan_doc_id($stmt_txn, $loan_doc_ids,  "loans.cust_mobile_num = $mobile_num",$descr,);*/
        } else {
            DB::update("update account_stmts set recon_status = '60_non_fa_credit' where id = ?", [$stmt_txn->id]);
            $this->report_recon_nonmatch("60_non_fa_credit", $stmt_txn, null, null);
        }
    }

    private function get_cust_id_by_acc_number($acc_number, $alt_acc_num = false){

        $cust_ids = [];

        $acc_repo = new AccountRepositorySQL();

        if(!$alt_acc_num){
            $customers = $acc_repo->get_records_by_many(['acc_number','status'], [$acc_number, Consts::ENABLED], ['cust_id']);
        }else{
            $customers = $acc_repo->get_records_by_many(['alt_acc_num','status'], [$acc_number, Consts::ENABLED], ['cust_id']);
        }

        foreach($customers as $cust){
            array_push($cust_ids, $cust->cust_id);
        }
        $cust_ids = array_unique($cust_ids);

        return $cust_ids;
    }

    private function process_mtn_stmt_txn($stmt_txn, $acc_prvdr_code){

        $descr = $stmt_txn->descr;
        $msisdn = $stmt_txn->ref_account_num;

        if(str_contains($descr, '/')){

            $descr_array = explode('/',$descr);

            if((!Str::startsWith($descr_array[1],'FLOW')) && ($descr_array[1] != 'SYSTEM')){

                $cust_ids = [];

                $descr_length = strlen($descr_array[0]);

                if(($acc_prvdr_code == Consts::MTN_AP_CODE) && (is_numeric($descr_array[0])) && ($descr_length == 6)){

                    $agent_id = $descr_array[0];
                    $cust_ids = $this->get_cust_id_by_acc_number($agent_id, false);
                    $matching_token = "a.acc_number = $agent_id";

                }else if(($acc_prvdr_code == Consts::RMTN_AP_CODE) && (is_numeric($descr_array[0])) && ($descr_length == 10 || $descr_length == 9 || $descr_length == 12)){

                    $mobile_num = $descr_array[0];
                    if($descr_length == 12 && (Str::startsWith($descr_array[0], "250"))){
                        $mobile_num = substr($descr_array[0], 3);
                    }elseif($descr_length == 10 && (Str::startsWith($descr_array[0], "0"))){
                        $mobile_num = substr($descr_array[0], 1);
                    }
                    $cust_ids = $this->get_cust_id_by_acc_number($mobile_num, false);
                    $matching_token = "a.acc_number = $mobile_num";
                }

                if((sizeof($cust_ids) == 0) && $msisdn != null){

                    $acc_number = substr($msisdn, 3);
                    $alt_acc_num = $acc_prvdr_code != Consts::RMTN_AP_CODE;
                    $cust_ids = $this->get_cust_id_by_acc_number($acc_number, $alt_acc_num);
                    $matching_token = "a.acc_number = $acc_number";
                }

                if(sizeof($cust_ids) == 0){

                    $cust_name = $descr_array[1];
                    $cust_array = explode(' ', $cust_name);
                    if (count($cust_array) >= 3) {
                        $cust_name = $cust_array[0] . " " . $cust_array[1];
                    }
                    $cust_ids = $this->get_cust_id($cust_name);
                    $matching_token = "b.biz_name or p.first_name + p.last_name = $cust_name";
                }

                $this->process_stmt_txn_by_cust_id($stmt_txn, $cust_ids, $matching_token);

            }else{
                (new AccountStmtRepositorySQL)->update_model(['recon_status' => '60_non_fa_credit', 'id' => $stmt_txn->id]);
                $this->report_recon_nonmatch("60_non_fa_credit", $stmt_txn, null, null);
            }
        }
    }

    private function process_atl_stmt_txn($stmt_txn){

        $descr = $stmt_txn->descr;
        $msisdn = $stmt_txn->ref_account_num;

        if(str_contains($descr, '/')){

            $descr_array = explode('/',$descr);

            if(is_numeric($descr_array[0])){

                $cust_ids = [];

                $descr_length = strlen($descr_array[0]);

                if($descr_length == 9){

                    $agent_id = $descr_array[0];
                    $cust_ids = $this->get_cust_id_by_acc_number($agent_id, false);
                    $matching_token = "a.acc_number = $agent_id";

                }

                if(sizeof($cust_ids) == 0){

                    $cust_name = $descr_array[1];
                    $cust_array = explode(' ', $cust_name);
                    if (count($cust_array) >= 3) {
                        $cust_name = $cust_array[0] . " " . $cust_array[1];
                    }
                    $cust_ids = $this->get_cust_id($cust_name);
                    $matching_token = "b.biz_name or p.first_name + p.last_name = $cust_name";
                }

                $this->process_stmt_txn_by_cust_id($stmt_txn, $cust_ids, $matching_token);

            }else{
                (new AccountStmtRepositorySQL)->update_model(['recon_status' => '60_non_fa_credit', 'id' => $stmt_txn->id]);
                $this->report_recon_nonmatch("60_non_fa_credit", $stmt_txn, null, null);
            }
        }
    }

    private function has_investment($remarks){

        $remarks = strtolower($remarks);

        if(str_contains($remarks, "investment")){
            return true;
        }else{
            return false;
        }
    }

    private function get_mob_num($value){

        $mobile_num = null;

        if(is_numeric($value)){
            if((strlen($value) == 10) && (Str::startsWith($value, "0"))){
                $mobile_num = substr($value, 1);
            }else if(strlen($value) == 9 ){
                $mobile_num = $value;
            }
        }
        return $mobile_num;
    }


    public function send_rm_feedback_notification(){

        $customers = DB::select("select person_id from app_users where role_codes = 'customer' and status = 'enabled' and country_code = ?",[session('country_code')]);
        foreach ($customers as $customer){
            $borrower_data = (new BorrowerRepositorySQL())->get_borrower_by('owner_person_id', $customer->person_id, ['flow_rel_mgr_id', 'rm_feedback_due', 'prob_fas', 'status']);
            if ($borrower_data->rm_feedback_due && $borrower_data->prob_fas == 0 && $borrower_data->status == 'enabled'){
                $rm_name = (new PersonRepositorySQL())->full_name($borrower_data->flow_rel_mgr_id);
                $data['notify_type'] = 'flash_message';
                $data['message'] = "We request you to rate your experience with FLOW Relationship Manager $rm_name";
                $data['title'] = "Rate RM";
                $label1 = ['label' => "I'll do later", 'type' => 'link', 'action' => null];
                $label2 = ['label' => 'Rate RM', 'type' => 'button', 'action' => 'FeedbackScreen'];
                $data['buttons'] = json_encode([$label1, $label2]);
                (new CustAppService())->send_fire_base_notify($customer->person_id, $data);

            }
        }

    }

    public function process_bok_stmt_txn($stmt_txn){

        $descr = $stmt_txn->descr;

        // $descr = "Agent Cash Deposit / de64bf9fccaa H E SOLUTION LTD KWISH KWISHYURA 0788399591";
        // $descr = "Agent Cash Deposit / adbc658a896c RAPIDE IWACU CONTRACTO";
        // $descr = "IB Account transfer / Float investment into the loan book";

        $descr_array = explode(' / ',$descr);

        if(!$this->has_investment($descr_array[1])){

            $reference_id = explode(' ', $descr_array[1])[0];

            $full_desc = ltrim($descr_array[1], $reference_id." ");

            $desc_items = explode(' ', $full_desc);

            foreach($desc_items as $index => $value){

                $mobile_num = $this->get_mob_num($value);
                if($mobile_num){
                    break;
                }
            }

            if($mobile_num){
                $cust_ids = (new BorrowerRepositorySQL)->get_cust_id_from_mobile_num($mobile_num, true);
                $cust_ids = is_array($cust_ids) ? $cust_ids : array($cust_ids);
                m_array_filter($cust_ids);
                $matching_token = "a.acc_number = $mobile_num";
            }else{
                $cust_name = $descr;
                if(count($desc_items) >= 3){
                    $cust_name = $desc_items[0]." ".$desc_items[1];
                }
                $cust_ids = $this->get_cust_id($cust_name);
                $matching_token = "b.biz_name or p.first_name + p.last_name = $cust_name";
            }
            $this->process_stmt_txn_by_cust_id($stmt_txn, $cust_ids, $matching_token);

        }else{
            (new AccountStmtRepositorySQL)->update_model(['recon_status' => '60_non_fa_credit', 'id' => $stmt_txn->id]);
            $this->report_recon_nonmatch("60_non_fa_credit", $stmt_txn, null, null);
        }
    }

    private function process_stmt_txn_by_cust_id($stmt_txn, $cust_ids, $matching_token = null){
        if(sizeof($cust_ids) == 1){
            Log::warning(array($stmt_txn));
            $this->loan_repo = new LoanRepositorySQL();
            $loans = $this->loan_repo->get_os_loan_doc_id_by('cust_id', $cust_ids[0], $stmt_txn->stmt_txn_date, 'float_advance');
            $this->process_stmt_txn_by_loan_doc_id($stmt_txn, $loans, $matching_token, $cust_ids);
        } else if (sizeof($cust_ids) == 0) {
            #use $matching_token as recon_desc
            DB::update("update account_stmts set recon_status = '20_no_match_customers' , recon_desc = ?, updated_at = ? where id = ?", [$matching_token, Carbon::now(), $stmt_txn->id]);
            $this->report_recon_nonmatch("20_non_match_customers", $stmt_txn, $matching_token, $cust_ids);
        } else {

            $cust_id = implode(', ', $cust_ids);
            #$cust_id =	"$cust_ids[0],$cust_ids[1]";

            DB::update("update account_stmts set recon_status = '40_multi_match_customers', recon_desc = ?, updated_at = ? where id = ?", [$cust_id, Carbon::now(), $stmt_txn->id]);

            $this->report_recon_nonmatch("40_multi_match_customers", $stmt_txn, $matching_token, $cust_ids);
        }
    }


    public function is_valid_repayment_account($loan, $account_id)
    {

        $account_repo = new AccountRepositorySQL();

        $cust_acc_prvdr_code = $loan->acc_prvdr_code;

        if($cust_acc_prvdr_code == Consts::RMTN_AP_CODE && $account_id = 4182){
            return true;
        }

        if($account_id == 1783){
            return true;
        }
            
        $ref_accounts = $account_repo->get_accounts_by(['network_prvdr_code','to_recon','status'],[$cust_acc_prvdr_code,true,Consts::ENABLED], ['id','acc_number','acc_prvdr_code'], true);

        foreach ($ref_accounts as $ref_account) {
            if ($ref_account->id == $account_id) {
                return true;
            }
        }

        return false;
    }

    public function process_stmt_txn_by_loan_doc_id($stmt_txn, $loans, $matching_token = null, $cust_ids = null){

        if(sizeof($loans) == 1){
            $pending_dup_disb_amt = (new LoanService)->get_disb_amt_to_reverse($loans[0]->loan_doc_id);
            if($pending_dup_disb_amt > 0 && $stmt_txn->cr_amt <= $pending_dup_disb_amt){
                DB::update("update account_stmts set recon_status = '11_capture_disb_rvrsl_pending', loan_doc_id = ?, cust_id = ?, updated_at = ? where id = ?" , [$loans[0]->loan_doc_id, $cust_ids[0], Carbon::now(), $stmt_txn->id]);
            }
            else if($this->is_valid_repayment_account($loans[0], $stmt_txn->account_id)){
                DB::update("update account_stmts set recon_status = '10_capture_payment_pending', loan_doc_id = ?, cust_id = ?, updated_at = ? where id = ?" , [$loans[0]->loan_doc_id, $cust_ids[0], Carbon::now(), $stmt_txn->id]);
            }else{
                DB::update("update account_stmts set recon_status = '31_paid_to_different_acc', recon_desc = ?,  loan_doc_id = ?, cust_id = ?, updated_at = ? where id = ?" , [$matching_token, $loans[0]->loan_doc_id, $cust_ids[0],  Carbon::now(), $stmt_txn->id]);
                $this->report_recon_nonmatch("31_paid_to_different_acc", $stmt_txn, $matching_token, $loans);
            }
            //$this->capture_payment($loan_doc_ids[0], $stmt_txn, $account_to_recon);
        } else if (sizeof($loans) == 0) {

            DB::update("update account_stmts set recon_status = '30_no_matching_fa', recon_desc = ?, updated_at = ? where id = ?" , [$matching_token, Carbon::now(), $stmt_txn->id]);
            $this->report_recon_nonmatch("30_no_matching_fa" , $stmt_txn, $cust_ids, $matching_token, $loans);

        }else{
            $loan_doc_id = implode(', ', $loans->loan_doc_id);
            #$loan_doc_id =	"$loan_doc_ids[0],$loan_doc_ids[1]";
            DB::update("update account_stmts set recon_status = '50_multiple_fas_matched', recon_desc = ?, updated_at = ? where id = ?" , [$loan_doc_id, Carbon::now(), $stmt_txn->id]);
            $this->report_recon_nonmatch("50_multiple_fas_matched" , $stmt_txn, $cust_ids, $matching_token, $loans );
        }
    }
    public function get_cust_id($cust_name)
    {

        $person_ids = collect(DB::select("select id from persons where 
					(? like CONCAT(first_name, '%')  and ? like CONCAT('%', last_name)) 
					OR 
					(? like CONCAT(last_name, '%')  and ? like CONCAT('%', first_name)) and country_code = ?", [$cust_name, $cust_name,$cust_name,$cust_name, session('country_code')	]))->pluck('id'); #Case
        // echo $person_ids;
        $customers = null;
        if (sizeof($person_ids) >= 1) {

            /*$customers = collect(DB::select("select cust_id from borrowers where contact_person_id  in (?) or owner_person_id in (?)", [$person_ids, $person_ids]))->pluck('cust_id'); #Case sensitiveness
            */
            $customers = DB::table('borrowers')->whereIn('owner_person_id', $person_ids)->where('profile_status','open')->get()->pluck('cust_id')->toArray();
        }else if(sizeof($person_ids) == 0){

            $customers = collect(DB::select("select cust_id from borrowers where profile_status = 'open' and country_code = ? and biz_name like ? or ? like CONCAT('%',CONCAT(biz_name, '%'))", [session('country_code'),"%{$cust_name}%", $cust_name]))->pluck('cust_id')->toArray(); #Case sensitiveness

        }

        return $customers;
    }


    private function report_recon_nonmatch($nonmatch_type, $stmt_txn, $cust_name, $cust_ids, $loan_doc_ids = null)
    {
        Log::warning("Nonmatch {$nonmatch_type} for {$stmt_txn->stmt_txn_id} id - {$stmt_txn->id}");
    }

    public function run_stmt_import_scripts($is_exempt_account)
    {

        $recon_accounts = (new AccountRepositorySQL)->get_recon_accounts(['id', 'acc_prvdr_code', 'country_code', 'web_cred', 'acc_number', 'disb_int_type', 'stmt_int_type', 'acc_purpose', 'network_prvdr_code']);

        foreach ($recon_accounts as $account_to_recon) {
            $acc_prvdr_code = $account_to_recon->acc_prvdr_code;
            if($is_exempt_account && in_array($account_to_recon->acc_number, config('app.stmt_imp_scheduler_accs'))){
                continue;
            }else if(!$is_exempt_account && !in_array($account_to_recon->acc_number, config('app.stmt_imp_scheduler_accs'))){
                continue;
            }

            if ($account_to_recon->stmt_int_type == 'web') {

                if (is_single_session_ap($acc_prvdr_code)) {
                    $queue = get_stmt_import_queue($account_to_recon);
                    StatementImportJob::dispatch($account_to_recon)->onQueue($queue);
                } else {
                    StatementImportJob::dispatchSync($account_to_recon);
                }
            }
        }
    }


    public function notify_acc_balance_above_threshold(){

        $account_records = (new AccountRepositorySQL)->get_recon_accounts(['country_code', 'balance', 'upper_limit', 'acc_prvdr_code', 'acc_number']);

        foreach($account_records as $account_record)
        {
            $balance = $account_record->balance;
            $upper_limit = $account_record->upper_limit;

            if($balance > $upper_limit && $upper_limit != null)
            {
                $currency_code = (new CommonRepositorySQL())->get_currency()->currency_code;

                $account_record->current_datetime = Carbon::now();
                $account_record->currency_code = $currency_code;
                $account = (array)$account_record;

                $ops_admin_email = get_ops_admin_email($account['country_code']);
                $market_admin_email = get_market_admin_email($account['country_code']);

                send_email('notify_balance_above_threshold', [get_l3_email(), $ops_admin_email, $market_admin_email], $account);

            }
        }
    }

    public function capture_tf_repayment_transactions(){

        $data = [];

        $loan_repo = new LoanRepositorySQL();
        $account_repo = new AccountRepositorySQL();

        $file_name = 'UEZM_tf_loan_repayment_report';

        $data['username'] = config('app.UEZM_TF_CRED.LOAN_REPORT_CRED.username');
        $data['password'] = config('app.UEZM_TF_CRED.LOAN_REPORT_CRED.password');

        $to_account = $account_repo->get_account_by(['lender_code', 'acc_purpose', 'network_prvdr_code', 'status'], ['UFLW', 'tf_repayment', Consts::EZEEMONEY_AP_CODE, Consts::ENABLED], ['id']);

        $addl_condn = "and loan_purpose = 'terminal_financing'";
        $tf_loans = $loan_repo->get_records_by_in('status', [Consts::LOAN_ONGOING, Consts::LOAN_DUE, Consts::LOAN_OVERDUE], ['acc_number', 'cust_acc_id', 'loan_doc_id', 'cust_id', 'disbursal_date'], null, $addl_condn);

        foreach ($tf_loans as $tf_loan) {

            try {
                $data['acc_number'] = $tf_loan->acc_number;
                $data['start_date'] = date('d-m-Y', strtotime($tf_loan->disbursal_date)); #'05-05-2022';

                $data_json = json_encode($data);

                $import_status = run_python_script("UEZM/{$file_name}", $data_json);

                if ($import_status['status'] == 'success') {

                    $acc_number = $data['acc_number'];
                    $current_date = date_db();

                    $repay_transactions = DB::select("SELECT stmt_txn_date, cr_amt from tf_repay_txn_imports where from_acc_num = ? and date(created_at) = ?", [$acc_number, $current_date]);

                    if ($repay_transactions) {

                        foreach ($repay_transactions as $repay_transaction) {

                            $txn_id = $acc_number . "-" . (strtotime($repay_transaction->stmt_txn_date));

                            $tf_loan->amount = $repay_transaction->cr_amt;
                            $tf_loan->from_ac_id = $tf_loan->cust_acc_id;
                            $tf_loan->to_ac_id = $to_account->id;
                            $tf_loan->txn_mode = 'daily_deduction';
                            $tf_loan->txn_id = $txn_id;
                            $tf_loan->txn_date = $repay_transaction->stmt_txn_date;
                            $tf_loan->is_part_payment = true;
                            $tf_loan->send_sms = false;

                            $loan_serv = new RepaymentService();

                            $loan_txns = $loan_serv->capture_repayment((array)$tf_loan);
                        }
                    }
                } else {
                    $import_status['failed_at'] = Carbon::now();
                    $import_status['cust_id'] = $tf_loan->cust_id;
                    $import_status['loan_doc_id'] = $tf_loan->loan_doc_id;
                    Mail::to(config('app.app_support_email'))->send(new FlowCustomMail('tf_transaction_import_failure', $import_status));
                }
            } catch (\Exception $e) {
                $data['failed_at'] = Carbon::now();
                $data['exception'] = ($e->getMessage());
                $data['cust_id'] = $tf_loan->cust_id;
                $data['loan_doc_id'] = $tf_loan->loan_doc_id;
                Mail::to(config('app.app_support_email'))->send(new FlowCustomMail('capture_tf_repayment_failure', $data));
            }
        }
    }
    public function notify_stalled_disbursals()
    {
        $acc_providers = DB::select("select name, acc_prvdr_code from acc_providers where status = 'enabled' and biz_account = 1 and country_code = ?",[session('country_code')]);
        foreach ($acc_providers as $acc_provider) {
            $count = DB::selectOne("select count(id) count from (select id, status, disbursal_status, customer_consent_rcvd from loans where country_code = ? and acc_prvdr_code = ? order by id desc limit ?)p where status in ('hold', 'pending_disbursal') and disbursal_status in ('unknown', 'failed') and customer_consent_rcvd is true", [session("country_code"), $acc_provider->acc_prvdr_code, config('app.stalled_disbursals_threshold')])->count;
            $notification = "Disbursals on {$acc_provider->acc_prvdr_code} are failing continuously.";
            if (($count >= config('app.stalled_disbursals_threshold'))) {
                send_event_notification(['type' => 'disbursal_stalled_notification', 'entity' => $acc_provider->name, 'interval' => config('app.stalled_disbursals_notify_interval'), 'entity_id' => $acc_provider->acc_prvdr_code, 'group_id' => config('app.whatsapp_group_codes'), 'notification' => $notification, 'channel' => 'whatsapp']);
            }
        }

    }
    public function notify_failed_stmt_imports()
    {
        $accounts = DB::select("select distinct account_id, acc_prvdr_code from float_acc_stmt_imports where country_code = ? and status = 'enabled' and to_recon = 1",[session('country_code')]);
        foreach ($accounts as $account) {
            $count = DB::selectOne("select count(status) count from (select status, country_code from float_acc_stmt_imports where account_id = ? order by id desc limit ?) p where status in ('failed', 'timed_out') and country_code = ?", [$account->account_id, config('app.statement_import_fails_threshold'), session("country_code")])->count;
            $notification = "Statement imports for {$account->account_id} - {$account->acc_prvdr_code} are failing continuously.";
            if (($count == config('app.statement_import_fails_threshold'))) {
                send_event_notification(['type' => 'statement_import_failed', 'entity' => 'accounts', 'interval' => config('app.statement_import_fails_notify_interval'), 'entity_id' => $account->account_id, 'group_id' => config('app.whatsapp_group_codes'), 'notification' => $notification, 'channel' => 'whatsapp']);
            }
        }
    }

    public function send_unknown_txn_email()
    {
        $yesterday = Carbon::now()->yesterday()->toDateString();
        $account_ids = DB::select("select distinct account_id from account_stmts where country_code = ?", [session('country_code')]);
        $unknown_txns = array();
        foreach ($account_ids as $account_id) {
            $acc_number = (new AccountRepositorySQL)->get_acc_num($account_id->account_id);
            $details = DB::select("select network_prvdr_code, acc_prvdr_code, dr_amt, cr_amt from account_stmts where date(stmt_txn_date) = '$yesterday' and recon_status!='80_recon_done' and country_code=? and account_id=?", [session("country_code"), $account_id->account_id]);
            if ($details) {
                $total_amount_credited = 0;
                $no_of_credit_txns = 0;
                $total_amount_debited = 0;
                $no_of_debit_txns = 0;
                foreach ($details as $detail) {
                    if ($detail->cr_amt) {
                        $total_amount_credited += $detail->cr_amt;
                        $no_of_credit_txns += 1;
                    }
                    if ($detail->dr_amt) {
                        $total_amount_debited += $detail->dr_amt;
                        $no_of_debit_txns += 1;
                    }
                }
                $unknown_txns[] = ['network_prvdr' => $details[0]->network_prvdr_code, 'acc_prvdr' => $details[0]->acc_prvdr_code, 'acc_number' => $acc_number, 'no_of_credit_txns' => $no_of_credit_txns, 'total_amt_credited' => number_format($total_amount_credited), 'no_of_debit_txns' => $no_of_debit_txns, 'total_amt_debited' => number_format($total_amount_debited)];
            }
        }
        if ($unknown_txns) {
            $mail_content['unknown_txns'] = $unknown_txns;
            $mail_content['yesterday'] = $yesterday;
            $mail_content['country_code'] = session('country_code');
            Mail::to([get_ops_admin_email(), get_market_admin_email(), get_l3_email(), config('app.app_support_email'), get_csm_email()])->queue((new FlowCustomMail('unknown_txn_email', $mail_content))->onQueue('emails'));
        }
    }

    public function run_internal_integrity_checks(){
        $year = Carbon::now()->year;
        $this->run_disbursal_txn_integrity_check($year);
        $this->run_payment_txn_integrity_check($year);
    }


    public function run_disbursal_txn_integrity_check($year){
        $disb_mismatches = DB::select("select l.loan_doc_id, loan_principal l_amt, sum(amount) t_amt, disbursal_date, paid_date
										  from loans l, loan_txns t  where 
										  l.loan_doc_id = t.loan_doc_id and txn_type = 'disbursal' 
										  and year(disbursal_date) = ?
										  and l.country_code = ?
										  group by l.loan_doc_id having l_amt != t_amt", [$year, session('country_code')]);

        $mail_data = ['mismatch_type' => "disbursal", "loans" => $disb_mismatches, 'country_code' => session('country_code')];
        if(sizeof($disb_mismatches) > 0){
            send_email("internal_integrity_warning", [config('app.app_support_email'), config('app.level3_support_email')], $mail_data);
        }
    }


    public function run_payment_txn_integrity_check($year){
        $mismatches = DB::select("select l.loan_doc_id, paid_principal + paid_fee + penalty_collected + paid_excess as l_amt, 
										sum(amount) t_amt, disbursal_date, paid_date 
										from  loans l, loan_txns t  where 
										l.loan_doc_id = t.loan_doc_id and 
										txn_type = 'payment' and 
										l.status = 'settled' and year(paid_date) = ?
										and l.country_code = ?
										group by l.loan_doc_id having l_amt != t_amt", [$year, session('country_code')]);

        $mail_data = ['mismatch_type' => "payment", "loans" => $mismatches, 'country_code' => session('country_code')];
        if(sizeof($mismatches) > 0){
            send_email("internal_integrity_warning", [config('app.app_support_email'), config('app.level3_support_email')], $mail_data);
        }
    }

    public function download_all_acc_stmts(){

        $records = (new AccountRepositorySQL)->get_recon_accounts(['acc_prvdr_code', 'acc_number', 'id', 'web_cred', 'country_code']);
        foreach($records as $record){

            if($record->acc_prvdr_code == Consts::CHAP_CHAP_AP_CODE) {
                // (new ChapChapService)->req_cust_statement(['acc_number' => $record->acc_number]);
                continue;

            }elseif($record->acc_prvdr_code == Consts::MTN_AP_CODE){
                $umtn_n_iterations = config('app.umtn_n_iterations');
                if(array_key_exists($record->id, $umtn_n_iterations)){
                    $no_of_interations = $umtn_n_iterations[$record->id];
                    $split_dates = $this->split_days_of_month($no_of_interations);
                    foreach($split_dates as $split_date){
                        $record->umtn_start_day = $split_date['start_day'];
                        $record->umtn_end_day = $split_date['end_day'];
                        $this->stmts_upload_script($record, $record->umtn_start_day." 00:00", $record->umtn_end_day." 00:00");
                    }
                }else{
                    $this->stmts_upload_script($record);
                }

            }else{
                $this->stmts_upload_script($record);
            }
        }

    }

    private function split_days_of_month($no_of_interations){

        $first_day_of_current_month = Carbon::now()->startOfMonth()->format("Y-m-d");
        $first_day_of_prev_month = Carbon::now()->startOfMonth()->subMonth()->format("Y-m-d");

        // No of dates between the start and end month
        $no_of_dates_exists = CarbonPeriod::create($first_day_of_prev_month, $first_day_of_current_month);
        foreach ($no_of_dates_exists as $date) {
            $tot_dates[] = $date->toDateString('Y-m-d');
        }
        $days_slot = round(sizeof($tot_dates) / $no_of_interations);

        // Generate start and end dates based on the days slots in the month
        $slot_dates = CarbonPeriod::create($first_day_of_prev_month, $days_slot. 'days', $first_day_of_current_month); // The days should be number space days(1 days)
        foreach ($slot_dates as $slot_date) {
            $all_slot_dates[] = $slot_date->toDateString('Y-m-d');
        }

        foreach($all_slot_dates as $key => $date){
            if( $key == array_key_last($all_slot_dates) and ($first_day_of_current_month != $all_slot_dates[$key])){
                $end_day = $first_day_of_current_month;
            }else{
                $end_day = Carbon::parse(implode(array_slice($all_slot_dates, $key+1, 1)))->subDay()->toDateString('Y-m-d');
            }
            $split_dates[] = [
                'start_day' => implode(array_slice($all_slot_dates, $key, 1)),
                'end_day' => $end_day
            ];
        }
        return $split_dates;
    }

    private function stmts_upload_script($record, $umtn_start_day = null, $umtn_end_day = null){

        $google_service = (new GoogleService);
        $web_cred = json_decode($record->web_cred);

        if(isset($web_cred->password_stmt)){
            $web_cred->password = $web_cred->password_stmt;
            unset($web_cred->password_stmt);
        }
        unset($web_cred->timeout, $web_cred->staff_id, $web_cred->access_no, $web_cred->password_disb);
        $web_cred->acc_number = $record->acc_number;
        $web_cred->acc_prvdr_code = $record->acc_prvdr_code;
        $web_cred->country_code = $record->country_code;
        $web_cred->start_day = $umtn_start_day;
        $web_cred->end_day = $umtn_end_day;
        $web_cred->storage_path = env('FLOW_STORAGE_PATH')."/".$record->acc_prvdr_code;
        $web_cred = json_encode($web_cred);
        $script_result = run_python_script('vendors/stmts/monthly_stmts', $web_cred);
        $status = $script_result['status'];
        $message = $script_result['message'];
        if($status == 'success' and isset($script_result['file_path'])){

            $file_upload_response = $google_service->google_drive_file_upload($script_result['file_path'], env("APP_SUPPORT_STMTS_FOLDER"), $record->acc_prvdr_code, $record->acc_number);
            if(isset($file_upload_response['file_id'])){
                $gdrive_folder_path = "https://drive.google.com/drive/folders/".$file_upload_response['folder_id'];
                $this->send_stmts_upload_email($record, $status, $message, $file_upload_response, $gdrive_folder_path);

            }else{
                $this->send_stmts_upload_email($record, $status, $message, $file_upload_response);
            }
        }else{
            $this->send_stmts_upload_email($record, $status, $message);
        }
    }

    private function send_stmts_upload_email($record, $status, $message = null, $file_upload_response = null, $gdrive_folder_path = null){

        $file_data = [];
        $start_end_dates = null;
        $month_year = Carbon::now()->format('M-Y');

        if(isset($record->umtn_start_day)){
            $start_end_dates = "(".Carbon::parse($record->umtn_start_day)->format('d-M-Y')." to ".Carbon::parse($record->umtn_end_day)->format('d-M-Y').")";
        }
        $data = [
            "country_code" => $record->country_code, "status" => $status, "acc_prvdr_code" => $record->acc_prvdr_code,
            "acc_number" => $record->acc_number, "month_year" => $month_year, "umtn_start_end_dates" => $start_end_dates
        ];
        if($status == 'success'){
            if(isset($file_upload_response['file_id'])){
                $file_data = ["gdrive_folder_path" => $gdrive_folder_path, "file_id" => $file_upload_response['file_id']];
                $recipients = [get_ops_admin_csm_email(), get_l3_email(), config('app.app_support_email')];
                // $recipients = [config('app.app_support_email')];
            }else{
                $file_data = [
                    "file_id" => $file_upload_response['file_id'], "exception" => $file_upload_response['exception'],
                    "trace" => $file_upload_response['trace']
                ];
                $recipients = [config('app.app_support_email'), get_csm_email()];
            }
        }else{
            $file_data = ["message" => $message];
            $recipients = [config('app.app_support_email'), get_csm_email()];

        }
        $email_data = array_merge($data, $file_data);
        send_email('acc_stmts_upload_email', $recipients, $email_data);

    }

    public function send_email_delayed_transaction($import_id){

        $acc_stmts_repo = new AccountStmtRepositorySQL();
        $stmt_import = new StatementImport();
        $records = $acc_stmts_repo->get_records_by('import_id', $import_id, ['stmt_txn_date', 'acc_number', 'import_id', 'created_at', 'acc_prvdr_code', 'stmt_txn_id']);
        $import_data = $stmt_import->get_record_by('id', $import_id, ['start_time', 'end_time', 'acc_prvdr_code', 'account_id']);
        $results = [];
        foreach($records as $record){

            if(in_array($record->acc_prvdr_code, config('app.ignored_acc_prvdr_for_stmt_time_diff'))) break;
            $time_diff = (Carbon::parse($record->created_at))->diffInMinutes(Carbon::parse($record->stmt_txn_date));
            if($time_diff >= config('app.txn_delay_cut_off_time')){
                $time_variants[] = $time_diff;
                $record->stmt_txn_date = Carbon::parse($record->stmt_txn_date)->toTimeString();
                $record->created_at = Carbon::parse($record->created_at)->toTimeString();
                unset($record->id);
                $results[] = $record;
            }
        }
        if(!empty($results)){
            $acc_number = (new AccountRepositorySQL)->get_account_by(['id'], [$import_data->account_id], ['acc_number']);
            $txn_count = sizeof($results);
            $avg_txn_delay = array_sum($time_variants)/$txn_count;
            $email_data = [
                "delayed_records" => $results, "no_of_txns" => $txn_count, "acc_prvdr_code"=> $import_data->acc_prvdr_code,
                "date" => Carbon::now()->format('d-M-Y'),"avg_txn_delay" => round($avg_txn_delay), "import_id" => $import_id,
                "country_code" => session('country_code'), "start_time" => Carbon::parse($import_data->start_time)->toTimeString(),
                "acc_number" => $acc_number->acc_number, "end_time" => Carbon::parse($import_data->end_time)->toTimeString()
            ];
            $recipients = [get_l3_email(), config('app.app_support_email')];
            send_email('acc_stmts_delay_txn_email', $recipients, $email_data);
        }
    }
    public function get_sms_vendor_balance(){

        $sms_vendors = get_sms_vendors(['credentials', 'vendor_code', 'country_code']);

        foreach($sms_vendors as $sms_vendor){

            $sms_vendor->username = $sms_vendor->credentials->username;
            $sms_vendor->password = (isset($sms_vendor->credentials->password) ? $sms_vendor->credentials->password : null);
            $sms_vendor->api_key = (isset($sms_vendor->credentials->api_key) ? $sms_vendor->credentials->api_key : null);

            $data = json_encode($sms_vendor);

            if($sms_vendor->vendor_code == "USIS"){

                $resp = run_python_script("vendors/sms/{$sms_vendor->vendor_code}", $data);

                if($resp['status'] == 'success'){

                    $balance = str_replace(',', '', $resp['balance']);
                    (new Vendor)->update_model(["balance" => (double)$balance, "id" => $sms_vendor->id]);
                }

            }elseif($sms_vendor->vendor_code == "UAIT"){

                $resp = (new AitSMSService)->get_balance($sms_vendor->country_code);

                if($resp['status'] == 'success'){

                    $balance = substr($resp['balance'], '4');
                    (new Vendor)->update_model(["balance" => (double)$balance, "id" => $sms_vendor->id]);
                }

            }
        }
    }
}
