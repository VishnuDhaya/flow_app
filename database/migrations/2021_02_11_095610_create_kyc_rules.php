<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKycRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kyc_rules', function (Blueprint $table) {
            $table->increments('id'); 
            $table->string('rule_group_code',40)->nullable();
            $table->string('alias_name',64)->nullable();
            $table->string('rule_type',10)->nullable()->default('check');
            $table->string('rule_message',256)->nullable();
            $table->string('status',10)->nullable();
            $table->string('country_code',4)->nullable();
            $table->string('data_prvdr_code',4)->nullable();
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
        Schema::dropIfExists('kyc_rule');
    }
}
