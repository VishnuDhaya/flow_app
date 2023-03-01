<?php

use App\Services\DataScoreModelService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCustCsfValuesAddLimitForRmtn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        set_app_session('RWA');
        $csf_records = DB::SELECT(" SELECT id, acc_number, acc_prvdr_code, csf_type, csf_normal_value, csf_gross_value, run_id, country_code FROM cust_csf_values 
                                    WHERE csf_type = 'monthly_comms' AND acc_prvdr_code = 'RMTN' ");

        foreach($csf_records as $csf_data) {
            $csf_data = (array)$csf_data;
            $cust_csf_values = [$csf_data];

            $limit_record = (new DataScoreModelService())->check_monthly_comms_and_insert_limit($csf_data, $cust_csf_values);
            if(!empty($limit_record)) {
                $acc_elig = 'allow_low_comms';
                $acc_number = $csf_data['acc_number'];

                DB::UPDATE("UPDATE accounts SET acc_elig = '$acc_elig' WHERE acc_number = '$acc_number' AND status = 'enabled'");
            }
        }
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
