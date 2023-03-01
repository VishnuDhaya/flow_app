<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBondMonthly extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('report')->table('bonds_monthly', function(Blueprint $table){
           $table->double('bad_debts_recovered')->nullable()->after('bad_debts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('report')->table('bonds_monthly', function(Blueprint $table){
           $table->dropColumn('bad_debts_recovered');
        });
    }
}
