<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLoanDocIdInCallLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("update call_logs c, loans l set c.loan_doc_id = l.loan_doc_id where c.loan_doc_id is null and ((JSON_CONTAINS(call_purpose, JSON_ARRAY('cust_sms_not_rcvd'))) or (JSON_CONTAINS(call_purpose, JSON_ARRAY('cust_sms_not_sent')))) and date(disbursal_date) = date(c.created_at) and l.cust_id = c.cust_id") ;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('call_logs', function (Blueprint $table) {
            //
        });
    }
}
