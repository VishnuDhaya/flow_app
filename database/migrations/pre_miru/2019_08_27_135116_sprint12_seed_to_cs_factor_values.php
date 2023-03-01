<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Sprint12SeedToCsFactorValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         DB::update("update cs_factor_values set country_code='UGA'");  
         DB::update("update loan_txns set to_ac_id = from_ac_id, from_ac_id = NULL where to_ac_id is NULL and from_ac_id is NOT NULL and txn_type='payment'");  

         DB::table('master_data')->insert([
      
            ['country_code' => 'UGA', 'data_key' => 'csf_type', 'data_value' => 'Percent of Ontime FA Repayments', 'data_code' => 'ontime_loans_pc',  'parent_data_code' => 'past_performance',   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()], 
            ['country_code' => 'UGA', 'data_key' => 'csf_type', 'data_value' => 'Percent of FAs Repaid After 3 day Delay', 'data_code' => 'repaid_after_3_days_pc',  'parent_data_code' => 'past_performance',   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => 'UGA', 'data_key' => 'csf_type', 'data_value' => 'Number of Advances Till Now', 'data_code' => 'number_of_advances_till_now',  'parent_data_code' => 'past_performance',   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => 'UGA', 'data_key' => 'csf_type', 'data_value' => 'Number of Advances Per Quarter', 'data_code' => 'number_of_advances_per_quarter',  'parent_data_code' => 'past_performance',   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()]
            ]);


          DB::table('cs_factor_values')->insert([
              ['country_code' => 'UGA', 'csf_group'=> 'past_performance','csf_type' => 'ontime_loans_pc',
              'value_from' => 0,'value_to' => 50,'normal_value' => 2, 'created_by' => '0'],

              ['country_code' => 'UGA', 'csf_group'=> 'past_performance','csf_type' => 'ontime_loans_pc',
              'value_from' => 50,'value_to' => 75,'normal_value' => 4, 'created_by' => '0'],

              ['country_code' => 'UGA', 'csf_group'=> 'past_performance','csf_type' => 'ontime_loans_pc',
              'value_from' => 75,'value_to' => 85,'normal_value' => 6, 'created_by' => '0'],

              ['country_code' => 'UGA', 'csf_group'=> 'past_performance','csf_type' => 'ontime_loans_pc',
              'value_from' => 85,'value_to' => 95 ,'normal_value' => 8, 'created_by' => '0'],

              ['country_code' => 'UGA', 'csf_group'=> 'past_performance','csf_type' => 'ontime_loans_pc',
              'value_from' => 95 ,'value_to' => 100, 'normal_value' => 10 , 'created_by' => '0'],


              ['country_code' => 'UGA', 'csf_group'=> 'past_performance','csf_type' => 'repaid_after_3_days_pc',
              'value_from' => 25,'value_to' => 100,'normal_value' => 2, 'created_by' => '0'],

              ['country_code' => 'UGA', 'csf_group'=> 'past_performance','csf_type' => 'repaid_after_3_days_pc',
              'value_from' => 20,'value_to' => 25,'normal_value' => 4, 'created_by' => '0'],

              ['country_code' => 'UGA', 'csf_group'=> 'past_performance','csf_type' => 'repaid_after_3_days_pc',
              'value_from' => 15,'value_to' => 20,'normal_value' => 6, 'created_by' => '0'],

              ['country_code' => 'UGA', 'csf_group'=> 'past_performance','csf_type' => 'repaid_after_3_days_pc',
              'value_from' => 10,'value_to' => 15 ,'normal_value' => 8, 'created_by' => '0'],

              ['country_code' => 'UGA', 'csf_group'=> 'past_performance','csf_type' => 'repaid_after_3_days_pc',
              'value_from' => 0 ,'value_to' => 10, 'normal_value' => 10 , 'created_by' => '0'],


              ['country_code' => 'UGA', 'csf_group'=> 'past_performance','csf_type' => 'number_of_advances_till_now',
              'value_from' => 0,'value_to' => 10,'normal_value' => 2, 'created_by' => '0'],

              ['country_code' => 'UGA', 'csf_group'=> 'past_performance','csf_type' => 'number_of_advances_till_now',
              'value_from' => 10,'value_to' => 25,'normal_value' => 4, 'created_by' => '0'],

              ['country_code' => 'UGA', 'csf_group'=> 'past_performance','csf_type' => 'number_of_advances_till_now',
              'value_from' => 25,'value_to' => 35,'normal_value' => 6, 'created_by' => '0'],

              ['country_code' => 'UGA', 'csf_group'=> 'past_performance','csf_type' => 'number_of_advances_till_now',
              'value_from' => 35,'value_to' => 50 ,'normal_value' => 8, 'created_by' => '0'],

              ['country_code' => 'UGA', 'csf_group'=> 'past_performance','csf_type' => 'number_of_advances_till_now',
              'value_from' => 50 ,'value_to' => 999999, 'normal_value' => 10 , 'created_by' => '0'],


              ['country_code' => 'UGA', 'csf_group'=> 'past_performance','csf_type' => 'number_of_advances_per_quarter',
              'value_from' => 0,'value_to' => 3 ,'normal_value' => 2, 'created_by' => '0'],

              ['country_code' => 'UGA', 'csf_group'=> 'past_performance','csf_type' => 'number_of_advances_per_quarter',
              'value_from' => 3 ,'value_to' => 6,'normal_value' => 4, 'created_by' => '0'],

              ['country_code' => 'UGA', 'csf_group'=> 'past_performance','csf_type' => 'number_of_advances_per_quarter',
              'value_from' => 6 ,'value_to' => 9,'normal_value' => 6, 'created_by' => '0'],

              ['country_code' => 'UGA', 'csf_group'=> 'past_performance','csf_type' => 'number_of_advances_per_quarter',
              'value_from' => 9,'value_to' => 12 ,'normal_value' => 8, 'created_by' => '0'],

              ['country_code' => 'UGA', 'csf_group'=> 'past_performance','csf_type' => 'number_of_advances_per_quarter',
              'value_from' => 12 ,'value_to' => 999999, 'normal_value' => 10 , 'created_by' => '0']
          ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cs_factor_values', function (Blueprint $table) {
            //
        });
    }
}
