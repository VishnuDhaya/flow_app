<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFinanceReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('report')->table('finance_reports', function(Blueprint $table){
            $table->double('wallet_balance')->nullable()->after('dp_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::connection('report')->table('finance_reports', function(Blueprint $table){
            $table->dropColumn('wallet_balance');
        });
    }
}
