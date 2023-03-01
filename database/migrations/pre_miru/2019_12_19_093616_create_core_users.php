<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoreUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('core_users');
        Schema::create('core_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('person_id');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('cust_id',20);
            $table->string('mobile_number',15);
            $table->string('role_codes',80);
            $table->string('belongs_to',16);
            $table->string('belongs_to_code', 4);
            $table->string('country_code', 4);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->string('status',10)->nullable()->default('enabled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('core_users');
    }
}
