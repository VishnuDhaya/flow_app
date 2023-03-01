<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSmsLogsPurpose extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("update sms_logs set purpose = 'ib_sms_unregistered' where purpose ='unregistered'");
        DB::update("update sms_logs set purpose = 'ib_sms_unknown_txt' where purpose ='unknown'");
        DB::update("update sms_logs set purpose = 'ib_sms_repeat_fa' where purpose ='repeat_fa'");
        DB::update("update sms_logs set purpose = 'ib_sms_alt_num' where purpose ='alt_num'");
        DB::update("update sms_logs set purpose = 'disb_portal_verify' where purpose ='otp/mtn_login'");
        DB::update("update sms_logs set purpose = 'ib_sms_otp' where purpose ='otp'");

        DB::update("update otps set otp_type = 'disb_portal_verify' where otp_type = 'mtn_login'");
      



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
