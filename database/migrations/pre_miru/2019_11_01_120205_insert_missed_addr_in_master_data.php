<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertMissedAddrInMasterData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data')->insert([
            ['country_code' =>  '*', 'data_key' => 'biz_addr_prop_type', 'data_value' => 'Unknown', 'data_code' => 'unknown',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'],
            ['country_code' =>  '*', 'data_key' => 'biz_addr_prop_type', 'data_value' => 'Others', 'data_code' => 'others',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'],

            ['country_code' => 'UGA', 'data_key' => 'county', 'data_value' => 'Kawempe Division', 'data_code' => 'kawempe_division', 'parent_data_code' => 'kampala',  'status' => 'enabled', 'data_type' => 'address']
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_data', function (Blueprint $table) {
            //
        });
    }
}
