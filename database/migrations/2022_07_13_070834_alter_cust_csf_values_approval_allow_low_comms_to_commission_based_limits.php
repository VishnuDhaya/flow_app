<?php

use App\Services\DataScoreModelService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCustCsfValuesApprovalAllowLowCommsToCommissionBasedLimits extends Migration
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
                                    WHERE csf_type = 'monthly_comms' AND acc_prvdr_code in ('RBOK', 'RMTN') ");
        
        foreach($csf_records as $csf_data) {

            $csf_data = (array)$csf_data;
            $acc_number = $csf_data['acc_number'];
            $cust_csf_values = [$csf_data];

            $approval_exists = DB::SELECT("SELECT id FROM cust_csf_values WHERE acc_number = '$acc_number' AND csf_type = 'approval'");

            if(empty($approval_exists)) {
                $acc_elig_reason = 'commission_based_limits';
                $limit_record = (new DataScoreModelService())->check_monthly_comms_and_insert_limit($csf_data, $cust_csf_values);
                if(!empty($limit_record)) {
                    DB::UPDATE("UPDATE accounts SET acc_elig = '$acc_elig_reason' WHERE acc_number = '$acc_number' AND status = 'enabled'");
                }
            }
        }

        DB::UPDATE("UPDATE cust_csf_values 
                    SET csf_normal_value = CONCAT('$acc_elig_reason,',substring_index(csf_normal_value, ',', -2)), 
                        csf_gross_value =  CONCAT('$acc_elig_reason,',substring_index(csf_gross_value, ',', -2))
                    WHERE find_in_set('allow_low_comms',csf_gross_value) = 1");
        
        DB::UPDATE("UPDATE accounts 
                    SET acc_elig = '$acc_elig_reason' 
                    WHERE acc_elig = 'allow_low_comms'"); 

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
