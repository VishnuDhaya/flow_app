<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiskCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('risk_category_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable();
            $table->string('data_prvdr_code',4)->nullable();
            $table->string('cust_state',24)->nullable();
            $table->double('exposure_from',15,2)->nullable();
            $table->double('exposure_upto',15,2)->nullable();
            $table->string('days_type',20)->nullable();
            $table->unsignedInteger('late_days_from')->nullable();
            $table->unsignedInteger('late_days_to')->nullable();
            $table->unsignedInteger('fas_from')->nullable();
            $table->unsignedInteger('fas_to')->nullable();
            $table->string('risk_category',20)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('risk_categories');
    }
}
