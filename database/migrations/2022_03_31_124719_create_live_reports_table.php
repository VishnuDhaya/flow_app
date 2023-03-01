<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiveReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('report')->create('live_reports', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 5);
            $table->string('acc_prvdr_code', 5)->nullable();
            $table->unsignedInteger("reg_count")->nullable();
            $table->unsignedInteger('enable_count')->nullable();
            $table->unsignedInteger('active_count')->nullable();
            $table->unsignedInteger('open_count')->nullable();
            $table->unsignedInteger('supported_custs')->nullable();
            $table->unsignedInteger('custs_w_os_fa')->nullable();
            $table->unsignedInteger('custs_w_30d_od')->nullable();
            $table->decimal('male_perc', 6, 4)->nullable();
            $table->decimal('female_perc', 6, 4)->nullable();
            $table->decimal('youth_perc', 6, 4)->nullable();
            $table->double('tot_disb_amt')->nullable();
            $table->unsignedInteger('tot_disb_fas')->nullable();
            $table->double('gross_txn_value')->nullable();
            $table->double('revenue')->nullable();
            $table->double('excess_reversed')->nullable();
            $table->double('avg_fa_size')->nullable();
            $table->double('avg_fa_duration')->nullable();
            $table->double('avg_fa_fee')->nullable();
            $table->double('write_off_amt')->nullable();
            $table->double('recovery_amt')->nullable();
            $table->unsignedInteger('write_off_count')->nullable();
            $table->double('principal_os')->nullable();
            $table->double('fee_os')->nullable();
            $table->string('max_os', 25)->nullable();
            $table->string('max_ontime_repay_rate', 25)->nullable();
            $table->double('od_amount')->nullable();
            $table->unsignedInteger('od_count')->nullable();
            $table->unsignedInteger('os_count')->nullable();
            $table->unsignedInteger('settled_count')->nullable();
            $table->unsignedInteger('ontime_count')->nullable();
            $table->decimal('ontime_repayment_rate', 6, 3)->nullable();
            $table->double('tot_retail_txn_count')->nullable();
            $table->double('people_benefited')->nullable();
            $table->double('tot_retail_txn_val')->nullable();
            $table->double('cust_revenue')->nullable();
            $table->double('par15')->nullable();
            $table->double('par30')->nullable();
            $table->double('par60')->nullable();
            $table->double('par90')->nullable();
            $table->double('npl')->nullable();
            $table->date('report_date');
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
        Schema::connection('report')->dropIfExists('live_reports');
    }
}
