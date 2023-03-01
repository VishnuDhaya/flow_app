<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProbationPeriod1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("alter table probation_period rename to probation_period_live");

        Schema::create('probation_period', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cust_id',20);
            $table->string('country_code',4);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('type',20)->nullable();
            $table->unsignedInteger('fa_count')->nullable();
            $table->string('status',15)->nullable();
            $table->unsignedInteger('perf_tot_loan_appls')->nullable()->default(0);
            $table->unsignedInteger('perf_tot_loans')->nullable()->default(0);
            $table->unsignedInteger('perf_tot_default_loans')->nullable()->default(0);
            $table->unsignedInteger('perf_late_loans')->nullable()->default(0);
            $table->unsignedInteger('perf_late_1_day_loans')->nullable()->default(0);
            $table->unsignedInteger('perf_late_2_day_loans')->nullable()->default(0);
            $table->unsignedInteger('perf_late_3_day_loans')->nullable()->default(0);
            $table->unsignedInteger('perf_late_3_day_plus_loans')->nullable()->default(0);
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
        Schema::dropIfExists('probation_period1');
    }
}
