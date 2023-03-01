<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedAtColumnInAccountStmtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_stmts', function (Blueprint $table) {
            $table->dateTime('created_at')->nullable()->after('loan_txn_type');
            $table->unsignedInteger('created_by')->nullable()->after('created_at');
            DB::statement("ALTER TABLE account_stmts CHANGE stmt_txn_id stmt_txn_id varchar(64) DEFAULT NULL"); //Make txn_id column as nullable
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
            $table->dropColumn('created_at');
            $table->dropColumn('created_by');
        });
    }
}
