<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use DB;

class UISG_Seeder extends Seeder
{
    /**
     * Run the fieldbase seeds.
     *
     * @return void
     */
    public function run()
    {

        $country_code = 'UGA';
        $created_at = datetime_db();
        $acc_prvdr_code = 'UISG';
        $data_prvdr_code = 'UISG';
        $lender_code = 'UFLW';

        DB::delete("delete from persons where associated_entity_code ='UISG'");
        DB::delete("delete from loan_products where acc_prvdr_code = 'UISG'");
        DB::delete("delete from acc_providers where acc_prvdr_code ='UISG'");
        DB::delete("delete from data_prvdrs where data_prvdr_code ='UISG'");
        DB::delete("delete from orgs where name ='INTERSWITCH GROUP'");

        // #To create data_prvdr_rm

        // DB::table('persons')->insert([
        //     ['first_name' => 'MAXIMILLIANA V', 'initials' => 'M', 'last_name' => 'MBABAZI', 'gender' => 'female','email_id' => 'maximilliana.mbabazi@interswitchgroup.com' ,'mobile_num' =>'752256607', 'whatsapp' => '752256607','designation' => 'Lead','national_id' => 'CXXXXXXX','associated_with' => 'data_prvdr','associated_entity_code' => 'UISG' ,'country_code' => $country_code,'created_at' => $created_at , 'created_by' => 0]
        // ]);

        // #To create data_prvdr
        // $data_prvdr_addr_id =  DB::table('address_info')->insertGetId(
        //     ['field_1' => 'central', 'field_2' => 'kampala', 'field_3' => 'central_division', 'field_4' => 'Nakawa Division', 'field_8' => 'TEST','field_9' => 'TEST','country_code' => $country_code,'created_at' => $created_at , 'created_by' => 0]

        // );

        // $data_prvdr_org_id = DB::table('orgs')->insertGetId(
        //     ['name' => 'INTERSWITCH GROUP', 'inc_name' => 'INTERSWITCH GROUP', 'reg_address_id' => $data_prvdr_addr_id, 'country_code' => $country_code,'created_at' => $created_at , 'created_by' => 0 ]

        // );

        // $data_prvdr_person_id = DB::table('persons')->insertGetId(
        //     ['first_name' => 'LULE SAMUEL', 'initials' => 'L', 'last_name' => 'TREVOR', 'gender' => 'male','email_id' => 'trevor.lule@interswitchgroup.com' ,'mobile_num' =>'256752256607', 'whatsapp' => '256752256607','designation' => 'Head Financial Inclusion','national_id' => 'CXXXXXXX','country_code' => $country_code,'created_at' => $created_at , 'created_by' => 0]

        // );

        // DB::table('data_prvdrs')->insert([
        //    ['acc_prvdr_code' => 'UISG', 'name' => 'INTERSWITCH GROUP', 'contract_name' => 'XYZ', 'provider_type' => 'agent_network_manager','provider_type' => 'agent_network_manager','contact_person_id' => $data_prvdr_person_id ,'org_id' => $data_prvdr_org_id , 'agent_code_name' => '', 'created_at' => $created_at , 'created_by' => 0 ],
           
        // ]);

        #To create account_prvdr

        $acc_prvdr_addr_id =  DB::table('address_info')->insertGetId(
            ['field_1' => 'central', 'field_2' => 'kampala', 'field_3' => 'central_division', 'field_4' => 'Nakawa Division', 'field_8' => 'TEST','field_9' => 'TEST','country_code' => $country_code,'created_at' => $created_at , 'created_by' => 0]
        );

        $acc_prvdr_org_id = DB::table('orgs')->insertGetId(
            ['name' => 'INTERSWITCH GROUP', 'inc_name' => 'INTERSWITCH GROUP', 'reg_address_id' => $acc_prvdr_addr_id, 'country_code' => $country_code,'created_at' => $created_at , 'created_by' => 0 ]

        );

        $acc_prvdr_contact_person_id = DB::table('persons')->insertGetId(
            ['first_name' => 'MAXIMILLIANA V', 'initials' => 'M', 'last_name' => 'MBABAZI', 'gender' => 'female','email_id' => 'maximilliana.mbabazi@interswitchgroup.com' ,'mobile_num' =>'752256607', 'whatsapp' => '752256607','designation' => 'Lead','national_id' => 'CXXXXXXX','associated_with' => 'acc_prvdr','associated_entity_code' => $acc_prvdr_code ,'country_code' => $country_code,'created_at' => $created_at , 'created_by' => 0]
        );

        DB::table('acc_providers')->insert([
            [ 'name' => 'INTERSWITCH GROUP', 'acc_prvdr_code' => $acc_prvdr_code,'org_id' => $acc_prvdr_org_id, 'contact_person_id' => $acc_prvdr_contact_person_id, 'biz_account' => true, 'country_code' => $country_code, 'created_at' => $created_at , 'created_by' => 0 ],
        ]);

        #To create products
        
        $acc_purpose = 'float_advance';
        DB::table('loan_products')->insert([

            ['country_code' => $country_code, 'product_name' => 'ISG1', 'product_code' => 'ISG1', 'acc_prvdr_code' => $acc_prvdr_code, 'lender_code' => $lender_code, 'cs_model_code' => 'welcome_products', 'product_type' => 'regular', 'acc_purpose'=> $acc_purpose, 'flow_fee_type' => 'Flat', 'flow_fee' => 6000, 'flow_fee_duration' => '3', 'duration' => 3, 'max_loan_amount' => 500000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0],
            ['country_code' => $country_code, 'product_name' => 'ISG2', 'product_code' => 'ISG2', 'acc_prvdr_code' => $acc_prvdr_code, 'lender_code' => $lender_code, 'cs_model_code' => 'welcome_products', 'product_type' => 'regular', 'acc_purpose'=> $acc_purpose, 'flow_fee_type' => 'Flat', 'flow_fee' => 12000, 'flow_fee_duration' => '6', 'duration' => 6, 'max_loan_amount' => 500000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0],
            ['country_code' => $country_code, 'product_name' => 'ISG3', 'product_code' => 'ISG3', 'acc_prvdr_code' => $acc_prvdr_code, 'lender_code' => $lender_code, 'cs_model_code' => 'welcome_products', 'product_type' => 'regular', 'acc_purpose'=> $acc_purpose, 'flow_fee_type' => 'Flat', 'flow_fee' => 8500, 'flow_fee_duration' => '3', 'duration' => 3, 'max_loan_amount' => 750000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0],
            ['country_code' => $country_code, 'product_name' => 'ISG4', 'product_code' => 'ISG4', 'acc_prvdr_code' => $acc_prvdr_code, 'lender_code' => $lender_code, 'cs_model_code' => 'welcome_products', 'product_type' => 'regular', 'acc_purpose'=> $acc_purpose, 'flow_fee_type' => 'Flat', 'flow_fee' => 17000, 'flow_fee_duration' => '6', 'duration' => 6, 'max_loan_amount' => 750000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0],
            ['country_code' => $country_code, 'product_name' => 'ISG5', 'product_code' => 'ISG5', 'acc_prvdr_code' => $acc_prvdr_code, 'lender_code' => $lender_code, 'cs_model_code' => 'welcome_products', 'product_type' => 'regular', 'acc_purpose'=> $acc_purpose, 'flow_fee_type' => 'Flat', 'flow_fee' => 11000, 'flow_fee_duration' => '3', 'duration' => 3, 'max_loan_amount' => 1000000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0],
            ['country_code' => $country_code, 'product_name' => 'ISG6', 'product_code' => 'ISG6', 'acc_prvdr_code' => $acc_prvdr_code, 'lender_code' => $lender_code, 'cs_model_code' => 'welcome_products', 'product_type' => 'regular', 'acc_purpose'=> $acc_purpose, 'flow_fee_type' => 'Flat', 'flow_fee' => 22000, 'flow_fee_duration' => '6', 'duration' => 6, 'max_loan_amount' => 1000000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0],
 
            ['country_code' => $country_code, 'product_name' => 'ISGPROBATION 1', 'product_code' => 'ISGPROBATION 1', 'acc_prvdr_code' => $acc_prvdr_code, 'lender_code' => $lender_code, 'cs_model_code' => 'half_probation', 'product_type' => 'probation', 'acc_purpose'=> $acc_purpose, 'flow_fee_type' => 'Flat', 'flow_fee' => 6000, 'flow_fee_duration' => '3', 'duration' => 3, 'max_loan_amount' => 500000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0],
            ['country_code' => $country_code, 'product_name' => 'ISGPROBATION 2', 'product_code' => 'ISGPROBATION 2', 'acc_prvdr_code' => $acc_prvdr_code, 'lender_code' => $lender_code, 'cs_model_code' => 'half_probation', 'product_type' => 'probation', 'acc_purpose'=> $acc_purpose, 'flow_fee_type' => 'Flat', 'flow_fee' => 12000, 'flow_fee_duration' => '6', 'duration' => 6, 'max_loan_amount' => 500000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0],
            ['country_code' => $country_code, 'product_name' => 'ISGPROBATION 3', 'product_code' => 'ISGPROBATION 3', 'acc_prvdr_code' => $acc_prvdr_code, 'lender_code' => $lender_code, 'cs_model_code' => 'half_probation', 'product_type' => 'probation', 'acc_purpose'=> $acc_purpose, 'flow_fee_type' => 'Flat', 'flow_fee' => 8500, 'flow_fee_duration' => '3', 'duration' => 3, 'max_loan_amount' => 750000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0],
            ['country_code' => $country_code, 'product_name' => 'ISGPROBATION 4', 'product_code' => 'ISGPROBATION 4', 'acc_prvdr_code' => $acc_prvdr_code, 'lender_code' => $lender_code, 'cs_model_code' => 'half_probation', 'product_type' => 'probation', 'acc_purpose'=> $acc_purpose, 'flow_fee_type' => 'Flat', 'flow_fee' => 17000, 'flow_fee_duration' => '6', 'duration' => 6, 'max_loan_amount' => 750000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0],
            ['country_code' => $country_code, 'product_name' => 'ISGPROBATION 5', 'product_code' => 'ISGPROBATION 5', 'acc_prvdr_code' => $acc_prvdr_code, 'lender_code' => $lender_code, 'cs_model_code' => 'half_probation', 'product_type' => 'probation', 'acc_purpose'=> $acc_purpose, 'flow_fee_type' => 'Flat', 'flow_fee' => 11000, 'flow_fee_duration' => '3', 'duration' => 3, 'max_loan_amount' => 1000000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0],
            ['country_code' => $country_code, 'product_name' => 'ISGPROBATION 6', 'product_code' => 'ISGPROBATION 6', 'acc_prvdr_code' => $acc_prvdr_code, 'lender_code' => $lender_code, 'cs_model_code' => 'half_probation', 'product_type' => 'probation', 'acc_purpose'=> $acc_purpose, 'flow_fee_type' => 'Flat', 'flow_fee' => 22000, 'flow_fee_duration' => '6', 'duration' => 6, 'max_loan_amount' => 1000000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0],
     ]);

        

    }
}
