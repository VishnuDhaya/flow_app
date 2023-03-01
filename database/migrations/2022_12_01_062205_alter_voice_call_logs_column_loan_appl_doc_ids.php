<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterVoiceCallLogsColumnLoanApplDocIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('voice_call_logs', function (Blueprint $table) {
            $table->json('loan_appl_doc_ids')->nullable()->after('details');
        });

        DB::statement("ALTER TABLE voice_call_logs CHANGE loan_appl_doc_ids loan_appl_doc_ids JSON  DEFAULT ('[]') ");
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
