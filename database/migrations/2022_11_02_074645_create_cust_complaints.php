<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustComplaints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cust_complaints', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code', 5);
            $table->string('cust_id')->nullable();
            $table->date('raised_date', 10)->nullable();
            $table->string('complaint_type')->nullable();
            $table->string('remarks')->nullable();
            $table->json('resolution')->nullable();
            $table->string('status', 15)->nullable();
            $table->date('resolved_date', 10)->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->dateTime('created_at')->nullable();
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
        Schema::dropIfExists('cust_complaints');
    }
}
