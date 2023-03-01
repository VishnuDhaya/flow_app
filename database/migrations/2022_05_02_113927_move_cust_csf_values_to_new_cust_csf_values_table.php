<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MoveCustCsfValuesToNewCustCsfValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("alter table new_cust_csf_values modify conditions JSON  DEFAULT ('{}')");
        DB::table('new_cust_csf_values')->truncate();

        DB::UPDATE("update cust_csf_values set csf_normal_value = 'appr_inelig_on_comms,2022-05-25,1000000', csf_gross_value='appr_inelig_on_comms,2022-05-25,1000000' where id = 19375");
        DB::UPDATE("update cust_csf_values set csf_normal_value = 'appr_inelig_on_comms,2022-06-14,1000000', csf_gross_value='appr_inelig_on_comms,2022-06-14,1000000' where id = 20658");
        
        $acc_numbers = DB::SELECT("SELECT DISTINCT acc_number FROM cust_csf_values");
        foreach($acc_numbers as $acc_number) {
            $acc_number = $acc_number->acc_number;
            $cust_csf_values = DB::SELECT("SELECT * FROM cust_csf_values WHERE acc_number = '$acc_number'");
            $cust_score_factors = [];
            $score = NULL;
            $result = NULL;
            $conditions = [];

            foreach($cust_csf_values as $cust_csf_value) {
                $csf_detail = [];
                if ($cust_csf_value->csf_type == 'score') {
                    $score = $cust_csf_value->csf_normal_value;
                }
                elseif (str_contains($cust_csf_value->csf_type, 'result')) {
                    $result = explode(':', $cust_csf_value->csf_type)[1];
                }
                elseif ($cust_csf_value->csf_type == 'approval') {
                    $approval_details = explode(',', $cust_csf_value->csf_normal_value);
                    $conditions["acc_elig_reason"] = $approval_details[0]; 
                    $conditions["validity"] = $approval_details[1]; 
                    $conditions["limit"] = $approval_details[2];
                }
                else {
                    $csf_detail['csf_type'] =  $cust_csf_value->csf_type;
                    $csf_detail['n_val'] =  $cust_csf_value->csf_normal_value;
                    $csf_detail['g_val'] =  $cust_csf_value->csf_gross_value;
                    array_push($cust_score_factors, $csf_detail);
                }
            }
            $conditions = (empty($conditions)) ? json_encode(new stdClass()) : json_encode($conditions);

            $record = [
                "country_code" => $cust_csf_values[0]->country_code,
                "acc_prvdr_code" => $cust_csf_values[0]->acc_prvdr_code,
                "acc_number" => $cust_csf_values[0]->acc_number,
                "score" => $score,
                "result" => $result,
                "cust_score_factors" => json_encode($cust_score_factors),
                "conditions" => $conditions,
                "run_id" => $cust_csf_values[0]->run_id,
                "created_by" => $cust_csf_values[0]->created_by,
                "updated_by" => $cust_csf_values[0]->updated_by,
                "created_at" => $cust_csf_values[0]->created_at,
                "updated_at" => $cust_csf_values[0]->updated_at,
                
            ];
            DB::table('new_cust_csf_values')->insert([$record]);
        }
        Schema::rename('cust_csf_values', 'old_cust_csf_values');
        Schema::rename('new_cust_csf_values', 'cust_csf_values');
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
