<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeadStatusNewStatusMasterData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         DB::table('master_data')->insert([
            ['country_code'=> '*',  'data_type' => 'common', 'data_key' => 'lead_status', 'data_code' => '09_pending_rm_alloc', 'data_value' => 'Pending RM Allocation', 'status' => 'enabled','created_at' => now()]
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
