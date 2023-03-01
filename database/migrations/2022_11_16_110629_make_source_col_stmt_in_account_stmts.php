<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeSourceColStmtInAccountStmts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("update account_stmts set source = 'stmt' where source is null and account_id = 4161");
        DB::update("update account_stmts set source = 'stmt' where source is null and account_id = 4094");
        DB::update("update account_stmts set source = 'stmt' where source is null and account_id = 3605");
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
