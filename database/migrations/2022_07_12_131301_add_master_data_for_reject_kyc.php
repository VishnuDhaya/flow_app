<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMasterDataForRejectKyc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'reject_reason', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);

        DB::table('master_data')->insert([ 
            ['country_code'=> '*', 'data_key' => 'lead_status', 'data_code' => '52_kyc_failed' , 'data_value' => 'KYC Failed' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'reject_reason', 'data_code' => 'kyc_mismatch/failure' , 'data_value' => 'KYC mismatch/failure' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
        ]);

        DB::UPDATE("UPDATE master_data SET status = 'disabled' WHERE data_code IN ('kyc_rejected', 'kyc_mismatch/failure')");
    
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
