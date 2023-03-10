<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameUnderusedFundsToFundUtilizationReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::rename('underused_funds','fund_utilization_reports');
        Schema::table('fund_utilization_reports', function(Blueprint $table){
            $table->double('util_perc')->nullable()->after('current_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fund_utilization_reports', function (Blueprint $table) {
            //
        });
    }
}
