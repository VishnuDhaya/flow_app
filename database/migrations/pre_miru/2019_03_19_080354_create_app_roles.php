<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code', 4);
            $table->string('role_code',16);
            $table->string('role_name', 16);
            $table->string('status',10)->nullable()->default('enabled');
            $table->string('created_by',120);
            $table->string('updated_by',120);
           
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
        Schema::table('app_roles', function (Blueprint $table) {
            //
        });
    }
}
