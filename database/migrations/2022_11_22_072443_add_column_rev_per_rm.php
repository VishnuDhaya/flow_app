<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnRevPerRm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection("report")->table('live_reports', function (Blueprint $table) {
            $table->unsignedInteger('rev_per_cust')->nullable()->after('cust_revenue');
            $table->unsignedInteger('rev_per_rm')->nullable()->after('tot_retail_txn_val');
            $table->unsignedInteger('rm_count_for_rev_calc')->nullable()->after('ontime_count');
            $table->unsignedInteger('cust_count_for_rev_calc')->nullable()->after('ontime_count');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection("report")->table('live_reports', function (Blueprint $table) {
            $table->dropColumn(['rev_per_cust','rev_per_rm','rm_count_for_rev_calc','cust_count_for_rev_calc']);
        });
    }
}
