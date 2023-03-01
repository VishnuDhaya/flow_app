<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TFRepaymentAccSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('accounts')->insert(['country_code' => 'UGA',
                                            'lender_code' => 'UFLW',
                                            'lender_acc_prvdr_code' => 'UEZM',
                                            'acc_prvdr_name' => 'EzeeMoney',
                                            'acc_prvdr_code' => 'UEZM',
                                            'acc_purpose' => json_encode(['tf_repayment']),
                                            'type' => 'wallet',
                                            'holder_name' => 'FLOW UGANDA',
                                            'acc_number' => '92249908',
                                            'is_primary_acc' => true,
                                            'to_recon' => false,
                                            'status' => 'enabled'
                                            ]);
    }
}
