<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('investments','investment_txns');
        Schema::table('investment_txns', function (Blueprint $table) {
            $table->string('txn_type',15)->after('fund_code');
            $table->renameColumn('inv_amount','amount');
            $table->renameColumn('inv_currency_code','currency_code');
            $table->renameColumn('invested_date','txn_date');
        });
        Schema::table('investment_txns', function (Blueprint $table) {
            $table->string('txn_date')->nullable()->change();
            $table->date('realisation_date')->nullable()->after('txn_date');
        });
        Schema::table('capital_funds', function (Blueprint $table) {
            $table->string('fund_name',64)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('investment_txns', function (Blueprint $table) {
            $table->dropColumn(['txn_type','realisation_date']);
            $table->renameColumn('amount', 'inv_amount');
            $table->renameColumn('currency_code', 'inv_currency_code');
            $table->renameColumn('txn_date', 'invested_date');
    });
    Schema::rename('investment_txns', 'investments');
}
}
