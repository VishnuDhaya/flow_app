<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoiceCallLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voice_call_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable();
            $table->string('direction',10)->nullable();
            $table->text('vendor_ref_id')->nullable();
            $table->string('vendor_code',5)->nullable();
            $table->string('purpose',10)->nullable();
            $table->string('mobile_num',10)->nullable();
            $table->json('details')->nullable();
            $table->string('person_id',10)->nullable();
            $table->unsignedInteger('cost_of_call')->nullable();
            $table->string('status',20)->nullable();
            $table->string('hang_up_cause',20)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table ->unsignedInteger('created_by')->nullable();
            $table ->unsignedInteger('updated_by')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('call_logs');
    }
}
