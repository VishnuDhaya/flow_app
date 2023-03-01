<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',60);
            $table->string('data_prvdr_code',20);
            $table->string('mob_num',20);
            $table->string('data_prvdr_cust_id',16);
            $table->string('password',255);
            $table->string('registered_by',10)->default('Agent');
            $table->boolean('verified_by_dp');
            $table->boolean('consent'); 
            $table->string('status',20)->default('Registered');

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
        Schema::dropIfExists('agents');
    }
}
