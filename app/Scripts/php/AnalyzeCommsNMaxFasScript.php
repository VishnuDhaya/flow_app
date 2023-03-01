<?php

namespace App\Scripts\php;

use App\Repositories\SQL\CustCommissionRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Services\DataScoreModelService;
use App\Services\Vendors\File\ExcelWriter;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Log;
        
class AnalyzeCommsNMaxFasScript{

    public function get_max_fa_in_month($cust_id, $year_month_to_consider) {
        
        return DB::SELECTONE("SELECT max(loan_principal) as max_fa FROM loans WHERE cust_id = ? and EXTRACT(YEAR_MONTH FROM disbursal_date) = ?", [$cust_id, $year_month_to_consider])->max_fa;
    }

    public function get_onboarded_customers($acc_prvdr_code, $onboarded_after_year_month, $onboarded_till_year_month) {

		$customers = DB::SELECT("SELECT b.cust_id, b.acc_number FROM borrowers b, accounts a WHERE b.acc_prvdr_code = ? AND (EXTRACT( YEAR_MONTH FROM `reg_date` ) BETWEEN ? and ?) AND a.cust_id = b.cust_id AND a.status = ?", [$acc_prvdr_code, $onboarded_after_year_month, $onboarded_till_year_month, 'enabled']);
		return $customers;
	}

    
    public function set_customer_data($acc_number, $comms_info, $limit_conditions, $max_fa) {

        $score_model_serv = new DataScoreModelService;
        
        $avg_comms = $comms_info; $comms_based_limit = 'None';
        if (is_array($comms_info)) {
            $avg_comms = round(array_sum($comms_info) / count($comms_info), 0, PHP_ROUND_HALF_UP);
            $comms_based_limit = $score_model_serv->commission_based_limit($limit_conditions, $avg_comms);
            $comms_based_limit = ($comms_based_limit === null) ? 'Unlimited' : $comms_based_limit;
        }

        $max_fa = $max_fa ? $max_fa : 'No FAs';
        return [
            'Account Number' => $acc_number,
            'Commission' => $avg_comms,
            'Commission Based Limit' => $comms_based_limit,
            'Max FA' => $max_fa
        ];
    }

    public function get_commission_w_limit_n_max_fa($acc_prvdr_code, $limit_conditions, $month_to_consider, $starting_year_month) {
        
        $commission_n_max_fa = [];
        $year = substr($month_to_consider, 0, 4);
        $month = substr($month_to_consider, 4, 6);
        $score_model_serv = new DataScoreModelService;
        
        $customers = $this->get_onboarded_customers($acc_prvdr_code, $starting_year_month, $month_to_consider);
        foreach($customers as $customer) {
            $acc_number = $customer->acc_number;
            $cust_id = $customer->cust_id;
            $N_months_commission = (new CustCommissionRepositorySQL)->get_filtered_agent_commission($acc_number, $acc_prvdr_code, $year, $month, 3, false);
            $max_fa = $this->get_max_fa_in_month($cust_id, $month_to_consider);
            $commission_n_max_fa[] = $this->set_customer_data($acc_number, $N_months_commission, $limit_conditions, $max_fa);
        }
        return $commission_n_max_fa;
    }

	public function run($acc_prvdr_code) {
		
        $months_to_consider = ['202202', '202203', '202204', '202205', '202206', '202207', '202208', '202209', '202210'];
        $limit_conditions = config('app.account_elig_conditions')[$acc_prvdr_code]['commission_based_limits'];

        $starting_year_month = $months_to_consider[0];
        $excel_writer = new ExcelWriter();

        foreach($months_to_consider as $month_to_consider) {
            
            $commission_n_max_fa = $this->get_commission_w_limit_n_max_fa($acc_prvdr_code, $limit_conditions, $month_to_consider, $starting_year_month);
            $sheet_name = Carbon::createFromFormat('Ym', $month_to_consider)->format('M Y');
            $excel_writer->write($sheet_name, $commission_n_max_fa);
        }
        $excel_writer->save(public_path("files/Commission Analysis.xlsx"));
        return $commission_n_max_fa;
	}
}