<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB;

class RMTN_audit_name_acc extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $country_code = 'RWA';
        set_app_session($country_code);
        $created_at = datetime_db();

        $lender_code = 'RFLW';
        $acc_prvdr_code = 'RMTN';
        $acc_prvdr_name = 'MTN RWANDA';

        DB::table('accounts')->insert([
            [   'country_code' => $country_code, 'lender_code' => $lender_code, 
                'network_prvdr_code' => $acc_prvdr_code, 'acc_prvdr_name' => $acc_prvdr_name, 
                'acc_prvdr_code' => $acc_prvdr_code, 'acc_purpose' => json_encode(["ussd"]), 
                'is_primary_acc' => true, 'holder_name' => 'Flow Rwanda Ltd', 
                'acc_number' => '792361705',
                'created_at' => $created_at , 'created_by' => 0, 'to_recon' => 0,
            ],
        ]);
    }
}
