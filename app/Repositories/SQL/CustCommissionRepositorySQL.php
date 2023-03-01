<?php

namespace App\Repositories\SQL;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\CustCommission;
use App\Consts;
use Carbon\Carbon;

class CustCommissionRepositorySQL extends BaseRepositorySQL
{
    public function __construct()
    {
        parent::__construct();
    }
    public function model()
    {
        return CustCommission::class;
    }

    public function get_available_comms_by_year($acc_number, $acc_prvdr_code, $years)
    {
        $comms_records = $this->get_records_by_in('year', $years, ['year', 'commissions'], null, " AND acc_number = '$acc_number' AND acc_prvdr_code = '$acc_prvdr_code'");
        $comms_records = json_decode(json_encode($comms_records), true);

        $records = array_column($comms_records, 'commissions', 'year');
        return $records;
    }


    public function filter_comms($comms, $comms_months)
    {
        $filtered_comms = [];
        foreach ($comms_months as $year => $months) {
            foreach ($months as $month) {
                if (isset($comms[$year][$month])) {
                    $filtered_comms[] = $comms[$year][$month];
                } else {
                    return "Missing comms: Year: $year, Month: $month";
                }
            }
        }
        return $filtered_comms;
    }

    public function get_filtered_agent_commission($acc_number, $acc_prvdr_code, $year, $month, $last_N_months, $include_curr_month)
    {
        $comms_months = get_last_N_months($year, $month, $last_N_months, $include_curr_month);
        $comms = $this->get_available_comms_by_year($acc_number, $acc_prvdr_code, array_keys($comms_months));
        return $this->filter_comms($comms, $comms_months);
    }
}
