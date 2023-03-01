<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryCodeToLeadportalUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leadportal_users', function (Blueprint $table) {
            $table->string('country_code', 5)->nullable();
        });
        DB::update("update leadportal_users set country_code = 'UGA'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leadportal_users', function (Blueprint $table) {
            //
        });
    }
}
