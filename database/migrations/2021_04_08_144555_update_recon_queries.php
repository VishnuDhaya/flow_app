<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateReconQueries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("update account_stmts set loan_doc_id = null , recon_status = null");
        DB::update("update loan_txns set to_ac_id = 2895 where txn_type = 'payment' and to_ac_id = 3");
        DB::update("alter table account_stmts drop index account_stmts_account_id_stmt_txn_id_stmt_txn_date_unique");
        DB::update("update account_stmts set stmt_txn_id = TRIM('.0' FROM stmt_txn_id)");
        DB::update("delete t1 FROM account_stmts t1 INNER JOIN account_stmts t2 WHERE t1.id < t2.id AND t1.stmt_txn_id = t2.stmt_txn_id");
        DB::update("alter TABLE account_stmts ADD CONSTRAINT account_stmts_account_id_stmt_txn_id_stmt_txn_date_unique UNIQUE (account_id,stmt_txn_id,stmt_txn_date)");
        #DB::update("alter TABLE loan_txns ADD CONSTRAINT txn_id UNIQUE (txn_id)");
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
