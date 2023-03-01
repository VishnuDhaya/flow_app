<?php

use Illuminate\Database\Seeder;
 use Illuminate\Support\Facades\Log;
use App\Repositories\SQL\PersonRepositorySQL;

class UpdateUserPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

    	$country_code =  'UGA';
    	session()->put('country_code','UGA');
		$person_repo = new PersonRepositorySQL();
    	$last_id = $person_repo->get_last_id();
    	Log::warning('$last_id');

    	Log::warning($last_id);

    	DB::update("UPDATE app_users SET password = '".bcrypt('Nitin@2020')."',role_codes ='super_admin',country_code = '*' WHERE email='nitin@flowglobal.net'");
        
        DB::update("UPDATE app_users SET password = '".bcrypt('Mich@el@2020')."' WHERE email='michael@flowglobal.net'");

        DB::update("UPDATE app_users SET password = '".bcrypt('Geoffrey@2020')."',role_codes ='market_admin' WHERE email='geoffrey@flowglobal.net'");

        DB::update("UPDATE app_users SET password = '".bcrypt('Justine@2020')."',role_codes ='relationship_manager' WHERE email='justine@flowglobal.net'");


		
        
        DB::table('persons')->insert([
     
        ['first_name' => 'TECH','last_name'=> 'DEV','initials' => 'MR','dob' => null,'gender' => 'MALE','whatsapp' => '','email_id' => 'techdev@flowglobal.net','mobile_num' => '','phone_num' => '','designation' => 'TECH','country_code' =>  $country_code,'national_id'=> 'Z4322368'],

        ['first_name' => 'SHIELA','last_name'=> 'SHIELA','initials' => 'MS','dob' => null,'gender' => 'FEMALE','whatsapp' => '','email_id' => 'shiela@flowglobal.net','mobile_num' => '','phone_num' => '','designation' => 'USER','country_code' =>  $country_code,'national_id'=> 'Z4322368'],


        ['first_name' => 'FAITH','last_name'=> 'FAITH','initials' => 'MR','dob' => null,'gender' => 'MALE','whatsapp' => '','email_id' => 'faith@flowglobal.net','mobile_num' => '','phone_num' => '','designation' => 'USER','country_code' =>  $country_code,'national_id'=> 'Z4322368'],


        ['first_name' => 'CAROL','last_name'=> 'CAROL','initials' => 'MS','dob' => null,'gender' => 'MALE','whatsapp' => '','email_id' => 'carol@flowglobal.net','mobile_num' => '','phone_num' => '','designation' => 'USER','country_code' =>  $country_code,'national_id'=> 'Z4322368'],

        ['first_name' => 'ARNOLD','last_name'=> 'ARNOLD','initials' => 'MR','dob' => null,'gender' => 'MALE','whatsapp' => '','email_id' => 'arnold@flowglobal.net','mobile_num' => '','phone_num' => '','designation' => 'USER','country_code' =>  $country_code,'national_id'=> 'Z4322368'],

        ['first_name' => 'MUGISHA','last_name'=> 'MUGIHA','initials' => 'MS','dob' => null,'gender' => 'FEMALE','whatsapp' => '','email_id' => 'mugisha@flowglobal.net','mobile_num' => '','phone_num' => '','designation' => 'USER','country_code' =>  $country_code,'national_id'=> 'Z4322368']
    ]);


    	DB::table('app_users')->insert([

        ['email' => 'techdev@flowglobal.net','password' => bcrypt('TechDev@2020'), 'role_codes' => 'super_admin','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  "*", 'person_id' => ++$last_id],

        
        ['email' => 'Shiela@flowglobal.net','password' => bcrypt('Shiel@@2020'), 'role_codes' => 'level1_admin','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],
        
        ['email' => 'faith@flowglobal.net','password' => bcrypt('F@ith@2020'), 'role_codes' => 'level1_admin','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],
        
        ['email' => 'carol@flowglobal.net','password' => bcrypt('C@rol@2020'), 'role_codes' => 'level1_admin','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],
        
        ['email' => 'arnold@flowglobal.net','password' => bcrypt('@rnold@2020'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

        ['email' => 'mugisha@flowglobal.net','password' => bcrypt('Mugish@@2020'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id]

    ]);

    	$password = bcrypt('Flow@glob@l');
		DB::update("UPDATE app_users SET password = '$password' WHERE email='transactor@flowglobal.net'");
        DB::update("UPDATE app_users SET password = '$password' WHERE email='applier@flowglobal.net'");
        DB::update("UPDATE app_users SET password = '$password' WHERE email='madmin@flowglobal.net'");
        DB::update("UPDATE app_users SET password = '$password' WHERE email='michael@consultcolors.com'");
        DB::update("UPDATE app_users SET password = '$password' WHERE email='nitin@consultcolors.com'");
        
        $password = bcrypt('N!t!n#cc');
        DB::update("UPDATE app_users SET status = 'disabled', password = '$password' WHERE email='nitin@consultcolors.com'");
        
        $password = bcrypt('M!ch@el#cc');
        DB::update("UPDATE app_users SET status = 'disabled', password = '$password' WHERE email='michael@consultcolors.com'");
        
        $password = bcrypt('N!t!n#fg');
        DB::update("UPDATE app_users SET status = 'disabled', password = '$password' WHERE email='nitin@flowglobal.net'");
        
        $password = bcrypt('M!ch@el#fg1');
        DB::update("UPDATE app_users SET password = '$password' WHERE email='michael@flowglobal.net'");
        DB::update("UPDATE app_users SET role_codes ='super_admin' WHERE email='michael@flowglobal.net'");
        
        $password = bcrypt('TechDEC@$56');
        DB::update("UPDATE app_users SET password = '$password' WHERE email='techdev@flowglobal.net'");
       
        
       
        $password = bcrypt('Geoffre^#7');
        DB::update("UPDATE app_users SET password = '$password' WHERE email='geoffrey@flowglobal.net'");
        
        $password = bcrypt('Ju$t!ne#6');
        DB::update("UPDATE app_users SET password = '$password' WHERE email='justine@flowglobal.net'");
        
        $password = bcrypt('@rn01d#5');
        DB::update("UPDATE app_users SET password = '$password' WHERE email='arnold@flowglobal.net'");
        
        $password = bcrypt('Mug!$h@#6');
        DB::update("UPDATE app_users SET password = '$password' WHERE email='mugisha@flowglobal.net'");
        
        $password = bcrypt('F@!th#4');
        DB::update("UPDATE app_users SET role_codes ='customer_success', password = '$password' WHERE email='faith@flowglobal.net'");
        
        $password = bcrypt('C@r01#4');
        DB::update("UPDATE app_users SET role_codes ='customer_success', password = '$password' WHERE email='carol@flowglobal.net'");
        
        $password = bcrypt('She!1a#5');
        DB::update("UPDATE app_users SET role_codes ='customer_success', password = '$password' WHERE email='sheila@flowglobal.net'");
        
        
        $password = bcrypt('Re@d0n1y');
        DB::update("UPDATE app_users SET password = '$password' WHERE email='read_only@consultcolors.com'");
        
        $password = bcrypt('!nvest0r#7');
        DB::update("UPDATE app_users SET password = '$password' WHERE email='investor@consultcolors.com'");
        
       	DB::table('app_users')->insert([['email' => 'tom@flowglobal.net','password' => bcrypt('T@m$56Flow'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  "UGA", 'person_id' => 0]    ]);
        
        $country_code =  'UGA';
    	session()->put('country_code','UGA');
		$person_repo = new PersonRepositorySQL();
    	$last_id = $person_repo->get_last_id();
    	
         DB::table('persons')->insert([
     
        ['first_name' => 'Anifred','last_name'=> 'Nabaasa','initials' => 'MS','dob' => null,'gender' => 'FEMALE','whatsapp' => '772027482','email_id' => 'anifred@flowglobal.net','mobile_num' => '772027482','phone_num' => '','designation' => 'Partnership Manager','country_code' =>  $country_code],

        ['first_name' => 'Asea','last_name'=> 'Oliver','initials' => 'MS','dob' => null,'gender' => 'FEMALE','whatsapp' => '774504507','email_id' => 'oliver@flowglobal.net','mobile_num' => '774504507','phone_num' => '','designation' => 'Customer Success','country_code' =>  $country_code],

        ['first_name' => 'Ssendagire','last_name'=> 'John','initials' => 'MR','dob' => null,'gender' => 'MALE','whatsapp' => '785157870','email_id' => 'john@flowglobal.net','mobile_num' => '785157870','phone_num' => '','designation' => 'Rel. Manager','country_code' =>  $country_code],

         ]);
         
         
         DB::table('app_users')->insert([

        ['email' => 'anifred@flowglobal.net','password' => bcrypt('@n!fr#d@2020'), 'role_codes' => 'partnership_manager','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],
        ['email' => 'oliver@flowglobal.net','password' => bcrypt('01!v#r@2020'), 'role_codes' => 'customer_success','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],
        ['email' => 'john@flowglobal.net','password' => bcrypt('J0hn!@2020'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' => $country_code, 'person_id' => ++$last_id],
        ]);        
         
         
          DB::table('app_users')->insert([

        ['email' => 'jacqueline@flowglobal.net','password' => bcrypt('j@c9u1!ne@2021'), 
        'role_codes' => 'customer_success','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 
        'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => 2204 ]]);
        
         DB::table('app_users')->insert([
        ['email' => 'sikyomu@flowglobal.net','password' => bcrypt('H#n@ry@21'), 
        'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 
        'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => 2427 ]]);
        
         DB::table('app_users')->insert([
        ['email' => 'stephen@flowglobal.net','password' => bcrypt('$t#ph#n@21'), 
        'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 
        'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => 2428 ]]);
        
       

	}
}
