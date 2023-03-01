<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertAccountRelatedMasterData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert([
       
            ['country_code' => '*', 'data_key' => 'account_purpose', 'parent_data_key' => NULL, 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'account_transaction_type', 'parent_data_key' => NULL, 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]
        ]);

          DB::table('master_data')->insert([
            ['country_code' => '*', 'data_key' => 'account_purpose', 'data_code' => 'disbursement', 'data_value' => 'Disbursement Capital (Lender)',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'account_purpose', 'data_code' => 'repayment', 'data_value' => 'Repayment  (Lender)',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'account_purpose', 'data_code' => 'commission', 'data_value' => 'Commission (Data Provider)',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'account_purpose', 'data_code' => 'float_advance', 'data_value' => 'Float Advance (Customer)',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'account_purpose', 'data_code' => 'others', 'data_value' => 'Others',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],

            ['country_code' => '*', 'data_key' => 'account_transaction_type', 'data_code' => 'capital_investment', 'data_value' => 'Capital Investment',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'account_transaction_type', 'data_code' => 'payment_internal_transfer', 'data_value' => 'Repayment Internal Transfer',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],        

            ['country_code' => '*', 'data_key' => 'account_transaction_type', 'data_code' => 'disbursal', 'data_value' => 'Disbursal',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],

            ['country_code' => '*', 'data_key' => 'account_transaction_type', 'data_code' => 'repayment', 'data_value' => 'Repayment',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],        
            ['country_code' => '*', 'data_key' => 'account_transaction_type', 'data_code' => 'new_cust_comm', 'data_value' => 'New Customer Commission',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'account_transaction_type', 'data_code' => 'repay_comm', 'data_value' => 'Repayment Commission',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'account_transaction_type', 'data_code' => 'others', 'data_value' => 'Others',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'account_transaction_type', 'data_code' => 'provisional_penalty', 'data_value' => 'Provisional Penalty',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()]
            
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
