<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class RWA_deleteSeeder extends Seeder
{
	/**
	 * Run the fieldbase seeds.
	 *
	 * @return void
	 */
	public function run()
	{
        DB::delete("delete from lenders where country_code ='RWA'");
        DB::delete("delete from address_info where country_code ='RWA'");
        DB::delete("delete from app_users where country_code ='RWA'");
		DB::delete("delete from persons where country_code ='RWA'");
        DB::delete("delete from accounts where country_code ='RWA'");
        DB::delete("delete from capital_funds where country_code ='RWA'");
        DB::delete("delete from loan_products where country_code ='RWA'");
        DB::delete("delete from markets where country_code ='RWA'");
        
        DB::delete("delete from orgs where country_code ='RWA'");
        DB::delete("delete from acc_providers where country_code ='RWA'");

    }
}