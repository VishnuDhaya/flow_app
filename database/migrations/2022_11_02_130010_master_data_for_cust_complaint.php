<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MasterDataForCustComplaint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data')->insert([
            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'complaint_type', 'parent_data_code' => NULL, 'data_code' => 'relationship_manager', 'data_value' => 'Relationship Manager', 'status' => 'enabled'],
            
            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'complaint_type', 'parent_data_code' => NULL, 'data_code' => 'customer_success', 'data_value' => 'Customer Success', 'status' => 'enabled'],
            
            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'complaint_type', 'parent_data_code' => NULL, 'data_code' => 'mobile_app_issues', 'data_value' => 'Mobile App Issues', 'status' => 'enabled'],
            
            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'complaint_type', 'parent_data_code' => NULL, 'data_code' => 'others', 'data_value' => 'Others', 'status' => 'enabled'],
            
            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'complaint_type', 'parent_data_code' => NULL, 'data_code' => 'apply_repeat_fa', 'data_value' => 'Apply/Repeat FA', 'status' => 'enabled'],
        
            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'complaint_status', 'parent_data_code' => NULL, 'data_code' => 'raised', 'data_value' => 'Raised', 'status' => 'enabled'],
        
            ['country_code' => '*', 'data_type' => 'common', 'data_key' => 'complaint_status', 'parent_data_code' => NULL, 'data_code' => 'resolved', 'data_value' => 'Resolved', 'status' => 'enabled'],]);
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
