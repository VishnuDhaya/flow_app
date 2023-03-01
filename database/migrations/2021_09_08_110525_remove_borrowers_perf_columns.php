<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveBorrowersPerfColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('borrowers', function(Blueprint $table){
            $table->dropColumn(['perf_tot_loan_appls', 'perf_tot_loans', 'perf_tot_default_loans', 'perf_late_loans', 'perf_late_1_day_loans',
            'perf_late_2_day_loans', 'perf_late_3_day_loans', 'perf_late_3_day_plus_loans', 'require_addl_appr', 'addl_appr_until']);
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
