<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReconTypeMasterDataCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'recon_status', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);

        DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'stmt_txn_type', 'parent_data_key' => "", 'status' => 'enabled', 'data_type' => 'common', 'created_at' => now()]);

        DB::table('master_data')->insert([ 
            ['country_code'=> '*', 'data_key' => 'recon_status', 'data_code' => '10_capture_payment_pending' , 'data_value' => 'Capture Payment Pending' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 

             ['country_code'=> '*', 'data_key' => 'recon_status', 'data_code' => '20_no_match_customers' , 'data_value' => 'No Matching Customer' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],

            ['country_code'=> '*', 'data_key' => 'recon_status', 'data_code' => '30_no_matching_fa' , 'data_value' => 'No Matching FA' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],

            ['country_code'=> '*', 'data_key' => 'recon_status', 'data_code' => '40_multi_match_customers' , 'data_value' => 'Multiple Customers Matched' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],

            ['country_code'=> '*', 'data_key' => 'recon_status', 'data_code' => '50_multiple_fas_matched' , 'data_value' => 'Multiple FAs Matched' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],

             ['country_code'=> '*', 'data_key' => 'recon_status', 'data_code' => '60_non_fa_credit' , 'data_value' => 'Not an FA related Credit' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],

               ['country_code'=> '*', 'data_key' => 'recon_status', 'data_code' => '70_incorrect_amount_in_fa' , 'data_value' => 'Incorect Amount in FA' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],

                ['country_code'=> '*', 'data_key' => 'recon_status', 'data_code' => '80_recon_done' , 'data_value' => 'Recon Done' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],

           
            

            ['country_code'=> '*', 'data_key' => 'stmt_txn_type', 'data_code' => 'credit' , 'data_value' => 'Credit' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
            ['country_code'=> '*', 'data_key' => 'stmt_txn_type', 'data_code' => 'debit' , 'data_value' => 'Debit' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],

        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
