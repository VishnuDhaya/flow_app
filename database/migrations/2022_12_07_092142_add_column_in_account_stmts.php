<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInAccountStmts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_stmts', function(Blueprint $table)
        {
            $table->string('photo_statement_proof',50)->after('loan_doc_id')->nullable();
            $table->string('reason_for_add_txn', 64)->nullable()->after('photo_statement_proof');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_stmts', function (Blueprint $table) {
            //
        });
    }
}
