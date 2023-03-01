<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('persons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable();
            $table->unsignedInteger('address_id')->nullable();
            $table->string('first_name',20)->nullable();
            $table->string('middle_name',20)->nullable();
            $table->string('last_name',20)->nullable();
            $table->string('initials',5)->nullable();
            $table->string("national_id",40)->nullable();
            $table->string("nationality",60)->nullable();
            $table->string("photo_national_id",50)->nullable();
            $table->string("photo_pps",50)->nullable();
            $table->date('dob')->nullable();
            $table->string('gender',16)->nullable();
            $table->string('whatsapp',20)->nullable();
            $table->string('email_id',50)->nullable();
            $table->string('mobile_num',20)->nullable();
            $table->string('phone_num',20)->nullable();
            $table->string('designation',40)->nullable();
            $table->string('associated_with',100)->nullable();
            $table->string('associated_entity_code',4)->nullable();
            $table->string('status',10)->nullable()->default('enabled');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('persons');
    }
}
