<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RmPunchTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rm_punch_time', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code', 4)->nullable();
            $table->string('rel_mgr_id',10)->nullable();
            $table->date('date')->nullable();
            $table->time('punch_in_time')->nullable();
            $table->time('punch_out_time')->nullable();
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
        //
    }
}
