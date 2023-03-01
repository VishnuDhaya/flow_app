<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddresses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable();
            $table->string('addr_type',30)->nullable();
            $table->string('door_num',20)->nullable();
            $table->string('street',150)->nullable();
            $table->string('area',150)->nullable();
            $table->string('province',100)->nullable();
            $table->string('city',100)->nullable();
            $table->string('parish',100)->nullable();
            $table->string('village',100)->nullable();
            $table->string('sub_county',100)->nullable();
            $table->string('county',100)->nullable();            
            $table->string('postal_code',20)->nullable();
            $table->string('landmark',150)->nullable();
            $table->string('district',100)->nullable();
            $table->string('gps_coords',150)->nullable();
            $table->unsignedInteger('created_by')->nullable();   // Need to add reference?
            $table->unsignedInteger('updated_by')->nullable();
            $table->nullableTimestamps();

            //
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       
    }
}
