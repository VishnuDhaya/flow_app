<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterDataWriteOffStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'write_off_status', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
        
        DB::table('master_data')->insert([ 
           ['country_code'=> '*', 'data_key' => 'write_off_status', 'data_code' => 'requested' , 'data_value' => 'Requested' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
           ['country_code'=> '*', 'data_key' => 'write_off_status', 'data_code' => 'approved' , 'data_value' => 'Approved' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
           ['country_code'=> '*', 'data_key' => 'write_off_status', 'data_code' => 'rejected' , 'data_value' => 'Rejected' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
           ['country_code'=> '*', 'data_key' => 'write_off_status', 'data_code' => 'recovered' , 'data_value' => 'Recovered' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
           ['country_code'=> '*', 'data_key' => 'write_off_status', 'data_code' => 'partially_recovered' , 'data_value' => 'Partially Recovered' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

           ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_data_write_off_status');
    }
}
