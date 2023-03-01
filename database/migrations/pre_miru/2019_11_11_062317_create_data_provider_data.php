<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataProviderData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_provider_data', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('txn_date')->nullable();
            $table->string('description',100)->nullable();
            $table->string('debit',40)->nullable();
            $table->string('credit',40)->nullable();
            $table->string('balance',40)->nullable();
            $table->string('terminal',20)->nullable();
            $table->string('tx_id',20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_provider_data');
    }
}
