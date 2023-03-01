<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class RWA_AppUserSeeder extends Seeder
{
	/**
	 * Run the fieldbase seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$country_code = 'RWA';
        DB::table('app_users')->insert([

        ['email' => 'geoffrey+rwa@flowglobal.net','password' => bcrypt('G#0ff#rY!Rw@'), 'role_codes' => 'market_admin','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' => $country_code, 'person_id' => 13],

		['email' => 'daisy+rwa@flowglobal.net','password' => bcrypt('D@!$yRw*'), 'role_codes' => 'operations_auditor','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' => $country_code, 'person_id' => 2438],

		['email' => 'kevina+rwa@flowglobal.net','password' => bcrypt('K#v!n@Rw$'), 'role_codes' => 'operations_auditor','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' => $country_code, 'person_id' => 2370]

        ]);
    }
}