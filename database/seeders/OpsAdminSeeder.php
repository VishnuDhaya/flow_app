<?php

use Illuminate\Database\Seeder;
use App\Repositories\SQL\PersonRepositorySQL;

class OpsAdminSeeder extends Seeder
{
    
    public function run()
    {
        $country_code =  'UGA';
    	session()->put('country_code','UGA');
		 
        DB::table('app_users')->insert([

        ['email' => 'praveen@flowglobal.net','password' => bcrypt('PR@^een@06'), 'role_codes' => 'ops_admin','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => 2443]
        ]);
    }
}