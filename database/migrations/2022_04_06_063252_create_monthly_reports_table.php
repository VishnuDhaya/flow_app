<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthlyReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('report')->create('monthly_mgmt_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',5);
            $table->unsignedInteger('month');
            $table->string('acc_prvdr_code',5)->nullable();
            $table->unsignedInteger('cust_reg_count')->nullable();
            $table->unsignedInteger('cust_active_count')->nullable();
            $table->unsignedInteger('cust_churn_count')->nullable();
            $table->decimal('cust_churn_perc',6,4)->nullable();
            $table->double('tot_disb_val')->nullable();
            $table->unsignedInteger('tot_disb_count')->nullable();
            $table->unsignedInteger('tot_fa_settled_count')->nullable();
            $table->double('gross_txn_val')->nullable();
            $table->double('os_val_eom')->nullable();
            $table->double('os_fee_eom')->nullable();
            $table->unsignedInteger('os_count_eom')->nullable();
            $table->unsignedInteger('od_count')->nullable();
            $table->double('od_amount')->nullable();
            $table->string('max_os_val', 25)->nullable();
            $table->decimal('ontime_repayment_rate',6,4)->nullable();
            $table->unsignedInteger('new_overdue_count')->nullable();
            $table->decimal('due_perc',6,4)->nullable();
            $table->double('new_overdue_val')->nullable();
            $table->double('revenue')->nullable();
            $table->double('excess_reversed')->nullable();
            $table->unsignedInteger('biz_supported_count')->nullable();
            $table->decimal('female_perc',6,4)->nullable();
            $table->decimal('youth_perc',6,4)->nullable();
            $table->unsignedInteger('retail_txn_count')->nullable();
            $table->double('retail_txn_val')->nullable();
            $table->unsignedInteger('people_benefited')->nullable();
            $table->double('revenue_by_small_biz')->nullable();
            $table->double('fee_per_cust')->nullable();
            $table->double('fee_per_fa')->nullable();
            $table->dateTime('run_at');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('report')->dropIfExists('monthly_mgmt_reports');
    }
}
