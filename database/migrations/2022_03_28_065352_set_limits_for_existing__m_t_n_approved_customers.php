<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetLimitsForExistingMTNApprovedCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $cust_csf_values = DB::select("select csf_normal_value,csf_gross_value,id,acc_number,acc_prvdr_code from cust_csf_values where acc_prvdr_code = 'UMTN' and csf_type = 'approval' and csf_gross_value not like('%,%') and csf_normal_value not like('%,%')");
        foreach ($cust_csf_values as $cust_csf_value){
            if($cust_csf_value){
                $account = DB::selectOne("select acc_elig,acc_prvdr_code from accounts where acc_prvdr_code = '{$cust_csf_value->acc_prvdr_code}' and acc_number = {$cust_csf_value->acc_number} and status = 'enabled'");
                if($account){
                    $appr_terms =  config('app.account_elig_temp_approval')[$account->acc_prvdr_code][$account->acc_elig];
                    $limit = $appr_terms['limit'];
                    $csf_normal_value = $cust_csf_value->csf_normal_value.",".$limit;
                    $csf_gross_value = $cust_csf_value->csf_gross_value.",".$limit;
                    DB::update("update cust_csf_values set csf_normal_value = '$csf_normal_value',csf_gross_value = '$csf_gross_value' where id = {$cust_csf_value->id} ");
                }
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
