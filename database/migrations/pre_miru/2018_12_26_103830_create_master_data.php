<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_data', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable();
            $table->string('data_type',16)->nullable();
            $table->string('data_key',40)->nullable();
            $table->string('parent_data_code',40)->nullable();
            $table->string('data_code',40)->nullable();
            $table->string('data_value',40)->nullable();
            $table->string('status',10)->nullable()->default('enabled');
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
        Schema::dropIfExists('master_data');
    }
}
