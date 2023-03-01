<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCapitalFundsAddCalcAllocDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('capital_funds', function(Blueprint $table){
           $table->dateTime("calc_alloc_date")->nullable()->after("alloc_date");
        });
        DB::update("update capital_funds set calc_alloc_date = '2021-06-22 00:00:00' where fund_code in ('VC-MAY21-USD', 'VC-MAY21-EUR')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('capital_funds', function(Blueprint $table){
           $table->dropColumn("calc_alloc_date");
        });
    }
}
