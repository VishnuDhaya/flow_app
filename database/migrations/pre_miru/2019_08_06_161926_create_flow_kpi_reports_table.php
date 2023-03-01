<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlowKpiReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flow_kpi_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable('false');
            $table->date('report_date')->nullable('false');
            $table->string('kpi_metric', 40)->nullable('false');
            $table->string('kpi_unit', 10)->nullable('false');
            $table->double('kpi_value')->nullable('false');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flow_kpi_reports');
    }
}
