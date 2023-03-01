<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class RWA_MasterDataSeeder extends Seeder
{
	/**
	 * Run the fieldbase seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$country_code = 'RWA';

		DB::table('master_data_keys')->insert([

			['country_code' => $country_code, 'data_key' => 'csf_group', 'parent_data_key' => NULL,  'data_type' => 'common', 'data_group' => 'credit_score', 'status' => 'enabled', 'created_by' => '0', 'created_at' => now()],
			['country_code' => $country_code, 'data_key' => 'csf_type', 'parent_data_key' => 'csf_group',  'data_type' => 'common', 'data_group' => 'credit_score', 'status' => 'enabled', 'created_by' => '0', 'created_at' => now()],
			['country_code' => $country_code, 'data_key' => 'csf_model', 'parent_data_key' => NULL,  'data_type' => 'common', 'data_group' => 'credit_score', 'status' => 'enabled', 'created_by' => '0', 'created_at' => now()],
		]);


		DB::table('master_data')->insert([

			['country_code' => $country_code, 'data_key' => 'csf_group', 'data_code' => 'meta_data', 'data_value' => 'Meta Data',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_by' => '0', 'created_at' => now()],

			['country_code' => $country_code, 'data_key' => 'csf_group', 'data_code' => 'past_performance', 'data_value' => 'Past Performance',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_by' => '0', 'created_at' => now()],

			['country_code' => $country_code, 'data_key' => 'csf_group', 'data_code' => 'cust_retail_txns', 'data_value' => 'Customer Retail Txns',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_by' => '0', 'created_at' => now()],

			['country_code' => $country_code, 'data_key' => 'csf_type', 'data_value' => 'Percent of Ontime FA Repayments', 'data_code' => 'ontime_loans_pc',  'parent_data_code' => 'past_performance',   'status' =>  'enabled', 'data_type'  => 'common', 'created_by' => '0', 'created_at' => now()],
			['country_code' => $country_code, 'data_key' => 'csf_type', 'data_value' => 'Percent of FAs Repaid After 3 day Delay', 'data_code' => 'repaid_after_3_days_pc',  'parent_data_code' => 'past_performance',   'status' =>  'enabled', 'data_type'  => 'common', 'created_by' => '0', 'created_at' => now()],
			['country_code' => $country_code, 'data_key' => 'csf_type', 'data_value' => 'Number of Advances Till Now', 'data_code' => 'number_of_advances_till_now',  'parent_data_code' => 'past_performance',   'status' =>  'enabled', 'data_type'  => 'common', 'created_by' => '0', 'created_at' => now()],
			['country_code' => $country_code, 'data_key' => 'csf_type', 'data_value' => 'Number of Advances Per Quarter', 'data_code' => 'number_of_advances_per_quarter',  'parent_data_code' => 'past_performance',   'status' =>  'enabled', 'data_type'  => 'common', 'created_by' => '0', 'created_at' => now()],

			['country_code' =>  $country_code, "data_key" => "csf_type", "data_code" => "5_day_avg_roi", "data_value" => "5 Day Avg ROI", 'status' => 'enabled',   'parent_data_code' => 'roi', 'data_type'  => 'common', 'created_by' => '0', 'created_at' => now()],
			['country_code' =>  $country_code, "data_key" => "csf_type", "data_code" => "15_day_avg_roi", "data_value" => "15 Day Avg ROI", 'status' => 'enabled',   'parent_data_code' => 'roi', 'data_type'  => 'common', 'created_by' => '0', 'created_at' => now()],
			['country_code' =>  $country_code, "data_key" => "csf_type", "data_code" => "30_day_avg_roi", "data_value" => "30 Day Avg ROI", 'status' => 'enabled',   'parent_data_code' => 'roi', 'data_type'  => 'common', 'created_by' => '0', 'created_at' => now()],
			['country_code' =>  $country_code, "data_key" => "csf_type", "data_code" => "30_day_avg_txns", "data_value" => "30 Day Avg Txns", 'status' => 'enabled',   'parent_data_code' => 'avg_txns', 'data_type'  => 'common', 'created_by' => '0', 'created_at' => now()],

			['country_code' =>  $country_code, "data_key" => "csf_type", "data_code" => "meta_txn_start_date", "data_value" => "Meta Txn Start Date", 'status' => 'enabled',   'parent_data_code' => 'meta_data', 'data_type'  => 'common', 'created_by' => '0', 'created_at' => now()],
			['country_code' =>  $country_code, "data_key" => "csf_type", "data_code" => "meta_txn_end_date", "data_value" => "Meta Txn End Date", 'status' => 'enabled',   'parent_data_code' => 'meta_data', 'data_type'  => 'common', 'created_by' => '0', 'created_at' => now()],
			['country_code' =>  $country_code, "data_key" => "csf_type", "data_code" => "meta_txn_days", "data_value" => "Meta Txn Days", 'status' => 'enabled',   'parent_data_code' => 'meta_data', 'data_type'  => 'common', 'created_by' => '0', 'created_at' => now()],
			['country_code' =>  $country_code, "data_key" => "csf_type", "data_code" => "meta_cal_days", "data_value" => "Meta Cal Days", 'status' => 'enabled',   'parent_data_code' => 'meta_data', 'data_type'  => 'common', 'created_by' => '0', 'created_at' => now()],
		]);
	}
}
