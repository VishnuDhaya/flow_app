<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertCommOnlyModelFaPerformanceModel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $country_code = 'UGA';
		$created_at = datetime_db();

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
			
			['country_code' => $country_code, 'csf_group' => 'avg_comms', 'csf_type' => 'monthly_comms', 'value_from' => 0, 'value_to' => 80000, 'normal_value' => 0, 'status' => 'enabled', 'created_at' => $created_at],
			['country_code' => $country_code, 'csf_group' => 'avg_comms', 'csf_type' => 'monthly_comms', 'value_from' => 80000, 'value_to' => 999999999, 'normal_value' => 10, 'status' => 'enabled', 'created_at' => $created_at],
		]);

		DB::table('score_models')->insert([
			['country_code' => $country_code, 'model_name' => 'FA Performance Model', 'model_code' => 'fa_performance_model', 'created_at' => $created_at],
			['country_code' => $country_code, 'model_name' => 'Comm Only Model', 'model_code' => 'comm_only_model', 'created_at' => $created_at],
		]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
