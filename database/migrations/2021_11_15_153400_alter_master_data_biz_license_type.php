<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMasterDataBizLicenseType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert(['country_code' => 'UGA', 'data_key' => 'biz_license_type', 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);

        DB::table('master_data')->insert([
            ['country_code'=> 'UGA',  'data_type' => 'common', 'data_key' => 'biz_license_type', 'data_code' => 'trading_license_certificate', 'data_value' => 'Trading License Certificate','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA',  'data_type' => 'common', 'data_key' => 'biz_license_type', 'data_code' => 'shop_permit', 'data_value' => 'Shop Permit','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA',  'data_type' => 'common', 'data_key' => 'biz_license_type', 'data_code' => 'local_conusel_letter', 'data_value' => 'Local Conusel Letter','status' => 'enabled','created_at' => now()],
            
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
