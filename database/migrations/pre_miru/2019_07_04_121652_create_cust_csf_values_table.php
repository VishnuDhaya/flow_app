<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustCsfValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cust_csf_values', function (Blueprint $table) {
            $table->increments('id');
            $table->string('data_prvdr_cust_id',20)->nullable(false);
            $table->string('csf_type',40)->nullable(false);
            $table->float('csf_normal_value',8,2)->nullable(false);
            $table->unsignedInteger('run_id')->nullable(false);
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
