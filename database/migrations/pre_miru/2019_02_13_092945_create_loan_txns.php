<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanTxns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_txns', function (Blueprint $table) {
            $table->increments('id');
            $table->string('loan_doc_id',50)->nullable();  
            $table->unsignedInteger('from_ac_id')->nullable();  
            $table->unsignedInteger('to_ac_id')->nullable();  
            $table->float('amount')->nullable();  
            $table->string('txn_type',50)->nullable();  
            $table->string('txn_id',50)->nullable();  
            $table->string('txn_mode',50)->nullable();  
            $table->string('txn_exec_by',50)->nullable();  
            $table->dateTime('txn_date')->nullable();  
            $table->longText('remarks')->nullable();  
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
        Schema::dropIfExists('loan_txns');
    }
}
