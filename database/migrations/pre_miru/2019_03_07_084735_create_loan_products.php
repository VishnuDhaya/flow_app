<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable();
            $table->string('product_name',30)->nullable(); 
            $table->unsignedInteger('product_template_id')->nullable();
            $table->string('data_prvdr_code',4)->nullable();
            $table->string('lender_code',4)->nullable();
            $table->unsignedInteger('cs_model_id')->nullable();
            $table->string('flow_fee_type',10)->nullable();
            $table->double('flow_fee' ,15,2)->nullable();
            $table->string('flow_fee_duration',20)->nullable();
            $table->smallInteger('duration')->nullable();
            $table->double('max_loan_amount', 15,2)->nullable();
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
        Schema::dropIfExists('loan_products');
    }
}
