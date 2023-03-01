<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use DB;

class LoanProvisioningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){

        DB::table('loan_loss_provisions')->truncate();
        DB::table('loan_loss_provisions')->insert([
	        ['country_code' => 'UGA', 'year' => '2020', 'prov_amount' => '50000000.00','balance' => '50000000.00'],
	        ['country_code' => 'UGA', 'year' => '2021', 'prov_amount' => '50000000.00','balance' => '50000000.00'],
    	]);

    }
}