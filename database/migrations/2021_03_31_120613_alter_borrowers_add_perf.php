<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterBorrowersAddPerf extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('borrowers', function (Blueprint $table) {
            
            $table->dateTime('perf_eff_date')->nullable()->after("last_loan_date");
            $table->unsignedInteger('perf_tot_loan_appls')->nullable()->default(0)->after("perf_eff_date");
            $table->unsignedInteger('perf_tot_loans')->nullable()->default(0)->after("perf_tot_loan_appls");
            $table->unsignedInteger('perf_tot_default_loans')->nullable()->default(0)->after("perf_tot_loans");
            $table->unsignedInteger('perf_late_loans')->nullable()->default(0)->after("perf_tot_default_loans");
            $table->unsignedInteger('perf_late_1_day_loans')->nullable()->default(0)->after("perf_late_loans");
            $table->unsignedInteger('perf_late_2_day_loans')->nullable()->default(0)->after("perf_late_1_day_loans");
            $table->unsignedInteger('perf_late_3_day_loans')->nullable()->default(0)->after("perf_late_2_day_loans");
            $table->unsignedInteger('perf_late_3_day_plus_loans')->nullable()->default(0)->after("perf_late_3_day_loans");
            
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
