<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddNewBondNov2022 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            DB::beginTransaction();
            DB::table('investment_txns')->insert([
                ['person_id' => 6213, 'fund_code' => 'FC-NOV22-EUR', 'txn_type' => 'investment', 'amount' => 25000, 'currency_code' => 'EUR', 'txn_date' => '2022-10-11', 'realisation_date' => '2022-11-01', 'created_at' => now()],
                ['person_id' => 6213, 'fund_code' => 'FC-SEPT22-EUR-A', 'txn_type' => 'redemption', 'amount' => 10000, 'currency_code' => 'EUR', 'txn_date' => '2022-10-31', 'realisation_date' => '2022-10-31', 'created_at' => now()],
            ]);

            DB::table('capital_funds')->where('fund_code','FC-SEP22-EUR-A')->update(['status' => 'inactive', 'closing_date' => '2022-10-31', 'updated_at' => now()]);

            DB::table('capital_funds')->insert([
                ['country_code' => 'UGA', 'fund_code' => 'FC-NOV22-EUR', 'fund_name' => 'Fixed Coupon - NOV 2022 (EUR)', 'lender_code' => 'UFLW', 'fund_type' => 'fixed_coupon', 'is_lender_default' => 0, 'alloc_date' => '2022-11-01', 'forex' => 3758.86, 'fe_currency_code' => 'EUR', 'alloc_amount_fc' => 25000, 'alloc_amount' => 93971500, 'duration' => '30', 'profit_rate' => '0.1550', 'status' => 'active', 'created_at' => now()],
            ]);

            DB::commit();
        }
        catch (\Exception $e){
            DB::rollBack();
        }

    }
}
