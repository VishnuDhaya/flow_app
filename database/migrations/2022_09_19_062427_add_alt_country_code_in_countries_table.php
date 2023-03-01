<?php

use Database\Seeders\Alpha2CountryCodeSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class AddAltCountryCodeInCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->string("alt_country_code", 2)->default(null)->after("country_code");
        });
        $alt_country = new Alpha2CountryCodeSeeder();
        $alt_country();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn("alt_country_code");
        });
    }
}
