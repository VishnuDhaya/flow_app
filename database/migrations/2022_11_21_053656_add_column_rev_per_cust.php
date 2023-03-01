<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnRevPerCust extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection("report")->table('monthly_mgmt_reports', function (Blueprint $table) {
            $table->unsignedInteger('rev_per_cust')->nullable()->after('fee_per_cust');
            $table->unsignedInteger('rev_per_rm')->nullable()->after('fee_per_fa');
            $table->unsignedInteger('rm_count_for_rev_calc')->nullable()->after('fee_per_fa');
            $table->unsignedInteger('cust_count_for_rev_calc')->nullable()->after('fee_per_fa');
        });
    } 

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection("report")->table('monthly_mgmt_reports', function (Blueprint $table) {
            $table->dropColumn(['rev_per_cust','rev_per_rm','rm_count_for_rev_calc','cust_count_for_rev_calc']);
        });
    }
}


