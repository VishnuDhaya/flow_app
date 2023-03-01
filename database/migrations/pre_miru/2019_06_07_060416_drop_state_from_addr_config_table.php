<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropStateFromAddrConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('addr_config')->where('field_code', '=', 'state')->delete();
       // Schema::table('addr_config', function (Blueprint $table) {
            //
       // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('addr_config', function (Blueprint $table) {
            //
        });
    }
}
