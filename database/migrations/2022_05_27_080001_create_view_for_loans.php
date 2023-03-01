<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViewForLoans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("drop view [if exists] loans_view");
        DB::statement("CREATE VIEW loans_view
                        AS
                        SELECT
                        *,
                        (provisional_penalty * penalty_days) tot_penalty_amount,
                        (loan_principal + flow_fee + (provisional_penalty * penalty_days) - IFNULL(paid_principal,0) -  IFNULL(paid_fee,0) - penalty_collected - penalty_waived ) curr_os,
                        (paid_principal + paid_fee + penalty_collected ) tot_paid
                        FROM loans");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("drop view [if exists] loans_view");
    }
}
