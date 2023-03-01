<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCustEvalChecklists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cust_eval_checklists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code', 5);
            $table->string('data_prvdr_code',4);
            $table->string('data_prvdr_cust_id',20);
            $table->string('biz_name',80);
            $table->double('wallet_balance',15,2)->nullable();
            $table->dateTime('wallet_bal_time')->nullable();
            $table->json('checklist_json');
            $table->boolean('rm_recommendation');
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
        Schema::dropIfExists('table_cust_eval_checklists');
    }
}
