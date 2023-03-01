<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRmLocation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rm_locations', function (Blueprint $table) {

            $table->increments('id');
            $table->string('country_code', 4)->nullable();
            $table->string('rel_mgr_id',10)->nullable();
            $table->string('date', 10)->nullable();
            $table->json('locations')->nullable();
            $table ->unsignedInteger('created_by')->nullable();
            $table ->dateTime('created_at')->nullable();
            $table ->unsignedInteger('updated_by')->nullable();
            $table ->dateTime('updated_at')->nullable();
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rm_locations');
    }
}
