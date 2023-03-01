<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLoanApprovedDateInLoanAppls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("update loan_applications join loans on loan_applications.id = loans.loan_appl_id set loan_applications.loan_approved_date = loans.loan_approved_date where loan_applications.loan_approved_date is null");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_appls', function (Blueprint $table) {
            //
        });
    }
}
