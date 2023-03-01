<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateReconStatusForSkipTxnIdCheck extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("update account_stmts a, loan_txns lt set recon_status = '71_pending_manual_recon' where  lt.txn_id = a.stmt_txn_id and lt.reason_for_skip is not null");
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
