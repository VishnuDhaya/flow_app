<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;


//use database\seeds\PrivilegeSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    
    public function run()
    {


      Model::unguard();
      
        $one_time_seeders = array ('SeqNumbersSeeder', 'AppUserSeeder', 'CountriesTableSeeder',  'MasterDataKeysSeeder', 'MasterDataSeeder', 'PrivilegeSeeder', 'RolePrivSeeder');
        // $seeders = array ('MasterDataSeeder');
         
        foreach ($one_time_seeders as $seeder)

        { 
           $this->call($seeder);
        }
        
        Model::reguard();
        
    }

}
