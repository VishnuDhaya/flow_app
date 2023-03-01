<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustAccStmts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cust_acc_stmts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable();
            $table->unsignedInteger('run_id')->nullable();
            $table->string('dp_code',4)->nullable();      
            $table->string('data_prvdr_cust_id',20)->nullable();
            $table->dateTime('txn_date')->nullable();
            $table->string('txn_id',40)->nullable();
            $table->string('descr',100)->nullable();
            $table->double('dr_amt',15,2)->nullable();
            $table->double('cr_amt',15,2)->nullable();
            $table->double('balance' ,15,2)->nullable(); 
            $table->double('comms' ,15,2)->nullable(); 
            $table->double('float_amt' ,15,2)->nullable(); 
            $table->tinyInteger('is_float')->nullable();
            
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cust_acc_stmts');
    }
}
