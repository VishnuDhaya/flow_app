<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCsDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cs_devices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',5);
            $table->string('type',10)->nullable();
            $table->string('number', 255)->nullable();
            $table->unsignedInteger('person_id')->nullable();
            $table->string('call_status',20)->nullable();
            $table->date('date')->nullable();
            $table->time('call_duration')->nullable();
            $table->string('status',10)->nullable();
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
        Schema::dropIfExists('cs_devices');
    }
}
