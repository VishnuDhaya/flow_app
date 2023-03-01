<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableLoansAndLoanApplTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_applications', function(Blueprint $table){
            $table->string("applied_location")->after('cust_id')->nullable();
           
         });
         Schema::table('loans', function(Blueprint $table){
            $table->string("applied_location")->after('cust_id')->nullable();
           
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
