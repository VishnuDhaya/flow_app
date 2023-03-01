<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_request', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable(false);
            $table->string('h_url')->nullable(false);
            $table->string('h_page')->nullable(false);
            $table->string('h_user_agent')->nullable(false);
            $table->text('request_json')->nullable(false);
            $table->dateTime('request_time')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_request');
    }
}
