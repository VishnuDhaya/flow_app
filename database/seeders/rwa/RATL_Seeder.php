<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use DB;

use App\Repositories\SQL\PersonRepositorySQL;
use Exception;
use Illuminate\Support\Facades\Log;

class RATL_Seeder extends Seeder
{
    /**
     * Run the fieldbase seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            DB::beginTransaction();

            $country_code = 'RWA';
            set_app_session($country_code);
            $created_at = datetime_db();

            $lender_code = 'RFLW';
            $acc_prvdr_code = 'RATL';
            $acc_prvdr_name = 'RTN Airtel';
            $fund_code = 'FLOW-RWA-INT';
            $ussd_short_code = "*182*6";
            $ussd_code_format = "$ussd_short_code*0:recipient*:amount*:pin#";
            $ussd_holder_name_code = "$ussd_short_code*0:recipient*500#";

            $mobile_cred_format = ['ussd_short_code' => $ussd_short_code, 'ussd_code_format' => $ussd_code_format, 'ussd_holder_name_code' => $ussd_holder_name_code, 'heartbeat' => 'enabled'];
            $url = 'https://197.157.130.8:5555/AirtelMoney'; // TODO Where to include this?
            $logo_number = '1669100398';

            $person_repo = new PersonRepositorySQL();
            $web_cred = [
                'timeout' => 600,
            ];

            DB::table('accounts')->insert([
                [   'country_code' => $country_code, 'lender_code' => $lender_code, 
                    'network_prvdr_code' => $acc_prvdr_code, 'acc_prvdr_name' => $acc_prvdr_name, 
                    'acc_prvdr_code' => $acc_prvdr_code, 'acc_purpose' => json_encode(["disbursement"]), 
                    'is_primary_acc' => true, 'holder_name' => 'Flow Rwanda Ltd', 
                    'acc_number' => '737179936', 'web_cred' => json_encode($web_cred), 'stmt_int_type' => 'web',
                    'created_at' => $created_at , 'created_by' => 0, 'to_recon' => 1, 'disb_int_type' => 'mob',
                    'process_txn_sms' => 1
                ],
            ]);
            
            DB::table('accounts')->insert([
                [   'country_code' => $country_code, 'lender_code' => $lender_code, 
                    'network_prvdr_code' => $acc_prvdr_code, 'acc_prvdr_name' => $acc_prvdr_name, 
                    'acc_prvdr_code' => $acc_prvdr_code, 'acc_purpose' => json_encode(["ussd"]), 
                    'is_primary_acc' => true, 'holder_name' => 'Flow Rwanda Ltd', 
                    'acc_number' => '737179937',
                    'created_at' => $created_at , 'created_by' => 0, 'to_recon' => 0
                ],
            ]);

            DB::table('persons')->insertGetId(
                ['first_name' => 'Felix ','last_name'=> 'Povel','national_id' => 'C4J6R6VTZ','gender' => 'male','whatsapp' => '00250787289025','email_id' => 'felix@flowglobal.net','mobile_num' => '00250787289025', 'designation' => 'Relationship Manager', 'associated_with' => 'acc_prvdr', 'associated_entity_code' => $acc_prvdr_code, 'status' => 'enabled', 'country_code' =>  $country_code]
            );

            // Our address is used here
            // acc prvdr RBOK
            $acc_prvdr_addr_id =  DB::table('address_info')->insertGetId( 
                ['field_1' => 'Kigali City', 'field_2' => 'Kiyovu', 'field_3' => 'Nyarugenge', 'field_4' => 'KN 43 St','country_code' => $country_code, 'status' => 'enabled', 'created_at' => $created_at , 'created_by' => 0]
            );

            // Organisation Details
            $acc_prvdr_org_id = DB::table('orgs')->insertGetId(
                ['name' => 'AIRTEL RWANDA', 'inc_name' => 'AIRTEL RWANDA', 'reg_address_id' => $acc_prvdr_addr_id, 'country_code' => $country_code,'status' => 'enabled', 'created_at' => $created_at , 'created_by' => 0 ]
            );
        
            DB::table('acc_providers')->insert([
                ['name' => 'AIRTEL RWANDA', 'acc_prvdr_code' => $acc_prvdr_code, 'acc_provider_logo' => "$logo_number.png", 'org_id' => $acc_prvdr_org_id , 'biz_account' => 1, 'country_code' => $country_code, 'status' => 'enabled', 'created_at' => $created_at , 'created_by' => 0, 'mobile_cred_format' => json_encode($mobile_cred_format) ]
            ]);

            DB::table('loan_products')->insert([
                // products
                ['country_code' => $country_code, 'product_name' => "{$acc_prvdr_code}_1", 'product_code' => "{$acc_prvdr_code}_1", 'acc_prvdr_code' => $acc_prvdr_code, 'lender_code' => $lender_code, 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 1500, 'flow_fee_duration' => 3, 'duration' => 3, 'max_loan_amount' => 150000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 500],

                ['country_code' => $country_code, 'product_name' => "{$acc_prvdr_code}_2", 'product_code' => "{$acc_prvdr_code}_2", 'acc_prvdr_code' => $acc_prvdr_code, 'lender_code' => $lender_code, 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 2000, 'flow_fee_duration' => 3, 'duration' => 3, 'max_loan_amount' => 200000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 650],

                ['country_code' => $country_code, 'product_name' => "{$acc_prvdr_code}_3", 'product_code' => "{$acc_prvdr_code}_3", 'acc_prvdr_code' => $acc_prvdr_code, 'lender_code' => $lender_code, 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 3000, 'flow_fee_duration' => 3, 'duration' => 3, 'max_loan_amount' => 300000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 1000],


                ['country_code' => $country_code, 'product_name' => "{$acc_prvdr_code}_6", 'product_code' => "{$acc_prvdr_code}_6", 'acc_prvdr_code' => $acc_prvdr_code, 'lender_code' => $lender_code, 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 3000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 150000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 500],

                ['country_code' => $country_code, 'product_name' => "{$acc_prvdr_code}_7", 'product_code' => "{$acc_prvdr_code}_7", 'acc_prvdr_code' => $acc_prvdr_code, 'lender_code' => $lender_code, 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 4000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 200000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 650],

                ['country_code' => $country_code, 'product_name' => "{$acc_prvdr_code}_8", 'product_code' => "{$acc_prvdr_code}_8", 'acc_prvdr_code' => $acc_prvdr_code, 'lender_code' => $lender_code, 'cs_model_code' => 'fa_performance_model', 'product_type' => 'probation', 'loan_purpose' => 'float_advance', 'flow_fee_type' => 'Flat', 'flow_fee' => 6000, 'flow_fee_duration' => 6, 'duration' => 6, 'max_loan_amount' => 300000, 'status' => 'enabled','created_at' => $created_at , 'created_by' => 0, 'penalty_amount' => 1000],

            ]);
        
            DB::commit();
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::warning($e->getTraceAsString());
            thrw($e->getMessage());
        }
    }
}