<?php
namespace App\Services\Schedule;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\FlowCustomMail;
use Carbon\Carbon;
use App\Repositories\SQL\CommonRepositorySQL;

use Consts;

use Illuminate\Support\Facades\Mail;


class DailyMetricsScheduleService{

    public function send_daily_metrics(){

        $yesterday = Carbon::yesterday();
        $month = $yesterday->copy()->format('M');
        $yesterday_date = $yesterday->format(Consts::DB_DATE_FORMAT);

        $country_code = session("country_code");
        $addl_sql = "country_code = '{$country_code}'";

        [$total_reg_cust_count, $tdy_reg_cust_count] = $this->get_no_of_registered_customers($yesterday_date, $addl_sql);
        [$total_disbursal_today, $total_repaid_today, $total_volume_of_disbursal] = $this->get_total_transactions_amounts($yesterday_date, $addl_sql);
        $outstanding_fas_count = $this->get_no_of_fas_outstanding($yesterday_date, $addl_sql);
        $outstanding_fas_value = $this->get_outsanding_fas_value($yesterday_date, $addl_sql);
        $repeat_customers = $this->get_repeat_customer_perc($yesterday_date, $addl_sql);
        $repayment_rate = $this->get_repayment_rate($yesterday_date, $addl_sql);
        $ontime_repayment_rate = $this->get_ontime_repayment_rate($yesterday_date, $addl_sql);
        $total_fee = $this->get_total_fee_received_today($yesterday_date, $addl_sql);
        $overdue_fas = $this->get_overdue_fas_value($yesterday_date, $addl_sql);
        $total_fee_mtd = $this->get_total_fee_received_mtd($yesterday_date, $addl_sql);
        $tot_disbursal_count = $this->get_total_no_fa_disbursed($yesterday_date, $addl_sql);
        $tot_active_cust = $this->get_total_no_active_customers($addl_sql);
        
        $yesterday_date = $yesterday->format(Consts::UI_DATE_FORMAT);
        $day =  $yesterday->format('l');
        $currency_code = (new CommonRepositorySQL())->get_currency_code($country_code)->currency_code;

        $mail_data = compact('month', 'yesterday_date', 'country_code', 'total_reg_cust_count', 'tdy_reg_cust_count', 'total_disbursal_today','total_repaid_today','total_volume_of_disbursal','outstanding_fas_count','outstanding_fas_value', 'repeat_customers', 'repayment_rate', 'ontime_repayment_rate', 'total_fee', 'overdue_fas', 'day', 'currency_code', 'total_fee_mtd', 'tot_disbursal_count', 'tot_active_cust' );
        // Mail::to([get_ops_admin_email(), get_market_admin_email(),get_l3_email()])->queue((new FlowCustomMail('daily_metrics', $mail_data))->onQueue('emails'));
        Mail::to([get_ops_admin_email(), get_market_admin_email(),get_l3_email()])->queue(new FlowCustomMail('daily_metrics', $mail_data));
    }

    private function get_ignore_written_off_condn($country_code, $date ){
        $written_off_fa_ids = $this->get_written_off_loans($country_code, $date);
        
        if ($written_off_fa_ids == ""){
            return "";
        }else{
            return " and loan_doc_id not in ({$written_off_fa_ids}) ";
        }
    }
    


    public function get_written_off_loans($country_code, $date){

        $written_off_loans = DB::select("select loan_doc_id from loan_write_off 
                                  where country_code = '{$country_code}' and year < year(date_add('{$date}', INTERVAL 1 MONTH))
                                  and write_off_status in ('approved','partially_recovered','recovered')");

        $write_off_fa_ids = '"'.implode('" ,"', collect($written_off_loans)->pluck('loan_doc_id')->toArray()).'"';

        return $write_off_fa_ids;
       
    }

    private function ignore_float_vend_prods(){
        
        $products = DB::select("select id from loan_products where product_type = 'float_vending'");
        $product_ids = implode(',', collect($products)->pluck('id')->toArray());
        return "and product_id not in ($product_ids)";

    }

    private function get_overdue_fas_value($yesterday_date, $addl_sql){

        $addl_sql .= $this->get_ignore_written_off_condn(session('country_code'), $yesterday_date);
        $addl_sql .= $this->ignore_float_vend_prods();

        $loans = DB::selectOne("select sum(loan_principal- ifnull(paid_principal, 0)) as overdue_fas_value from loans where status not in('voided', 'hold', 'pending_disbursal', 'pending_mnl_dsbrsl') and date(disbursal_date) <= '{$yesterday_date}' and $addl_sql and datediff('{$yesterday_date}', due_date) > 1 and (date(paid_date) > '{$yesterday_date}' or paid_date is null)");

        return $loans ? number_format($loans->overdue_fas_value) : 0;
    }

    private function get_total_fee_received_today($yesterday_date, $addl_sql){
        // $loans = DB::selectOne("select sum(paid_fee) as total_fee from loans  where date(paid_date) = '{$yesterday_date}' and $addl_sql");
        $loan_txns = DB::selectOne("select sum(IF(txn_type = 'payment', fee, 0)) as total_fee from loan_txns where date(txn_date) = '{$yesterday_date}' and $addl_sql");
        return $loan_txns ? number_format($loan_txns->total_fee) : 0;
    }

    private function get_ontime_repayment_rate($yesterday_date, $addl_sql){
        $loans = DB::selectOne("select SUM(IF(date(paid_date) <= DATE_ADD(date(due_date), INTERVAL 1 DAY), 1, 0))/count(loan_doc_id) as ontime_repayment_rate from loans where date(paid_date) <= '{$yesterday_date}' and $addl_sql");
        return $loans ? round($loans->ontime_repayment_rate *100 , 2) : 0;
    }

    private function get_repayment_rate($yesterday_date, $addl_sql){
       $loans =  DB::selectOne("select count(if(date(paid_date) <= '{$yesterday_date}' and date(due_date) <= '{$yesterday_date}' and paid_date is not null, 1, null))/count(if(date(due_date) <= '{$yesterday_date}' and disbursal_date is not null,0,null))as repayment_rate from loans where $addl_sql");
       return $loans ? round($loans->repayment_rate *100, 2) : 0;
    }

    private function get_repeat_customer_perc($yesterday_date, $addl_sql){
       $borrowers =  DB::selectOne("select count(loans_taken)/(SELECT count(distinct cust_id)  from borrowers where $addl_sql and date(reg_date) <= '{$yesterday_date}') as repeat_customers_perc from 
        (select count(loan_doc_id) as loans_taken from loans where $addl_sql and date(disbursal_date) <= '{$yesterday_date}' group by cust_id having loans_taken > 1) as loans");
        return $borrowers ? round($borrowers->repeat_customers_perc *100, 2) : 0;
    }

    public function get_outsanding_fas_value($yesterday_date, $addl_sql){
        $addl_sql .= $this->get_ignore_written_off_condn(session('country_code'), $yesterday_date);
        $addl_sql .= $this->ignore_float_vend_prods();

        $loans = DB::selectOne("select sum(loan_principal- ifnull(paid_principal,0)) as outstanding_fas_value from loans where status not in('voided', 'hold', 'pending_disbursal', 'pending_mnl_dsbrsl') and $addl_sql and date(disbursal_date) <= '{$yesterday_date}' and (date(paid_date) > '{$yesterday_date}' or paid_date is null)");

        return $loans ? number_format($loans->outstanding_fas_value) :0;
    }

    private function get_no_of_fas_outstanding($yesterday_date, $addl_sql){
        $addl_sql .= $this->get_ignore_written_off_condn(session('country_code'), $yesterday_date);
        $addl_sql .= $this->ignore_float_vend_prods();
        $loans =  DB::selectOne("select count(distinct loan_doc_id) as outstanding_fas from loans where $addl_sql and status in ('ongoing', 'due', 'overdue') and date(disbursal_date) <= '{$yesterday_date}' and (date(paid_date) > '{$yesterday_date}' or paid_date is null)");
        return $loans ? $loans->outstanding_fas :0;
    }

    private function get_no_of_registered_customers($yesterday_date, $addl_sql){
        $borrowers =  DB::selectOne("select count(if(date(reg_date) <= '{$yesterday_date}', 1, null)) as total_reg_cust_count, count(if(date(reg_date) = '{$yesterday_date}', 1, null)) as tdy_reg_cust_count from borrowers where $addl_sql");
        return  $borrowers ? [$borrowers->total_reg_cust_count, $borrowers->tdy_reg_cust_count] : [0, 0];
    }

    private function get_total_transactions_amounts($yesterday_date, $addl_sql){
        $loan_txns = DB::selectOne("select sum(if(txn_type = 'disbursal' and date(txn_date) = '{$yesterday_date}', amount, null)) as total_disbursal_today, sum(if(txn_type = 'payment' and date(txn_date) = '{$yesterday_date}', amount, null)) as total_repaid_today, sum(if(txn_type = 'disbursal' and date(txn_date) <= '{$yesterday_date}', amount, null)) as total_volume_of_disbursal from loan_txns where $addl_sql");
        return  $loan_txns ? [number_format($loan_txns->total_disbursal_today), number_format($loan_txns->total_repaid_today), number_format($loan_txns->total_volume_of_disbursal)] : [0, 0, 0];
    }
    
    private function get_total_fee_received_mtd($yesterday_date, $addl_sql){

        $start_date = Carbon::yesterday()->startOfMonth('Y-m-d');

        $loans = DB::selectOne("select sum(paid_fee) as total_fee from loans  where date(paid_date) >= '{$start_date}' and date(paid_date) <= '{$yesterday_date}' and $addl_sql");
        return $loans ? number_format($loans->total_fee) : 0;
    }

    private function get_total_no_fa_disbursed($yesterday_date, $addl_sql){
        $loans = DB::selectOne("select count(loan_doc_id) tot_disbursed from loans where date(disbursal_date) = '{$yesterday_date}' and disbursal_status = 'disbursed' and $addl_sql")->tot_disbursed;
        return $loans ?? 0;
    }

    Private function get_total_no_active_customers($addl_sql){
        $borrowers = DB::selectOne("select count(cust_id) as active_cust from borrowers where status = 'enabled' and activity_status = 'active' and $addl_sql")->active_cust;
        return $borrowers ?? 0;
    }

}