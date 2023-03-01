<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MasterDataForSmsLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data')->insert([
            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'vendor', 'parent_data_code' => NULL, 'data_code' => 'UAIT', 'data_value' => 'UAIT', 'status' => 'enabled'],
 
            ['country_code' => 'UGA', 'data_type' => 'common', 'data_key' => 'vendor', 'parent_data_code' => NULL, 'data_code' => 'USIS', 'data_value' => 'USIS', 'status' => 'enabled'],
 
            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'direction', 'parent_data_code' => NULL, 'data_code' => 'incoming', 'data_value' => 'Incoming', 'status' => 'enabled'],
 
            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'direction', 'parent_data_code' => NULL, 'data_code' => 'outgoing', 'data_value' => 'Outgoing', 'status' => 'enabled'],
 
            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'sms_status', 'parent_data_code' => NULL, 'data_code' => 'delivery_failed', 'data_value' => 'Delivery Failed', 'status' => 'enabled'],
            
            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'sms_status', 'parent_data_code' => NULL, 'data_code' => 'received', 'data_value' => 'Received', 'status' => 'enabled'],

            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'sms_status', 'parent_data_code' => NULL, 'data_code' => 'delivered', 'data_value' => 'Delivered', 'status' => 'enabled'],
 
            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'sms_status', 'parent_data_code' => NULL, 'data_code' => 'expired', 'data_value' => 'Expired', 'status' => 'enabled'],
 
            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'sms_status', 'parent_data_code' => NULL, 'data_code' => 'sent', 'data_value' => 'Sent', 'status' => 'enabled'],
 
            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'sms_status', 'parent_data_code' => NULL, 'data_code' => 'send_failed', 'data_value' => 'Send Failed', 'status' => 'enabled'],
 
            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'sms_status', 'parent_data_code' => NULL, 'data_code' => 'rejected', 'data_value' => 'Rejected', 'status' => 'enabled'],

            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'purpose', 'parent_data_code' => NULL, 'data_code' => 'ib_sms_unregistered', 'data_value' => 'Inbound SMS Unregistered', 'status' => 'enabled'],
            
            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'purpose', 'parent_data_code' => NULL, 'data_code' => 'ib_sms_unknown_txt', 'data_value' => 'Inbound SMS Unknown Text', 'status' => 'enabled'],

            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'purpose', 'parent_data_code' => NULL, 'data_code' => 'ib_sms_repeat_fa', 'data_value' => 'Inbound SMS Repeat FA', 'status' => 'enabled'],

            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'purpose', 'parent_data_code' => NULL, 'data_code' => 'ib_sms_alt_num', 'data_value' => 'Inbound SMS Alternate Number', 'status' => 'enabled'],

            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'purpose', 'parent_data_code' => NULL, 'data_code' => 'disb_portal_verify', 'data_value' => 'Disbursal Portal Verify', 'status' => 'enabled'],

            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'purpose', 'parent_data_code' => NULL, 'data_code' => 'ib_sms_otp', 'data_value' => 'Inbound SMS OTP', 'status' => 'enabled'],

            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'purpose', 'parent_data_code' => NULL, 'data_code' => 'notification', 'data_value' => 'Notification', 'status' => 'enabled'],

            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'purpose', 'parent_data_code' => NULL, 'data_code' => 'otp/confirm_fa', 'data_value' => 'OTP / Confirm FA', 'status' => 'enabled'],

            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'purpose', 'parent_data_code' => NULL, 'data_code' => 'otp/verify_mobile', 'data_value' => 'OTP / Verify Mobile', 'status' => 'enabled'],

            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'purpose', 'parent_data_code' => NULL, 'data_code' => 'duplicate', 'data_value' => 'Duplicate', 'status' => 'enabled'],
            
            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'purpose', 'parent_data_code' => NULL, 'data_code' => 'otp/confirm_recovery', 'data_value' => 'OTP / Confirm Recovery', 'status' => 'enabled'],

            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'purpose', 'parent_data_code' => NULL, 'data_code' => 'otp/cust_kyc', 'data_value' => 'OTP / Cust KYC', 'status' => 'enabled'],

       ]);
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
