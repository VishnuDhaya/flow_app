<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class RWA_AddressConfigSeeder extends Seeder
{
	/**
	 * Run the fieldbase seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$country_code = 'RWA';

		DB::table('addr_config')->insert([
			['country_code' => $country_code, 'field_num' => 'field_1', 'field_code' => 'province', 'field_name' => 'Province', 'status' => 'enabled', 'child_field_code' => 'district', 'field_type' => 'select', 'validation_rules' => 'required'],
			['country_code' => $country_code, 'field_num' => 'field_2', 'field_code' => 'district', 'field_name' => 'District', 'status' => 'enabled', 'child_field_code' => 'sector', 'field_type' => 'select', 'validation_rules' => 'required'],
			['country_code' => $country_code, 'field_num' => 'field_3', 'field_code' => 'sector', 'field_name' => 'Sector', 'status' => 'enabled', 'child_field_code' => null, 'field_type' => 'select', 'validation_rules' => 'required'],
			['country_code' => $country_code, 'field_num' => 'field_4', 'field_code' => 'cell', 'field_name' => 'Cell', 'status' => 'enabled', 'child_field_code' => null, 'field_type' => 'text', 'validation_rules' => 'required'],
			['country_code' => $country_code, 'field_num' => 'field_5', 'field_code' => 'village', 'field_name' => 'Village', 'status' => 'enabled', 'child_field_code' => null, 'field_type' => 'text', 'validation_rules' => 'required'],
			['country_code' => $country_code, 'field_num' => 'field_6', 'field_code' => 'location', 'field_name' => 'Location', 'status' => 'enabled', 'child_field_code' => null, 'field_type' => 'select', 'validation_rules' => 'required'],
			['country_code' => $country_code, 'field_num' => 'field_7', 'field_code' => 'gps', 'field_name' => 'GPS', 'status' => 'enabled', 'child_field_code' => null, 'field_type' => 'text', 'validation_rules' => 'required'],
			['country_code' => $country_code, 'field_num' => 'field_8', 'field_code' => 'territory', 'field_name' => 'Territory', 'status' => 'enabled', 'child_field_code' => null, 'field_type' => 'select', 'validation_rules' => 'required'],

			// land mark, plot number

		]);
	}
}
