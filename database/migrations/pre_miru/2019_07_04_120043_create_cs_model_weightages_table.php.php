<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCsModelWeightagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cs_model_weightages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable(false);
            $table->string('cs_model_code',32)->nullable(false);
            $table->string('csf_type',40)->nullable(false);
            $table->tinyInteger('new_cust_weightage')->nullable();
            $table->tinyInteger('repeat_cust_weightage')->nullable();
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
        //
    }
}
