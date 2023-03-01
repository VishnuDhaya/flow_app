<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class TFProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $country_code = 'UGA';
        $created_at = datetime_db();

        DB::table('borrowers')->update(['product_group' => "float_advance"]);
        DB::table('cust_agreements')->update(['product_group' => "float_advance"]);
        DB::table('loan_products')->insert([
            ['country_code' => $country_code, 'product_group' =>'terminal_financing', 'product_name' => 'EM TF PROBATION 01', 'product_code' => 'EMTF01', 'lender_code' => 'UFLW', 'cs_model_code' => 'half_probation', 'product_type' => 'probation', 'flow_fee_type' => 'Flat', 'flow_fee' => 3500, 'flow_fee_duration' => '3', 'duration' => 3, 'max_loan_amount' => 250000, 'status' => 'enabled','data_prvdr_code' => 'UEZM','created_at' => $created_at],
            ['country_code' => $country_code, 'product_group' =>'terminal_financing','product_name' => 'EM TF PROBATION 02', 'product_code' => 'EMTF02', 'lender_code' => 'UFLW', 'cs_model_code' => 'half_probation', 'product_type' => 'probation', 'flow_fee_type' => 'Flat', 'flow_fee' => 6000, 'flow_fee_duration' => '6', 'duration' => 6, 'max_loan_amount' => 250000, 'status' => 'enabled','data_prvdr_code' => 'UEZM','created_at' => $created_at],
            ['country_code' => $country_code, 'product_group' =>'terminal_financing','product_name' => 'EM TF PROBATION 03', 'product_code' => 'EMTF03', 'lender_code' => 'UFLW', 'cs_model_code' => 'half_probation', 'product_type' => 'probation', 'flow_fee_type' => 'Flat', 'flow_fee' => 6000, 'flow_fee_duration' => '3', 'duration' => 3, 'max_loan_amount' => 500000, 'status' => 'enabled','data_prvdr_code' => 'UEZM','created_at' => $created_at],
            ['country_code' => $country_code, 'product_group' =>'terminal_financing','product_name' => 'EM TF PROBATION 04', 'product_code' => 'EMTF04', 'lender_code' => 'UFLW', 'cs_model_code' => 'half_probation', 'product_type' => 'probation', 'flow_fee_type' => 'Flat', 'flow_fee' => 12000, 'flow_fee_duration' => '6', 'duration' => 6, 'max_loan_amount' => 500000, 'status' => 'enabled','data_prvdr_code' => 'UEZM','created_at' => $created_at],

        ]);
        DB::table('loan_products')->whereNull('product_group')->update(['product_group' => "float_advance"]);

    }
}
