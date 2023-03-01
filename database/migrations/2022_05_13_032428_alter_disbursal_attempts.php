<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDisbursalAttempts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disbursal_attempts', function(Blueprint $table){
            $table->string('country_code', 5)->nullable();
        });
        DB::update("update disbursal_attempts set country_code = 'RWA' where loan_doc_id like 'RFLW%' ");
        DB::update("update disbursal_attempts set country_code = 'UGA' where country_code is null");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disbursal_attempts', function(Blueprint $table){
            $table->dropColumn('country_code');
        });
    }
}
