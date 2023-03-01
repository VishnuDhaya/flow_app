<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_events', function (Blueprint $table) {
            $table->increments('id');   
            $table->string('loan_doc_id', 50)->nullable();  
            $table->string('event_type',50)->nullable();
            $table->dateTime('event_datetime')->nullable();
            $table->string('event_data',100)->nullable();
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
        Schema::dropIfExists('loan_events');
    }
}
