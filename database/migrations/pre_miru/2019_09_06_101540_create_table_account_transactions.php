<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAccountTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_txns', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable();
            $table->unsignedInteger('acc_id')->nullable();
            $table->dateTime('txn_date')->nullable();  
            $table->double('credit' ,15,2)->nullable();
            $table->double('debit' ,15,2)->nullable();
            $table->double('balance' ,15,2)->nullable();    
            $table->unsignedInteger('ref_acc_id')->nullable();  
            $table->string('txn_id',50)->nullable();  
            $table->string('acc_txn_type',50)->nullable(); 
            $table->string('txn_mode',50)->nullable();  
            $table->string('txn_exec_by',50)->nullable();  
            $table->string('remarks', 255)->nullable();  
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
        Schema::dropIfExists('account_transactions');
    }
}
