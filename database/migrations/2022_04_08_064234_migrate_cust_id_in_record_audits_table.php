<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrateCustIdInRecordAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("update borrowers b, record_audits ra set record_code = b.cust_id where b.id = ra.record_id and b.cust_id <> b.old_cust_id");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('record_audits', function (Blueprint $table) {
            //
        });
    }
}
