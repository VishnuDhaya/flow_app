<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCommOnlyModel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::UPDATE("UPDATE cs_model_weightages SET repeat_cust_weightage = ? WHERE cs_model_code = ? AND csf_type = ?", [100, 'comm_only_model', 'monthly_comms']);
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
