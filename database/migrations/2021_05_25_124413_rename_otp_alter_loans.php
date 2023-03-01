<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameOtpAlterLoans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::rename('otp', 'otps');
        Schema::table('loan_applications', function (Blueprint $table) {

            $table->dropColumn('confirm_code');
        });
        Schema::table('loans', function (Blueprint $table) {

            $table->dropColumn('confirm_code');
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
