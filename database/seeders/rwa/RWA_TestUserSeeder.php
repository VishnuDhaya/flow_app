<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class RWA_TestUserSeeder extends Seeder
{
	/**
	 * Run the fieldbase seeds.
	 *
	 * @return void
	 */
	public function run()
	{
        $country_code = 'RWA';
        // $rm_person_id = DB::table('persons')->insertGetId(
        //     ['first_name' => 'Vivianne', 'last_name'=> 'Kamikazi', 'gender' => 'female', 'whatsapp' => '785285019','email_id' => 'vivianne@flowglobal.net', 'national_id' => '', 'mobile_num' => '785285019', 'designation' => 'Relationship Manager','country_code' =>  $country_code, 'associated_with' => 'FLOW'],
        // );

        $cs_person_id = DB::table('persons')->insertGetId(
            ['first_name' => 'Joana', 'last_name'=> 'Manzi', 'gender' => 'female', 'whatsapp' => '785285019','email_id' => 'joana.m@inbox.flowglobal.net', 'national_id' => '1199670055164204', 'mobile_num' => '788314783', 'designation' => 'Customer Success','country_code' =>  $country_code, 'associated_with' => 'FLOW'],

        );

        // $oa_person_id = DB::table('persons')->insertGetId(
        //     ['first_name' => 'Felix ', 'last_name'=> 'Povel', 'gender' => 'male','whatsapp' => '00250787289025','email_id' => 'felix.oa@flowglobal.net','mobile_num' => '00250787289025', 'national_id' => 'C4J6R6VTZ', 'designation' => 'Operations Auditor','country_code' =>  $country_code, 'associated_with' => 'FLOW'],
        // );

        // $opm_id = DB::table('persons')->insertGetId(
        //     ['first_name' => 'Felix ', 'last_name'=> 'Povel', 'gender' => 'male','whatsapp' => '00250787289025','email_id' => 'felix@flowglobal.net','mobile_num' => '00250787289025', 'national_id' => 'C4J6R6VTZ', 'designation' => 'COO','country_code' =>  $country_code, 'associated_with' => 'FLOW']
        // );

        

		DB::table('app_users')->insert([

			// ['email' => 'vivianne@flowglobal.net','password' => bcrypt('v!V!@N*E#RM'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => $rm_person_id],
	
			['email' => 'joana.m@inbox.flowglobal.net','password' => bcrypt('M@n$!CS'), 'role_codes' => 'customer_success','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => $cs_person_id],

			// ['email' => 'felix.oa@flowglobal.net','password' => bcrypt('F#L!x%0A'), 'role_codes' => 'operations_auditor','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => $oa_person_id],
			
			// ['email' => 'felix@flowglobal.net','password' => bcrypt('f#e1!x=^'), 'role_codes' => 'ops_admin','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' => $country_code, 'person_id' => $opm_id],
	
		]);
    }
}