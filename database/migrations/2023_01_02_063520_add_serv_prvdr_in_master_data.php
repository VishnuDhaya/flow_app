<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServPrvdrInMasterData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    
    {

        DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'mob_num_operator', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);


        DB::table('master_data')->insert([ 
            ['country_code'=> '*', 'data_key' => 'mob_num_operator', 'data_code' => 'airtel' , 'data_value' => 'Airtel' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
            ['country_code'=> '*', 'data_key' => 'mob_num_operator', 'data_code' => 'mtn' , 'data_value' => 'MTN' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

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
