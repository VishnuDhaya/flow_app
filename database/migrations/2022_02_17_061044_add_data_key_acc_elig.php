<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDataKeyAccElig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("alter table master_data modify column data_value varchar(64)");
        DB::table('master_data_keys')->insert(['country_code' => 'UGA', 'data_key' => 'acc_elig', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);

        DB::table('master_data')->insert([ 
            
            ['country_code'=> 'UGA', 'data_key' => 'acc_elig', 'data_code' => 'new_acc' , 'data_value' => 'New MTN Agent Line Account' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'acc_elig', 'data_code' => 'exist_acc_wo_elig' , 'data_value' => 'Existing MTN Account (Not eligible based on commission)' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'acc_elig', 'data_code' => 'exist_acc_w_elig' , 'data_value' => 'Existing MTN Account (Eligible based on commission)' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            
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
