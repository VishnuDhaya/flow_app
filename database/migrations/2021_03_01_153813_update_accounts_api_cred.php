<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAccountsApiCred extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            
            $table->string('int_type',5)->nullable()->after("web_cred");
       });
        DB::update("update accounts set api_cred = '{\"username\" : \"TEST_BILLER\", \"password\" : \"11111\", \"service\" : \"TEST_SERVICE\"}' where id='1783'");
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
