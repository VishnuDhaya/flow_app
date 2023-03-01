<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastLoginColumnInInvestorUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('investor_users', function (Blueprint $table) {
            $table->timestamp('last_login')->after('remember_token')->nullable();
            $table->Integer('login_count')->after('last_login')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('investor_users', function (Blueprint $table) {
            $table->dropColumn(['last_login','login_count']);
        });
    }
}
