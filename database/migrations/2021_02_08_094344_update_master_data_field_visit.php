<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMasterDataFieldVisit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'visit_purpose', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
        
        DB::table('master_data')->insert([ 
            ['country_code'=> '*', 'data_key' => 'visit_purpose', 'data_code' => 'new_cust_reg' , 'data_value' => 'New Customer Registration' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        
            ['country_code'=> '*', 'data_key' => 'visit_purpose', 'data_code' => 'kyc' , 'data_value' => 'Know Your Customer (KYC)' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        
            ['country_code'=> '*', 'data_key' => 'visit_purpose', 'data_code' => 'regular_visit' , 'data_value' => 'Regular Visit' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
       
            ['country_code'=> '*', 'data_key' => 'visit_purpose', 'data_code' => 'renew_agreement' , 'data_value' => 'Renew Agreement' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        
            ['country_code'=> '*', 'data_key' => 'visit_purpose', 'data_code' => 'profile_update' , 'data_value' => 'Profile Update' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        
            ['country_code'=> '*', 'data_key' => 'visit_purpose', 'data_code' => 'overdue_visit' , 'data_value' => 'Overdue Visit' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
        
            ['country_code'=> '*', 'data_key' => 'visit_purpose', 'data_code' => 'cust_feedback' , 'data_value' => ' Customer Feedback' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
        
            ['country_code'=> '*', 'data_key' => 'visit_purpose', 'data_code' => 'prob_confirm' , 'data_value' => 'Probation Confirmation' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] 
 

             

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
