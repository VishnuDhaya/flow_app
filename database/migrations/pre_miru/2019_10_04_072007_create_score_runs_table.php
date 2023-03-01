<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoreRunsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('score_runs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable();
            $table->string('data_prvdr_cust_id',20)->nullable();
            $table->unsignedInteger('run_id')->nullable();
            $table->unsignedInteger('score')->nullable();
            $table->string('result_code',20)->nullable();
            $table->string('cust_txn_file_name',50)->nullable();
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
        Schema::dropIfExists('score_runs');
    }
}
