<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFinanceReportsDPtoAP extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('report')->table('finance_reports', function(Blueprint $table){
            $table->renameColumn('dp_code', 'acc_prvdr_code');
        });

        Schema::connection('report')->table('client_performance_funds', function(Blueprint $table){
            $table->renameColumn('data_prvdr_code', 'acc_prvdr_code');
            $table->renameColumn('data_prvdr_cust_id', 'acc_number');
        });

        Schema::connection('report')->table('client_performance', function(Blueprint $table){
            $table->renameColumn('data_prvdr_code', 'acc_prvdr_code');
            $table->renameColumn('data_prvdr_cust_id', 'acc_number');
        });
        Schema::connection('report')->table('portfolio_risk', function(Blueprint $table){
            $table->renameColumn('data_prvdr_code', 'acc_prvdr_code');
        });
        Schema::connection('report')->table('portfolio_risks', function(Blueprint $table){
            $table->renameColumn('data_prvdr_code', 'acc_prvdr_code');
        });
        Schema::connection('report')->table('bad_debts', function(Blueprint $table){
            $table->renameColumn('data_prvdr_code', 'acc_prvdr_code');
        });
        Schema::connection('report')->table('revpercustomer', function(Blueprint $table){
            $table->renameColumn('data_prvdr_code', 'acc_prvdr_code');
        });


        DB::connection('report')->update("update finance_reports set acc_prvdr_code = 'UMTN' where acc_prvdr_code = 'UFLO'");
        DB::connection('report')->update("update client_performance_funds set acc_prvdr_code = 'UMTN' where acc_prvdr_code = 'UFLO'");
        DB::connection('report')->update("update portfolio_risk set acc_prvdr_code = 'UMTN' where acc_prvdr_code = 'UFLO'");
        DB::connection('report')->update("update portfolio_risks set acc_prvdr_code = 'UMTN' where acc_prvdr_code = 'UFLO'");
        DB::connection('report')->update("update bad_debts set acc_prvdr_code = 'UMTN' where acc_prvdr_code = 'UFLO'");
        DB::connection('report')->update("update revpercustomer set acc_prvdr_code = 'UMTN' where acc_prvdr_code = 'UFLO'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('report')->table('finance_reports', function(Blueprint $table){
            $table->renameColumn('acc_prvdr_code', 'dp_code');
        });

        Schema::connection('report')->table('client_performance_funds', function(Blueprint $table){
            $table->renameColumn('acc_prvdr_code', 'data_prvdr_code');
        });
        Schema::connection('report')->table('client_performance', function(Blueprint $table){
            $table->renameColumn('acc_prvdr_code', 'data_prvdr_code');
        });
        Schema::connection('report')->table('portfolio_risk', function(Blueprint $table){
            $table->renameColumn('acc_prvdr_code', 'data_prvdr_code');
        });
        Schema::connection('report')->table('portfolio_risks', function(Blueprint $table){
            $table->renameColumn('acc_prvdr_code', 'data_prvdr_code');
        });
        Schema::connection('report')->table('bad_debts', function(Blueprint $table){
            $table->renameColumn('acc_prvdr_code', 'data_prvdr_code');
        });
        Schema::connection('report')->table('revpercustomer', function(Blueprint $table){
            $table->renameColumn('acc_prvdr_code', 'data_prvdr_code');
        });
    }
}
