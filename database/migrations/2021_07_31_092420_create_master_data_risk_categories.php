<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterDataRiskCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'risk_category', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
        
        
        DB::table('master_data')->insert([ 
            ['country_code'=> '*', 'data_key' => 'risk_category', 'data_code' => '1_low_risk' , 'data_value' => 'Low Risk' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'risk_category', 'data_code' => '2_medium_risk' , 'data_value' => 'Medium Risk' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'risk_category', 'data_code' => '3_high_risk' , 'data_value' => 'High Risk' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'risk_category', 'data_code' => '4_very_high_risk' , 'data_value' => 'Very High Risk' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            
            
            
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
