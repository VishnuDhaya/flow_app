<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use DB;

use App\Repositories\SQL\PersonRepositorySQL;

class RMTN_RBOK_Seeder extends Seeder
{
    /**
     * Run the fieldbase seeds.
     *
     * @return void
     */
    public function run()
    {

        $country_code = 'RWA';
        $created_at = datetime_db();
        session()->put('country_code',$country_code);

        $lender_code = 'RFLW';
        $person_repo = new PersonRepositorySQL();
        $last_id = $person_repo->get_last_id();
        

        #To create lender
        // Registered Address Details
        $lender_addr_id =  DB::table('address_info')->insertGetId(
        ['field_1' => 'Kigali City', 'field_2' => 'Kiyovu', 'field_3' => 'Nyarugenge', 'field_4' => 'KN 43 St', 'field_5' => 'Plot 444','country_code' => $country_code,'created_at' => $created_at , 'created_by' => 0]

        );
        // Organisation Details
        $lender_org_id = DB::table('orgs')->insertGetId(
        ['name' => 'Flow Rwanda', 'inc_name' => 'Flow Rwanda Ltd', 'reg_address_id' => $lender_addr_id, 'country_code' => $country_code,'created_at' => $created_at , 'created_by' => 0 ]

        );
        $lender_contact_person_id = DB::table('persons')->insertGetId(
        ['first_name' => 'Felix', 'last_name' => 'Povel', 'gender' => 'male', 'email_id' => 'felix@flowglobal.net' ,'mobile_num' =>'00250787289025', 'alt_biz_mobile_num_1' => '', 'whatsapp' => '','designation' => 'Country Representative','country_code' => $country_code,'created_at' => $created_at , 'created_by' => 0]
        );

        $lender_code = 'RFLW';
        DB::table('lenders')->insert([

        ['lender_code' => $lender_code, 'name' => 'Flow Rwanda', 'lender_type' => 'self' , 'country_code' => $country_code, 'contact_person_id' => $lender_contact_person_id, 'org_id' => $lender_org_id, 'created_at' => $created_at , 'created_by' => 0 ],

        ]);
        // CAPITAL FUNDS
        DB::table('capital_funds')->insert([
            ['fund_code' => 'FLOW-RWA-INT', 'fund_name' => 'Flow Rwanda - Internal', 'lender_code' => $lender_code,'fund_type' => 'internal', 'is_lender_default' => true, 'fe_currency_code' => 'USD','forex'=> 3545.0000, 'alloc_amount_fc' => 25000.00, 'status' => 'enabled', 'country_code' => $country_code, 'created_at' => $created_at , 'created_by' => 0 ],
        
        ]);

        DB::table('accounts')->insert([

            ['country_code' => $country_code, 'lender_code' => $lender_code, 'lender_acc_prvdr_code' => 'RMTN', 'acc_prvdr_name' => 'MTN Rwanda', 'acc_prvdr_code' => 'RMTN', 'acc_purpose' => '["disbursement"]', 'is_primary_acc' => true, 'holder_name' => 'Flow Rwanda Ltd', 'acc_number' => 0, 'created_at' => $created_at , 'created_by' => 0, 'to_recon' => 1 ],

            ['country_code' => $country_code, 'lender_code' => $lender_code, 'lender_acc_prvdr_code' => 'RBOK', 'acc_prvdr_name' => 'Bank of Kigali', 'acc_prvdr_code' => 'RBOK', 'acc_purpose' => '["disbursement"]', 'is_primary_acc' => true, 'holder_name' => 'Flow Rwanda Ltd', 'acc_number' => '410777710253', 'created_at' => $created_at , 'created_by' => 0, 'to_recon' => 1 ],

        ]);

       

        $RMTN_partner_id = DB::table('persons')->insertGetId(
            ['first_name' => 'Felix ', 'last_name'=> 'Povel', 'gender' => 'male','whatsapp' => '00250787289025','email_id' => 'felix@flowglobal.net','mobile_num' => '00250787289025', 'national_id' => 'C4J6R6VTZ', 'designation' => 'Relationship Manager', 'associated_with' => 'acc_prvdr', 'associated_entity_code' => 'RMTN', 'country_code' =>  $country_code]
        );
    
        $RBOK_partner_id = DB::table('persons')->insertGetId(
            ['first_name' => 'Felix ', 'last_name'=> 'Povel', 'gender' => 'male','whatsapp' => '00250787289025','email_id' => 'felix@flowglobal.net','mobile_num' => '00250787289025', 'national_id' => 'C4J6R6VTZ', 'designation' => 'Relationship Manager', 'associated_with' => 'acc_prvdr', 'associated_entity_code' => 'RBOK', 'country_code' =>  $country_code]
        );
        
        

        

        //market
        $market_addr_id =  DB::table('address_info')->insertGetId(
            ['field_1' => 'Kigali City', 'field_2' => 'Kiyovu', 'field_3' => 'Nyarugenge', 'field_4' => 'KN 43 St', 'field_5' => 'Plot 444','country_code' => $country_code,'created_at' => $created_at , 'created_by' => 0]
        );

        $market_org_id = DB::table('orgs')->insertGetId(
            ['name' => 'Flow Rwanda', 'inc_name' => 'Flow Rwanda Ltd', 'country_code' => $country_code, 'reg_address_id' => $market_addr_id, 'status' => 'enabled', 'created_at' => $created_at , 'created_by' => 0 ]
        );
    
        DB::table('markets')->insert([
            ['country_code' => 'RWA', 'currency_code' => 'RWF', 'org_id' => $market_org_id,  'head_person_id' => 13, 'isd_code' => 250, 'time_zone' => 'CAT', 'status' => 'enabled', 'created_at' => $created_at , 'created_by' => 0 ]
        ]);


        // acc prvdr RBOK
        $acc_prvdr_addr_id_BOK =  DB::table('address_info')->insertGetId( 
            ['field_1' => 'Kigali', 'field_2' => 'Kigali', 'field_3' => 'KG 9 Ave', 'field_4' => 'MTN Centre','country_code' => $country_code, 'status' => 'enabled', 'created_at' => $created_at , 'created_by' => 0]
        );
        // Organisation Details
        $acc_prvdr_org_id_BOK = DB::table('orgs')->insertGetId(
            ['name' => 'BANK OF KIGALI', 'inc_name' => 'BANK OF KIGALI', 'reg_address_id' => $acc_prvdr_addr_id_BOK, 'country_code' => $country_code,'status' => 'enabled', 'created_at' => $created_at , 'created_by' => 0 ]
        );
    
        // Contact Person Details
        $acc_prvdr_contact_person_id_BOK= DB::table('persons')->insertGetId(
            ['first_name' => 'Solange', 'last_name' => 'UMUHOZA', 'gender' => 'Female','email_id' => 'sumuhoza@bk.rw' ,'mobile_num' =>'788351431', 'whatsapp' => '788351431','designation' => 'Head Financial Inclusion','national_id' => 'CXXXXXXX','country_code' => $country_code, 'status' => 'enabled', 'created_at' => $created_at , 'created_by' => 0]
        );
    
        DB::table('acc_providers')->insert([
            ['name' => 'BANK OF KIGALI', 'acc_prvdr_code' => 'RBOK', 'acc_provider_logo' => '2201458279.png', 'org_id' => $acc_prvdr_org_id_BOK , 'biz_account' => 1, 'country_code' => $country_code, 'int_type' => 'web', 'status' => 'enabled', 'created_at' => $created_at , 'created_by' => 0 ]
        ]);
    
    
        $acc_prvdr_addr_id_MTN =  DB::table('address_info')->insertGetId(
            ['field_1' => 'Kigali Heights', 'field_2' => '', 'field_3' => '', 'status' => 'enabled', 'country_code' => $country_code,'created_at' => $created_at , 'created_by' => 0]
        );
        // Organisation Details
        $acc_prvdr_org_id_MTN = DB::table('orgs')->insertGetId(
            ['name' => 'MTN RWANDA', 'inc_name' => 'MTN RWANDA', 'reg_address_id' => $acc_prvdr_addr_id_MTN, 'country_code' => $country_code, 'status' => 'enabled', 'created_at' => $created_at , 'created_by' => 0 ]
        );
    
        // Contact Person Details
        $acc_prvdr_contact_person_id_MTN= DB::table('persons')->insertGetId(
            ['first_name' => 'Ezechiel', 'last_name' => 'Cyiza', 'gender' => 'male','email_id' => 'cyiza.ezechiel@mtn.com' ,'mobile_num' =>'788312687', 'whatsapp' => '788312687', 'designation' => 'Head Financial Inclusion', 'country_code' => $country_code, 'status' => 'enabled', 'created_at' => $created_at , 'created_by' => 0]
        );
    
        DB::table('acc_providers')->insert([
            ['name' => 'MTN RWANDA', 'acc_prvdr_code' => 'RMTN', 'acc_provider_logo' => '1627630100.png', 'org_id' => $acc_prvdr_org_id_MTN , 'biz_account' => 1, 'int_type' => 'web', 'country_code' => $country_code, 'status' => 'enabled', 'created_at' => $created_at , 'created_by' => 0 ]
        ]);
    }
}
