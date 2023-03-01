<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAccStmtRequestsModifyLambdaStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_acc_stmt_requests', function (Blueprint $table) {
            $table->string('lambda_status',20)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partner_acc_stmt_requests', function (Blueprint $table) {
            $table->string('lambda_status',15)->change();
        });
    }
}
