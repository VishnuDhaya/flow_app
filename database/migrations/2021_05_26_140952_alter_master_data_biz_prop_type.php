<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMasterDataBizPropType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("delete from master_data where data_key = 'biz_addr_prop_type'");
        DB::table('master_data')->insert([ 
            ['country_code'=> '*', 'data_key' => 'biz_addr_prop_type', 'data_code' => 'regular_shop' , 'data_value' => 'Regular Shop' , 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'], 
            
            ['country_code'=> '*', 'data_key' => 'biz_addr_prop_type', 'data_code' => 'umbrella_shop' , 'data_value' => 'Umbrella Shop' , 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
    
            ['country_code'=> '*', 'data_key' => 'biz_addr_prop_type', 'data_code' => 'kiosk' , 'data_value' => 'Kiosk' , 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],

            ['country_code'=> '*', 'data_key' => 'biz_addr_prop_type', 'data_code' => 'hardware_shop' , 'data_value' => 'Hardware Shop' , 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
            
            ['country_code'=> '*', 'data_key' => 'biz_addr_prop_type', 'data_code' => 'saloon' , 'data_value' => 'Saloon' , 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],

            ['country_code'=> '*', 'data_key' => 'biz_addr_prop_type', 'data_code' => 'canteen' , 'data_value' => 'Canteen' , 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
            
            ['country_code'=> '*', 'data_key' => 'biz_addr_prop_type', 'data_code' => 'market' , 'data_value' => 'Market' , 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],

            ['country_code'=> '*', 'data_key' => 'biz_addr_prop_type', 'data_code' => 'boutique' , 'data_value' => 'Boutique' , 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
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
