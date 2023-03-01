<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumOfTxnInBondsMonthly extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('report')->table('bonds_monthly', function (Blueprint $table) {
            $table->integer("num_of_txn")->nullable()->after("tot_retail_txn_value");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bonds_monthly', function (Blueprint $table) {
            //
        });
    }
}
