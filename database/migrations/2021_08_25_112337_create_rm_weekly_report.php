<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRmWeeklyReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('report')->create('rm_weekly_report', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('rm_id');
            $table->string('first_name',20)->nullable();
            $table->string('last_name',20)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedInteger('od_calls')->nullable()->default(0);
            $table->unsignedInteger('od_calls_cust')->nullable()->default(0);

            $table->unsignedInteger('sow_active_cust')->nullable()->default(0);
            $table->unsignedInteger('all_calls')->nullable()->default(0);
            $table->unsignedInteger('all_calls_cust')->nullable()->default(0);
            $table->unsignedInteger('tot_enabled_cust')->nullable()->default(0);
            $table->unsignedInteger('tot_cust')->nullable()->default(0);

            $table->unsignedInteger('fas_repaid_ontime')->nullable()->default(0);
            $table->unsignedInteger('fas_repaid_1d_late')->nullable()->default(0);
            $table->unsignedInteger('fas_repaid_2d_late')->nullable()->default(0);
            $table->unsignedInteger('fas_repaid_3d_late')->nullable()->default(0);
            $table->unsignedInteger('fas_repaid_3_plus_d_late')->nullable()->default(0);

            $table->unsignedInteger('sow_od_fas')->nullable()->default(0);
            $table->double('sow_od_perc',15, 2)->nullable()->default(0);
            $table->unsignedInteger('fas_od_settled')->nullable()->default(0);
            $table->unsignedInteger('fas_ppaid')->nullable()->default(0);
            $table->unsignedInteger('fas_fresh_od')->nullable()->default(0);
            $table->unsignedInteger('eow_od_fas')->nullable()->default(0);
            $table->double('eow_od_perc',15, 2)->nullable()->default(0);

            $table->double('sow_od_amt',15, 2)->nullable()->default(0);
            $table->double('settled_od_amt',15, 2)->nullable()->default(0);
            $table->double('ppaid_amt',15, 2)->nullable()->default(0);
            $table->double('fresh_od_amt',15, 2)->nullable()->default(0);
            $table->double('eow_od_amt',15, 2)->nullable()->default(0);

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
        Schema::dropIfExists('rm_weekly_report');
    }
}
