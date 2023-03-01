<?php

use Illuminate\Database\Seeder;
use App\Repositories\SQL\PersonRepositorySQL;


class UGA_AppUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */ 
    public function run()
    {
        $country_code =  'UGA';

        $person_repo = new PersonRepositorySQL();

        $last_id = $person_repo->get_last_id();


        DB::table('persons')->insert([
     
        ['first_name' => 'NITIN','last_name'=> 'GARG','initials' => 'MR','dob' => '1997-08-10','gender' => 'MALE','whatsapp' => '919650088114','email_id' => 'nitin@consultcolors.com','mobile_num' => '919650088114','phone_num' => '','designation' => 'COFOUNDER','country_code' =>  $country_code,'national_id'=> 'Z4322368'],


        ['first_name' => 'NITIN','last_name'=> 'GARG','initials' => 'MR','dob' => '1997-08-10','gender' => 'MALE','whatsapp' => '919650088114','email_id' => 'nitin@flowglobal.net','mobile_num' => '919650088114','phone_num' => '','designation' => 'COFOUNDER','country_code' =>  $country_code,'national_id'=> 'Z4322368'],

        ['first_name' => 'Michael','last_name'=> 'Rothe','initials' => 'MR','dob' => '1984-04-30','gender' => 'MALE','whatsapp' => '447478094933','email_id' => 'michael@consultcolors.com','mobile_num' => '447478094933','phone_num' => '','designation' => 'COFOUNDER','country_code' =>  $country_code,'national_id'=> ''],

         ['first_name' => 'Michael','last_name'=> 'Rothe','initials' => 'MR','dob' => '1984-04-30','gender' => 'MALE','whatsapp' => '447478094933','email_id' => 'michael@flowglobal.net','mobile_num' => '447478094933','phone_num' => '','designation' => 'COFOUNDER','country_code' =>  $country_code,'national_id'=> ''],

        ['first_name' => 'Geoffrey','last_name'=> 'Acini','initials' => 'AG','dob' => '1992-09-17','gender' => 'MALE','whatsapp' => '772656752','email_id' => 'geoffrey@flowglobal.net','mobile_num' => '772656752','phone_num' => '703463210','designation' => 'Country Representative','country_code' =>  $country_code,'national_id'=> 'CM92029101CRHA'],

        ['first_name' => 'Market','last_name'=> 'Admin','initials' => 'AM','dob' => '1992-09-19','gender' => 'MALE','whatsapp' => '9994870891','email_id' => 'madmin@flowglobal.net','mobile_num' => '9994870891','phone_num' => '','designation' => 'User','country_code' =>  $country_code,'national_id'=> ''],

        ['first_name' => 'Applier','last_name'=> 'Applier','initials' => 'AN','dob' => '1992-09-18','gender' => 'MALE','whatsapp' => '8884870892','email_id' => 'applier@flowglobal.net','mobile_num' => '8884870892','phone_num' => '','designation' => 'User','country_code' =>  $country_code,'national_id'=> ''],

        ['first_name' => 'Approver','last_name'=> 'Approver','initials' => 'AC','dob' => '1992-09-20','gender' => 'MALE','whatsapp' => '7774870893','email_id' => 'approver@flowglobal.net','mobile_num' => '7774870893','phone_num' => '','designation' => 'User','country_code' =>  $country_code,'national_id'=> ''],

        ['first_name' => 'Transactor','last_name'=> 'Transactor','initials' => 'AP','dob' => '1992-09-21','gender' => 'MALE','whatsapp' => '6674870899','email_id' => 'transactor@flowglobal.net','mobile_num' => '6674870899','phone_num' => '','designation' => 'User','country_code' =>  $country_code,'national_id'=> '']
         ]);  

     
 	DB::table('app_users')->insert([

        ['email' => 'nitin@consultcolors.com','password' => bcrypt('password'), 'role_codes' => 'super_admin','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  "*", 'person_id' => ++$last_id],

         ['email' => 'nitin@flowglobal.net','password' => bcrypt('password'), 'role_codes' => 'market_admin','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],


        ['email' => 'michael@consultcolors.com','password' => bcrypt('password'), 'role_codes' => 'super_admin','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

        ['email' => 'michael@flowglobal.net','password' => bcrypt('password'), 'role_codes' => 'market_admin','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],


        ['email' => 'geoffrey@flowglobal.net','password' => bcrypt('password'), 'role_codes' => 'loan_applier,loan_approver,transactor','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],


        ['email' => 'madmin@flowglobal.net','password' => bcrypt('password'),  'role_codes' => 'market_admin','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

        ['email' => 'applier@flowglobal.net','password' => bcrypt('password') ,'role_codes' => 'loan_applier','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

        ['email' => 'approver@flowglobal.net','password' => bcrypt('password'), 'role_codes' => 'loan_approver','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

        ['email' => 'transactor@flowglobal.net','password' => bcrypt('password'), 'role_codes' => 'transactor','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id]



	 ]);

     
     
       
    }
}
