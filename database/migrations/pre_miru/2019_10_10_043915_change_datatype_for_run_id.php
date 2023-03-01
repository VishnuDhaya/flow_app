<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDatatypeForRunId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cust_acc_stmts', function (Blueprint $table) {
            $table->unsignedBigInteger('run_id')->change();
        });

        Schema::table('borrowers', function (Blueprint $table) {
            $table->unsignedBigInteger('csf_run_id')->change();
        });

        Schema::table('cust_csf_values', function (Blueprint $table) {
            $table->unsignedBigInteger('run_id')->change();
        });

        Schema::table('score_runs', function (Blueprint $table) {
            $table->unsignedBigInteger('run_id')->change();
        });
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
