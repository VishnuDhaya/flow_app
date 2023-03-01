<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustNoBondMonthly extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('report')->table('bonds_monthly', function (Blueprint $table){
           $table->unsignedInteger('current_alloc_cust');
        });
        $funds = DB::select("select fund_code, current_alloc_cust from capital_funds where fund_type != 'internal'");
        $month = (DB::connection('report')->selectOne('select max(month) month from bonds_monthly'))->month;
        foreach($funds as $fund){
            DB::connection('report')->update("update bonds_monthly set current_alloc_cust = {$fund->current_alloc_cust} 
                                              where fund_code = '{$fund->fund_code}' and month = $month");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
