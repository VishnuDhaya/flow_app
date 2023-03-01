<?php

use Illuminate\Database\Seeder;
use App\Repositories\SQL\PersonRepositorySQL;

class AppUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $person_repo = new PersonRepositorySQL();

        $last_id = $person_repo->get_last_id();
        
         DB::table('persons')->insert([
              ['first_name' => 'Super','last_name'=> 'Admin','initials' => 'AM','dob' => '1992-09-19','gender' => 'MALE','whatsapp' => '9994870891','email_id' => 'sadmin@flowglobal.net','mobile_num' => '9994870891','phone_num' => '','designation' => 'User','country_code' => 'IND' ,'national_id'=> '']
          ]);

    	DB::table('app_users')->insert([
            
		 ['email' => 'sadmin@flowglobal.net','password' => bcrypt('password'),  'role_codes' => 'super_admin','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  "*", 'person_id' => ++$last_id]

      
	]);

       
    }
}
