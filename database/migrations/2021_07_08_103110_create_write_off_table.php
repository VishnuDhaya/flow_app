<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWriteOffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_write_off', function (Blueprint $table) {
            $table->increments('id');
            $table->string('loan_doc_id', 50)->nullable();
            $table->string('country_code', 4);
            $table->string('acc_prvdr_code', 4);
            $table->unsignedInteger('year');
            $table->string('write_off_status', 20)->nullable();
            $table->unsignedInteger('loan_prov_id')->nullable();
            $table->unsignedInteger('write_off_amount')->default(0);
            $table->unsignedInteger('recovery_amount')->default(0);
            $table->longText('remarks')->nullable();
            $table->unsignedInteger('req_by')->nullable();
            $table->unsignedInteger('appr_by')->nullable();
            $table->dateTime('req_date')->nullable();
            $table->dateTime('appr_date')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
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
        Schema::dropIfExists('loan_write_off');
    }
}
