<?php

use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

       
       
        DB::table('master_data')->insert([
	           ['country_code' => '*', 'data_key' => 'addr_type', 'data_value' => 'Head Office', 'data_code' => 'head_office',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' => '*', 'data_key' => 'addr_type', 'data_value' => 'Residential', 'data_code' => 'residential',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' => '*', 'data_key' => 'addr_type', 'data_value' => 'Branch Office', 'data_code' => 'branch office',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'],  
        ['country_code' => '*', 'data_key' => 'addr_type', 'data_value' => 'Other', 'data_code' => 'other',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' => '*', 'data_key' => 'gender', 'data_value' => 'Male', 'data_code' => 'Male',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' => '*', 'data_key' => 'gender', 'data_value' => 'Female', 'data_code' => 'female',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' => '*', 'data_key' => 'borrower_type', 'data_value' => 'Individual', 'data_code' => 'individual',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' => '*', 'data_key' => 'borrower_type', 'data_value' => 'Institutional', 'data_code' => 'institutional',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' => '*', 'data_key' => 'status', 'data_value' => 'enabled', 'data_code' => 'enable',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' => '*', 'data_key' => 'status', 'data_value' => 'Disable', 'data_code' => 'disable',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' => '*', 'data_key' => 'lender_type', 'data_value' => 'Bank', 'data_code' => 'bank',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' =>  '*', 'data_key' => 'lender_type', 'data_value' => 'Local Debt Funds', 'data_code' => 'local_debt_funds',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' =>  '*', 'data_key' => 'lender_type', 'data_value' => 'International Debt Funds', 'data_code' => 'international_debt_funds',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' =>  '*', 'data_key' => 'lender_type', 'data_value' => 'Flow - Self', 'data_code' => 'self',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 



        ['country_code' =>  '*', 'data_key' => 'biz_addr_prop_type', 'data_value' => 'Rented', 'data_code' => 'rented',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' =>  '*', 'data_key' => 'biz_addr_prop_type', 'data_value' => 'Owned', 'data_code' => 'owned',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 

        // Above are global data


      
        ['country_code' =>  '*', 'data_key' => 'csf_types', 'data_value' => 'Avg Daily Transaction', 'data_code' => 'avg_daily_transaction',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' =>  '*', 'data_key' => 'csf_types', 'data_value' => 'Field Assessment', 'data_code' => 'field_assessment',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' =>  '*', 'data_key' => 'csf_types', 'data_value' => 'Absolute Factor', 'data_code' => 'absolute_factor',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' =>  '*', 'data_key' => 'csf_types', 'data_value' => 'Potential', 'data_code' => 'potential',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' =>  '*', 'data_key' => 'csf_types', 'data_value' => 'Past Loan Performance', 'data_code' => 'plp',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' =>  '*', 'data_key' => 'csf_types', 'data_value' => 'Reference From Existing Borrower', 'data_code' => 'reference_from_existing_borrower',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' =>  '*', 'data_key' => 'csf_types', 'data_value' => 'Cost Benefit Analysis', 'data_code' => 'cost_benefit_analysis',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' =>  '*', 'data_key' => 'data_provider_type', 'data_value' => 'Mobile Network Operator', 'data_code' => 'mobile_network_operator',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' =>  '*', 'data_key' => 'data_provider_type', 'data_value' => 'Mobile Money Operator', 'data_code' => 'mobile_money_operator',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' =>  '*', 'data_key' => 'data_provider_type', 'data_value' => 'Payments Aggregator', 'data_code' => 'payments_aggregator',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' =>  '*', 'data_key' => 'data_provider_type', 'data_value' => 'Agent Network Manager', 'data_code' => 'agent_network_manager',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        
        ['country_code' =>  '*', 'data_key' => 'designation', 'data_value' => 'Supervisor', 'data_code' => 'supervisor',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' =>  '*', 'data_key' => 'designation', 'data_value' => 'Admin', 'data_code' => 'admin',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 
        ['country_code' =>  '*', 'data_key' => 'designation', 'data_value' => 'Field Representative', 'data_code' => 'field_representative',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 

          ['country_code' =>  '*', 'data_key' => 'designation', 'data_value' => 'Field Representative', 'data_code' => 'field_representative',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'], 

          
        ['country_code' =>  '*', "data_key" => "transaction_mode","data_code" => "net_banking","data_value" => "Net Banking", 'status' => 'enabled' ,   'parent_data_code' => null, 'data_type'  => 'common'], 
        ['country_code' =>  '*', "data_key" => "transaction_mode","data_code" => "mobile_banking","data_value" => "Mobile Banking", 'status' => 'enabled' ,   'parent_data_code' => null, 'data_type'  => 'common'], 

        ['country_code' =>  '*', "data_key" => "action_reason_code","data_code" => "ineligible_credit_score","data_value" => "Ineligible Credit Score", 'status' => 'enabled' ,   'parent_data_code' => null, 'data_type'  => 'common'], 
        ['country_code' =>  '*', "data_key" => "action_reason_code","data_code" => "past_fa_performance","data_value" => "Past FA Performance", 'status' => 'enabled' ,   'parent_data_code' => null, 'data_type'  => 'common'], 
        ['country_code' =>  '*', "data_key" => "action_reason_code","data_code" => "incorrect_appl","data_value" => "Incorrect Application", 'status' => 'enabled' ,   'parent_data_code' => null, 'data_type'  => 'common'], 
        ['country_code' =>  '*', "data_key" => "action_reason_code","data_code" => "others","data_value" => "Others", 'status' => 'enabled' ,   'parent_data_code' => null, 'data_type'  => 'common'], 

        ['country_code' =>  '*', "data_key" => "loan_appl_status","data_code" => "approved","data_value" => "Approved", 'status' => 'enabled' ,   'parent_data_code' => null, 'data_type'  => 'common'], 
        ['country_code' =>  '*', "data_key" => "loan_appl_status","data_code" => "pending_approval","data_value" => "Pending Approval", 'status' => 'enabled' ,   'parent_data_code' => null, 'data_type'  => 'common'], 
        ['country_code' =>  '*', "data_key" => "loan_appl_status","data_code" => "rejected","data_value" => "Rejected", 'status' => 'enabled' ,   'parent_data_code' => null, 'data_type'  => 'common'], 
        ['country_code' =>  '*', "data_key" => "loan_appl_status","data_code" => "cancelled","data_value" => "Cancelled", 'status' => 'enabled' ,   'parent_data_code' => null, 'data_type'  => 'common'], 

        ['country_code' =>  '*', "data_key" => "loan_status","data_code" => "pending_disbursal","data_value" => "Pending Disbursal", 'status' => 'enabled' ,   'parent_data_code' => null, 'data_type'  => 'common'], 
        ['country_code' =>  '*', "data_key" => "loan_status","data_code" => "ongoing","data_value" => "Ongoing", 'status' => 'enabled',   'parent_data_code' => null, 'data_type'  => 'common'], 
        ['country_code' =>  '*', "data_key" => "loan_status","data_code" => "due","data_value" => "Due Today", 'status' => 'enabled' ,   'parent_data_code' => null, 'data_type'  => 'common'], 
        ['country_code' =>  '*', "data_key" => "loan_status","data_code" => "overdue","data_value" => "Overdue", 'status' => 'enabled' ,   'parent_data_code' => null, 'data_type'  => 'common'], 
        ['country_code' =>  '*', "data_key" => "loan_status","data_code" => "settled","data_value" => "Settled", 'status' => 'enabled' ,   'parent_data_code' => null, 'data_type'  => 'common'], 

        ['country_code' =>  '*', "data_key" => "time_zone","data_code" => "IST","data_value" => "Asia/Kolkata", 'status' => 'enabled' ,   'parent_data_code' => null, 'data_type'  => 'common'], 
        ['country_code' =>  '*', "data_key" => "time_zone","data_code" => "EAT","data_value" => "Africa/Kampala", 'status' => 'enabled',   'parent_data_code' => null, 'data_type'  => 'common'], 
        ['country_code' =>  '*', "data_key" => "time_zone","data_code" => "CAT","data_value" => "Africa/Kigali", 'status' => 'enabled' ,   'parent_data_code' => null, 'data_type'  => 'common'], 
        ['country_code' =>  '*', "data_key" => "time_zone","data_code" => "BST","data_value" => "Europe/London", 'status' => 'enabled',   'parent_data_code' => null, 'data_type'  => 'common'],

        ['country_code' =>  '*', "data_key" => "product_type","data_code" => "regular","data_value" => "Regular", 'status' => 'enabled' ,   'parent_data_code' => null, 'data_type'  => 'common'], 
        ['country_code' =>  '*', "data_key" => "product_type","data_code" => "topup","data_value" => "Topup", 'status' => 'enabled' ,   'parent_data_code' => null, 'data_type'  => 'common']

        
        ]);
    }
}
