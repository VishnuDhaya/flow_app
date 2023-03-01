<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMasterDataReconStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       DB::table('master_data')->insert([ 
            ['country_code'=> '*', 'data_key' => 'recon_status', 'data_code' => '05_multiple_fas_captured' , 'data_value' => 'Multiple FAs Captured ', 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled']
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
