<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMasterDataActivityStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'activity_status', 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
        
        DB::table('master_data')->insert([ 
            ['country_code'=> '*', 'data_key' => 'activity_status', 'data_code' => 'active', 'data_value' => 'Active', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'], 
            
            ['country_code'=> '*', 'data_key' => 'activity_status', 'data_code' => 'passive', 'data_value' => 'Passive', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
    
            ['country_code'=> '*', 'data_key' => 'activity_status', 'data_code' => 'dormant', 'data_value' => 'Dormant', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
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
