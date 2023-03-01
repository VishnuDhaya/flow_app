<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMasterDataFileProcess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data')->insert([ 
            
            ['country_code'=> '*', 'data_key' => 'lead_status', 'data_code' => '22_pending_data_upload' , 'data_value' => 'Pending Data Upload' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],

            ['country_code'=> '*', 'data_key' => 'lead_status', 'data_code' => '23_pending_data_process' , 'data_value' => 'Pending Data Process' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],

            ['country_code'=> '*', 'data_key' => 'lead_status', 'data_code' => '24_ineligible' , 'data_value' => 'Ineligible' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],


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
