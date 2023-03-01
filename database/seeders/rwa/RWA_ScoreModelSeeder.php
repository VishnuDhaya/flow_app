<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class RWA_ScoreModelSeeder extends Seeder
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

		DB::table('score_models')->insert([
			['country_code' => $country_code, 'model_name' => 'FA Performance Model', 'model_code' => 'fa_performance_model', 'created_at' => $created_at],
			['country_code' => $country_code, 'model_name' => 'Comm Only Model', 'model_code' => 'comm_only_model', 'created_at' => $created_at],
		]);
	}
}
