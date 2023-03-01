<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class VisitIdAddedInCustEvalChecklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cust_eval_checklists', function (Blueprint $table) {
            $table->unsignedInteger('visit_id')->nullable()->after('rm_recommendation');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cust_eval_checklists', function (Blueprint $table) {
            //
        });
    }
}
