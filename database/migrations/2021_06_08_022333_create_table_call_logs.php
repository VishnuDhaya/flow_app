<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCallLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('call_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable();
            $table->string('cust_id',20);
            $table->string('cust_name',25)->nullable();
            $table->unsignedInteger('call_logger_id')->nullable();
            $table->string('call_logger_name',50)->nullable();
            $table->dateTime('call_start_time');
            $table->dateTime('call_end_time')->nullable();
            $table->unsignedInteger('time_spent');
            $table->string('remarks',50)->nullable();
            $table->json('call_purpose')->nullable();
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
        Schema::dropIfExists('table_call_logs');
    }
}
