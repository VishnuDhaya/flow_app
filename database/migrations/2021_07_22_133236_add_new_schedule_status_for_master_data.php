<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewScheduleStatusForMasterData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data')->insert([ 

            ['country_code'=> '*', 'data_key' => 'sch_status', 'data_code' => 'cancelled' , 'data_value' => 'Cancelled' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] 

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