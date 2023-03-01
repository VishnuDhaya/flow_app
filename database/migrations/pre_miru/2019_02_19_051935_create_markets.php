<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarkets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('markets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('org_id')->nullable();  // Need to add reference?
            $table->unsignedInteger('head_person_id')->nullable();
            $table->string('country_code',4)->nullable()->unique();
            $table->string('currency_code',3)->nullable();
            $table->string('time_zone',3)->nullable();
            $table->string('status',10)->nullable()->default('enabled');
            $table->unsignedInteger('created_by')->nullable();   
            $table->unsignedInteger('updated_by')->nullable();
            $table->nullableTimestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('markets');
    }
}
