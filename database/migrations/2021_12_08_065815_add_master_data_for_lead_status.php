<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMasterDataForLeadStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'lead_status', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);

        DB::table('master_data')->insert([ 
            
            ['country_code'=> '*', 'data_key' => 'lead_status', 'data_code' => '10_pending_rm_eval' , 'data_value' => 'Pending RM Evaluation' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> '*', 'data_key' => 'lead_status', 'data_code' => '20_rm_rejected' , 'data_value' => 'RM Rejected' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> '*', 'data_key' => 'lead_status', 'data_code' => '30_cust_not_interested' , 'data_value' => 'Customer Not Interested' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> '*', 'data_key' => 'lead_status', 'data_code' => '40_pending_kyc' , 'data_value' => 'Pending KYC' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> '*', 'data_key' => 'lead_status', 'data_code' => '41_kyc_inprogress' , 'data_value' => 'KYC In Progress' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> '*', 'data_key' => 'lead_status', 'data_code' => '50_pending_audit' , 'data_value' => 'Pending Audit' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> '*', 'data_key' => 'lead_status', 'data_code' => '59_pending_enable' , 'data_value' => 'Pending Enable' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> '*', 'data_key' => 'lead_status', 'data_code' => '60_customer_onboarded' , 'data_value' => 'Customer Onboarded' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

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
