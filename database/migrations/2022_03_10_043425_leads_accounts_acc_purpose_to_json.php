<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeadsAccountsAccPurposeToJson extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("update accounts set acc_purpose = JSON_ARRAY(acc_purpose)");
        DB::statement("alter table accounts modify acc_purpose JSON  DEFAULT ('[]')");
        DB::update("update leads set acc_purpose = JSON_ARRAY(acc_purpose)");
        DB::statement("alter table leads modify acc_purpose JSON  DEFAULT ('[]')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('json', function (Blueprint $table) {
            //
        });
    }
}
