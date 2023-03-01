<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLoanProductsInsertAbove300kRmtnProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $country_code = 'RWA';
        $created_at = datetime_db();
        
		DB::table('loan_products')->insert([

            ['country_code' => 'RWA', 'product_name' => 'MTN4', 'product_code' => 'MTN4', 'acc_prvdr_code' => 'RMTN', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 4000, 'flow_fee_duration' => 3, 'duration' => 3, 'max_loan_amount' => 400000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 1300],

            ['country_code' => 'RWA', 'product_name' => 'MTN5', 'product_code' => 'MTN5', 'acc_prvdr_code' => 'RMTN', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 5000, 'flow_fee_duration' => 3, 'duration' => 3, 'max_loan_amount' => 500000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 1650],
    
            ['country_code' => 'RWA', 'product_name' => 'MTN9', 'product_code' => 'MTN9', 'acc_prvdr_code' => 'RMTN', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 8000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 400000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 1300],

            ['country_code' => 'RWA', 'product_name' => 'MTN10', 'product_code' => 'MTN10', 'acc_prvdr_code' => 'RMTN', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 10000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 500000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 1650],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
