<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSmsLogsAlterVendorRefId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->string('vendor_ref_id', 120)->change();
            $table->index('vendor_ref_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::update("ALTER TABLE sms_logs DROP INDEX sms_logs_vendor_ref_id_index");

        Schema::table('sms_logs', function (Blueprint $table) {
            $table->text('vendor_ref_id')->change();
        });
    }
}
