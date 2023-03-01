<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAccStmtRequestsOrigFlowReqIdPresignedUrl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acc_stmt_requests', function (Blueprint $table) {
            $table->string('orig_flow_req_id',32)->nullable()->after('flow_req_id');
            $table->string('presigned_url',400)->nullable()->after('lambda_status');            
            $table->string('end_date',12)->nullable()->after('ap_cust_id');
            $table->string('start_date',12)->nullable()->after('ap_cust_id');
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
