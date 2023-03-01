<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;


//use database\seeds\PrivilegeSeeder;

class IND_DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    
    public function run()
    {
    	Model::unguard();
      //
      $country_code = 'IND';
        $one_time_seeders = array ("{$country_code}_AddressConfigSeeder",  "{$country_code}_AppUserSeeder");
     
        foreach ($one_time_seeders as $seeder)
        { 
           $this->call($seeder, $country_code);
        }
        
        Model::reguard();
        return "completed";
    }
   


    public function call($class, $param = null){
        $this->resolve($class)->run($param);

        if(isset($this->command)){
            $this->command->getOutput()->writeln("<info>Seeded : </info> $class");
        }
    }

}
