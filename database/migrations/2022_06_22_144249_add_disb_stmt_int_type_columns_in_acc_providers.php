<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisbStmtIntTypeColumnsInAccProviders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acc_providers', function (Blueprint $table) {
            //
        });

        DB::statement("alter table acc_providers drop column int_type");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acc_providers', function (Blueprint $table) {
            //
        });
    }
}
