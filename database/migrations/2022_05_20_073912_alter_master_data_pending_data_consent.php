<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMasterDataPendingDataConsent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data')->insert(
			['country_code' => '*', 'data_key' => 'lead_status', 'data_code' => '21_pending_data_consent', 'data_value' => 'Pending Data Consent',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_by' => '0', 'created_at' => now()]);
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
