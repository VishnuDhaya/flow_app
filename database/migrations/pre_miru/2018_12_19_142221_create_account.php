<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        

        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable();
            $table->string('cust_id', 50)->nullable();
            $table->string('lender_code',4)->nullable();
            $table->string('acc_prvdr_name',100)->nullable();
            $table->string('acc_prvdr_code',4)->nullable();
            $table->string('type',50)->nullable();
            $table->string('holder_name',100)->nullable();
            $table->string('acc_number',150)->nullable();
            $table->string('branch',100)->nullable();
            $table->boolean('is_primary_acc')->nullable();
            $table->boolean('to_recon')->default(false);
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
        Schema::dropIfExists('accounts');
    }
}
