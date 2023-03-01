<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLoansLoanApplnsApproverRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            
            $table->string('approver_role', 24)->nullable()->after("loan_approver_id");
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            
            $table->string('approver_role', 24)->nullable()->after("loan_approver_id");
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
