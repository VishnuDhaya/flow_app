<?php

namespace App\Services;

use App\Consts;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\LoanApplicationRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Config;
use Log;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Confidence;


class OperationDashboardService{

    public function get_operation_dashboard_data($data){

        $country_code = $data['country_code'];
        $start_date = $data['start_date'] ?? Carbon::today();
        $end_date = $data['end_date'] ?? Carbon::today();
        $criterias = isset($data['criteria']) ? is_array($data['criteria']) ? $data['criteria'] : [$data['criteria']] : Config('app.default_operations_dashboard_load');
        $acc_prvdr_code = session('acc_prvdr_code');
        $addl_data = $data['addl_data'] ?? [];
        $addl_sql = "country_code = '{$country_code}'";
        if($acc_prvdr_code) {
            $addl_sql .= " and acc_prvdr_code = '{$acc_prvdr_code}'";
        }
        $history = $data['history'] ?? false;
        $records = null;
        foreach ($criterias as $criteria) {
            if(!$history){
                if ($criteria == 'fas_pending'){
                    $records['fas_pending'] = $this->fas_pending_count_with_criteria($country_code, $addl_sql);
                }elseif ($criteria == 'active_customer_info'){
                    $records['active_customer_info'] = $this->get_active_cust_count_info($country_code, $addl_sql);
                }elseif ($criteria == 'acquisition_target'){
                    $records['acquisition_target'] = $this->get_aqusition_data($country_code, $addl_sql);
                }elseif ($criteria == 'fas_due'){
                    $records['fas_due'] = $this->fas_due($country_code, $addl_sql);
                }elseif ($criteria == 'lead_pending'){
                    $records['lead_pending'] = $this->lead_pending($country_code,$acc_prvdr_code,$addl_sql);
                }elseif ($criteria == 'get_aggr_due_count'){
                    $records['get_aggr_due_count'] = $this->get_aggr_due_count($addl_sql);
                }elseif ($criteria == 'get_fa_requst_count'){
                    $records['get_fa_requst_count'] = $this->get_fa_requst_count($addl_sql);
                }elseif ($criteria == 'get_complaints_count'){
                    $records['get_complaints_count'] = $this->get_complaints_count($country_code, $acc_prvdr_code);
                }elseif ($criteria == 'repayment_metrics'){
                    $records['repayment_metrics'] = $this->get_repayment_metric($start_date,$end_date, $addl_sql);
                }elseif ($criteria == 'account_balance') {
                    $records['account_balance'] = $this->account_balance($country_code);
                }
            }elseif ($history){
                if ($criteria == 'fa_disbursements') {
                    $records['fa_disbursements']['total_fas'] = $this->get_total_fas_count($start_date, $end_date, $addl_sql);
                    $records['fa_disbursements']['disb_attempt_count'] = $this->get_total_attempts_count($start_date, $end_date, $country_code, $acc_prvdr_code);
                }elseif ($criteria == 'fa_repayments') {
                    $records['fa_repayments'] = $this->get_fa_repayments_count($start_date, $end_date, $country_code, $acc_prvdr_code);
                }elseif ($criteria == 'fa_applications') {
                    $records['fa_applications'] = $this->get_fa_applications_count($start_date, $end_date, $addl_sql);
                }elseif ($criteria == 'leads') {
                    $records['leads'] = $this->get_leads_count($start_date, $end_date, $addl_sql);
                }elseif ($criteria == 'accounts') {
                    $records['accounts'] = $this->get_account_balance();
                }elseif ($criteria == 'sms_vendors') {
                    $records['sms_vendors'] = $this->get_sms_vendor_balance();
                }elseif ($criteria == 'manual_capture_nid') {
                    $records['rm_metrics']['manually_captured_nid_count'] = $this->get_manually_captured_national_id_account($start_date, $end_date, $addl_sql);
                }elseif ($criteria == 'field_visits') {
                    $records['rm_metrics']['field_visits'] = $this->get_rm_checkin_checkout_count($start_date, $end_date, $country_code);
                }elseif ($criteria == 'fa_applied_by') {
                    $records['fa_applied_by_count'] = $this->get_fa_applied_by_count($start_date, $end_date, $addl_sql);
                }elseif ($criteria == 'statement_import'){
                    $records['statement_imports'] = $this->get_stmt_import_info($start_date, $end_date, $country_code, $acc_prvdr_code);
                }elseif($criteria == 'disb_dup_n_rtn_count'){
                    $records['disb_dup_n_rtn_count'] = $this->get_disbursal_return_and_duplicate_count($start_date, $end_date, $country_code, $acc_prvdr_code);
                }elseif($criteria == 'rm_checkin_checkout_count'){
                    $records['rm_checkin_checkout_count'] = $this->get_rm_checkin_checkout_count($start_date, $end_date, $addl_sql);
                }elseif($criteria == 'appl_approvals'){
                    $records['appl_approvals'] = $this->get_appl_approvals($start_date, $end_date, $addl_sql);
                }elseif($criteria == 'rm_visit_chart'){
                    $records['rm_visit_chart'] = $this->get_rm_visit_chart($start_date, $end_date, $acc_prvdr_code, $country_code);
                }elseif($criteria == 'disb_delay_reason'){
                    $records['disb_delay_reason'] = $this->get_disb_delay_reason($start_date, $end_date, $country_code,$acc_prvdr_code);
                }elseif($criteria == 'apply_to_disb_time_chart'){
                    $data = null;
                    if(isset($addl_data['apply_to_disb_time_chart'])){
                        $data = $addl_data['apply_to_disb_time_chart'];
                    }
                    $records['apply_to_disb_time_chart'] = $this->apply_to_disb_time_ranged_chart($start_date, $end_date, $country_code, $acc_prvdr_code, $data);
                }elseif($criteria == 'repay_to_settle_time_chart'){
                    $data = null;
                    if(isset($addl_data['repay_to_settle_time_chart'])){
                        $data = $addl_data['repay_to_settle_time_chart'];
                    }
                    $records['repay_to_settle_time_chart'] = $this->get_repay_to_settle_time_chart($start_date, $end_date, $country_code, $acc_prvdr_code, $data);
                }
            }
            if ($criteria == 'mobile_users') {
                $records['mobile_users'] = $this->get_cust_details_count($start_date, $end_date, $country_code, $acc_prvdr_code, $addl_sql, $history);
            }elseif ($criteria == 'penalty'){
                $records['penalty'] = $this->penalty($start_date, $end_date, $addl_sql, $history);
            }
        }

		return $records;
    }

    private function get_total_fas_count($start_date, $end_date, $addl_sql){

		$loans =  DB::selectOne("SELECT IFNULL(count(loan_doc_id),0) total_fas from loans where status not in ('voided', 'hold', 'pending_disbursal', 'pending_mnl_dsbrsl') and date(disbursal_date) >= '{$start_date}' and date(disbursal_date) <= '{$end_date}' and $addl_sql");

        return $loans->total_fas;

	}

	private function get_total_attempts_count($start_date, $end_date, $country_code, $acc_prvdr_code): array
    {
        $data = null;
		$disb_attempts =  DB::select("SELECT attempts, count(*) as loans from (select count(*) as attempts from disbursal_attempts d, loans l where d.country_code = '{$country_code}' and l.loan_doc_id = d.loan_doc_id and l.acc_prvdr_code = '{$acc_prvdr_code}' and date(d.created_at) >= '{$start_date}' and date(d.created_at) <= '{$end_date}' group by d.loan_doc_id) attempt group by attempts ORDER BY attempts");

		foreach($disb_attempts as $disb_attempt){

			$data[$disb_attempt->attempts] = $disb_attempt->loans;

		}

		return $data;
	}

	private function get_fa_repayments_count($start_date, $end_date, $country_code, $acc_prvdr_code){

        return DB::selectOne("SELECT count(IF(txn_mode = 'review_n_sync',1,null)) as auto_captured_count, count(IF(txn_mode = 'wallet_transfer', 1, null)) as manual_captured_count, count(IF(reason_for_skip is not null, 1, null)) as skipped_txn_id_count, count(IF(txn_type='penalty_waiver',1,null)) as penalty_waived_count, count(IF(txn_type='excess_reversal',1,null)) as excess_reversal_count from loans l, loan_txns t where t.loan_doc_id = l.loan_doc_id and l.acc_prvdr_code = '{$acc_prvdr_code}' and date(txn_date) >= '{$start_date}' and date(txn_date) <= '{$end_date}' and t.country_code = '{$country_code}'");
    }


	private function get_fa_applications_count($start_date, $end_date, $addl_sql){

        return DB::selectOne("SELECT count(*) as total_appls, count(IF(pre_appr_id is not null,1,null)) as pre_approved_count, count(IF(status='approved',1,null)) as manually_approved_count, count(IF(status='rejected',1,null)) as rejected_count, count(IF(status='voided',1,null)) as voided_count  from loan_applications where date(loan_appl_date) >= '{$start_date}' and date(loan_appl_date) <= '{$end_date}' and $addl_sql");
	}

	private function get_leads_count($start_date, $end_date, $addl_sql){

        return DB::selectOne("SELECT count(IF(profile_status='open',1,null)) as open_leads_count, count(IF(status='41_kyc_inprogress',1,null)) as kyc_progress_count, count(IF(status='60_customer_onboarded',1,null)) as onboarded_count from
		leads where date(lead_date) >= '{$start_date}' and date(lead_date) <= '{$end_date}' ");
	}

	private function get_account_balance(): array
    {

        return (new AccountRepositorySQL)->get_recon_accounts(['id', 'acc_number', 'acc_prvdr_code', 'network_prvdr_code', 'balance', 'acc_prvdr_name', 'holder_name', 'updated_at']);
	}

	private function get_sms_vendor_balance(): array
    {

        return get_sms_vendors(['vendor_code', 'vendor_name', 'balance', 'updated_at']);
	}

	private function get_manually_captured_national_id_account($start_date, $end_date, $addl_sql): array
    {

		$leads =  DB::selectOne("SELECT count(IF(JSON_EXTRACT(cust_reg_json, '$.allow_biz_owner_manual_id_capture')=true,1,null)) as allowed_manual_capture_id_count, count(IF(JSON_EXTRACT(cust_reg_json,'$.allow_tp_ac_owner_manual_id_capture')=true,1,null)) as allowed_manual_captured_tp_id_count from leads where $addl_sql");

		$rm_nid_count['primary_account'] = $leads->allowed_manual_capture_id_count;
		$rm_nid_count['tp_account'] = $leads->allowed_manual_captured_tp_id_count;

		return $rm_nid_count;
	}

	private function get_cust_details_count($start_date, $end_date, $country_code, $acc_prvdr_code, $addl_sql, $history): array
    {
        $adl_sql = $date_sql = $date_adl_1 = "";
        if($acc_prvdr_code){
            $adl_sql = " and acc_prvdr_code = '{$acc_prvdr_code}'";
        }
        if($history){
            if($start_date){
                $date_sql .= " and date(a.created_at) >= date('{$start_date}') ";
            }
            if($end_date){
                $date_sql .= " and date(a.created_at) <= date('{$end_date}') ";
                $date_adl_1 .= " and date(a.created_at) <= date('{$end_date}') ";

            }
        }
        $data['registered']['title'] = 'Installations';
        $data['active']['title'] = 'Active Installations';
        $data['fas_applied']['title'] = 'FAs Applied';
        $data['fas_repaid']['title'] = 'FAs Repaid';
        if(!$history) {
            $active_user_check_date = Carbon::today()->subDays(config('app.days_for_active_user'));
        }else{
            $active_user_check_date = Carbon::parse($start_date)->subDays(config('app.days_for_active_user'));
        }
		$data['registered']['value'] = DB::selectOne("SELECT count(IF(role_codes='customer',1,null)) as reg from app_users a,borrowers b where  is_new_user is false and b.country_code = '{$country_code}' and person_id = owner_person_id {$date_sql} {$adl_sql}")->reg;
        $data['registered']['total'] = DB::selectOne("select count(*) tot from borrowers where (tot_loans > 0 or status = 'enabled') and country_code = '{$country_code}' {$adl_sql} ")->tot;
		$data['active']['value'] = DB::selectOne("SELECT count(IF(role_codes='customer',1,null)) as reg from app_users a,borrowers b where date(a.updated_at) >= date('{$active_user_check_date}') and is_new_user is false and b.country_code = '{$country_code}' and person_id = owner_person_id {$date_adl_1} {$adl_sql} ")->reg;
        $data['active']['total'] = $data['registered']['value'];
        $data['fas_applied']['value'] = DB::selectOne("Select count(*) applied from loan_applications a where channel = 'cust_app' and status = 'approved'  {$date_sql} and {$addl_sql}")->applied;
        $data['fas_repaid']['value'] = DB::selectOne("select count(distinct a.loan_doc_id) repaid from payment_attempts pa, account_stmts a where pa.loan_doc_id = a.loan_doc_id and stmt_txn_type = 'credit' and timestampdiff(minute, pa.created_at, a.stmt_txn_date) < 2 and timestampdiff(minute, pa.created_at, a.stmt_txn_date) >= 0 and a.country_code = '{$country_code}' {$adl_sql} {$date_sql} order by a.loan_doc_id")->repaid;
		return $data;
	}

	private function get_rm_checkin_checkout_count($start_date, $end_date, $country_code)
    {
        $allowed_dist = config('app.max_dist_to_force_checkin');
        return DB::selectOne("SELECT  count(if((force_checkin is true and checkin_distance > $allowed_dist),1,null)) as force_checkin_out_count,
       count(if((force_checkin is true and checkin_distance <= $allowed_dist),1,null)) as force_checkin_in_count,
       count(if((force_checkout is true and checkout_distance > $allowed_dist),1,null)) as force_checkout_out_count,
       count(if((force_checkout is true and checkout_distance <= $allowed_dist),1,null)) as force_checkout_in_count,
       count(force_checkin is false) as regular_checkin_count, count(force_checkout is false) as regular_checkout_count,
       count(*) total
       from field_visits where date(sch_date) >= '{$start_date}' and date(sch_date) <= '{$end_date}' and country_code = '{$country_code}'");
	}

	private function get_fa_applied_by_count($start_date, $end_date, $addl_sql){

        return DB::selectOne("SELECT count(*) as total_count, count(IF(channel='web_app',1,null)) as cs_rpt_applied_count, count(IF(channel='cust_app',1,null)) as cust_app_applied_count,
		count(IF(channel is null,1,null)) as sms_applied_count, count(*) cs_new_applied_count from loan_applications where date(loan_appl_date) >= '{$start_date}' and date(loan_appl_date) <= '{$end_date}' and $addl_sql");
	}

    private  function  get_stmt_import_info($start_date, $end_date, $country_code, $acc_prvdr_code): array
    {
        $data['date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['country_code'] = $country_code;
        $data['acc_prvdr_code'] = $acc_prvdr_code;

        return (new CommonService())->list_stmt_imports($data);
    }

    private function fas_pending_count_with_criteria($country_code, $addl_sql): array
    {
        $data = array();
        $data['w_cust']['title'] = "With Customer";
        $data['w_prvdr']['title'] = "Disbursal";
        $data['w_rm']['title'] = "With RM";
        $now = Carbon::now();
        $data['cptr_disb']['title'] = "Capture Disbursal";
        $data['w_cust']['value'] = DB::selectOne("select count(*) w_cust from loans where status = 'pending_disbursal' and customer_consent_rcvd is false and $addl_sql")->w_cust;
        $data['w_cust']['time'] = DB::selectOne("select max(TIMESTAMPDIFF(MINUTE,created_at, '{$now}')) time from loans where status = 'pending_disbursal' and customer_consent_rcvd is false and $addl_sql")->time;
        $data['w_prvdr']['value'] = DB::selectOne("select count(*) w_prvdr from loans where status in ('hold', 'pending_disbursal') and customer_consent_rcvd is true and $addl_sql")->w_prvdr;
        $data['w_prvdr']['time'] = DB::selectOne("select max(TIMESTAMPDIFF(MINUTE,updated_at, '{$now}')) time from loans where status in ('hold', 'pending_disbursal') and customer_consent_rcvd is true and $addl_sql")->time;
        $data['w_rm']['value'] = DB::selectOne("select count(*) w_rm from loan_applications where status = 'pending_approval' and $addl_sql")->w_rm;
        $data['w_rm']['time'] = DB::selectOne("select max(TIMESTAMPDIFF(MINUTE,created_at, '{$now}')) time from loan_applications where status = 'pending_approval' and $addl_sql")->time;
        $data['cptr_disb']['value'] = DB::selectOne("select count(*) cptr_disb from loans where status  = 'pending_disb_capture' and $addl_sql")->cptr_disb;
        $data['cptr_disb']['time'] = DB::selectOne("select max(TIMESTAMPDIFF(MINUTE,updated_at, '{$now}')) time from loans where  status  = 'pending_disb_capture' and $addl_sql")->time;
        return $data;
    }

    private function  get_active_cust_count_info($country_code, $addl_sql){
        return db::selectOne("select count(if(ongoing_loan_doc_id is null and pending_loan_appl_doc_id is null, 1, null)) wa_fa, count(*) tot from borrowers where activity_status = 'active' and status = 'enabled' and $addl_sql");
    }

    private function get_aqusition_data($country_code,$addl_sql): array
    {
        $today = Carbon::now();
        $last_month_start = $today->copy()->subMonth()->startOfMonth();
        $this_month_start = $today->copy()->startOfMonth();
        $this_month = "\"$.".$today->format('M')."\"";
        $last_month = "\"$.".$today->subMonth()->format('M')."\"";
        $tar['total'] = db::selectOne("select sum(ifnull(targets->{$this_month}, 0)) this_month, sum(targets->{$last_month}) last_month from rm_targets where country_code = '{$country_code}'");
        $tar['acq']['this_month'] = db::selectOne("select count(*) acquired from leads where date(onboarded_date) >= '{$this_month_start}' and $addl_sql")->acquired;
        $tar['acq']['last_month'] = db::selectOne("select count(*) acquired from leads where date(onboarded_date) >= '{$last_month_start}' and date(onboarded_date) < '{$this_month_start}' and $addl_sql")->acquired;

        return $tar;
    }

    private function fas_due($country_code,$addl_sql): array
    {
        $today = Carbon::now();
        $yesterday = Carbon::yesterday();
        $due['today'] = db::selectOne("select count(paid_date) paid, count(*) tot from loans where date(due_date) = date('{$today}') and $addl_sql");
        $due['yesterday'] = db::selectOne("select count(paid_date) paid, count(*) tot from loans where date(due_date) = date('{$yesterday}') and $addl_sql");

        return $due;
    }

    public function lead_pending($country_code,$acc_prvdr_code,$addl_sql): array
    {
        $kyc_prgs = Consts::KYC_INPROGRESS;
        $onbrd_sts = Consts::CUSTOMER_ONBOARDED;
        $addl_sql2 = " and country_code = '{$country_code}' ";
        if($acc_prvdr_code){
            $addl_sql2 .= " and if(cust_reg_json->'$.account.acc_prvdr_code.value' = '{$acc_prvdr_code}',1, if(acc_prvdr_code = '{$acc_prvdr_code}',1,0)) ";
        }
        $data['w_rm'] = db::selectOne("select count(*) w_rm from leads where status <= '{$kyc_prgs}' and profile_status = 'open' $addl_sql2 ")->w_rm;
        $data['w_rc'] = db::selectOne("select count(*) w_rc from leads where status > '{$kyc_prgs}' and status < '{$onbrd_sts}' and profile_status = 'open' $addl_sql2")->w_rc;
        $data['wo_fa'] = db::selectOne("select count(*) wo_fa from borrowers where status='enabled' and tot_loan_appls = 0 and $addl_sql")->wo_fa;
        $data['total'] = $data['w_rm'] + $data['w_rc'] + $data['wo_fa'];

        return $data;
    }

    private function penalty($start_date, $end_date, $addl_sql, $history){
        $start = $end = "";
        if($history){
        if($start_date){
            $start = " and date(due_date) > date('{$start_date}') ";
        }
        if($end_date){
            $end = " and date(due_date) < date('{$end_date}') ";
        }}
        return DB::selectOne("select sum(penalty_collected) collected, count(if(penalty_collected > 0, 1, null)) collected_count, sum(penalty_waived) waived, count(if(penalty_waived > 0, 1, null)) waived_count, sum(provisional_penalty * penalty_days) imposed, count(if((provisional_penalty * penalty_days) > 0, 1, null)) imposed_count from loans where write_off_id is null {$start} {$end} and $addl_sql");
    }

    private function get_aggr_due_count($addl_sql): array
    {
        $today = Carbon::today();
        $exp_days = config('app.aggr_expiry_interval');
        $start_date = $today->copy()->subDays($exp_days);
        $end_date = $today->copy()->addDays($exp_days);
        $aggr['counts'] = DB::selectOne("select count(if((date(aggr_valid_upto) <= date('{$end_date}') and date(aggr_valid_upto) = date('{$today}')), 1, null)) expiring, count(if((date(aggr_valid_upto) >= date('{$start_date}') and date(aggr_valid_upto) < date('{$today}')), 1, null)) expired from borrowers where {$addl_sql} ");
        $aggr['exp_days'] = $exp_days;

        return $aggr;
    }

    private function get_fa_requst_count($addl_sql){
        return DB::selectOne("select count(*) fa_request from fa_upgrade_requests fa where fa.status = 'requested' and $addl_sql")->fa_request;
    }

    private function get_complaints_count($country_code, $acc_prvdr_code){
        return DB::selectOne("select count(if(cc.status = 'raised',1,null)) raised, count(*) total from cust_complaints cc, borrowers b where cc.cust_id = b.cust_id and b.country_code = '{$country_code}' and b.acc_prvdr_code = '{$acc_prvdr_code}'");
    }

    private function get_repayment_metric($start_date,$end_date, $addl_sql): array
    {

          $data['ontime'] = DB::selectOne("select count(paid_date) ontime, count(*) total from loans where date(due_date) >= date('{$start_date}') and date(due_date) <= date('{$end_date}') and $addl_sql");
          $overdue_start_date = Carbon::parse($start_date)->subDay();
          $data['overdue'] = DB::selectOne("select count(paid_date) paid, count(*) total from loans where date(due_date) >= date('{$overdue_start_date}') and date(due_date) < date('{$end_date}') and (paid_date is null or date(paid_date) > date(due_date)) and $addl_sql");

          return $data;
    }

    public function account_balance($country_code): array
    {
        $network_prvdrs = db::select("select id,acc_number, to_recon, network_prvdr_code, acc_prvdr_code from accounts where lender_code is not null and status = 'enabled' and country_code = '{$country_code}' and (json_contains(acc_purpose,json_array('repayment')) or json_contains(acc_purpose,json_array('disbursement'))) and network_prvdr_code != '*'");
        $common_network_prvdrs = db::select("select id,acc_number, to_recon, network_prvdr_code, acc_prvdr_code from accounts where lender_code is not null and status = 'enabled' and country_code = '{$country_code}' and (json_contains(acc_purpose,json_array('repayment')) or json_contains(acc_purpose,json_array('disbursement'))) and network_prvdr_code = '*'");
        $data = array();
        $already_added = array();
        $now = Carbon::now();
        foreach ($network_prvdrs as $network_prvdr){
            $network_prvdr_code = $network_prvdr->network_prvdr_code;
            if($network_prvdr->to_recon) {
                $data[$network_prvdr_code][] = json_decode(json_encode(DB::SelectOne("select balance, timestampdiff(minute,stmt_txn_date, '{$now}') time from account_stmts where account_id = {$network_prvdr->id} order by stmt_txn_date desc limit 1")),true) + json_decode(json_encode($network_prvdr),true);
            }else{
                $data[$network_prvdr_code][] = ['time' => null, 'balance' => null] + json_decode(json_encode($network_prvdrs), true)[0];
            }
            if(!in_array($network_prvdr_code, $already_added)) {
                $already_added[] =$network_prvdr_code;
                foreach ($common_network_prvdrs as $common_network_prvdr) {
                    $data[$network_prvdr_code][] = json_decode(json_encode($common_network_prvdr), true) + ['time' => null, 'balance' => null];
                }
            }
        }

        return $data;
    }

    private function get_disbursal_return_and_duplicate_count($start_date, $end_date, $country_code, $acc_prvdr_code){
        $addl_sql = "";
        if($acc_prvdr_code){
            $addl_sql = " and acc_prvdr_code = '{$acc_prvdr_code}'";
        }
        return DB::selectOne("select count(if(txn_type = 'disbursal_reversal',1,null)) return_disb, count(if(txn_type = 'duplicate_disbursal',1,null)) dup_disb from loan_txns lt, loans l where lt.loan_doc_id = l.loan_doc_id and  date(txn_date) >= date('{$start_date}') and date(txn_date) <= date('{$end_date}') and l.country_code = '{$country_code}' $addl_sql ");
    }

    private function get_appl_approvals($start_date, $end_date, $addl_sql): array
    {
        $values = db::selectOne("select count(if(status = 'approved' and loan_approver_id is not null,1,null)) manual,
                count(if(status = 'approved' and pre_appr_id is not null,1,null)) pre,
                count(if(status = 'rejected',1,null)) rejected, count(if(status = 'voided',1,null)) canceled, count(*) tot
                from loan_applications where date(loan_appl_date) >= date('{$start_date}') and
                date(loan_appl_date) <= date('{$end_date}') and $addl_sql");
        $data['data'] = array_values(json_decode(json_encode($values), true));
        $data['total'] = array_pop($data['data']);
        $data['category'] = ['Manual Approval', 'Pre-Approval', 'Rejected', 'Cancelled'];

        return $data;
    }

    private function get_rm_visit_chart($start_date, $end_date, $acc_prvdr_code, $country_code): array
    {
        $addl = " where ";
        if($acc_prvdr_code){
            $addl = ", borrowers b where v.cust_id = b.cust_id and b.acc_prvdr_code = '{$acc_prvdr_code}' and ";
        }
        $rm_visits = DB::select("select date(sch_date) sch_date, count(if(sch_status='checked_out', 1, null)) total, count(distinct visitor_id) rm_count from field_visits v {$addl} date(sch_date) >= date('{$start_date}') and date(sch_date) <= date('{$end_date}')  and v.country_code = '{$country_code}' group by date(sch_date)");
        $resp = array();
        foreach ($rm_visits as $rm_visit){
            $date = carbon::parse($rm_visit->sch_date)->format('Y-m-d');
            $resp['Total Visit'][] = ['x' => $date, 'y' => round($rm_visit->total)];
            $resp['Avg visits per RM'][] = ['x' => $date, 'y' => round(($rm_visit->total/$rm_visit->rm_count),1)];
        }

        return $resp;
    }

    private function get_disb_delay_reason($start_date, $end_date, $country_code, $acc_prvdr_code): array
    {
        $adl = "";
        $delay_by_sec = 60;
        if($acc_prvdr_code){
            $adl = " and l.acc_prvdr_code = '{$acc_prvdr_code}' ";
        }
        $results = DB::selectOne("select count(if(TO_SECONDS(rm_time) > $delay_by_sec, 1, NULL)) rm, count(if(cust_time is not null, TO_SECONDS(cust_time) > $delay_by_sec, if(cs_time is not null, TO_SECONDS(cs_time)>$delay_by_sec,null))) conf, count(if(no_of_attempts > 1, 1, null)) multi_atmpt, count(if(disbursal_mode = 'manual disbursal',1, null)) manual, count(*) total from loan_event_times le, loans l where l.loan_doc_id = le.loan_doc_id and  date(l.disbursal_date) >= date('{$start_date}') and date(l.disbursal_date) <= date('{$end_date}') and l.country_code = '{$country_code}' and le.country_code = '{$country_code}'{$adl} ");
        $data = array();
        $data['data'] = array_values(json_decode(json_encode($results), true));
        $data['total'] = array_pop($data['data']);
        $data['delay_sec'] = 'Above '.$delay_by_sec.' sec';
        $data['category'] = [['RM','Approval'], ['Customer', 'Confirmation'], ['Multiple', 'Attempts'], ['Manual', 'Capture']];

        return $data;

    }

    private function apply_to_disb_time_ranged_chart($start_date, $end_date, $country_code, $acc_prvdr_code, $addl_data){
        $addl ="";
        if($acc_prvdr_code){
            $addl .= " and l.acc_prvdr_code = '{$acc_prvdr_code}' ";
        }
        if($addl_data != null){
            if($addl_data['manual_capture'] != $addl_data['auto_capture']){
                if($addl_data['manual_capture']){
                    $addl .= " and txn_type = 'credit' and stmt_txn_type = 'credit' and (reason_for_skip is not null or reason_for_add_txn is not null) ";
                }else{
                    $addl .= " and txn_type = 'credit' and stmt_txn_type = 'credit' and (reason_for_skip is null or reason_for_add_txn is null) ";
                }
            }
            if($addl_data['auto_approval'] != $addl_data['manual_approval']){
                if($addl_data['auto_approval']){
                    $addl .= " and loan_approver_id is null ";
                }else{
                    $addl .= " and loan_approver_id is not null";
                }
            }
            if($addl_data['multi_attempt'] != $addl_data['single_attempt']){
                if($addl_data['multi_attempt']){
                    $addl .= " and loan_event_time->'$.no_of_atmpts' > 1 ";
                }else{
                    $addl .= " and loan_event_time->'$.no_of_atmpts' = 1 ";
                }
            }
            if($addl_data['otp'] != $addl_data['not_otp']){
                if($addl_data['otp']){
                    $addl .= " and ((cust_conf_channel is null and customer_consent_rcvd is true) or cust_conf_channel = 'cust_otp') ";
                }else{
                    $addl .= " and ((cust_conf_channel is null and customer_consent_rcvd is false) or cust_conf_channel != 'cust_otp') ";
                }
            }if($addl_data['manual_disbursal'] != $addl_data['auto_disbursal']){
                if($addl_data['manual_disbursal']){
                    $addl .= " and txn_type = 'debit' and stmt_txn_type = 'debit' and manual_disb_user_id is not null ";
                }else{
                    $addl .= " and txn_type = 'debit' and stmt_txn_type = 'debit' and manual_disb_user_id is null ";
                }
            }


        }

        $results = DB::selectOne("select count(if(timestampdiff(second,loan_appl_date,disbursal_date) between 0 and 59,1,null)) 0_1,
                                    count(if(timestampdiff(second,loan_appl_date,disbursal_date) between 60 and 119,1,null)) 1_2,
                                    count(if(timestampdiff(second,loan_appl_date,disbursal_date) between 120 and 239,1,null)) 2_4,
                                    count(if(timestampdiff(second,loan_appl_date,disbursal_date) between 240 and 359,1,null)) 4_6,
                                    count(if(timestampdiff(second,loan_appl_date,disbursal_date) between 360 and 479,1,null)) 6_8,
                                    count(if(timestampdiff(second,loan_appl_date,disbursal_date) between 480 and 600,1,null)) 8_10,
                                    count(if(timestampdiff(second,loan_appl_date,disbursal_date) > 600,1,null)) 10_00,
                                    count(*) total
                                    from loans l, account_stmts ac, loan_txns lt where l.loan_doc_id = ac.loan_doc_id and ac.loan_doc_id =lt.loan_doc_id and l.loan_doc_id = lt.loan_doc_id and disbursal_date is not null and date(l.disbursal_date) between '{$start_date}' and '{$end_date}' and l.country_code = '{$country_code}' {$addl}");

        $data = array();
        $data['data'] = array_values(json_decode(json_encode($results), true));
        $data['total'] = array_pop($data['data']);
        $data['category'] = ['Under 1', '1-2', '2-4', '4-6', '6-8', '8-10', '10+'];

        return $data;
    }

    private function get_repay_to_settle_time_chart($start_date, $end_date, $country_code, $acc_prvdr_code, $addl_data){
        $addl = "";
        if($acc_prvdr_code){
            $addl .= " and ac.acc_prvdr_code = '{$acc_prvdr_code}' ";
        }
        if($addl_data != null) {
            if ($addl_data['manual_capture'] != $addl_data['auto_capture']) {
                if ($addl_data['manual_capture']) {
                    $addl .= " and (reason_for_skip is not null or reason_for_add_txn is not null) ";
                } else {
                    $addl .= " and (reason_for_skip is null or reason_for_add_txn is null) ";
                }
            }
        }

        $results = DB::selectOne("select count(if(timestampdiff(second,lt.txn_date,lt.created_at) between 0 and 59,1,null)) 0_1,
                                    count(if(timestampdiff(second,lt.txn_date,lt.created_at) between 60 and 119,1,null)) 1_2,
                                    count(if(timestampdiff(second,lt.txn_date,lt.created_at) between 120 and 239,1,null)) 2_4,
                                    count(if(timestampdiff(second,lt.txn_date,lt.created_at) between 240 and 359,1,null)) 4_6,
                                    count(if(timestampdiff(second,lt.txn_date,lt.created_at) between 360 and 479,1,null)) 6_8,
                                    count(if(timestampdiff(second,lt.txn_date,lt.created_at) between 480 and 600,1,null)) 8_10,
                                    count(if(timestampdiff(second,lt.txn_date,lt.created_at) > 600,1,null)) 10_00,
                                    count(*) total
                                    from loan_txns lt, account_stmts ac where lt.loan_doc_id=ac.loan_doc_id and date(lt.txn_date) between '{$start_date}' and '{$end_date}' and ac.country_code = '{$country_code}' {$addl} ");

        $data = array();
        $data['data'] = array_values(json_decode(json_encode($results), true));
        $data['total'] = array_pop($data['data']);
        $data['category'] = ['Under 1', '1-2', '2-4', '4-6', '6-8', '8-10', '10+'];

        return $data;
    }

	
}






?>
