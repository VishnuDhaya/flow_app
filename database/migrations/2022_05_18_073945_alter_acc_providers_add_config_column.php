<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAccProvidersAddConfigColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acc_providers', function(Blueprint $table){
            $table->json('mobile_cred_format')->nullable();
        });
        DB::update("update acc_providers set mobile_cred_format = JSON_OBJECT('ussd_code_format','*165*2*:recipient*:amount*:remarks*:pin#') where acc_prvdr_code = 'UMTN'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acc_providers', function(Blueprint $table){
            $table->dropColumn('mobile_cred_format');
        });
    }
}
