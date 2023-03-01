<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class RWA_ScoreFactorsSeeder extends Seeder
{
	/**
	 * Run the fieldbase seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$country_code = 'RWA';
		$created_at = datetime_db();

		DB::delete("delete from cs_model_weightages where country_code ='$country_code'");
		DB::delete("delete from cs_factor_values where country_code ='$country_code'");
		DB::delete("delete from cs_result_config where country_code ='$country_code'");

		DB::table('cs_model_weightages')->insert([
			// Comms Only Model
			['country_code' => $country_code, 'cs_model_code' => 'comm_only_model', 'csf_type' => 'monthly_comms', 'new_cust_weightage' => 100, 'repeat_cust_weightage' => 0, 'created_at' => $created_at],

			// FA Performance Model
			['country_code' => $country_code, 'cs_model_code' => 'fa_performance_model', 'csf_type' => 'ontime_loans_pc', 'new_cust_weightage' => 0, 'repeat_cust_weightage' => 40, 'created_at' => $created_at],
			['country_code' => $country_code, 'cs_model_code' => 'fa_performance_model', 'csf_type' => 'repaid_after_3_days_pc', 'new_cust_weightage' => 0, 'repeat_cust_weightage' => 20, 'created_at' => $created_at],
			['country_code' => $country_code, 'cs_model_code' => 'fa_performance_model', 'csf_type' => 'repaid_after_30_days_pc', 'new_cust_weightage' => 0, 'repeat_cust_weightage' => 20, 'created_at' => $created_at],
			['country_code' => $country_code, 'cs_model_code' => 'fa_performance_model', 'csf_type' => 'repaid_after_10_days_pc', 'new_cust_weightage' => 0, 'repeat_cust_weightage' => 20, 'created_at' => $created_at],
		]);

		DB::table('cs_result_config')->insert([
			// Comms Only Model
			['country_code' => $country_code, 'csf_model' => 'comm_only_model', 'score_result_code' => 'ineligible', 'score_from' => 0, 'score_to' => 1, 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_model' => 'comm_only_model', 'score_result_code' => 'requires_flow_rm_approval', 'score_from' => 2, 'score_to' => 3, 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_model' => 'comm_only_model', 'score_result_code' => 'eligible', 'score_from' => 4, 'score_to' => 1000, 'created_at' => $created_at],

			// FA Performance Model
			['country_code' => $country_code, 'csf_model' => 'fa_performance_model', 'score_result_code' => 'ineligible', 'score_from' => 0, 'score_to' => 650, 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_model' => 'fa_performance_model', 'score_result_code' => 'requires_flow_rm_approval', 'score_from' => 651, 'score_to' => 800, 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_model' => 'fa_performance_model', 'score_result_code' => 'eligible', 'score_from' => 801, 'score_to' => 1000, 'created_at' => $created_at],
		]);
		DB::table('cs_factor_values')->insert([
			
			['country_code' => $country_code, 'csf_group' => 'avg_comms', 'csf_type' => 'monthly_comms', 'value_from' => 0, 'value_to' => 34999, 'normal_value' => 0, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'avg_comms', 'csf_type' => 'monthly_comms', 'value_from' => 35000, 'value_to' => 999999999, 'normal_value' => 10, 'status' => 'enabled', 'created_at' => $created_at],

			
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'ontime_loans_pc', 'value_from' => 0, 'value_to' => 50, 'normal_value' => 2, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'ontime_loans_pc', 'value_from' => 50, 'value_to' => 75, 'normal_value' => 4, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'ontime_loans_pc', 'value_from' => 75, 'value_to' => 85, 'normal_value' => 6, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'ontime_loans_pc', 'value_from' => 85, 'value_to' => 95, 'normal_value' => 8, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'ontime_loans_pc', 'value_from' => 95, 'value_to' => 100, 'normal_value' => 10, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_3_days_pc', 'value_from' => 25, 'value_to' => 100, 'normal_value' => 2, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_3_days_pc', 'value_from' => 20, 'value_to' => 25, 'normal_value' => 4, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_3_days_pc', 'value_from' => 15, 'value_to' => 20, 'normal_value' => 6, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_3_days_pc', 'value_from' => 10, 'value_to' => 15, 'normal_value' => 8, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_3_days_pc', 'value_from' => 0, 'value_to' => 10, 'normal_value' => 10, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'number_of_advances_till_now', 'value_from' => 0, 'value_to' => 10, 'normal_value' => 2, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'number_of_advances_till_now', 'value_from' => 10, 'value_to' => 25, 'normal_value' => 4, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'number_of_advances_till_now', 'value_from' => 25, 'value_to' => 35, 'normal_value' => 6, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'number_of_advances_till_now', 'value_from' => 35, 'value_to' => 50, 'normal_value' => 8, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'number_of_advances_till_now', 'value_from' => 50, 'value_to' => 999999, 'normal_value' => 10, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'number_of_advances_per_quarter', 'value_from' => 0, 'value_to' => 3, 'normal_value' => 2, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'number_of_advances_per_quarter', 'value_from' => 3, 'value_to' => 6, 'normal_value' => 4, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'number_of_advances_per_quarter', 'value_from' => 6, 'value_to' => 9, 'normal_value' => 6, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'number_of_advances_per_quarter', 'value_from' => 9, 'value_to' => 12, 'normal_value' => 8, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'number_of_advances_per_quarter', 'value_from' => 12, 'value_to' => 999999, 'normal_value' => 10, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_10_days_pc', 'value_from' => 25, 'value_to' => 100, 'normal_value' => 2, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_10_days_pc', 'value_from' => 15, 'value_to' => 25, 'normal_value' => 4, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_10_days_pc', 'value_from' => 10, 'value_to' => 15, 'normal_value' => 6, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_10_days_pc', 'value_from' => 5, 'value_to' => 10, 'normal_value' => 8, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_10_days_pc', 'value_from' => 0, 'value_to' => 5, 'normal_value' => 10, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_30_days_pc', 'value_from' => 20, 'value_to' => 100, 'normal_value' => 2, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_30_days_pc', 'value_from' => 15, 'value_to' => 25, 'normal_value' => 4, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_30_days_pc', 'value_from' => 10, 'value_to' => 15, 'normal_value' => 6, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_30_days_pc', 'value_from' => 5, 'value_to' => 10, 'normal_value' => 8, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_30_days_pc', 'value_from' => 0, 'value_to' => 5, 'normal_value' => 10, 'status' => 'enabled', 'created_at' => $created_at]
		]);
	}
}
