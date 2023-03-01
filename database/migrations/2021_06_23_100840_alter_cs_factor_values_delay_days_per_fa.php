<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCsFactorValuesDelayDaysPerFa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data')->insert([ 
            ['country_code' => 'UGA', 'data_key' => 'csf_type', 'parent_data_code' => 'past_performance', 'data_code' => 'delay_days_per_fa', 'data_value' => 'Average Days Delayed per FA', 'status' =>  'enabled', 'data_type'  => 'common','created_by' => '0', 'created_at' => now()],
        ]);

        DB::table('cs_factor_values')->insert([ 
            ['country_code' => 'UGA', 'csf_group' => 'past_performance', 'csf_type' => 'delay_days_per_fa', 'value_from' => '5',  'value_to' => '99999999', 'normal_value'  => '2', 'status' =>  'enabled', 'created_by' => '0','created_at' => now()], 
            ['country_code' => 'UGA', 'csf_group' => 'past_performance', 'csf_type' => 'delay_days_per_fa', 'value_from' => '4',  'value_to' => '5', 'normal_value'  => '4', 'status' =>  'enabled', 'created_by' => '0','created_at' => now()], 
            ['country_code' => 'UGA', 'csf_group' => 'past_performance', 'csf_type' => 'delay_days_per_fa', 'value_from' => '2',  'value_to' => '4', 'normal_value'  => '6', 'status' =>  'enabled', 'created_by' => '0','created_at' => now()], 
            ['country_code' => 'UGA', 'csf_group' => 'past_performance', 'csf_type' => 'delay_days_per_fa', 'value_from' => '1',  'value_to' => '2', 'normal_value'  => '8', 'status' =>  'enabled', 'created_by' => '0','created_at' => now()], 
            ['country_code' => 'UGA', 'csf_group' => 'past_performance', 'csf_type' => 'delay_days_per_fa', 'value_from' => '0',  'value_to' => '1', 'normal_value'  => '10', 'status' =>  'enabled', 'created_by' => '0','created_at' => now()], 
             
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
