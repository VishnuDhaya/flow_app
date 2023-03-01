<?php

use Illuminate\Database\Seeder;
use App\Repositories\SQL\PersonRepositorySQL;

class TestSeeder extends Seeder
{
    
    public function run()
    {
        $country_code =  'UGA';
    	session()->put('country_code','UGA');
		$person_repo = new PersonRepositorySQL();
    	$last_id = $person_repo->get_last_id();
    	
         DB::table('persons')->insert([
     
        ['first_name' => 'Eben','last_name'=> 'Paul','initials' => 'S','dob' => null,'gender' => 'MALE','whatsapp' => '907466540','email_id' => 'eben@flowglobal.net','mobile_num' => '907466540','phone_num' => '','designation' => 'Customer Success','country_code' =>  $country_code],

        ['first_name' => 'Ebran','last_name'=> 'Bright','initials' => 'B','dob' => null,'gender' => 'MALE','whatsapp' => '790438196','email_id' => 'ebran@flowglobal.net','mobile_num' => '790438196','phone_num' => '','designation' => 'Customer Success','country_code' =>  $country_code],

        ['first_name' => 'Vino','last_name'=> 'Shankar','initials' => 'SA','dob' => null,'gender' => 'MALE','whatsapp' => '882548341','email_id' => 'vino@flowglobal.net','mobile_num' => '882548341','phone_num' => '','designation' => 'Customer Success','country_code' =>  $country_code],
        
        ['first_name' => 'Uma','last_name'=> 'Maheswaran','initials' => 'D','dob' => null,'gender' => 'MALE','whatsapp' => '984015673','email_id' => 'uma@flowglobal.net','mobile_num' => '9840156732','phone_num' => '','designation' => 'Customer Success','country_code' =>  $country_code],

         ]);
         
         
         DB::table('app_users')->insert([

        ['email' => 'eben@flowglobal.net','password' => bcrypt('E#e#@2021'), 'role_codes' => 'customer_success','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],
        ['email' => 'ebran@flowglobal.net','password' => bcrypt('E%r@n@2021'), 'role_codes' => 'customer_success','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],
        ['email' => 'vino@flowglobal.net','password' => bcrypt('V&n0@2021'), 'role_codes' => 'customer_success','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' => $country_code, 'person_id' => ++$last_id],
        ['email' => 'uma@flowglobal.net','password' => bcrypt('U&a12@2021'), 'role_codes' => 'customer_success','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' => $country_code, 'person_id' => ++$last_id],
        ]);
    }
}