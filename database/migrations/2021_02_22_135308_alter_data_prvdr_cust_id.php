<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDataPrvdrCustId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("update borrowers set  data_prvdr_cust_id = TRIM(LEADING '0' FROM data_prvdr_cust_id) where data_prvdr_code ='CCA'");
        DB::update("update loans set  data_prvdr_cust_id = TRIM(LEADING '0' FROM data_prvdr_cust_id) where data_prvdr_code ='CCA'");
        DB::update("update loan_applications set  data_prvdr_cust_id = TRIM(LEADING '0' FROM data_prvdr_cust_id) where data_prvdr_code ='CCA'");
        DB::update("update cust_acc_stmts set  data_prvdr_cust_id = TRIM(LEADING '0' FROM data_prvdr_cust_id) where dp_code ='CCA'");
        DB::update("update persons set whatsapp = TRIM(LEADING '0' FROM whatsapp), mobile_num = TRIM(LEADING '0' FROM mobile_num), phone_num =  TRIM(LEADING '0' FROM phone_num)");
        DB::update("drop table loan_approvers");
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
