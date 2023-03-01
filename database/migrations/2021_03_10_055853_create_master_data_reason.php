<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterDataReason extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       

        DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'reason_for_disable', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
        
        DB::table('master_data')->insert([ 
            ['country_code'=> '*', 'data_key' => 'reason_for_disable', 'data_code' => 'bad_repayment_behaviour' , 'data_value' => 'Bad repayment behaviour' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'reason_for_disable', 'data_code' => 'not_interested' , 'data_value' => 'Not interested ' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'reason_for_disable', 'data_code' => 'incomplete_kyc' , 'data_value' => 'Incomplete KYC' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'reason_for_disable', 'data_code' => 'stopped_using_account' , 'data_value' => 'Stopped using account ' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'reason_for_disable', 'data_code' => 'requested_by_partner_rm' , 'data_value' => 'Requested by Partner RM' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'reason_for_disable', 'data_code' => 'fraud' , 'data_value' => 'Fraud' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'reason_for_disable', 'data_code' => 'sold_terminal' , 'data_value' => 'Sold terminal ' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            
            ['country_code'=> '*', 'data_key' => 'reason_for_disable', 'data_code' => 'suspension' , 'data_value' => 'Suspension ' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'reason_for_disable', 'data_code' => 'inactive' , 'data_value' => 'Inactive ' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'reason_for_disable', 'data_code' => 'no_agreement' , 'data_value' => 'No agreement' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

             ['country_code'=> '*', 'data_key' => 'reason_for_disable', 'data_code' => 'others' , 'data_value' => 'others' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            
            
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_data_reason');
    }
}
