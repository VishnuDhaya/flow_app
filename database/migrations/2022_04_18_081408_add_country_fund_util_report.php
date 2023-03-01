<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryFundUtilReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fund_utilization_reports', function (Blueprint $table) {
            $table->string('country_code',5);
        });
        DB::update("update fund_utilization_reports set country_code = 'UGA'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fund_utilization_reports', function (Blueprint $table) {
            $table->dropColumn('country_code');
        });
    }
}
