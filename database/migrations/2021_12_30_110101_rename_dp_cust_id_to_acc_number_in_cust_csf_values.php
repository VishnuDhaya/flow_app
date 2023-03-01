<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameDpCustIdToAccNumberInCustCsfValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cust_csf_values', function (Blueprint $table) {
            $table->renameColumn('data_prvdr_cust_id','acc_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acc_number_in_cust_csf_values', function (Blueprint $table) {
            //
        });
    }
}
