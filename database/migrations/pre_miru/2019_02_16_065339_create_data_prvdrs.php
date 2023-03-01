<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataPrvdrs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_prvdrs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable();
            $table->string('name',40)->nullable();
            $table->string('provider_type',40)->nullable();
            $table->string('data_prvdr_code',4)->nullable()->unique();
            $table->unsignedInteger('org_id')->nullable();
            $table->unsignedInteger('contact_person_id')->nullable();
            $table->double('cust_comm', 15,2)->nullable();
            $table->double('repay_comm', 15,2)->nullable();
            $table->unsignedInteger('created_by')->nullable();   
            $table->string('status',10)->nullable()->default('enabled');
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
        Schema::dropIfExists('data_prvdrs');
    }
}
