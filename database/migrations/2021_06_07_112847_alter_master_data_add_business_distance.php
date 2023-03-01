<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMasterDataAddBusinessDistance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'business_distance', 'data_group' => 'address', 'data_type' => '', 'status' => 'enabled', 'created_at' => now()]);
        
        DB::table('master_data')->insert([ 
            ['country_code'=> '*', 'data_key' => 'business_distance', 'data_code' => 'business_at_home', 'data_value' => 'Business at home', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'], 
            
            ['country_code'=> '*', 'data_key' => 'business_distance', 'data_code' => 'walkable_from_home', 'data_value' => 'Walkable from home', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
    
            ['country_code'=> '*', 'data_key' => 'business_distance', 'data_code' => 'very_distant_location', 'data_value' => 'Very distant location', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
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
