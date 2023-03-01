<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPartnerAccStmtRequestsRenameApCustIdToAccNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_acc_stmt_requests', function (Blueprint $table) {
            $table->renameColumn('ap_cust_id', 'acc_number');
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
            $table->renameColumn('acc_number', 'ap_cust_id');
        });
    }
}
