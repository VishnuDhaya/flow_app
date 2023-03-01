<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductTypeInMasterData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'product_group', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
        DB::table('master_data')->insert([ 
            ['country_code'=> '*', 'data_key' => 'product_group', 'data_code' => 'terminal_financing', 'data_value' => 'Terminal Financing', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'], 
            
            ['country_code'=> '*', 'data_key' => 'product_group', 'data_code' => 'float_advance', 'data_value' => 'Float Advance', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
    
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master__data', function (Blueprint $table) {
            //
        });
    }
}
