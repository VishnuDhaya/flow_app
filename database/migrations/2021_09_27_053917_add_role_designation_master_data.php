<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRoleDesignationMasterData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'role_code', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);

        DB::table('master_data')->insert([
            ['country_code'=> '*', 'data_key' => 'designation', 'data_code' => 'Country Manager/CEO' , 'data_value' => 'Country Manager/CEO' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'designation', 'data_code' => 'COO' , 'data_value' => 'COO' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'designation', 'data_code' => 'Relationship Manager' , 'data_value' => 'Relationship Manager' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'designation', 'data_code' => 'Customer Success Officer' , 'data_value' => 'Customer Success Officer' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'designation', 'data_code' => 'Customer Success Manager' , 'data_value' => 'Customer Success Manager' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'designation', 'data_code' => 'Recovery Specialist' , 'data_value' => 'Recovery Specialist' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'designation', 'data_code' => 'Senior Internal Audit Manager' , 'data_value' => 'Senior Internal Audit Manager' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'designation', 'data_code' => 'Operations Auditor' , 'data_value' => 'Operations Auditor' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],

            ['country_code'=> '*', 'data_key' => 'designation', 'data_code' => 'Founder' , 'data_value' => 'Founder' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],

            ['country_code'=> '*', 'data_key' => 'designation', 'data_code' => 'Engineering Manager' , 'data_value' => 'Engineering Manager' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],

            ['country_code'=> '*', 'data_key' => 'designation', 'data_code' => 'App Support' , 'data_value' => 'App Support' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],

            
            ['country_code'=> '*', 'data_key' => 'role_code', 'data_code' => 'app_support' , 'data_value' => 'App Support' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
            ['country_code'=> '*', 'data_key' => 'role_code', 'data_code' => 'relationship_manager' , 'data_value' => 'Relationship Manager' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
            ['country_code'=> '*', 'data_key' => 'role_code', 'data_code' => 'customer_success' , 'data_value' => 'Customer Success Manager' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
            ['country_code'=> '*', 'data_key' => 'role_code', 'data_code' => 'read_only' , 'data_value' => 'Read Only' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
            ['country_code'=> '*', 'data_key' => 'role_code', 'data_code' => 'investor' , 'data_value' => 'Investor' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
            ['country_code'=> '*', 'data_key' => 'role_code', 'data_code' => 'rm_support' , 'data_value' => 'RM Support' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
            ['country_code'=> '*', 'data_key' => 'role_code', 'data_code' => 'operations_manager' , 'data_value' => 'Operations Manager' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
            ['country_code'=> '*', 'data_key' => 'role_code', 'data_code' => 'operations_auditor' , 'data_value' => 'Operations Auditor' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
            ['country_code'=> '*', 'data_key' => 'role_code', 'data_code' => 'mtn_csm' , 'data_value' => 'MTN CSM' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
            ['country_code'=> '*', 'data_key' => 'role_code', 'data_code' => 'customer_success_officer' , 'data_value' => 'Customer Success Officer' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
            ['country_code'=> '*', 'data_key' => 'role_code', 'data_code' => 'investor' , 'data_value' => 'Investor' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
            ['country_code'=> '*', 'data_key' => 'role_code', 'data_code' => 'recovery_specialist' , 'data_value' => 'Recovery Specialist' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],




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
