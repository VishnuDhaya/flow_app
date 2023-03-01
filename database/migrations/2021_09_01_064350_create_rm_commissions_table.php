<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRmCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('data_prvdr_code', 5)->nullable();
            $table->unsignedInteger('rm_id')->nullable();
            $table->string('rm_name', 50)->nullable();
            $table->unsignedInteger('month');
            $table->string('country_code', 5);
            $table->double('cust_acquisition_comm', 10,2)->default(0)->nullable();
            $table->double('ontime_repay_comm', 10,2)->default(0)->nullable();
            $table->double('facilitation_comm', 10,2)->default(0)->nullable();
            $table->double('agent_of_the_month_comm', 10,2)->default(0)->nullable();
            $table->double('total_paid', 10,2)->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commissions');
    }
}
