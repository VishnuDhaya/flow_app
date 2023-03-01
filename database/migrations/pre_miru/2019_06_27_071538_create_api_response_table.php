<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiResponseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_response', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('api_req_id')->nullable(false);
            $table->unsignedInteger('req_user_id')->nullable(false);
            $table->smallInteger('response_code')->nullable(false);        
            $table->string('response_msg',512)->nullable();
            $table->dateTime('response_time')->nullable(false);
            $table->string('response_status',16)->nullable(false);
            $table->text('response_json')->nullable(false);
            $table->smallInteger('ms')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_response');
    }
}
