<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanRecoveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_recoveries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cust_id', 50);
            $table->string('biz_name', 80);
            $table->string('loan_doc_id', 50);
            $table->double('amount', 15,2);
            $table->unsignedInteger('rs_id');
            $table->string('status', 40);
            $table->string('country_code', 5);
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_recoveries');
    }
}
