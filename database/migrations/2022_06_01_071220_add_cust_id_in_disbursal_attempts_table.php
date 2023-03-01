<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddCustIdInDisbursalAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disbursal_attempts', function (Blueprint $table) {
            $table->string('cust_id', 20)->after('loan_doc_id')->nullable();
        });

        DB::update("update disbursal_attempts d, loans l set d.cust_id = l.cust_id where d.loan_doc_id = l.loan_doc_id");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disbursal_attempts', function (Blueprint $table) {
            $table->dropColumn('cust_id');
        });
    }
}
