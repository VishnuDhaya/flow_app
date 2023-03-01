<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Sprint12InsertMasterData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert([
       
            ['country_code' => 'UGA', 'data_key' => 'address_fields', 'parent_data_key' => NULL, 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]
        ]);

          DB::table('master_data')->insert([
            ['country_code' => 'UGA', 'data_key' => 'address_fields', 'data_code' => 'region', 'data_value' => 'Region',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => 'UGA', 'data_key' => 'address_fields', 'data_code' => 'district', 'data_value' => 'District',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => 'UGA', 'data_key' => 'address_fields', 'data_code' => 'county', 'data_value' => 'County',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => 'UGA', 'data_key' => 'address_fields', 'data_code' => 'sub_county', 'data_value' => 'Sub County',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => 'UGA', 'data_key' => 'address_fields', 'data_code' => 'parish', 'data_value' => 'Parish',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => 'UGA', 'data_key' => 'address_fields', 'data_code' => 'village', 'data_value' => 'Village',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => 'UGA', 'data_key' => 'address_fields', 'data_code' => 'plot_number', 'data_value' => 'Plot Number',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => 'UGA', 'data_key' => 'address_fields', 'data_code' => 'landmark', 'data_value' => 'Landmark',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
              ['country_code' => 'UGA', 'data_key' => 'address_fields', 'data_code' => 'gps', 'data_value' => 'GPS',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()]
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
