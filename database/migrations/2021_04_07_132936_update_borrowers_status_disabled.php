<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBorrowersStatusDisabled extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("update borrowers set status = 'disabled' where kyc_status != 'completed'");
        DB::update("update borrowers set status = 'disabled' where DATEDIFF(CURRENT_DATE(), last_loan_date) > 90");
        DB::update("update borrowers set status = 'disabled' where current_aggr_doc_id is null or aggr_valid_upto < CURRENT_DATE()");
        DB::update("update borrowers set perf_tot_loan_appls = tot_loan_appls, perf_tot_loans = tot_loans, perf_tot_default_loans = tot_default_loans, perf_late_loans = late_loans, perf_late_1_day_loans = late_1_day_loans, perf_late_2_day_loans = late_2_day_loans, perf_late_3_day_loans = late_3_day_loans");
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
