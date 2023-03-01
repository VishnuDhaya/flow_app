<?php

use Illuminate\Database\Seeder;


class IND_AppUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run($country_code)
    {
        
    	DB::table('app_users')->insert([
		['email' => 'madmin_ind@flowglobal.net','password' => bcrypt('password'),  'role_codes' => 'market_admin','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLW', 'status' => 'enabled', 'country_code' => 'IND', 'person_id' => '2'],
		['email' => 'applier_ind@flowglobal.net','password' => bcrypt('password') ,'role_codes' => 'loan_applier','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLW', 'status' => 'enabled', 'country_code' => 'IND', 'person_id' => '3'],
		['email' => 'approver_ind@flowglobal.net','password' => bcrypt('password'), 'role_codes' => 'loan_approver','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLW', 'status' => 'enabled', 'country_code' => 'IND', 'person_id' => '4'],
        ['email' => 'transactor_ind@flowglobal.net','password' => bcrypt('password'), 'role_codes' => 'transactor','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLW', 'status' => 'enabled', 'country_code' => 'IND', 'person_id' => '5'],
	]);
       
    }
}
