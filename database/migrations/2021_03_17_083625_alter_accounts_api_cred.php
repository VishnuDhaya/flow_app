<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAccountsApiCred extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("update accounts set api_cred = '{\"username\" : \"FLOW_GLOBAL\", \"password\" : \"CN0ri9VbdJff\", \"service\" : \"LOAN_SERVICE\"}' where id='1783'");
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
