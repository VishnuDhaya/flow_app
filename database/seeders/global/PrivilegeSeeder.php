<?php
//namespace \database\seeds;

use Illuminate\Database\Seeder;

class PrivilegeSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       
       DB::table('app_privileges')->truncate();
        DB::table('app_privileges')->insert([


				['priv_code' => 'data_key/list_view' ,'priv_name' => 'data_key/list_view' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'data_key/create_view_edit' ,'priv_name' => 'data_key/create_view_edit' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'master_data/list_view' ,'priv_name' => 'master_data/list_view' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'master_data/create_view_edit' ,'priv_name' => 'master_data/create_view_edit' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'addr_fields/create_view_edit_list' ,'priv_name' => 'addr_fields/create_view_edit_list' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],

        		['priv_code' => 'market/create_view_edit' ,'priv_name' => 'market/create_view_edit' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'market/list_view' ,'priv_name' => 'market/list_view' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'data_prvdr/create_view_edit' ,'priv_name' => 'data_prvdr/create_view_edit' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'data_prvdr/list_view' ,'priv_name' => 'data_prvdr/list_view' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'lender/create_view_edit' ,'priv_name' => 'lender/create_view_edit' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'lender/list_view' ,'priv_name' => 'lender/list_view' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'lender/create_view_edit' ,'priv_name' => 'lender/create_view_edit' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'lender/list_view' ,'priv_name' => 'lender/list_view' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'customer/create_view_edit' ,'priv_name' => 'customer/create_view_edit' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'customer/search_list_view' ,'priv_name' => 'customer/search_list_view' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'flow_rel_mgr/create_view_edit' ,'priv_name' => 'flow_rel_mgr/create_view_edit' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'flow_rel_mgr/list_view' ,'priv_name' => 'flow_rel_mgr/list_view' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'data_prvdr_rel_mgr/create_view_edit' ,'priv_name' => 'data_prvdr_rel_mgr/create_view_edit' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'data_prvdr_rel_mgr/list_view' ,'priv_name' => 'data_prvdr_rel_mgr/list_view' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'scoring_factor/create_view_edit' ,'priv_name' => 'scoring_factor/create_view_edit' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'scoring_factor/list_view' ,'priv_name' => 'scoring_factor/list_view' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'product/create_view_edit' ,'priv_name' => 'product/create_view_edit' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'product/list_view' ,'priv_name' => 'product/list_view' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'template/create_view_edit' ,'priv_name' => 'template/create_view_edit' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'template/list_view' ,'priv_name' => 'template/list_view' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'application/apply' ,'priv_name' => 'application/apply' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'application/view' ,'priv_name' => 'application/view' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'application/search' ,'priv_name' => 'application/search' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'loan/view' ,'priv_name' => 'loan/view' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'loan/view_approve' ,'priv_name' => 'loan/view_approve' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'loan/disburse' ,'priv_name' => 'loan/disburse' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'loan/penalize' ,'priv_name' => 'loan/penalize' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'loan/waive' ,'priv_name' => 'loan/waive' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'loan/capture_repayment' ,'priv_name' => 'loan/capture_repayment' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'loan/search_list_view' ,'priv_name' => 'loan/search_list_view' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],

				['priv_code' => 'email_report/daily_due_loans' ,'priv_name' => 'email_report/daily_due_loans' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'report/flow_kpi_dashboard' ,'priv_name' => 'report/flow_kpi_dashboard' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'report/portfolio_quality' ,'priv_name' => 'report/portfolio_quality' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'report/growth_chart' ,'priv_name' => 'report/growth_chart' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'report/product_takeup' ,'priv_name' => 'report/product_takeup' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed'],
				['priv_code' => 'report/daily_activity' ,'priv_name' => 'report/daily_activity' ,'desc' => '', 'status' => 'enabled', 'created_by' => 'seed']





				]);

		
		
	}
}