<?php

use Illuminate\Database\Seeder;
use App\Repositories\SQL\PersonRepositorySQL;

class TestRMSeeder extends Seeder
{
    
    public function run()
    {
        $country_code =  'UGA';
    	session()->put('country_code','UGA');
		$person_repo = new PersonRepositorySQL();
    	$last_id = '2508';
    	
         DB::table('persons')->insert([
     
            ['first_name' => 'MAYIRA','last_name'=> 'JOSEPH','initials' => '','dob' => null,'gender' => 'MALE','whatsapp' => '',
            'email_id' => 'mayirajoseph4@gmail.com','mobile_num' => '783035193','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'KYAMPA','last_name'=> 'MUSA','initials' => '','dob' => null,'gender' => 'MALE','whatsapp' => '',
            'email_id' => 'champaamusa@gmail.com','mobile_num' => ' 770523812','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'LUZIRA','last_name'=> 'ABRAHAM','initials' => '','dob' => null,'gender' => 'MALE','whatsapp' => '',
            'email_id' => 'abrahamluzira@gmail.com','mobile_num' => '776463656','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'MAMBO','last_name'=> 'SILVER','initials' => '','dob' => null,'gender' => 'MALE','whatsapp' => '',
            'email_id' => 'm.silver.mtn@gmail.com','mobile_num' => '789877021','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'WAKWESA','last_name'=> 'YAFESI','initials' => '','dob' => null,'gender' => 'MALE','whatsapp' => '',
            'email_id' => 'wakwesaherman@gmail.com','mobile_num' => '770528649','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'NUWAREBA','last_name'=> 'ELIAS','initials' => '','dob' => null,'gender' => 'MALE','whatsapp' => '',
            'email_id' => 'johnelias115@gmail.com','mobile_num' => '787302432','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'KANKIRIHI','last_name'=> 'DANIEL','initials' => '','dob' => null,'gender' => 'MALE','whatsapp' => '',
            'email_id' => 'kankirihodaniel@gmail.com','mobile_num' => '754308576','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'MAGIRIGI','last_name'=> 'DANIEL','initials' => '','dob' => null,'gender' => 'MALE','whatsapp' => '',
            'email_id' => 'magirigidan@gmail.com','mobile_num' => '782792357','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'NABWIRE','last_name'=> 'WANYAMA','initials' => '','dob' => null,'gender' => 'FEMALE','whatsapp' => '',
            'email_id' => 'ritahnabwire2012@gmail.com','mobile_num' => '770829828','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'NDUGGA','last_name'=> 'SIMON','initials' => '','dob' => null,'gender' => 'MALE','whatsapp' => '',
            'email_id' => 'simonndugga@yahoo.com','mobile_num' => '786948550','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'ISABIRYE','last_name'=> 'PATRICK','initials' => '','dob' => null,'gender' => 'MALE','whatsapp' => '',
            'email_id' => 'patrical2013@gmail.com','mobile_num' => '771661150','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'TAYEBWA','last_name'=> 'KENEDY','initials' => '','dob' => null,'gender' => 'MALE','whatsapp' => '',
            'email_id' => 'kennedytayebwa@gmail.com','mobile_num' => '771641692','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'OMBOGI','last_name'=> 'OKELLO','initials' => '','dob' => null,'gender' => 'MALE','whatsapp' => '',
            'email_id' => 'salmonkello77@gmail.com','mobile_num' => '781978039','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'KITUTU','last_name'=> 'VICENT','initials' => '','dob' => null,'gender' => 'MALE','whatsapp' => '',
            'email_id' => 'kitutuvicent@gmail.com','mobile_num' => '7889099105','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'KATUSHABE','last_name'=> 'SARAH','initials' => '','dob' => null,'gender' => 'FEMALE','whatsapp' => '',
            'email_id' => 'katushabesar@gmail.com','mobile_num' => '778127930','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'ARYATUHA','last_name'=> 'CYNTHIA','initials' => '','dob' => null,'gender' => 'FEMALE','whatsapp' => '',
            'email_id' => 'cynthiaaryatuha@gmail.com','mobile_num' => '775976466','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'ABARIMO','last_name'=> 'ROBERT','initials' => '','dob' => null,'gender' => 'MALE','whatsapp' => '',
            'email_id' => 'jrabarimo@gmail.com','mobile_num' => '775242031','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'NAKATO','last_name'=> 'ASIYA','initials' => '','dob' => null,'gender' => 'FEMALE','whatsapp' => '',
            'email_id' => 'nakatoasiya1@gmail.com','mobile_num' => '718440806','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'MAWULE','last_name'=> 'RONALD','initials' => '','dob' => null,'gender' => 'MALE','whatsapp' => '',
            'email_id' => 'Mawuleronnie2019@gmail.com','mobile_num' => '71696018','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'BIRUNGI','last_name'=> 'ATANANSI','initials' => '','dob' => null,'gender' => 'MALE','whatsapp' => '',
            'email_id' => 'birungiatanansi@gmail.com','mobile_num' => '788451489','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'HAMUHAANDREW','last_name'=> '','initials' => '','dob' => null,'gender' => 'MALE','whatsapp' => '',
            'email_id' => 'hamuhaandrew777@gmail.com','mobile_num' => '783236038','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'MUGISHA','last_name'=> 'DENIS','initials' => '','dob' => null,'gender' => 'MALE','whatsapp' => '',
            'email_id' => 'Mugishad223@gmail.com','mobile_num' => '752608099','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'MAGWALI','last_name'=> 'PIUS','initials' => '','dob' => null,'gender' => 'MALE','whatsapp' => '',
            'email_id' => 'magpius45@gmail.com','mobile_num' => '704144369','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],

            ['first_name' => 'TENYWA','last_name'=> 'SIMON','initials' => '','dob' => null,'gender' => 'MALE','whatsapp' => '',
            'email_id' => 'simon.tenywa@yahoo.com','mobile_num' => '777162238','phone_num' => '','designation' => 'Relationship Manager',
            'country_code' => $country_code],
         ]);
         
         
         DB::table('app_users')->insert([

            ['email' => 'mayirajoseph4@gmail.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

            ['email' => 'champaamusa@gmail.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

            ['email' => 'abrahamluzira@gmail.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

            ['email' => 'm.silver.mtn@gmail.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],
            
            ['email' => 'wakwesaherman@gmail.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

            ['email' => 'johnelias115@gmail.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

            ['email' => 'kankirihodaniel@gmail.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

            ['email' => 'magirigidan@gmail.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

            ['email' => 'ritahnabwire2012@gmail.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

            ['email' => 'simonndugga@yahoo.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

            ['email' => 'patrical2013@gmail.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

            ['email' => 'kennedytayebwa@gmail.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

            ['email' => 'salmonkello77@gmail.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

            ['email' => 'kitutuvicent@gmail.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

            ['email' => 'katushabesar@gmail.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

            ['email' => 'cynthiaaryatuha@gmail.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

            ['email' => 'jrabarimo@gmail.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

            ['email' => 'nakatoasiya1@gmail.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

            ['email' => 'Mawuleronnie2019@gmail.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

            ['email' => 'birungiatanansi@gmail.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

            ['email' => 'hamuhaandrew777@gmail.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

            ['email' => 'Mugishad223@gmail.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

            ['email' => 'magpius45@gmail.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

            ['email' => 'simon.tenywa@yahoo.com','password' => bcrypt('rmf10w@2021'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 
            'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],
        ]);
    }
}