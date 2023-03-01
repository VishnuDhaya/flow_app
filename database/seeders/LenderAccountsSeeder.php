<?php

use Illuminate\Database\Seeder;

class LenderAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        DB::table('accounts')->insert([
	        ['country_code' => 'UGA', 'cust_id' => NULL, 'lender_code' => 'UFLW', 'lender_data_prvdr_code' => 'UEZM',  'data_prvdr_code' => null,'acc_prvdr_name' =>'DFCU' ,'acc_prvdr_code'=>'UDFC','acc_purpose'=>'repayment','type'=>'bank','balance'=>0,'holder_name'=>'Flow','acc_number' =>'1063626247612','branch'=>NULL,'is_primary_acc'=>false,'status' =>  'enabled', 'created_by'=>2],

	        ['country_code' => 'UGA', 'cust_id' => NULL, 'lender_code' => 'UFLW', 'lender_data_prvdr_code' => 'UEZM',  'data_prvdr_code' => null,'acc_prvdr_name' =>'DFCU' ,'acc_prvdr_code'=>'UDFC','acc_purpose'=>'repayment','type'=>'bank','balance'=>0,'holder_name'=>'Flow','acc_number' =>'01063616833446','branch'=>NULL,'is_primary_acc'=>false,'status' =>  'enabled', 'created_by'=>2],

            ['country_code' => 'UGA', 'cust_id' => NULL, 'lender_code' => 'UFLW', 'lender_data_prvdr_code' => 'UEZM',  'data_prvdr_code' => null,'acc_prvdr_name' =>'MOMO' ,'acc_prvdr_code'=>'UDFC','acc_purpose'=>'repayment','type'=>'bank','balance'=>0,'holder_name'=>'Flow','acc_number' =>'215010','branch'=>NULL,'is_primary_acc'=>false,'status' =>  'enabled', 'created_by'=>2]

    	]);
       	DB::table('master_data')->insert([
       		['country_code' => '*', 'data_key' => 'account_transaction_type', 'data_value' => 'Repayment Forward', 'data_code' => 'repayment_fwd',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'],

            ['country_code' =>  '*', "data_key" => "product_type","data_code" => "probation","data_value" => "Probation", 'status' => 'enabled' ,   'parent_data_code' => null, 'data_type'  => 'common']
     	]);

    }
}
