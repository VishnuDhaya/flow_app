<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurposeToLoanAppls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->string('loan_purpose',32)->after('cust_acc_id')->nullable();
        });

        DB::update("update loan_applications set loan_purpose = 'float_advance' where loan_purpose is null");
        DB::update("update loans set loan_purpose = 'float_advance' where loan_purpose is null");

        DB::update("update loan_products set status = 'disabled' where id in (94,95,96,97)");
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
