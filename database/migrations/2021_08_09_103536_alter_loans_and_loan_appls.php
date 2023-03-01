<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLoansAndLoanAppls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loans', function(Blueprint $table){
            $table->string('acc_prvdr_code', 7)->after('cust_acc_id');
        });
        Schema::table('loan_applications', function(Blueprint $table){
            $table->string('acc_prvdr_code', 7)->after('cust_acc_id');
        });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loans', function(Blueprint $table){
            $table->dropColumn('account_prvdr_code');
        });
        Schema::table('loan_applications', function(Blueprint $table){
            $table->dropColumn('account_prvdr_code');
        });
    }
}
