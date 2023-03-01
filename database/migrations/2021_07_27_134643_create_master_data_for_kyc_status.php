<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterDataForKycStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'kyc_status', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);

       DB::table('master_data')->insert([ 
            ['country_code'=> '*', 'data_key' => 'kyc_status', 'data_code' => 'completed' , 'data_value' => 'Completed' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
             ['country_code'=> '*', 'data_key' => 'kyc_status', 'data_code' => 'pending' , 'data_value' => 'Pending' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
             ['country_code'=> '*', 'data_key' => 'kyc_status', 'data_code' => 'in_progress' , 'data_value' => 'In Progress' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_data_for_kyc_status');
    }
}
