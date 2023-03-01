<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CustAppRequirementsAppUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_users', function (Blueprint $table) {
            $table->string('mobile_num',15)->after('email');
            $table->renameColumn('is_new_rm','is_new_user');
            $table->string('email',255)->nullable()->change();
            $table->string('password',255)->nullable()->change();
        });

        Schema::table('otps', function (Blueprint $table){
            $table->string('entity',20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_users', function (Blueprint $table) {
            $table->dropColumn('mobile_num');
            $table->renameColumn('is_new_user','is_new_rm');
            $table->string('email',255)->change();
            $table->string('password',255)->change();
        });

        Schema::table('otps', function (Blueprint $table){
            $table->string('entity',20)->change();
        });
    }
}
