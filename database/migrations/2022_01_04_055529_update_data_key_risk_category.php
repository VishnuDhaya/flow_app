<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDataKeyRiskCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_data', function (Blueprint $table) {
            DB::update("update master_data set data_code = '0_low_risk' where id = '1164'");
            DB::update("update master_data set data_code = '1_medium_risk' where id = '1165'");
            DB::update("update master_data set data_code = '2_high_risk' where id = '1166'");
            DB::update("update master_data set data_code = '3_very_high_risk' where id = '1167'");

         });
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
