<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTfRepayTxnImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tf_repay_txn_imports', function (Blueprint $table) {

            $table->increments('id');
            $table->string('country_code',4)->nullable();
            $table->string('from_acc_num',10);
            $table->string('from_acc_name');
            $table->string('to_acc_num',10);
            $table->dateTime('stmt_txn_date');
            $table->double('cr_amt',15,2);
            $table->double('tot_repaid',15,2);
            $table->double('tot_bal',15,2);
            $table->string('acc_prvdr_code',5);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->unique(['from_acc_num','stmt_txn_date']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tf_repay_txn_imports');
    }
}
