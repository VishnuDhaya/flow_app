<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use DB;

use App\Repositories\SQL\PersonRepositorySQL;
use Exception;

class RRTN_Seeder extends Seeder
{
    /**
     * Run the fieldbase seeds.
     *
     * @return void
     */
    public function run()
    {
        // try {
            DB::beginTransaction();

            $country_code = 'RWA';
            $created_at = datetime_db();
            set_app_session($country_code);

            $lender_code = 'RFLW';
            $acc_prvdr_code = 'RRTN';
            $fund_code = 'FLOW-RWA-INT';

            $person_repo = new PersonRepositorySQL();

            DB::table('accounts')->insert([
                [   'country_code' => $country_code, 'lender_code' => $lender_code, 
                    'network_prvdr_code' => $acc_prvdr_code, 'acc_prvdr_name' => 'RTN Rwanda', 
                    'acc_prvdr_code' => $acc_prvdr_code, 'acc_purpose' => json_encode(["disbursement"]), 
                    'is_primary_acc' => true, 'holder_name' => 'Flow Rwanda Ltd', 
                    'acc_number' => '71ab659d-603b-45e7-8c3d-6c66a523836f', 
                    'created_at' => $created_at , 'created_by' => 0, 'to_recon' => 1, 'disb_int_type' => 'api',
                    'api_cred' => json_encode(["token"=> "%oI//U1_ZpI-tjqtH-KIrl8Y.qT%kPF=1[lPvDLIT}DAYx/dQWr2g", "password"=> "5tT49&543gw="]) 
                ],
            ]);
        
            $RRTN_partner_id = DB::table('persons')->insertGetId(
                ['first_name' => 'Christine ', 'last_name'=> 'Povel', 'gender' => 'male','whatsapp' => '00250787289025','email_id' => 'felix@flowglobal.net','mobile_num' => '00250787289025', 'national_id' => 'C4J6R6VTZ', 'designation' => 'Relationship Manager', 'associated_with' => 'acc_prvdr', 'associated_entity_code' => $acc_prvdr_code, 'country_code' =>  $country_code]
            );

            // acc prvdr RBOK
            $acc_prvdr_addr_id_RTN =  DB::table('address_info')->insertGetId( 
                ['field_1' => 'Kigali', 'field_2' => 'Kigali', 'field_3' => 'KG 5 Ave', 'field_4' => 'Kigali Business Centre','country_code' => $country_code, 'status' => 'enabled', 'created_at' => $created_at , 'created_by' => 0]
            );
            // Organisation Details
            $acc_prvdr_org_id_RTN = DB::table('orgs')->insertGetId(
                ['name' => 'RWANDA TELECENTRE NETWORK', 'inc_name' => 'RWANDA TELECENTRE NETWORK', 'reg_address_id' => $acc_prvdr_addr_id_RTN, 'country_code' => $country_code,'status' => 'enabled', 'created_at' => $created_at , 'created_by' => 0 ]
            );
        
            // Contact Person Details
            $acc_prvdr_contact_person_id_RTN= DB::table('persons')->insertGetId(
                ['first_name' => 'Jean', 'last_name' => 'MARCUS', 'gender' => 'male','email_id' => 'jean@rtn.rw' ,'mobile_num' =>'788351441', 'whatsapp' => '788351441','designation' => 'Head Financial Inclusion','national_id' => 'CXXXXXXX','country_code' => $country_code, 'status' => 'enabled', 'created_at' => $created_at , 'created_by' => 0]
            );
        
            DB::table('acc_providers')->insert([
                ['name' => 'RWANDA TELECENTRE NETWORK', 'acc_prvdr_code' => $acc_prvdr_code, 'api_url' => 'https://stagingapi.iteme.co.rw/api/flow', 'acc_provider_logo' => '2201458280.png', 'org_id' => $acc_prvdr_org_id_RTN , 'biz_account' => 1, 'country_code' => $country_code, 'status' => 'enabled', 'created_at' => $created_at , 'created_by' => 0 ]
            ]);

            DB::table('loan_products')->insert([
                // RTN products
                ['country_code' => 'RWA', 'product_name' => 'RTN1', 'product_code' => 'RTN1', 'acc_prvdr_code' => 'RRTN', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 1500, 'flow_fee_duration' => 3, 'duration' => 3, 'max_loan_amount' => 150000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 500],

                ['country_code' => 'RWA', 'product_name' => 'RTN2', 'product_code' => 'RTN2', 'acc_prvdr_code' => 'RRTN', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 2000, 'flow_fee_duration' => 3, 'duration' => 3, 'max_loan_amount' => 200000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 650],

                ['country_code' => 'RWA', 'product_name' => 'RTN3', 'product_code' => 'RTN3', 'acc_prvdr_code' => 'RRTN', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 3000, 'flow_fee_duration' => 3, 'duration' => 3, 'max_loan_amount' => 300000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 1000],


                ['country_code' => 'RWA', 'product_name' => 'RTN6', 'product_code' => 'RTN6', 'acc_prvdr_code' => 'RRTN', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 3000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 150000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 500],

                ['country_code' => 'RWA', 'product_name' => 'RTN7', 'product_code' => 'RTN7', 'acc_prvdr_code' => 'RRTN', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 4000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 200000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 650],

                ['country_code' => 'RWA', 'product_name' => 'RTN8', 'product_code' => 'RTN8', 'acc_prvdr_code' => 'RRTN', 'lender_code' => 'RFLW', 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 6000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 300000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 1000],

            ]);

            // Customer 1
            $rm_user_id = 54;
            $rm_person_id = 3568;
            $dp_rel_mgr_id = $RRTN_partner_id;

            $territory = 'kigali';
            $location = 'kigali_new_market';
            $gps = '8.1599574,77.433939';

            $address_info = [
                'field_1' => 'kigali',
                'field_2' => 'nyarugenge',
                'field_3' => 'kigali',
                'field_4' => 'Kigali Business Center',
                'field_5' => 'kbc',
                'field_6' => $location ,
                'field_7' => $gps,
                'field_8' => $territory,
                'country_code' => $country_code,
                'created_at' => $created_at,
                'created_by' => $rm_user_id
            ];

            $owner_addr_id = DB::table('address_info')->insertGetId($address_info);

            $persons = [
                'mobile_num' => '788351450',
                'alt_biz_mobile_num_1' => '788351451',
                'country_code' => $country_code,
                'dob' => '1990-01-01',
                'gender' => 'male',
                'whatsapp' => '788351450',
                'last_name' => 'Ingabire',
                'photo_pps' => '1654169545.jpeg',
                'first_name' => 'Christine',
                'national_id' => '1199080140296035',
                'photo_selfie' => '1654169540.jpeg',
                'photo_national_id' => '1654169524.jpeg',
                'photo_national_id_back' => '1654169535.jpeg',
                'created_at' => $created_at,
                'created_by' => $rm_user_id,
            ];

            $owner_person_id = DB::table('persons')->insertGetId($persons);

            $cust_id = 'RFLW-788351450';
            $acc_number = '71ab659d-603b-45e7-8c3d-6c66a523836e';
            $accounts = [
                'cust_id' => $cust_id,
                'acc_number' => $acc_number,
                'country_code' => $country_code,
                'acc_prvdr_code' => $acc_prvdr_code,
                'holder_name' => 'Christine Ingabire',
                'acc_purpose' => json_encode(['float_advance']),
                'assessment_type' => 'self',
                'acc_prvdr_name' => 'RWANDA TELECENTRE NETWORK',
                'created_at' => $created_at,
                'created_by' => $rm_user_id
            ];

            $account_id = DB::table('accounts')->insertGetId($accounts);

            $borrowers = [
                'cust_id' => $cust_id,
                'lender_code' => $lender_code,
                'reg_flow_rel_mgr_id' => $rm_person_id,
                'reg_date' => $created_at,
                'prob_fas' => 15,
                'fund_code' => $fund_code,
                'flow_rel_mgr_id' => $rm_person_id,
                'country_code' => $country_code,
                'lead_id' => null,
                'acc_prvdr_code' => $acc_prvdr_code,
                'acc_number' => $acc_number,
                'biz_type' => 'individual',
                'status' => 'enabled',
                'kyc_status' => 'completed',
                'location' => $location,
                'territory' => $territory,
                'category' => 'Probation',
                'current_aggr_doc_id' => 'AGRT-RFLW-2206021350-788351450',
                'biz_name' => 'CHRISTINE INGABIRE',
                'ownership' => 'owned',
                'dp_rel_mgr_id' => $dp_rel_mgr_id,
                'business_distance' => 'business_at_home',
                'biz_addr_prop_type' => 'electronic_shop',
                'gps' => $gps,
                'photo_shop' => '1654167224.jpeg',
                'photo_biz_lic' => '1654167218.jpeg',
                'biz_address_id' => $owner_addr_id,
                'owner_person_id' => $owner_person_id,
                'owner_address_id' => $owner_addr_id,
                'created_at' => $created_at,
                'created_by' => $rm_user_id,
            ];

            $borrower_id = DB::table('borrowers')->insertGetId($borrowers);

            $probation_period = [
                'cust_id' => $cust_id,
                'start_date' => $created_at,
                'type' => 'probation',
                'fa_count' => 15,
                'status' => 'active',
                'created_at' => $created_at,
                'country_code' => $country_code,
                'created_by' => $rm_user_id,
            ];

            $probation_period_id = DB::table('probation_period')->insertGetId($probation_period);

            $cust_agreements = [
                'country_code' => 'RWA',
                'aggr_doc_id' => 'AGRT-RFLW-2206021350-788351450',
                'aggr_type' => 'probation',
                'duration_type' => 'fas',
                'cust_id' => $cust_id,
                'witness_name' => 'test',
                'witness_mobile_num' => '17423589',
                'status' => 'active',
                'valid_from' => $created_at,
                'created_by' => $rm_user_id,
                'created_at' => $created_at
            ];

            $cust_agreements_id = DB::table('cust_agreements')->insertGetId($cust_agreements);


            // Customer 2
            $gps = '8.1599574,77.533939';

            $address_info = [
                'field_1' => 'kigali',
                'field_2' => 'nyarugenge',
                'field_3' => 'kigali',
                'field_4' => 'Kigali Business Center',
                'field_5' => 'kbc',
                'field_6' => $location ,
                'field_7' => $gps,
                'field_8' => $territory,
                'country_code' => $country_code,
                'created_at' => $created_at,
                'created_by' => $rm_user_id
            ];

            $owner_addr_id = DB::table('address_info')->insertGetId($address_info);

            $persons = [
                'mobile_num' => '788351460',
                'alt_biz_mobile_num_1' => '788351451',
                'country_code' => $country_code,
                'dob' => '1990-01-01',
                'gender' => 'male',
                'whatsapp' => '788351460',
                'last_name' => 'Heinen',
                'photo_pps' => '1654169545.jpeg',
                'first_name' => 'Oliver',
                'national_id' => '1199080140296035',
                'photo_selfie' => '1654169540.jpeg',
                'photo_national_id' => '1654169524.jpeg',
                'photo_national_id_back' => '1654169535.jpeg',
                'created_at' => $created_at,
                'created_by' => $rm_user_id,
            ];

            $owner_person_id = DB::table('persons')->insertGetId($persons);

            $cust_id = 'RFLW-788351460';
            $acc_number = '80b8cd86-d46f-4e0d-8150-7e46fb505f09';
            $accounts = [
                'cust_id' => $cust_id,
                'acc_number' => $acc_number,
                'country_code' => $country_code,
                'acc_prvdr_code' => $acc_prvdr_code,
                'holder_name' => 'Oliver Heinen',
                'acc_purpose' => json_encode(['float_advance']),
                'assessment_type' => 'self',
                'acc_prvdr_name' => 'RWANDA TELECENTRE NETWORK',
                'created_at' => $created_at,
                'created_by' => $rm_user_id
            ];

            DB::table('accounts')->insert([$accounts]);

            $cust_agreements = [
                'country_code' => 'RWA',
                'aggr_doc_id' => 'AGRT-RFLW-2207021350-788351460',
                'aggr_type' => 'probation',
                'duration_type' => 'fas',
                'cust_id' => $cust_id,
                'witness_name' => 'test',
                'witness_mobile_num' => '17423589',
                'status' => 'active',
                'valid_from' => $created_at,
                'created_by' => $rm_user_id,
                'created_at' => $created_at
            ];

            DB::table('cust_agreements')->insert([$cust_agreements]);

            $borrowers = [
                'cust_id' => $cust_id,
                'lender_code' => $lender_code,
                'reg_flow_rel_mgr_id' => $rm_person_id,
                'reg_date' => $created_at,
                'prob_fas' => 15,
                'fund_code' => $fund_code,
                'flow_rel_mgr_id' => $rm_person_id,
                'country_code' => $country_code,
                'lead_id' => null,
                'acc_prvdr_code' => $acc_prvdr_code,
                'acc_number' => $acc_number,
                'biz_type' => 'individual',
                'status' => 'enabled',
                'kyc_status' => 'completed',
                'location' => $location,
                'territory' => $territory,
                'category' => 'Probation',
                'current_aggr_doc_id' => 'AGRT-RFLW-2207021350-788351460',
                'biz_name' => 'OLIVER HEINEN',
                'ownership' => 'owned',
                'dp_rel_mgr_id' => $dp_rel_mgr_id,
                'business_distance' => 'business_at_home',
                'biz_addr_prop_type' => 'electronic_shop',
                'gps' => $gps,
                'photo_shop' => '1654167224.jpeg',
                'photo_biz_lic' => '1654167218.jpeg',
                'biz_address_id' => $owner_addr_id,
                'owner_person_id' => $owner_person_id,
                'owner_address_id' => $owner_addr_id,
                'created_at' => $created_at,
                'created_by' => $rm_user_id,
            ];

            $borrower_id = DB::table('borrowers')->insertGetId($borrowers);

            $probation_period = [
                'cust_id' => $cust_id,
                'start_date' => $created_at,
                'type' => 'probation',
                'fa_count' => 15,
                'status' => 'active',
                'created_at' => $created_at,
                'country_code' => $country_code,
                'created_by' => $rm_user_id,
            ];

            $probation_period_id = DB::table('probation_period')->insertGetId($probation_period);

            DB::commit();
        // }
        // catch(Exception $e) {
        //     DB::rollback();
        //     thrw($e->getTraceAsString());
        // }
    }
}