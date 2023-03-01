<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAccStmtRequestsApCustIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acc_stmt_requests', function (Blueprint $table) {
            $table->string('ap_cust_ids',20)->change();
        });
        Schema::table('acc_stmt_requests', function (Blueprint $table) {
            $table->renameColumn('ap_cust_ids', 'ap_cust_id');
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
