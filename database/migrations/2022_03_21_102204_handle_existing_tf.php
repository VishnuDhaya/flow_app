<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HandleExistingTf extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        set_app_session('UGA');
        $sc_codes = ['85200886','89435481','41275310','19282502','49825940','26583889','47810426','00849460','14503440','15616030'];
        foreach($sc_codes as $sc_code){
            DB::update("update accounts set acc_purpose = JSON_ARRAY('terminal_financing','float_advance'), acc_elig = 'tf_w_fa' where acc_number = '$sc_code' and acc_prvdr_code = 'UEZM'");
            $account = DB::selectOne("select * from accounts where acc_number = '$sc_code' and acc_prvdr_code = 'UEZM'");
            $csf_run_id = (new \App\Services\AccountService())->temp_approve_acc((array)$account);
            DB::update("update borrowers set csf_run_id = ? where cust_id = ?",[$csf_run_id, $account->cust_id]);
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
