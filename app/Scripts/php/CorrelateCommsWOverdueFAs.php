<?php

namespace App\Scripts\php;

use App\Repositories\SQL\CustCommissionRepositorySQL;
use App\Services\Vendors\File\ExcelWriter;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Log;

class CorrelateCommsWOverdueFAs
{

    /**
     * The Account Provider to consider.
     *
     * @var string
     */
    private $acc_prvdr_code;

    /**
     * Days equal to or above to consider as overdue
     *
     * @var int
     */
    private $OVERDUE_DAYS_THRESHOLD = 10;

    public function get_overdues($year_month_to_check)
    {
        return DB::SELECT("SELECT a.cust_id, a.acc_number, a.acc_prvdr_code, l.loan_doc_id, l.flow_fee, l.due_date, l.paid_date, DAY(due_date) as due_day FROM loans l, accounts a WHERE a.acc_prvdr_code = ? AND l.overdue_days >= ? AND EXTRACT(YEAR_MONTH FROM due_date) = ? AND a.cust_id = l.cust_id AND a.status = ?", [$this->acc_prvdr_code, $this->OVERDUE_DAYS_THRESHOLD, $year_month_to_check, 'enabled']);
    }

    private function set_agent_data($customer_details, $comms_info) {

        $is_capable = false;
        if(is_array($comms_info)) {
            $comms_info = max($comms_info);
            $is_capable = ($comms_info > ($customer_details->flow_fee * 5)) ? true : false;
        }

        $paid_date = $customer_details->paid_date; 
        return [
            'Account Number' => $customer_details->acc_number,
            'Account Provider Code' => $customer_details->acc_prvdr_code,
            'Customer ID' => $customer_details->cust_id,
            'FA ID' => $customer_details->loan_doc_id,
            'Flow Fee' => $customer_details->flow_fee,
            'Due Date' => $customer_details->due_date,
            'Paid Date' => $paid_date ?? 'Not Paid Yet',
            'Commission Received' => $comms_info,
            'Capable to Repay' => $is_capable ? 'Capable' : 'Not Capable',
        ];
    }

    public function operations($overdues, $month_to_consider)
    {
        $agents_data = [];
        foreach ($overdues as $overdue) {
            $year = substr($month_to_consider, 0, 4);
            $month = substr($month_to_consider, 4, 6);

            $include_curr_month = ($overdue->due_day) > 15 ? false : true;
            $comms_info = (new CustCommissionRepositorySQL)->get_filtered_agent_commission($overdue->acc_number, $overdue->acc_prvdr_code, $year, $month, 1, $include_curr_month);
            $agents_data[] = $this->set_agent_data($overdue, $comms_info);
        }
        return $agents_data;
    }

    public function run($acc_prvdr_code)
    {
        set_app_session('UGA');
        $this->acc_prvdr_code = $acc_prvdr_code;
        $months_to_consider = ['202202','202203','202204','202205','202206','202207','202208','202209'];
        $excel_writer = new ExcelWriter(['freeze_header'=>True,'bold_header'=>True,'autosize_columns'=>True]);

        foreach ($months_to_consider as $month_to_consider) {
            $overdues = $this->get_overdues($month_to_consider);
            $agents_data = $this->operations($overdues, $month_to_consider);
            $sheet_name = Carbon::createFromFormat('Ym', $month_to_consider)->format('M Y');
            $excel_writer->write($sheet_name, $agents_data);
        }
        $excel_writer->save(public_path("files/CorrelateCommsWOverdueFAs.xlsx"));
    }
}
