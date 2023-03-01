<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedApiUrlCredTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        #DB::table('acc_providers')->insert([[]]);
        DB::update("update accounts set acc_prvdr_code = 'CCA' where acc_prvdr_code = 'CC'");
        DB::update("update acc_providers set acc_prvdr_code = 'CCA' where acc_prvdr_code = 'CC'");
        
        DB::update("update acc_providers set api_url = 'http://52.40.167.195:9097' where acc_prvdr_code='CCA'");
        DB::update("update accounts set api_cred = '{\"username\" : \"gtb_bank\", \"token\": \"nA4J87BcD1b4889js8\"}' where id = 1783");
        
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         DB::update("update acc_providers set api_url = NULL where acc_prvdr_code='CC'");
    }
}
