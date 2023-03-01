<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLoansViewAddOsFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tot_penalty = "(IFNULL(provisional_penalty, 0) * penalty_days)";
        $os_principal = "( loan_principal -  IFNULL(paid_principal,0) )";
        $os_fee = "(flow_fee - IFNULL(paid_fee, 0))";
        $os_penalty = "($tot_penalty - penalty_collected - penalty_waived)";

        DB::statement("drop view if exists loans_view");
        DB::statement("CREATE VIEW loans_view
                        AS
                        SELECT
                        *,
                        $tot_penalty tot_penalty,
                        (paid_principal + paid_fee + penalty_collected ) tot_paid,
                        $os_principal os_principal,
                        $os_fee os_fee,
                        $os_penalty os_penalty,
                        $os_principal + $os_fee + $os_penalty os_total

                        FROM loans");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("drop view if exists loans_view");
    }
}
