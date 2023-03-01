<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMasterDataCsFactorsValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data')->insert([ 
            ['country_code' => 'UGA', 'data_key' => 'csf_type', 'parent_data_code' => 'past_performance', 'data_code' => 'repaid_after_10_days_pc', 'data_value' => 'Percent of FAs Repaid After 10 day Delay', 'status' =>  'enabled', 'data_type'  => 'common','created_by' => '0', 'created_at' => now()],
            ['country_code' => 'UGA', 'data_key' => 'csf_type', 'parent_data_code' => 'past_performance', 'data_code' => 'repaid_after_30_days_pc', 'data_value' => 'Percent of FAs Repaid After 30 day Delay', 'status' =>  'enabled', 'data_type'  => 'common','created_by' => '0', 'created_at' => now()],

        ]);

        DB::table('cs_factor_values')->insert([ 
            ['country_code' => 'UGA', 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_10_days_pc', 'value_from' => '25',  'value_to' => '100', 'normal_value'  => '2', 'status' =>  'enabled', 'created_by' => '0','created_at' => now()], 
            ['country_code' => 'UGA', 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_10_days_pc', 'value_from' => '15',  'value_to' => '25', 'normal_value'  => '4', 'status' =>  'enabled', 'created_by' => '0','created_at' => now()], 
            ['country_code' => 'UGA', 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_10_days_pc', 'value_from' => '10',  'value_to' => '15', 'normal_value'  => '6', 'status' =>  'enabled', 'created_by' => '0','created_at' => now()], 
            ['country_code' => 'UGA', 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_10_days_pc', 'value_from' => '5',  'value_to' => '10', 'normal_value'  => '8', 'status' =>  'enabled', 'created_by' => '0','created_at' => now()], 
            ['country_code' => 'UGA', 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_10_days_pc', 'value_from' => '0',  'value_to' => '5', 'normal_value'  => '10', 'status' =>  'enabled', 'created_by' => '0','created_at' => now()], 
            ['country_code' => 'UGA', 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_30_days_pc', 'value_from' => '20',  'value_to' => '100', 'normal_value'  => '2', 'status' =>  'enabled', 'created_by' => '0','created_at' => now()], 
            ['country_code' => 'UGA', 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_30_days_pc', 'value_from' => '15',  'value_to' => '25', 'normal_value'  => '4', 'status' =>  'enabled', 'created_by' => '0','created_at' => now()], 
            ['country_code' => 'UGA', 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_30_days_pc', 'value_from' => '10',  'value_to' => '15', 'normal_value'  => '6', 'status' =>  'enabled', 'created_by' => '0','created_at' => now()], 
            ['country_code' => 'UGA', 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_30_days_pc', 'value_from' => '5',  'value_to' => '10', 'normal_value'  => '8', 'status' =>  'enabled', 'created_by' => '0','created_at' => now()], 
            ['country_code' => 'UGA', 'csf_group' => 'past_performance', 'csf_type' => 'repaid_after_30_days_pc', 'value_from' => '0',  'value_to' => '5', 'normal_value'  => '10', 'status' =>  'enabled', 'created_by' => '0','created_at' => now()], 
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
