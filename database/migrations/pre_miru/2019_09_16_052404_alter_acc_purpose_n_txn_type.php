<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAccPurposeNTxnType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->where('data_key','account_transaction_type')->update(['parent_data_key' => 'account_purpose']);

        DB::table('master_data')->where('data_key','account_purpose')->delete();
        DB::table('master_data')->where('data_key','account_transaction_type')->delete();

        DB::table('master_data')->insert([
            ['country_code' => '*', 'data_key' => 'account_purpose', 'data_code' => 'disbursement', 'data_value' => 'Disbursement / Repayment',  'parent_data_code' => 'lender',   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'account_purpose', 'data_code' => 'repayment', 'data_value' => 'Repayment',  'parent_data_code' => 'lender',   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'account_purpose', 'data_code' => 'commission', 'data_value' => 'Commission',  'parent_data_code' => 'data_provider',   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'account_purpose', 'data_code' => 'float_advance', 'data_value' => 'Float Advance',  'parent_data_code' => 'customer',   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'account_purpose', 'data_code' => 'others', 'data_value' => 'Others',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],

            ['country_code' => '*', 'data_key' => 'account_transaction_type', 'data_code' => 'capital_investment', 'data_value' => 'Capital Investment',  'parent_data_code' => 'disbursement',   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'account_transaction_type', 'data_code' => 'repayment_internal_transfer', 'data_value' => 'Repayment Internal Transfer',  'parent_data_code' => 'repayment',   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],        
            ['country_code' => '*', 'data_key' => 'account_transaction_type', 'data_code' => 'disbursal', 'data_value' => 'Disbursal',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],

            ['country_code' => '*', 'data_key' => 'account_transaction_type', 'data_code' => 'payment', 'data_value' => 'Repayment',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],        
            ['country_code' => '*', 'data_key' => 'account_transaction_type', 'data_code' => 'new_cust_comm', 'data_value' => 'New Customer Commission',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'account_transaction_type', 'data_code' => 'repay_comm', 'data_value' => 'Repayment Commission',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'account_transaction_type', 'data_code' => 'rm_commission', 'data_value' => 'RM Commission',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'account_transaction_type', 'data_code' => 'others', 'data_value' => 'Others',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()], 

            ['country_code' => '*', 'data_key' => 'loan_status', 'data_code' => 'disbursed', 'data_value' => 'Disbursed',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()]
            
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
