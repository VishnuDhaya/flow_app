<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCapitalFunds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('capital_funds', function(Blueprint $table){

            $table->string('fe_currency_code', 5)->after('alloc_date');
            $table->decimal('forex',8,4)->after('fe_currency_code');
            $table->decimal('alloc_amount_fc', 15, 2)->after('forex');
            $table->unsignedInteger('duration')->nullable();
            $table->decimal('profit_rate',8,4)->nullable();
            $table->decimal('license_rate', 8, 4)->nullable();
            $table->decimal('floor_rate',8,4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('capital_funds', function(Blueprint $table){

            $table->dropColumn('fe_currency_code');
            $table->dropColumn('forex');
            $table->dropColumn('alloc_amount_fc');
            $table->dropColumn('profit_rate');
            $table->dropColumn('license_rate');
            $table->dropColumn('floor_rate');
        });
    }
}
