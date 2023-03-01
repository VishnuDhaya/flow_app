<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCustCsfValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cust_csf_values', function (Blueprint $table) {
             $table->string('csf_normal_value', 20)->nullable(false)->change();
              
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cust_csf_values', function (Blueprint $table) {
             $table->float('csf_normal_value',8,2)->nullable(false)->change();
         });
    }
}
