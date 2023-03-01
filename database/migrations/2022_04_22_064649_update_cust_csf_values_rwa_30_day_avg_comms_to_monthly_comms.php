<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCustCsfValuesRwa30DayAvgCommsToMonthlyComms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $old_csf_type = '30_day_avg_comms';
        $new_csf_type = 'monthly_comms';

        DB::UPDATE("UPDATE cust_csf_values SET csf_type = '$new_csf_type' WHERE country_code = 'RWA' AND csf_type = '$old_csf_type'");    
        DB::UPDATE("UPDATE cs_model_weightages SET csf_type = '$new_csf_type' WHERE country_code = 'RWA' AND csf_type = '$old_csf_type' AND cs_model_code = 'comm_only_model'");    
        DB::UPDATE("UPDATE cs_factor_values SET csf_type = '$new_csf_type' WHERE country_code = 'RWA' AND csf_type = '$old_csf_type' AND csf_group = 'avg_comms'");    

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
