<?php

namespace App\Scripts\php;

use App\Exceptions\FlowCustomException;
use App\Models\Account;
use App\Models\CustCSFValues;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\CustCSFValuesRepositorySQL;
use App\Services\DataScoreModelService;
use App\Services\Factors;
use Illuminate\Support\Facades\DB;
use Log;


class TempReassessCustomersScript {

	public function last_n_fas_max_amount($cust_id, $acc_prvdr_code, $last_n_fas) {
		
        $loans = DB::SELECT("SELECT loan_principal, disbursal_date FROM loans WHERE cust_id = ? AND country_code = ? ORDER BY disbursal_date DESC LIMIT ?", [$cust_id, session('country_code'), $last_n_fas]);
        $last_fa_amounts = collect($loans)->pluck('loan_principal')->toArray();
        if (empty(array_filter($last_fa_amounts))) {
            $last_fa_amounts = [null];
        }
		return max($last_fa_amounts);
	}

	public function get_previous_limit_and_result($acc_number, $acc_prvdr_code) {
		
		$limit = null;
		$result = null;

		$cust_csf_value = (new CustCSFValues())->get_record_by_many(['acc_number', 'acc_prvdr_code'], [$acc_number, $acc_prvdr_code], ['conditions', 'result']);
		if(isset($cust_csf_value->conditions->limit)) {
			$limit = $cust_csf_value->conditions->limit;
		}
		if(isset($cust_csf_value->result)) {
			$result = $cust_csf_value->result;
		}

		return [$result, $limit];
	}

    public function get_fa_based_limit($product_limits, $max_fa) {
        
        $limit = null;
        foreach ( $product_limits as $product_limit ) {
            if ( $product_limit >= $max_fa )
            {
                $limit = $product_limit;
                break;
            }
        }
        return $limit;
    }

	public function get_normalized_factor_value($factors, $factor_name) {
		
		$comms_record = collect($factors)->where('csf_type', $factor_name)->toArray();
		$comms_record = array_shift($comms_record);
		$score = ($comms_record == null) ? null : $comms_record['n_val'];
		return $score;
	}

	public function temp_reassess_comms_customers($acc_prvdr_code, $previous_unlimited_amount, $current_unlimited_amount, $use_avg=True) {

        try {
            DB::beginTransaction();
            $start_time = microtime(true);

            session(['acc_prvdr_code' => $acc_prvdr_code]);
            $account_repo = new Account;
            $borrower_repo = new BorrowerRepositorySQL();
            $cust_csf_values_repo = new CustCSFValuesRepositorySQL();
            $score_model_serv = new DataScoreModelService();

            $months_to_consider = 3; // Last N Months Commission to consider
            // Get all enabled accounts for the specified acc_prvdr
            $accounts = $account_repo->get_records_by_many(['acc_prvdr_code', 'status'], [$acc_prvdr_code, 'enabled'], ['acc_number', 'cust_id', 'acc_prvdr_code', 'alt_acc_num', 'holder_name'], "and", "AND cust_id IS NOT NULL");
            $accs_wo_commission = [];
            $accs_w_less_limit = [];

            // Get commission based limits from config
            $acc_elig_reason = 'commission_based_limits';
            $limit_conditions = config('app.account_elig_conditions')[$acc_prvdr_code][$acc_elig_reason];
            $product_limits = array_column($limit_conditions, 'limit');

            foreach ( $accounts as $account) {
                $acc_number = $account->acc_number;
                $cust_id = $account->cust_id;
                
                $comms = $score_model_serv->get_last_N_comms($acc_number, $acc_prvdr_code, $months_to_consider);
                if(empty($comms)) {
                    $accs_wo_commission[] = $account;
                }
                else {
                    // Get average comms
                    $avg_comms = array_sum($comms) / count($comms);
                    $avg_comms = round($avg_comms, 0, PHP_ROUND_HALF_UP);
                    // Max from the selected months
                    $max_comms = max($comms);

                    $selected_comms = ($use_avg) ? $avg_comms : $max_comms; 

                    // Get Max FA amount from previous FAs
                    $max_fa = $this->last_n_fas_max_amount($cust_id, $acc_prvdr_code, 5);
                    // Get previous limit and result from `cust_csf_values` 
                    [$previous_result, $previous_csf_limit] = $this->get_previous_limit_and_result($acc_number, $acc_prvdr_code);
                    $previous_csf_limit = ($previous_csf_limit === null) ? $previous_unlimited_amount : $previous_csf_limit;
                    $previous_limit = $previous_csf_limit;

                    if ($max_fa !== null) { // If an FA was taken previously
                        $previous_fa_limit = $this->get_fa_based_limit($product_limits, $max_fa);
                        $previous_fa_limit = ($previous_fa_limit === null) ? $previous_unlimited_amount : $previous_fa_limit;
                        $previous_limit = $previous_fa_limit;
                    }
                    // Normalize the commission data
                    $factors = Factors::normalize($acc_number, session('country_code'), ['monthly_comms' => $selected_comms]);
                    $factors = json_decode(json_encode($factors), true);

                    $real_score = $this->get_normalized_factor_value($factors, 'monthly_comms');
                    $real_result = $real_score ? 'eligible' : 'ineligible';
                    $new_limit = $score_model_serv->commission_based_limit($limit_conditions, $selected_comms);
                    
                    // null means all products will be available
                    // $previous_limit = ($previous_limit === null) ? $previous_unlimited_amount : $previous_limit;
                    $new_limit = ($new_limit === null) ? $current_unlimited_amount : $new_limit;
                    
                    $limit_to_use = max([$previous_limit, $new_limit]);
                    $limit_used = ($limit_to_use == $current_unlimited_amount) ? null : $limit_to_use;

                    // Don't make previous eligible customers as ineligible
                    $result = ($previous_result == 'ineligible' && $real_result == 'ineligible') ? 'ineligible' : 'eligible';
                    if($result == 'ineligible') {
                        $score = 0;
                        $conditions = [];
                    }
                    else {
                        $score = 1000;
                        // If null, all products are available. Conditions are not needed.
                        $conditions = $limit_used ? ['limit' => $limit_used, 'acc_elig_reason' => $acc_elig_reason, 'validity' => '*'] : [];
                    }

                    $run_id = uniqid();
                    // Get customer's profile details
                    $customer_details = $score_model_serv->get_scoring_model($acc_number);
                    $borrower_id = $customer_details['borrower_id'];
                    $account_id = $customer_details['account_id'];
                    
                    $data = [
                        "country_code" => session('country_code'),
                        "acc_prvdr_code" => $acc_prvdr_code,
                        "acc_number" => $acc_number,
                        "cust_score_factors" => $factors,
                        "score" => $score,
                        "result" => $result,
                        "conditions" => $conditions,
                        "run_id" => $run_id,
                    ];

                    $cust_csf_values_repo->delete_cust_csf_values($acc_number, $acc_prvdr_code);
                    $cust_csf_values_repo->insert_model($data);

                    // Update csf_run_id in `borrowers` and acc_elig_reason in `accounts`
                    if ($borrower_id) {
                        $update_borrower = [ 'csf_run_id' => $run_id, 'id' => $borrower_id ];
                        ($borrower_repo)->update_model($update_borrower);
                    }
                    if($account_id) {
                        $reason = empty($conditions) ? null : $acc_elig_reason;
                        $update_account = ['acc_elig_reason' => $reason, 'id' => $account_id];
                        $account_repo->update_model($update_account);
                    }

                    if( $result == 'eligible' && ($new_limit < $previous_limit)) {	

                        $new_limit = ($new_limit == 0) ? 'ineligible' : $new_limit;	
                        $min_of_both = ($max_fa === null) ? $previous_csf_limit : min([$previous_csf_limit, $previous_fa_limit]);
                        $max_fa = ($max_fa === null) ? 'No FAs Taken' : $max_fa;

                        $addl_data = ['previous_csf_limit' => $previous_csf_limit, 'previous_loan_limit' => $max_fa, 'min_of_both' => $min_of_both, 'new_limit' => $new_limit, 'limit_used' => $limit_to_use, 'commission' => $selected_comms];
                        $accs_w_less_limit[] = (array)$account + $addl_data;
                    }
                }		
            }

            if ( count($accs_wo_commission) > 0) {
                $mail_data = ['country_code' => session('country_code'), 'accounts' => $accs_wo_commission, 'acc_prvdr_code' => $acc_prvdr_code];
                send_email('missing_comms_notification', config('app.ops_auditor_email'), $mail_data);
            }
            if ( count($accs_w_less_limit) > 0) {
                $mail_data = ['country_code' => session('country_code'), 'accounts' => $accs_w_less_limit, 'acc_prvdr_code' => $acc_prvdr_code, 'months_considered' => $months_to_consider];
                send_email('product_limit_adjustment_notification', config('app.ops_auditor_email'), $mail_data);
            }
            DB::commit();
            $end_time = microtime(true);
            $execution_time = floor($end_time - $start_time);
            return "Execution time of script = $execution_time sec";
		}
		catch (Exception $e) {
			DB::rollback();
			thrw($e->getMessage());
		}	
	}
}