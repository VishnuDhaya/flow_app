<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLenders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lenders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable();
            $table->string('name',40)->nullable();
            $table->string('lender_type',40)->nullable();
            $table->string('lender_code',4)->nullable()->unique();
            $table->unsignedInteger('org_id')->nullable();
            $table->unsignedInteger('contact_person_id')->nullable();
            $table->string('status',20)->nullable()->default('enabled');
            $table->unsignedInteger('created_by')->nullable();   // Need to add reference?
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
        Schema::dropIfExists('lenders');
    }
}
