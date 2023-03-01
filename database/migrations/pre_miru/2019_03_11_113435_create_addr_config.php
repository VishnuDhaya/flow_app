<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddrConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addr_config', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable();
            $table->string('field_num',10)->nullable();
            $table->string('field_code',20)->nullable();
            $table->string('field_name',20)->nullable();
            $table->string('field_type',20)->nullable();
            $table->string('child_field_code',20)->nullable();
            $table->string('validation_rules',60)->nullable();
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
        Schema::dropIfExists('market_addr_config');
    }
}
