<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAccountStmtsAddSmsLogId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_stmts', function (Blueprint $table) {
            $table->unsignedInteger('sms_log_id')->nullable()->after('import_id');
            $table->string('sms_import_status', 20)->nullable()->after('import_id');
            $table->json('sms_content')->nullable()->after('loan_txn_type');
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
