<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccProviders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acc_providers', function (Blueprint $table) {
              $table->increments('id');
            $table->string('country_code',4)->nullable();
            $table->string('name',100)->nullable();
            $table->text('account_types')->nullable();
            $table->string('acc_prvdr_code',50)->nullable();
            $table->unsignedInteger('org_id')->nullable();
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
        Schema::dropIfExists('acc_providers');
    }
}
