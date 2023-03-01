<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LoanEventTimes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        schema::create('loan_event_times',function (Blueprint $table){
            $table ->increments('id');
            $table ->string('country_code',4);
            $table ->string('loan_appl_doc_id',50)->nullable();
            $table ->string('loan_doc_id',50)-> nullable();
            $table ->unsignedInteger('flow_rel_mgr_id')->nullable();
            $table ->string('disbursal_mode', 20)->nullable();
            $table ->time('rm_time')->nullable();
            $table ->time('cust_time')->nullable();
            $table ->time('total_wait_time')->nullable();
            $table ->time('ops_wait_time')->nullable();
            $table ->time('disbursal_time')->nullable();
            $table ->time('cs_time')->nullable();
            $table ->string('cust_conf_channel')->nullable();
            $table ->unsignedInteger('no_of_attempts')->nullable();
            $table ->unsignedInteger('created_by')->nullable();
            $table ->dateTime('created_at')->nullable();
            $table ->unsignedInteger('updated_by')->nullable();
            $table ->dateTime('updated_at')->nullable();

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
