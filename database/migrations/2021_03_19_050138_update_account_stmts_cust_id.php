<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAccountStmtsCustId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('account_stmts', function (Blueprint $table) {
           $table->string('cust_id',20)->nullable()->after("account_id");
            $table->string('data_prvdr_cust_id',20)->nullable()->after("stmt_txn_id");
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
