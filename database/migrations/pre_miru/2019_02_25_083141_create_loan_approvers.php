<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanApprovers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_approvers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable();
            $table->unsignedInteger('person_id')->nullable();
            $table->string('email_id',100)->nullable();
            $table->string('mobile_num',20)->nullable();
            $table->unsignedInteger('created_by')->nullable();   
            $table->string('status',20)->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('created_on')->nullable(); 
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
        Schema::dropIfExists('loan_approvers');
    }
}
