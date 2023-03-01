<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMasterDataIneligibility extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("update master_data set data_value ='Requires OPM Approval' where id ='392';");
        DB::update("update master_data set data_value ='Requires RM Approval' where id ='393';");
        DB::update("update app_users set role_codes = 'operations_manager' where role_codes = 'partnership_manager';");
        DB::update("update loans set loan_approved_date = null;");
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
