<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMasterDataForTerritory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        DB::table('master_data_keys')->insert(['country_code' => 'UGA', 'data_key' => 'territory','data_group' => 'address', 'data_type' => '', 'status' => 'enabled', 'created_at' => now()]);

        DB::table('master_data')->insert([
            ['country_code'=> 'UGA',  'data_type' => 'address', 'data_key' => 'territory', 'data_code' => 'nakawa', 'data_value' => 'Nakawa','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA',  'data_type' => 'address', 'data_key' => 'territory', 'data_code' => 'mukono', 'data_value' => 'Mukono','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA',  'data_type' => 'address', 'data_key' => 'territory', 'data_code' => 'makindye ', 'data_value' => 'Makindye ','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA',  'data_type' => 'address', 'data_key' => 'territory', 'data_code' => 'lubaga ', 'data_value' => 'Lubaga ','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA',  'data_type' => 'address', 'data_key' => 'territory', 'data_code' => 'nsangi', 'data_value' => 'Nsangi','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA',  'data_type' => 'address', 'data_key' => 'territory', 'data_code' => 'kawempe', 'data_value' => 'Kawempe','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA',  'data_type' => 'address', 'data_key' => 'territory', 'data_code' => 'central', 'data_value' => 'Central','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA',  'data_type' => 'address', 'data_key' => 'territory', 'data_code' => 'luwero ', 'data_value' => 'Luwero ','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA',  'data_type' => 'address', 'data_key' => 'territory', 'data_code' => 'jinja', 'data_value' => 'Jinja','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA',  'data_type' => 'address', 'data_key' => 'territory', 'data_code' => 'kayunga ', 'data_value' => 'Kayunga ','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA',  'data_type' => 'address', 'data_key' => 'territory', 'data_code' => 'mityana ', 'data_value' => 'Mityana ','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA',  'data_type' => 'address', 'data_key' => 'territory', 'data_code' => 'iganga ', 'data_value' => 'Iganga ','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA',  'data_type' => 'address', 'data_key' => 'territory', 'data_code' => 'mbarara ', 'data_value' => 'Mbarara ','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA',  'data_type' => 'address', 'data_key' => 'territory', 'data_code' => 'mbale ', 'data_value' => 'Mbale ','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA',  'data_type' => 'address', 'data_key' => 'territory', 'data_code' => 'kasese ', 'data_value' => 'Kasese ','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA',  'data_type' => 'address', 'data_key' => 'territory', 'data_code' => 'wakiso ', 'data_value' => 'Wakiso ','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA',  'data_type' => 'address', 'data_key' => 'territory', 'data_code' => 'tororo ', 'data_value' => 'Tororo ','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA',  'data_type' => 'address', 'data_key' => 'territory', 'data_code' => 'masaka ', 'data_value' => 'Masaka ','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA',  'data_type' => 'address', 'data_key' => 'territory', 'data_code' => 'mpigi ', 'data_value' => 'Mpigi ','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA',  'data_type' => 'address', 'data_key' => 'territory', 'data_code' => 'fortportal ', 'data_value' => 'Fortportal ','status' => 'enabled','created_at' => now()],

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
