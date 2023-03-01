<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableFaRepeatQueue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fa_repeat_queue', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code', 4);
            $table->string('loan_doc_id', 50)->nullable();
            $table->string('cust_id', 20)->nullable();
            $table->string('mobile_num', 10);
            $table->string('status', 20)->nullable();
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
        Schema::dropIfExists('fa_repeat_queue');
    }
}
