<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMasterDataProfileStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'profile_status', 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
        
        DB::table('master_data')->insert([ 
            ['country_code'=> '*', 'data_key' => 'profile_status', 'data_code' => 'open', 'data_value' => 'Open', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'], 
            
            ['country_code'=> '*', 'data_key' => 'profile_status', 'data_code' => 'closed', 'data_value' => 'Closed', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
    
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
