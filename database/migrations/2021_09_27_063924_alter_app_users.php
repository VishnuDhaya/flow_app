<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAppUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_users', function(Blueprint $table){
           $table->string('belongs_to', 16)->nullable()->change();
           $table->string('belongs_to_code', 4)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_users', function(Blueprint $table){
           $table->string('belongs_to', 16)->change();
           $table->string('belongs_to_code', 4)->change();
        });
    }
}
