<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertAggrStatusInMasterDataSprint11 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'aggr_status', 'parent_data_key' => NULL, 'status' => 'enabled', 'created_at' => now()]);

         DB::table('master_data')->insert([
            ['country_code' => '*', 'data_key' => 'aggr_status', 'data_value' => 'Draft', 'data_code' => 'draft',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()], 
            ['country_code' => '*', 'data_key' => 'aggr_status', 'data_value' => 'Active', 'data_code' => 'active',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'aggr_status', 'data_value' => 'Inactive', 'data_code' => 'inactive',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('master_data_keys')->where('data_key','aggr_status')->delete();
        DB::table('master_data')->where([['data_key','aggr_status'],['data_code','draft']])->delete();
        DB::table('master_data')->where([['data_key','aggr_status'],['data_code','active']])->delete();
        DB::table('master_data')->where([['data_key','aggr_status'],['data_code','inactive']])->delete();

    }
}
