<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class RWA_productSeeder extends Seeder
{
	/**
	 * Run the fieldbase seeds.
	 *
	 * @return void
	 */
	public function run()
	{
	        $country_code = 'RWA';
                $created_at =datetime_db();
        
		DB::table('loan_products')->insert([
            ['country_code' => 'RWA', 'product_name' => 'BOK1', 'product_code' => 'BOK1', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance','flow_fee_type' => 'Flat', 'flow_fee' => 1500, 'flow_fee_duration' => 3, 'duration' => 3, 'max_loan_amount' => 150000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 500],

            ['country_code' => 'RWA', 'product_name' => 'BOK2', 'product_code' => 'BOK2', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 2000, 'flow_fee_duration' => 3, 'duration' => 3, 'max_loan_amount' => 200000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 650],

            ['country_code' => 'RWA', 'product_name' => 'BOK3', 'product_code' => 'BOK3', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 3000, 'flow_fee_duration' => 3, 'duration' => 3, 'max_loan_amount' => 300000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 1000],

            ['country_code' => 'RWA', 'product_name' => 'BOK4', 'product_code' => 'BOK4', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 4000, 'flow_fee_duration' => 3, 'duration' => 3, 'max_loan_amount' => 400000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 1300],

            ['country_code' => 'RWA', 'product_name' => 'BOK5', 'product_code' => 'BOK5', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 5000, 'flow_fee_duration' => 3, 'duration' => 3, 'max_loan_amount' => 500000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 1650],


            ['country_code' => 'RWA', 'product_name' => 'BOK6', 'product_code' => 'BOK6', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 3000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 150000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 500],

            ['country_code' => 'RWA', 'product_name' => 'BOK7', 'product_code' => 'BOK7', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 4000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 200000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 650],

            ['country_code' => 'RWA', 'product_name' => 'BOK8', 'product_code' => 'BOK8', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 6000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 300000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 1000],

            ['country_code' => 'RWA', 'product_name' => 'BOK9', 'product_code' => 'BOK9', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 8000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 400000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 1300],

            ['country_code' => 'RWA', 'product_name' => 'BOK10', 'product_code' => 'BOK10', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 10000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 500000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 1650],

            // MTN products
            ['country_code' => 'RWA', 'product_name' => 'MTN1', 'product_code' => 'MTN1', 'acc_prvdr_code' => 'RMTN', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 1500, 'flow_fee_duration' => 3, 'duration' => 3, 'max_loan_amount' => 150000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 500],

            ['country_code' => 'RWA', 'product_name' => 'MTN2', 'product_code' => 'MTN2', 'acc_prvdr_code' => 'RMTN', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 2000, 'flow_fee_duration' => 3, 'duration' => 3, 'max_loan_amount' => 200000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 650],

            ['country_code' => 'RWA', 'product_name' => 'MTN3', 'product_code' => 'MTN3', 'acc_prvdr_code' => 'RMTN', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 3000, 'flow_fee_duration' => 3, 'duration' => 3, 'max_loan_amount' => 300000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 1000],


            ['country_code' => 'RWA', 'product_name' => 'MTN6', 'product_code' => 'MTN6', 'acc_prvdr_code' => 'RMTN', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 3000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 150000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 500],

            ['country_code' => 'RWA', 'product_name' => 'MTN7', 'product_code' => 'MTN7', 'acc_prvdr_code' => 'RMTN', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 4000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 200000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 650],

            ['country_code' => 'RWA', 'product_name' => 'MTN8', 'product_code' => 'MTN8', 'acc_prvdr_code' => 'RMTN', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 6000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 300000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 1000],

         

        ]);

	}
}
