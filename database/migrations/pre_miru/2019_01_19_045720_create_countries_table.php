<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country',60);
            $table->string('country_code',4);
            $table->string('currency',50);
            $table->string('currency_code',10);
            $table->string('time_zone',3);
            //$table->string('timezone_code',12);
            $table->string('latitude',20);
            $table->string('longitude',20);
            $table->enum('status', array('enabled', 'disabled'))->default('disabled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
