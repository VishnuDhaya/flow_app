<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccStmtRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acc_stmt_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code', 6)->nullable();
            $table->string('acc_prvdr_code', 6)->nullable();
            $table->string('flow_req_id', 32);
            $table->string('ap_req_id', 32)->nullable();
            $table->json('ap_cust_ids');
            $table->string('status', 15)->nullable();
            $table->dateTime('req_time')->nullable();
            $table->dateTime('resp_time')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acc_stmt_requests');
    }
}
