<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AcctDataPrvdrComm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acct_data_prvdr_comm', function (Blueprint $table) {
            $table->increments('id');
            $table->string('data_prvdr_code',4)->nullable();
            $table->string('cust_id',20)->nullable();
            $table->string('loan_doc_id',50)->nullable();
            $table->double('credit', 15,2)->nullable();
            $table->double('debit', 15,2)->nullable();
            $table->date('comm_date')->nullable();
            $table->unsignedInteger('created_by')->nullable();   // Need to add reference?
            $table->unsignedInteger('updated_by')->nullable();
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
