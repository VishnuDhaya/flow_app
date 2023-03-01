<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLoansLoanApplnProductType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            
            $table->string('product_type', 20)->nullable()->after("product_name");
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            
            $table->string('product_type', 20)->nullable()->after("product_name");
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
