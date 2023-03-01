<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterDataForScheduleSlot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'sch_slot', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
        DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'sch_status', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
        DB::table('master_data')->insert([ 
            ['country_code'=> '*', 'data_key' => 'sch_slot', 'data_code' => 'morning' , 'data_value' => 'Morning' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'sch_slot', 'data_code' => 'post_noon' , 'data_value' => 'Post Noon' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] 
            
            ]);
        DB::table('master_data')->insert([ 
                ['country_code'=> '*', 'data_key' => 'sch_status', 'data_code' => 'scheduled' , 'data_value' => 'Scheduled' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
    
                ['country_code'=> '*', 'data_key' => 'sch_status', 'data_code' => 'rescheduled' , 'data_value' => 'Rescheduled' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
                
                ['country_code'=> '*', 'data_key' => 'sch_status', 'data_code' => 'checked_in' , 'data_value' => 'Checked In' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
    
                ['country_code'=> '*', 'data_key' => 'sch_status', 'data_code' => 'checked_out' , 'data_value' => 'Checked Out' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] 

                ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_data_for_schedule_slot');
    }
}
