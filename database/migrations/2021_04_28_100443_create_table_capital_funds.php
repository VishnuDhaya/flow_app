<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCapitalFunds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('capital_funds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable();
            $table->string('fund_code',32)->unique();
            $table->string('fund_name',32)->unique();
            $table->string('lender_code',4);
            $table->string('fund_type',20);
            $table->boolean('is_lender_default')->default(false);
            $table->dateTime('alloc_date')->nullable();
            $table->double('alloc_amount_usd' ,15,2)->nullable()->default(0);
            $table->double('alloc_amount_eur' ,15,2)->nullable()->default(0);
            $table->double('alloc_amount' ,15,2)->nullable()->default(0);
            $table->unsignedDecimal('tot_disb_amount' ,40,2)->nullable()->default(0); 
            $table->double('os_amount' ,15,2)->nullable()->default(0); 
            $table->unsignedDecimal('earned_fee' ,40,2)->nullable()->default(0); 
            $table->unsignedInteger('total_alloc_cust')->nullable()->default(0);
            $table->unsignedInteger('current_alloc_cust')->nullable()->default(0);
            $table->string('status',10);  
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
        Schema::dropIfExists('table_capital_funds');
    }
}
