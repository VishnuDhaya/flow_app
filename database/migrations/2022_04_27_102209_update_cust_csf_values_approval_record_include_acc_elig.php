<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCustCsfValuesApprovalRecordIncludeAccElig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $approval_records = DB::SELECT("SELECT id, acc_number, acc_prvdr_code, csf_type, csf_normal_value FROM cust_csf_values WHERE csf_type = 'approval'");
        foreach($approval_records as $approval_record) {
            $acc_number = $approval_record->acc_number;
            $acc_prvdr_code = $approval_record->acc_prvdr_code;

            $account = DB::selectOne("SELECT acc_elig FROM accounts WHERE acc_number = '$acc_number' AND acc_prvdr_code = '$acc_prvdr_code'");
            
            $acc_elig = $account->acc_elig;
            $old_csf_value = $approval_record->csf_normal_value;
            $csf_value = implode(",", [$acc_elig, $old_csf_value]);

            $cust_csf_value_id = $approval_record->id;
            DB::UPDATE("UPDATE cust_csf_values SET csf_normal_value='$csf_value', csf_gross_value='$csf_value' WHERE id = $cust_csf_value_id");
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
