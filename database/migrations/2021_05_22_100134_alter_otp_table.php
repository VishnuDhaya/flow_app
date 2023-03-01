<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOtpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('otp', function (Blueprint $table) {

            $table->string('cust_id', 20)->after("otp_type");
            $table->string('entity', 20)->after("cust_id");
            $table->string('entity_id', 20)->after('entity');
            $table->unsignedInteger('created_by')->nullable()->after("created_at");
            $table->unsignedInteger('updated_by')->nullable()->after("updated_at");
            $table->dropColumn('include_in_sync');
        });
    }
}