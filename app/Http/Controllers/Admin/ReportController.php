<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\ApiController;
use App\Model\KpiFlowReports;
use App\Services\KPIReportGenService;
use App\Services\OperationDashboardService;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;

//use App\Services\ProductTakeupReportService;


class ReportController extends ApiController
{

	public function get_kpi_report(Request $req){

        $data_prvdr_code = $req['data_prvdr_code'];
        $data = $req->data;


        $report_serv = new KPIReportGenService($data['country_code'], $data_prvdr_code);
        #$kpi_reports = $report_serv->get_kpi_report(true);
        #$kpi_reports = $report_serv->get_kpi_report($date);
        /*if(array_key_exists('last_n_months', $data)){
        	$kpi_reports = $report_serv->get_kpi_reports_trend($data['last_n_months'], true);
    	}else{
    		$date = Carbon::now();
    		$kpi_reports = $report_serv->get_kpi_report($date, true);
    	}*/
    	if($data['type'] == 'monthly'){
        	$kpi_reports = $report_serv->get_kpi_reports_trend(5, true);
    	}else{
    		$date = Carbon::now();
    		$kpi_reports = $report_serv->get_kpi_report($date, true);
    	}
        return $this->respondData($kpi_reports);
	}
   
   public function get_report(Request $req){
        // $data_prvdr_code = get_arr_val($req, 'data_prvdr_code');
        $data = $req->data;
        $country_code = $req['country_code'] ?? null;
        $report_type = $data['report_type'];
        //Log::warning($report_type);
        // $data_prvdr_code = get_arr_val($data, 'data_prvdr_code');
        $acc_prvdr_code = session('acc_prvdr_code');
        $report_serv = new ReportService($data['country_code'], $acc_prvdr_code);

        if($report_type == "portfolio_quality"){
            $report = $report_serv->get_portfolio_quality_rpt();
        }else if($report_type == "cust_growth"){
            $report = $report_serv->get_cust_growth($data['cust_id'], $data['country_code']);
        }else if($report_type == "product_takeup"){
            $report = $report_serv->get_product_takeup_rpt($acc_prvdr_code);
        }else if($report_type == "daily_activity"){            
            $report = $report_serv->get_daily_activity_rpt($data,$acc_prvdr_code);
        }else if($report_type == "overdue_fa_repayments"){
            $report = $report_serv->get_overdue_fa_repayments_rpt($data);
        }else if($report_type == "daily_visits"){
            $report = $report_serv->get_rm_distant_checkin_checkout_report($data);
        }else if($report_type == "daily_agreements"){
            $report = $report_serv->get_daily_agreements($data);
        }else if($report_type == "capital_funds"){
            $report = $report_serv->get_capital_funds($data);
        }else if($report_type == "lead_conversion"){
            $report = $report_serv->get_lead_report($data);
        }elseif($report_type == "management_dashboard_live"){
            $report = $report_serv->get_mgmt_dashboard_live_report($data);
        }elseif($report_type == "management_dashboard_monthly"){
            $report = $report_serv->get_mgmt_dashboard_monthly_report($data);
        }elseif($report_type == "rm_wise_repayment_rate"){
            $report = $report_serv->get_rm_wise_repayment_rate_rpt($data);
        }elseif($report_type == "get_monthly_cust_report"){
            $report = $report_serv->get_monthly_new_cust_report($country_code);
        }elseif($report_type == "get_reg_and_active_customer"){
            $report = $report_serv->get_reg_and_active_customer($country_code);
        }elseif($report_type == "get_revenue_per_cust"){
            $report = $report_serv->get_revenue_per_cust($country_code);
        }elseif($report_type == "get_revenue_per_fa"){
            $report = $report_serv->get_revenue_per_fa($country_code);
        }elseif($report_type == "get_tot_disb_report"){
            $report = $report_serv->get_tot_disb_report($country_code);
        }elseif($report_type == "get_tot_disb_count"){
            $report = $report_serv->get_tot_disb_count($country_code);
        }elseif($report_type == "get_tot_settled_disb_count"){
            $report = $report_serv->get_tot_settled_disb_count($country_code);
        }elseif($report_type == "get_ontime_payments_for_country"){
            $report = $report_serv->get_ontime_payments_for_country($country_code);
        }elseif($report_type == "get_ontime_payments"){
            $report = $report_serv->get_ontime_payments($country_code);
        } elseif($report_type == "get_outstanding_fa_for_country"){
            $report = $report_serv->get_outstanding_fa_for_country($country_code);
        }elseif($report_type == "get_outstanding_fa"){
            $report = $report_serv->get_outstanding_fa($country_code);
        }elseif($report_type == "get_revenue_for_country"){
            $report = $report_serv->get_revenue_for_country($country_code);
        }elseif($report_type == "get_monthly_cust_report_of_country"){
            $report = $report_serv->get_monthly_new_cust_report_of_country($country_code);
        }elseif($report_type == "get_revenue_per_cust_of_country"){
            $report = $report_serv->get_revenue_per_cust_of_country($country_code);
        }elseif($report_type == "get_revenue_per_fa_of_country"){
            $report = $report_serv->get_revenue_per_fa_of_country($country_code);
        }elseif($report_type == "get_tot_disb_report_of_country"){
            $report = $report_serv->get_tot_disb_report_of_country($country_code);
        }elseif($report_type == "get_tot_disb_count_of_country"){
            $report = $report_serv->get_tot_disb_count_of_country($country_code);
        }elseif($report_type == "get_tot_settled_disb_count_of_country"){
            $report = $report_serv->get_tot_settled_disb_count_of_country($country_code);
        }elseif($report_type == "get_revenue"){
            $report = $report_serv->get_revenue($country_code);
        }elseif($report_type == "get_portfolio_risk"){
            $report = $report_serv->get_portfoloio_risk($country_code);
        }elseif($report_type == "get_outstanding"){
            $report = $report_serv->get_outstanding($country_code);
        }elseif($report_type == "get_cust_performance"){
            $report = $report_serv->get_cust_performance($country_code);
        }elseif($report_type == "get_advances_disbursed_and_completed"){
            $report = $report_serv->get_advances_disbursed_and_completed($country_code);
        }elseif($report_type == "get_total_and_overdue_fa"){
            $report = $report_serv->get_total_and_overdue_fa($country_code);
        }elseif($report_type == "operation_dashboard"){
            $report = (new OperationDashboardService)->get_operation_dashboard_data($data);
        }elseif($report_type=="rm_productivity_report"){
            $report = $report_serv->get_rm_productivity_report($data);
        }


        return $this->respondData($report);
    }
    public function get_growth_chart(Request $req)
    {
         $acc_prvdr_code = $req['acc_prvdr_code'];
         $data = $req->data;

         $report_serv = new ReportService($data['country_code'],$acc_prvdr_code);
         $cust_performance  = $report_serv->get_growth_chart($data['cust_id']);
         return $this->respondData($cust_performance);
    }

    /*public function get_product_takeup_report(Request $req)
    {
         $data = $req->data;
         $prod_report_serv = new ProductTakeupReportService($data['country_code']);
         $product_takeup  = $prod_report_serv->get_product_takeup_rpt();
         return $this->respondData($product_takeup);
    }*/


    public function get_custom_report(Request $req){
        // $data_prvdr_code = $req['data_prvdr_code'];
        $country_code = $req['country_code'];
        if($req['type'] == 'risk_category_report'){
            $report_serv= new ReportService($country_code);
            $records = $report_serv->get_risk_category_report();
        }
        return $this->respondData($records);
    }

    public function get_rm_report(Request $req){
        $country_code = session('country_code');
        $report_serv= new ReportService($country_code);
        $records = $report_serv->get_rm_wise_report();
        return $this->respondData($records);
    }

    public function get_currency_details(Request $request)
    {
        $country_code = $request['country_code'];
        $report_serv= new ReportService($country_code);
        $resp = $report_serv->get_report_currency_info();

        return $this->respondData($resp);
    }
    public function get_sms_details(Request $req){

        $data= $req->data;
        $report_serv = new ReportService();
        $reports = $report_serv->get_sms_report($data);
        return $this->respondData($reports);

    }

    public function get_report_date(Request $request)
    {
        $data= $request->data;
        $report_serv = new ReportService();
        $date = $report_serv->get_monthly_report_date($data);
        return $this->respondData($date);
    }
    
}
