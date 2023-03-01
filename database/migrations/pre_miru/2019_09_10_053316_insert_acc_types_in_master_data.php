<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertAccTypesInMasterData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert([
       
            ['country_code' => '*', 'data_key' => 'account_type', 'parent_data_key' => NULL, 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]
        ]);

        DB::table('master_data')->insert([
       
            ['country_code' => '*', 'data_key' => 'account_type', 'data_code' => 'bank', 'data_value' => 'Bank',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'account_type', 'data_code' => 'wallet', 'data_value' => 'Wallet',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'account_type', 'data_code' => 'journal', 'data_value' => 'Journal',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()]
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
