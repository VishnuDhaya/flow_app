<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterDataKeysReasonForEnable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
               DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'reason_for_enable', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
        
        DB::table('master_data')->insert([ 

            ['country_code'=> '*', 'data_key' => 'reason_for_enable', 'data_code' => 'appraised_by_rm' , 'data_value' => 'Appraised by RM' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'reason_for_enable', 'data_code' => 'activated' , 'data_value' => 'Activated' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'reason_for_enable', 'data_code' => 'kyc_updated' , 'data_value' => 'KYC updated ' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'reason_for_enable', 'data_code' => 'new_customer' , 'data_value' => 'New customer ' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'reason_for_enable', 'data_code' => 'requested_by_partner_rm' , 'data_value' => 'Requested by Partner RM' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'reason_for_enable', 'data_code' => 'requested_by_rm' , 'data_value' => 'Requested by RM' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'reason_for_enable', 'data_code' => 'agreement_signed' , 'data_value' => 'Agreement signed ' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'reason_for_enable', 'data_code' => 'others' , 'data_value' => 'others' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            
            ['country_code'=> '*', 'data_key' => 'loan_status', 'data_code' => 'partially_paid' , 'data_value' => 'Partially Paid' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
           
            ['country_code'=> '*', 'data_key' => 'loan_status', 'data_code' => 'outstanding' , 'data_value' => 'Outstanding' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
           
            
            ]);
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_data_keys_reason_for_enable');
    }
}
