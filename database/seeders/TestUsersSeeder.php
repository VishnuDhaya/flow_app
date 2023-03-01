<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Repositories\SQL\PersonRepositorySQL;
use DB;
use Hash;
class TestUsersSeeder extends Seeder
{
    
    public function run()
    {
        $country_code =  'UGA';
    	session()->put('country_code','UGA');
		$person_repo = new PersonRepositorySQL();
    	$last_id = $person_repo->get_last_id();
    	
         DB::table('persons')->insert([
     
        ['first_name' => 'Test','last_name'=> 'RM','email_id' => 'testflowrm@gmail.com','designation' => 'Relationship Manager','country_code' =>  $country_code, 'associated_with' => 'FLOW'],

        ['first_name' => 'Test','last_name'=> 'CS','email_id' => 'testflowcs@gmail.com','designation' => 'Customer Success','country_code' =>  $country_code, 'associated_with' => null],
        
        ['first_name' => 'Test','last_name'=> 'Market_Admin','email_id' => 'testmarket_admin@gmail.com','designation' => 'Market Admin','country_code' =>  $country_code, 'associated_with' => 'FLOW'],
        
        ['first_name' => 'Somnath','last_name'=> 'Banerjee','email_id' => 'som@flowglobal.net','designation' => 'Market Admin','country_code' =>  $country_code, 'associated_with' => 'FLOW'],
        
        ['first_name' => 'Vivianne','last_name'=> 'Kamikazi','email_id' => 'vivianne@flowglobal.net','designation' => 'Relationship Manager','country_code' =>  $country_code, 'associated_with' => "FLOW"],
        
        ['first_name' => 'Subramaniyam','last_name'=> 'A','email_id' => 'subramaniyam@gmail.com','designation' => 'Relationship Manager','country_code' =>  'UGA', 'associated_with' => 'FLOW'],
        ['first_name' => 'Sakthi','last_name'=> 'Ganesh','email_id' => 'sakthi@gmail.com','designation' => 'Relationship Manager','country_code' =>  'UGA', 'associated_with' => 'FLOW'],
        ['first_name' => 'Bino','last_name'=> 'M','email_id' => 'bino@gmail.com','designation' => 'Relationship Manager','country_code' =>  'UGA', 'associated_with' => 'FLOW'],
        ['first_name' => 'Jose','last_name'=> 'Ambrose','email_id' => 'jose@gmail.com','designation' => 'Relationship Manager','country_code' =>  'UGA', 'associated_with' => 'FLOW'],
        

        ['first_name' => 'Vivianne','last_name'=> 'Kamikazi','email_id' => 'vivianne@flowglobal.net','designation' => 'Relationship Manager','country_code' =>  $country_code, 'associated_with' => null],


         ]);
         
         
         DB::table('app_users')->insert([

        ['email' => 'testflowrm@gmail.com','password' => bcrypt('T#st!m@c&21'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],
        ['email' => 'testflowcs@gmail.com','password' => bcrypt('T#st!m@c&21'), 'role_codes' => 'customer_success','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],
        ['email' => 'testmarket_admin@gmail.com','password' => bcrypt('T#st!m@c&21'), 'role_codes' => 'market_admin','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],
        ['email' => 'som@flowglobal.net','password' => bcrypt('T#st!m@c&21'), 'role_codes' => 'market_admin','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],

        ['email' => 'vivianne@flowglobal.net','password' => bcrypt('T#st!m@c&21'), 'role_codes' => 'customer_success','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],
        
        ['email' => 'subramaniyam@gmail.com','password' => bcrypt('T#st!m@c&21'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  'UGA', 'person_id' => 2535],
        ['email' => 'sakthi@gmail.com','password' => bcrypt('T#st!m@c&21'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  'UGA', 'person_id' => 2536],
        ['email' => 'bino@gmail.com','password' => bcrypt('T#st!m@c&21'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  'UGA', 'person_id' => 2537],
        ['email' => 'jose@gmail.com','password' => bcrypt('T#st!m@c&21'), 'role_codes' => 'relationship_manager','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  'UGA', 'person_id' => 2538],
        ]);

        DB::update("update borrowers set flow_rel_mgr_id=2535 where cust_id in ('UEZM-437748', 'UEZM-461162', 'UEZM-989325', 'UEZM-588776', 'UEZM-215503')");
        DB::update("update borrowers set flow_rel_mgr_id=2536 where cust_id in ('UEZM-754216', 'UEZM-427424', 'UEZM-502664', 'UEZM-238050', 'CCA-175052')");
        DB::update("update borrowers set flow_rel_mgr_id=2537 where cust_id in ('UEZM-437748', 'UEZM-461162', 'UEZM-989325', 'UEZM-588776', 'UEZM-215503')");
        DB::update("update borrowers set flow_rel_mgr_id=2538 where cust_id in ('UEZM-947349', 'UEZM-514358', 'UEZM-853830', 'UEZM-267430', 'UEZM-123661')");
        
        DB::update("update borrowers set flow_rel_mgr_id=2538 where cust_id in ('CCA-396359', 'CCA-676265', 'CCA-800978', 'CCA-700632', 'CCA-706046', 
        'UEZM-172160', 'UEZM-106502', 'UEZM-305114', 'UEZM-480446', 'UEZM-997198')");

         DB::table('investor_users')->where('name','Dennis')->update(['email' => 'michael@flowglobal.net', 'password' => Hash::make('fl0winv123')]);
         DB::table('investor_users')->where('name','Laurenz')->update(['email' => 'nitin@flowglobal.net', 'password' => Hash::make('fl0winv123')]);

    }
}