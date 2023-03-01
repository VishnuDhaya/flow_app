<?php

use Illuminate\Database\Seeder;

class MasterDataKeysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //DB::table('master_data_keys')->truncate();
        DB::table('master_data_keys')->insert([
        //$uganda_country_code = UGA;
        ['country_code' => '*','data_key' => 'addr_type', 'parent_data_key' => NULL, 'status' => 'enabled'], 


        ['country_code' => '*','data_key' => 'gender', 'parent_data_key' => NULL, 'status' => 'enabled'], 

        ['country_code' => '*','data_key' => 'borrower_type', 'parent_data_key' => NULL, 'status' => 'enabled'], 

        ['country_code' => '*', 'data_key' => 'biz_addr_prop_type', 'parent_data_key' => NULL, 'status' => 'enabled'], 

        ['country_code' => '*', 'data_key' => 'status', 'parent_data_key' => NULL, 'status' => 'enabled'], 
        // Above are global data


        ['country_code' => '*','data_key' => 'lender_type', 'parent_data_key' => NULL, 'status' => 'enabled'], 

        ['country_code' => '*','data_key' => 'csf_types', 'parent_data_key' => NULL, 'status' => 'enabled'], 

        ['country_code' => '*','data_key' => 'data_provider_type', 'parent_data_key' => NULL, 'status' => 'enabled'], 

        ['country_code' => '*','data_key' => 'designation', 'parent_data_key' => NULL, 'status' => 'enabled'], 
		
        ['country_code' => '*', 'data_key' => 'action_reason_code', 'parent_data_key' => null , 'status' => 'enabled'],

        ['country_code' => '*', 'data_key' => 'transaction_mode', 'parent_data_key' => null , 'status' => 'enabled'],

        ['country_code' => '*', 'data_key' => 'loan_appl_status', 'parent_data_key' => null , 'status' => 'enabled'],

        ['country_code' => '*', 'data_key' => 'loan_status', 'parent_data_key' => null , 'status' => 'enabled'],

        ['country_code' => '*', 'data_key' => 'region', 'parent_data_key' => NULL, 'status' => 'enabled'], 

        ['country_code' => '*', 'data_key' => 'district', 'parent_data_key' => 'region', 'status' => 'enabled'], 

        ['country_code' => '*', 'data_key' => 'county', 'parent_data_key' => 'district', 'status' => 'enabled'],  
        
        ['country_code' => '*', 'data_key' => 'sub_county', 'parent_data_key' => 'county', 'status' => 'enabled'], 

         [ 'country_code' => '*', 'data_key' => 'product_type', 'parent_data_key' => null, 'status' => 'enabled']

    ]);
    }
}
