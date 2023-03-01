<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRmCustAssignments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rm_cust_assignments', function (Blueprint $table) {
            
            $table->increments('id');
            $table->string('country_code',4);
            $table->string('cust_id',20)->nullable();
	        $table->unsignedInteger('from_rm_id')->nullable();
            $table->unsignedInteger('rm_id')->nullable();
            $table->dateTime('from_date')->nullable();
            $table->dateTime('to_date')->nullable();
            $table->string('territory',32)->nullable();
            $table->string('status',10)->nullable();
            $table->boolean('temporary_assign')->default(false);
            $table->dateTime('updated_at')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();

            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rm_cust_assignments');
    }
}
