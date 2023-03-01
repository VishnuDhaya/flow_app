<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnReasonForReassignAtRmCustAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rm_cust_assignments', function (Blueprint $table) {
            $table->string('reason_for_reassign',64)->default(null)->after('temporary_assign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rm_cust_assignments', function (Blueprint $table) {
            $table->dropColumn('reason_for_reassign');
        });
    }
}
