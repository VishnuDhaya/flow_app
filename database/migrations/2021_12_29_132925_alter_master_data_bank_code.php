<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMasterDataBankCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("delete from master_data where data_key = 'UEZM_MainContent_ddlBankCode'");
        DB::update("delete from master_data_keys where data_key = 'UEZM_MainContent_ddlBankCode'");
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
