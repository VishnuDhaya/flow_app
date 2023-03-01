<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewTableRmMetrics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rm_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 3);
            $table->unsignedInteger('rm_id')->nullable();
            $table->unsignedInteger('appr_count')->nullable();
            $table->unsignedInteger('30_days_appr_count')->nullable();
            $table->unsignedInteger('max_time')->nullable();
            $table->unsignedInteger('avg_time')->nullable();
            $table->unsignedInteger('30_days_avg_time')->nullable();
            $table->unsignedInteger('30_days_max_time')->nullable();
            $table->dateTime('created_by')->nullable();
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
        Schema::dropIfExists('new_table_rm_metrics');
    }
}
