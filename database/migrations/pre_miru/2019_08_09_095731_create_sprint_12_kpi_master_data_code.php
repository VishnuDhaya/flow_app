<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSprint12KpiMasterDataCode extends Migration
{

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function up()
    {
        
            DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'float_kpi_rpt_metrics', 'parent_data_key' => 'float_kpi_rpt_sections', 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);

            DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'float_kpi_rpt_sections', 'parent_data_key' => NULL, 'status' => 'enabled', 'data_type' => 'common', 'created_at' => now()]);



        DB::table('master_data')->insert([ 
        
        
       ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'core_reg_cust_a5' , 'data_value' => 'Number of Customers Registered' , 'parent_data_code' => 'cust_n_fa_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'core_uniq_cust_a6' , 'data_value' => 'No of Unique Customers' , 'parent_data_code' => 'cust_n_fa_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'core_active_cust_a6' , 'data_value' => 'No of Active Customers' , 'parent_data_code' => 'cust_n_fa_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'active_cust_pc_a7' , 'data_value' => 'Percentage of Active Customers' , 'parent_data_code' => 'cust_n_fa_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'core_disbursed_no_of_fa_a8' , 'data_value' => 'Number of FlowEzee FAs Disbursed' , 'parent_data_code' => 'cust_n_fa_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'core_settled_no_of_fa_a9' , 'data_value' => 'Number of FlowEzee FAs Completed' , 'parent_data_code' => 'cust_n_fa_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'ontime_fa_pc_a12' , 'data_value' => 'Percent of Repayment On time' , 'parent_data_code' => 'perf_metrics_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'avg_fa_amt_a13' , 'data_value' => 'Average Float Advance Amount' , 'parent_data_code' => 'perf_metrics_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'avg_no_of_fa_per_cust_a15' , 'data_value' => 'Number of FAs disbursed /Customer' , 'parent_data_code' => 'perf_metrics_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'avg_fa_duration_a16' , 'data_value' => 'Average FA duration' , 'parent_data_code' => 'perf_metrics_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'repeat_cust_pc_a15C' , 'data_value' => 'Percent of Repeat Customers' , 'parent_data_code' => 'perf_metrics_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'max_os_amt' , 'data_value' => 'Max Outstanding Amt' , 'parent_data_code' => 'perf_metrics_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 

        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'core_flow_invest_a19' , 'data_value' => 'Nett Capital Investment by Flow' , 'parent_data_code' => 'dp_float_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'ore_tot_disbursed_fa_a22' , 'data_value' => 'Total Float Injected into DP system' , 'parent_data_code' => 'dp_float_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'month_max_fa_os' , 'data_value' => 'Maximum Value of FAs Outstanding' , 'parent_data_code' => 'dp_float_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'core_tot_disbursed_fa_a22' , 'data_value' => 'Total Disbursed FA Amt' , 'parent_data_code' => 'dp_float_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 

        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'core_new_cust_comm' , 'data_value' => 'Customer Origination Commission' , 'parent_data_code' => 'dp_expense_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'core_repay_comm' , 'data_value' => 'Repayment Commission' , 'parent_data_code' => 'dp_expense_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'dp_commission_a31' , 'data_value' => 'Revenue for Data Prvdr from FLOW' , 'parent_data_code' => 'dp_expense_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'core_settled_flow_fee_a36' , 'data_value' => 'Total Fee Earned' , 'parent_data_code' => 'flow_revenue_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'fee_per_cust_a40' , 'data_value' => 'Fee Earned/Customer' , 'parent_data_code' => 'fin_metrics_unit_econ_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'fee_per_settled_fa_a42' , 'data_value' => 'Average Fee Earned/FA Completed' , 'parent_data_code' => 'fin_metrics_unit_econ_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'avg_return_per_amt_per_day_a44' , 'data_value' => 'Average Return/UGX Disbursed/Day' , 'parent_data_code' => 'fin_metrics_unit_econ_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'avg_margin_per_fa_a45' , 'data_value' => 'Average Margin Earned/FA Completed' , 'parent_data_code' => 'fin_metrics_unit_econ_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'avg_margin_per_amt_per_day_a47' , 'data_value' => 'Average Margin/UGX Disbursed/Day' , 'parent_data_code' => 'fin_metrics_unit_econ_section', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'margin_earned_a52' , 'data_value' => 'Total Margin Earned' , 'parent_data_code' => 'fin_metrics_overall', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'avg_return_per_amt_per_month_a54' , 'data_value' => 'Average Return on Advance Disbursed/Mon' , 'parent_data_code' => 'fin_metrics_overall', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'avg_margin_per_amt_per_month_a55' , 'data_value' => 'Average Margin on Advance Disbursed/Mon' , 'parent_data_code' => 'fin_metrics_overall', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'tot_roi_rev_based_a57' , 'data_value' => 'ROI based on Revenue' , 'parent_data_code' => 'fin_metrics_overall', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'tot_roi_margin_based_a59' , 'data_value' => 'ROI based on Margin Earned' , 'parent_data_code' => 'fin_metrics_overall', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'month_ret_on_max_fa_os' , 'data_value' => 'Return on Max Value of FAs Outstanding' , 'parent_data_code' => 'fin_metrics_overall', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'capt_mul_factor_a61' , 'data_value' => 'Capital Multiplying Factor' , 'parent_data_code' => 'fin_metrics_overall', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
       
       


        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_sections', 'data_code' => 'dp_expense_section' , 'data_value' => 'Fees/Commissions Paid to Data Provider' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_sections', 'data_code' => 'flow_revenue_section' , 'data_value' => 'Revenue Earned by Flow' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_sections', 'data_code' => 'fin_metrics_unit_econ_section' , 'data_value' => 'Financial Metrics / Unit Economics' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_sections', 'data_code' => 'fin_metrics_overall' , 'data_value' => 'Financial Metrics / Overall' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_sections', 'data_code' => 'cust_n_fa_section' , 'data_value' => 'Customers & Float Advances (FAs)' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_sections', 'data_code' => 'perf_metrics_section' , 'data_value' => 'Performance Metrics' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_sections', 'data_code' => 'curr_portfolio_snapshot' , 'data_value' => 'Current Portfolio Snapshot' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'cur_os_no_of_fa' , 'data_value' => 'Current OS FAs','parent_data_code' => 'curr_portfolio_snapshot', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'cur_os_fa' , 'data_value' => 'Current Outstanding Amt' , 'parent_data_code' => 'curr_portfolio_snapshot', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'], 
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_metrics', 'data_code' => 'cur_os_flow_fee' , 'data_value' => 'Current OS FAs' , 'parent_data_code' => 'Current Outstanding Fee', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],  
        ['country_code'=> '*', 'data_key' => 'float_kpi_rpt_sections', 'data_code' => 'dp_float_section' , 'data_value' => 'Float Injected by FLOW into DP System' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] 

    ]);
    }


}
