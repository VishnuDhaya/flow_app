<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortfolioSummaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('report')->create('portfolio_summary', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('month');
            $table->string('country_code', 5);
            $table->unsignedInteger('tot_loans_count')->nullable();
            $table->double('tot_disb_amt')->nullable();
            $table->double('avg_loan_tenor')->nullable();
            $table->double('total_os_amt')->nullable();
            $table->double('write_off_amt')->nullable();
            $table->double('total_os_count')->nullable();
            $table->double('overall_os_amt')->nullable();
            $table->double('total_income')->nullable();
            $table->double('delinquency_7_29')->nullable();
            $table->double('delinquency_30_59')->nullable();
            $table->double('delinquency_60_89')->nullable();
            $table->double('delinquency_90')->nullable();
            $table->decimal('collection_rate',5,3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('report')->dropIfExists('portfolio_summary');
    }
}
