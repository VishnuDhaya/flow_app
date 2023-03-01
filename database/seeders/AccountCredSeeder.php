<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountCredSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('accounts')->where('id', 1783)->update(['web_cred' => json_encode(['username' => '703463210', 'password' => 'Kyt8@AQVXqsAh69'])]);
        DB::table('accounts')->where('id', 2895)->update(['web_cred' => json_encode(['username' => '20718971', 'password_stmt' => '1CP@ghrul!', 'password_disb'=> 'ZOt1bpT*FLW', 'staff_id' => 'Admin', 'access_no' => '112233'])]);
        DB::table('accounts')->where('id', 1688)->update(['acc_prvdr_name' => 'MTN UGANDA', 'acc_prvdr_code' => 'UMTN']);
        DB::table('accounts')->where('id', 3074)->update(['lender_data_prvdr_code' => 'CCA', 'to_recon' => true, 'acc_purpose' => 'repayment', 'web_cred' => json_encode(['username' => 'flow1', 'password' => 'JESUS@2020'])]);
        DB::table('accounts')->insert(['country_code' => 'UGA',
                                            'lender_code' => 'UFLW',
                                            'lender_data_prvdr_code' => 'UFLO',
                                            'data_prvdr_code' => 'UFLO',
                                            'acc_prvdr_name' => 'MTN UGANDA',
                                            'acc_prvdr_code' => 'UMTN',
                                            'acc_purpose' => 'disbursement',
                                            'type' => 'wallet',
                                            'holder_name' => 'FLOW UGANDA',
                                            'acc_number' => '810986',
                                            'is_primary_acc' => true,
                                            'to_recon' => true,
                                            'status' => 'enabled',
                                            'web_cred' => json_encode(['username' => 'flow2', 'password' => 'Flow@glob@l'])
                                            ]);

    }
}
