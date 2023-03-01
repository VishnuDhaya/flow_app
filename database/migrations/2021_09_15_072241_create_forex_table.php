<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forex_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('base', 5);
            $table->string('quote', 5);
            $table->double('forex_rate',20, 10);
            $table->dateTime('forex_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forex_rates');
    }
}
