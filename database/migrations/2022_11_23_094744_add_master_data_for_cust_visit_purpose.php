<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMasterDataForCustVisitPurpose extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data')->insert([ 
            ['country_code'=> '*', 'data_key' => 'cust_visit_purpose', 'data_code' => 'update_profile_info' , 'data_value' => 'Update Profile Info' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
            ['country_code'=> '*', 'data_key' => 'cust_visit_purpose', 'data_code' => 'agreement_renewal' , 'data_value' => 'Agreement Renewal' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> '*', 'data_key' => 'cust_visit_purpose', 'data_code' => 'add_new_account' , 'data_value' => 'Add New Account' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
            ['country_code'=> '*', 'data_key' => 'cust_visit_purpose', 'data_code' => 'can_not_do_repayment_via_app' , 'data_value' => "Can't do repayment via app" , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 

        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
