<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadportalUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leadportal_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string("fname",10);
            $table->string("lname",10);
            $table->string("email",25)->unique();
            $table->string("password");
            $table->string("acc_prvdr",5);
            $table->string("role");
            $table->rememberToken();
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
        Schema::dropIfExists('leadportal_users');
    }
}
