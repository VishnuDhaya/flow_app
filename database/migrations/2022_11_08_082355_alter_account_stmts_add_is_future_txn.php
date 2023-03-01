<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAccountStmtsAddIsFutureTxn extends Migration
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
            $table->boolean('is_future_txn')->default(0)->after('is_reversal');
            $table->index('stmt_txn_id');
            $table->index('stmt_txn_date');
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
