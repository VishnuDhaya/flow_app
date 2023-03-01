<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class RWA_RMTN_Account_Seeder extends Seeder
{
	/**
	 * Run the fieldbase seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('accounts')->insert(['country_code' => 'RWA',
            'lender_code' => 'RFLW',
            'lender_acc_prvdr_code' => 'RMTN',
            'acc_prvdr_name' => 'MTN RWANDA',
            'acc_prvdr_code' => 'RMTN',
            'acc_purpose' => json_encode(['disbursement']),
            'type' => 'wallet',
            'holder_name' => 'Gasabo - FLOW RWANDA',
            'acc_number' => '791519171',
            'is_primary_acc' => true,
            'to_recon' => true,
            'status' => 'enabled',
            'web_cred' => json_encode(['username' => '250791519171', 'password' => '@flowrw12'])
            ]);

        DB::table('accounts')->insert(['country_code' => 'RWA',
            'lender_code' => 'RFLW',
            'lender_acc_prvdr_code' => 'RMTN',
            'acc_prvdr_name' => 'MTN RWANDA',
            'acc_prvdr_code' => 'RMTN',
            'acc_purpose' => json_encode(['disbursement']),
            'type' => 'wallet',
            'holder_name' => 'Nyarygenge - FLOW RWANDA',
            'acc_number' => '791516469',
            'is_primary_acc' => true,
            'to_recon' => true,
            'status' => 'enabled',
            'web_cred' => json_encode(['username' => '250791516469', 'password' => '@flowrw12'])
            ]);

        DB::table('accounts')->insert(['country_code' => 'RWA',
            'lender_code' => 'RFLW',
            'lender_acc_prvdr_code' => 'RMTN',
            'acc_prvdr_name' => 'Kicukiro - MTN RWANDA',
            'acc_prvdr_code' => 'RMTN',
            'acc_purpose' => json_encode(['disbursement']),
            'type' => 'wallet',
            'holder_name' => 'FLOW RWANDA',
            'acc_number' => '791334419',
            'is_primary_acc' => true,
            'to_recon' => true,
            'status' => 'enabled',
            'web_cred' => json_encode(['username' => '250791334419', 'password' => '@flowrw12'])
            ]);
	}
}
