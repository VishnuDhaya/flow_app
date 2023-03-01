<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InboundVoiceCallLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inbound_voice_call_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',5);
            $table->date('date',10)->nullable();
            $table->string('vendor_ref_id');
            $table->string('direction',10)->nullable();
            $table->string('cust_id',30)->nullable();
            $table->string('cust_number',15)->nullable();
            $table->string('cs_id',5)-> nullable();
            $table->string('csm_number',80)->nullable();
            $table->string('hangup_causes')->nullable();
            $table->string('status')->nullable();
            $table->unsignedInteger('cost_of_call')->nullable();
            $table->time('call_duration')->nullable();
            $table->string('recording_url')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
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
