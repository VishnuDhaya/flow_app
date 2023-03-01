<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('record_audits', function (Blueprint $table) {
            $table->increments('id');
            $table->string('remarks',256)->nullable();
            $table->json('data_before')->nullable();
            $table->json('data_after')->nullable();
            $table->string('record_type',25)->nullable();
            $table->unsignedInteger('record_id')->nullable();
            $table->string('record_code',50)->nullable();
            $table->string('audit_type',25)->nullable();
            $table->string('country_code',4)->nullable();
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
        Schema::dropIfExists('record_audits');
    }
}
