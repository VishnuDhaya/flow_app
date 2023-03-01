<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppRolePrivileges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_role_privileges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('role_code', 20);
            $table->string('priv_code', 40);
            $table->string('status',10)->nullable()->default('enabled');
            $table->string('created_by',120)->nullable();
            $table->string('updated_by',120)->nullable();
           
  $table->dateTime('created_at')->nullable();
  $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_role_privileges', function (Blueprint $table) {
            //
        });
    }
}
