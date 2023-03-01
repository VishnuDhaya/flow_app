<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMasterDataReassignReasonDataConsent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data')->insert(
            ['country_code'=> '*', 'data_key' => 'reassign_reason', 'data_code' => 'incorrect_data_consent' , 'data_value' => 'Incorrect Data Consent' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'pre_appr_ignr_reasons', 'data_code' => 'no_recent_visit' , 'data_value' => 'No Recent Visit' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
        );
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
