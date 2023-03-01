<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewCustCsfValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_cust_csf_values', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code', 4)->nullable(false);
            $table->string('acc_prvdr_code', 4)->nullable(false);
            $table->string('acc_number', 150)->nullable(false);
            $table->json('cust_score_factors')->nullable(false);
            $table->string('score',10)->nullable(true);
            $table->string('result', 30)->nullable(true);
            $table->json('conditions')->nullable(true);
            $table->string('run_id', 32)->nullable(false);
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
        Schema::dropIfExists('new_cust_csf_values');
    }
}
