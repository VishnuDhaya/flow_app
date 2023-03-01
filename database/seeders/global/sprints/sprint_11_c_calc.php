<?php

use Illuminate\Database\Seeder;

class Sprint11CCalc extends Seeder
{

	public function run()
    {

     try{
      DB::beginTransaction();

      DB::table('master_data_keys')->insert([

      	['country_code' => '*', 'data_key' => 'csf_group', 'parent_data_key' => NULL, 'status' => 'enabled'], 
        ['country_code' => '*', 'data_key' => 'csf_type', 'parent_data_key' => 'csf_group', 'status' => 'enabled'], 
        ['country_code' => '*', 'data_key' => 'csf_model', 'parent_data_key' => NULL, 'status' => 'enabled']

      ]);
            
      DB::table('master_data')->insert([

        ['country_code' =>  '*', "data_key" => "csf_group","data_code" => "avg_txns","data_value" => "Average Transactions", 'status' => 'enabled' ,   'parent_data_code' => null, 'data_type'  => 'common'], 
        ['country_code' =>  '*', "data_key" => "csf_group","data_code" => "roi","data_value" => "Return on Investment", 'status' => 'enabled' ,   'parent_data_code' => null, 'data_type'  => 'common'],

        ['country_code' =>  '*', "data_key" => "csf_type","data_code" => "5_day_avg_roi","data_value" => "5 Day Avg ROI", 'status' => 'enabled' ,   'parent_data_code' => 'roi', 'data_type'  => 'common'],
        ['country_code' =>  '*', "data_key" => "csf_type","data_code" => "15_day_avg_roi","data_value" => "15 Day Avg ROI", 'status' => 'enabled' ,   'parent_data_code' => 'roi', 'data_type'  => 'common'],
        ['country_code' =>  '*', "data_key" => "csf_type","data_code" => "30_day_avg_roi","data_value" => "30 Day Avg ROI", 'status' => 'enabled' ,   'parent_data_code' => 'roi', 'data_type'  => 'common'],
        ['country_code' =>  '*', "data_key" => "csf_type","data_code" => "30_day_avg_txns","data_value" => "30 Day Avg Txns", 'status' => 'enabled' ,   'parent_data_code' => 'avg_txns', 'data_type'  => 'common'],

        ['country_code' =>  'UGA', "data_key" => "csf_model","data_code" => "default_model","data_value" => "Default Model", 'status' => 'enabled' ,   'parent_data_code' => null, 'data_type'  => 'common']

       ]);

      DB::table('cs_model_weightages')->insert([

      	['country_code' =>  'UGA', "cs_model_code" => 'default_model',"csf_type" => '5_day_avg_roi',"new_cust_weightage" => '25', 'repeat_cust_weightage' => '30'],
      	['country_code' =>  'UGA', "cs_model_code" => 'default_model',"csf_type" => '15_day_avg_roi',"new_cust_weightage" => '15', 'repeat_cust_weightage' => '18'],
      	['country_code' =>  'UGA', "cs_model_code" => 'default_model',"csf_type" => '30_day_avg_roi',"new_cust_weightage" => '10', 'repeat_cust_weightage' => '12'],
      	['country_code' =>  'UGA', "cs_model_code" => 'default_model',"csf_type" => '30_day_avg_txns',"new_cust_weightage" => '50', 'repeat_cust_weightage' => '40']

      ]);

    DB::commit();
    
    }catch (\Exception $e) {

        DB::rollback();     
        throw new Exception($e);
    }


    }
}