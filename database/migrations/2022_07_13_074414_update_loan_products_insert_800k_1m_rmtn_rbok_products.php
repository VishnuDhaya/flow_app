<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLoanProductsInsert800k1mRmtnRbokProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $country_code = 'RWA';
        $created_at =datetime_db();

        DB::table('loan_products')->insert([
        
            // RBOK products
            ['country_code' => 'RWA', 'product_name' => 'BOK11', 'product_code' => 'BOK11', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'regular', 'loan_purpose' => 'float_advance','flow_fee_type' => 'Flat', 'flow_fee' => 1500, 'flow_fee_duration' => 3, 'duration' => 3, 'max_loan_amount' => 150000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 500],

            ['country_code' => 'RWA', 'product_name' => 'BOK12', 'product_code' => 'BOK12', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'regular', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 2000, 'flow_fee_duration' => 3, 'duration' => 3, 'max_loan_amount' => 200000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 650],

            ['country_code' => 'RWA', 'product_name' => 'BOK13', 'product_code' => 'BOK13', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'regular', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 3000, 'flow_fee_duration' => 3, 'duration' => 3, 'max_loan_amount' => 300000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 1000],

            ['country_code' => 'RWA', 'product_name' => 'BOK14', 'product_code' => 'BOK14', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'regular', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 4000, 'flow_fee_duration' => 3, 'duration' => 3, 'max_loan_amount' => 400000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 1300],

            ['country_code' => 'RWA', 'product_name' => 'BOK15', 'product_code' => 'BOK15', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'regular', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 5000, 'flow_fee_duration' => 3, 'duration' => 3, 'max_loan_amount' => 500000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 1650],

            ['country_code' => 'RWA', 'product_name' => 'BOK16', 'product_code' => 'BOK16', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'regular', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 3000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 150000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 500],

            ['country_code' => 'RWA', 'product_name' => 'BOK17', 'product_code' => 'BOK17', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'regular', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 4000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 200000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 650],

            ['country_code' => 'RWA', 'product_name' => 'BOK18', 'product_code' => 'BOK18', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'regular', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 6000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 300000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 1000],

            ['country_code' => 'RWA', 'product_name' => 'BOK19', 'product_code' => 'BOK19', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'regular', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 8000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 400000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 1300],

            ['country_code' => 'RWA', 'product_name' => 'BOK20', 'product_code' => 'BOK20', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'regular', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 10000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 500000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 1650],

            ['country_code' => 'RWA', 'product_name' => 'BOK21', 'product_code' => 'BOK21', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 15000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 800000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 3000],

            ['country_code' => 'RWA', 'product_name' => 'BOK22', 'product_code' => 'BOK22', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 18000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 1000000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 5000],

            ['country_code' => 'RWA', 'product_name' => 'BOK23', 'product_code' => 'BOK23', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'regular', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 15000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 800000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 3000],

            ['country_code' => 'RWA', 'product_name' => 'BOK24', 'product_code' => 'BOK24', 'acc_prvdr_code' => 'RBOK', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'regular', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 18000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 1000000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 5000],

            // RMTN products
            ['country_code' => 'RWA', 'product_name' => 'MTN21', 'product_code' => 'MTN21', 'acc_prvdr_code' => 'RMTN', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 15000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 800000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 3000],

            ['country_code' => 'RWA', 'product_name' => 'MTN22', 'product_code' => 'MTN22', 'acc_prvdr_code' => 'RMTN', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 18000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 1000000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 5000],

            ['country_code' => 'RWA', 'product_name' => 'MTN23', 'product_code' => 'MTN23', 'acc_prvdr_code' => 'RMTN', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'regular', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 15000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 800000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 3000],

            ['country_code' => 'RWA', 'product_name' => 'MTN24', 'product_code' => 'MTN24', 'acc_prvdr_code' => 'RMTN', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'regular', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 18000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 1000000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 5000],
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
