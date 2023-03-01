<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateApprovalLimitForRbok300k500k extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::UPDATE("UPDATE cust_csf_values 
                    SET csf_normal_value = CONCAT(substring_index(csf_gross_value, ',', 2), ',500000'), 
                    csf_gross_value = CONCAT(substring_index(csf_gross_value, ',', 2), ',500000') 
                    WHERE acc_prvdr_code = 'RBOK' AND csf_type = 'approval' AND substring_index(csf_gross_value, ',', -1) = '300000'");
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
