<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTxnExecutedByInLoanTxnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::update("update loan_txns lt, loans l set lt.txn_exec_by = l.paid_by where l.loan_doc_id = lt.loan_doc_id and lt.txn_exec_by is null and l.paid_by is not null;
        ");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_txns', function (Blueprint $table) {
            //
        });
    }
}
