<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterDataCallLogPurpose extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'incoming_call_purpose', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
         DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'outgoing_call_purpose', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
        
        DB::table('master_data')->insert([ 
            ['country_code'=> '*', 'data_key' => 'incoming_call_purpose', 'data_code' => 'payment_date_inquiry' , 'data_value' => 'Payment Date Inquiry' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'incoming_call_purpose', 'data_code' => 'payment_details_inquiry' , 'data_value' => 'Payment Details Inquiry' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'incoming_call_purpose', 'data_code' => 'payment_confirmation' , 'data_value' => 'Payment Confirmation' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'incoming_call_purpose', 'data_code' => 'repeat_last_fa' , 'data_value' => ' Repeat Last FA' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'incoming_call_purpose', 'data_code' => 'product_fees_inquiry' , 'data_value' => 'Product Fees Inquiry' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'incoming_call_purpose', 'data_code' => 'fa_upgrade' , 'data_value' => 'FA Upgrade' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            
             ['country_code'=> '*', 'data_key' => 'incoming_call_purpose', 'data_code' => 'others' , 'data_value' => 'Others' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            
            
            ]);
        DB::table('master_data')->insert([ 
            ['country_code'=> '*', 'data_key' => 'outgoing_call_purpose', 'data_code' => 'reminders' , 'data_value' => 'Reminders' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'outgoing_call_purpose', 'data_code' => 'overdue_follow-ups' , 'data_value' => 'Overdue Follow-ups' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'outgoing_call_purpose', 'data_code' => 'cashback_call' , 'data_value' => 'Cashback call ' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'outgoing_call_purpose', 'data_code' => 'feedback/survey_call' , 'data_value' => 'Feedback/Survey call' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
            
            ['country_code'=> '*', 'data_key' => 'outgoing_call_purpose', 'data_code' => 'others' , 'data_value' => 'Others' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_data_call_log_purpose');
    }
}
