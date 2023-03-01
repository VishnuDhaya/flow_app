<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mode',50)->nullable(); 
            $table->longText('message')->nullable(); 
            $table->unsignedInteger('loan_id')->nullable();
            $table->unsignedInteger('borrower_id')->nullable();
            $table->unsignedInteger('send_at')->nullable();   
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
        Schema::dropIfExists('notifications');
    }
}
