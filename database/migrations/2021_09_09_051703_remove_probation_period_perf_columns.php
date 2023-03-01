<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveProbationPeriodPerfColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('probation_period', function(Blueprint $table){
            $table->dropColumn(['perf_tot_loan_appls', 'perf_tot_loans', 'perf_tot_default_loans', 'perf_late_loans', 'perf_late_1_day_loans',
            'perf_late_2_day_loans', 'perf_late_3_day_loans', 'perf_late_3_day_plus_loans']);
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
