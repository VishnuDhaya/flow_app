<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLoanProductsCreateAbove2mUmtnProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $country_code = 'UGA';
        $acc_prvdr_code = 'UMTN';
        $lender_code = 'UFLW';
        $cs_model_code = 'fa_performance_model';
        $product_type = 'regular';

        $created_at = datetime_db();

		DB::table('loan_products')->insert([

            ['country_code' => $country_code, 'product_name' => 'FS13', 'product_code' => 'FS13', 'acc_prvdr_code' => $acc_prvdr_code, 'lender_code' => $lender_code, 'cs_model_code' => $cs_model_code, 'product_type' => $product_type, 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 47000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 2500000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 10000],

            ['country_code' => $country_code, 'product_name' => 'FS14', 'product_code' => 'FS14', 'acc_prvdr_code' => $acc_prvdr_code, 'lender_code' => $lender_code, 'cs_model_code' => $cs_model_code, 'product_type' => $product_type, 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 55000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 3000000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 10000],
    
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
