<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MasterDataForHolderNameMismatch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::table('master_data')->insert([ 
            
            ['country_code'=> '*', 'data_key' => 'holder_name_mismatch_reason', 'data_code' => 'spelling_mistake' , 'data_value' => 'Spelling mistake' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled']

        ]);

        DB::table('master_data')->insert([ 
            
            ['country_code'=> '*', 'data_key' => 'holder_name_mismatch_reason', 'data_code' => 'others' , 'data_value' => 'Others' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled']
            
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
