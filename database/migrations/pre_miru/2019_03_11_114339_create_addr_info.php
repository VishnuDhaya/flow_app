<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddrInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('address_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code', 4)->nullable();
            $table->string('field_1',32)->nullable();
            $table->string('field_2',32)->nullable();
            $table->string('field_3',32)->nullable();
            $table->string('field_4',32)->nullable();
            $table->string('field_5',32)->nullable();
            $table->string('field_6',32)->nullable();
            $table->string('field_7',32)->nullable();
            $table->string('field_8',32)->nullable();
            $table->string('field_9',40)->nullable();
            $table->string('field_10',32)->nullable();
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
        Schema::dropIfExists('addr_data');
    }
}
