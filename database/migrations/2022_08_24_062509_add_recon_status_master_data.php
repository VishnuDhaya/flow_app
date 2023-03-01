<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReconStatusMasterData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data')->insert(
            ['country_code'=> '*', 'data_key' => 'recon_status', 'data_code' => '11_capture_disb_rvrsl_pending' , 'data_value' => 'Capture Disbursal Reversal Pending' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled']);

            
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
