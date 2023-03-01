<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Repositories\SQL\PersonRepositorySQL;


class InvestorDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        session()->put('country_code', 'UGA');
        $person_repo = new PersonRepositorySQL();
        $last_id = $person_repo->get_last_id();

        DB::table('persons')->insert([
            ['first_name'=>"Sabine", "last_name"=>"Panchaud", "gender"=>"Male", "email_id"=>"les.abbesses@1026.ch", "designation"=>"investor", "dob"=>" 2022-03-30", "country_code"=>"UGA"],
            ['first_name'=>"David", "last_name"=>"Loddo", "gender"=>"Male", "email_id"=>"david.loddo@tu-dortmund.de", "designation"=>"investor", "dob"=>" 2022-03-30", "country_code"=>"UGA"]
        ]);
        DB::table('capital_funds')->insert([
            ['country_code' => 'UGA', 'fund_code' => 'FC-APR22-EUR-A', 'fund_name' => 'Fixed Coupon - April 2022 (EUR) - A', 'lender_code' => 'UFLW', 'fund_type' => 'fixed_coupon', 'is_lender_default' => 0, 'alloc_date' => '2022-04-01', 'forex' => 3953.77, 'fe_currency_code' => 'EUR', 'alloc_amount_fc' => 60000, 'duration' => '30', 'profit_rate' => '0.0850', 'status' => 'active'],
            ['country_code' => 'UGA', 'fund_code' => 'FC-APR22-EUR-B', 'fund_name' => 'Fixed Coupon - April 2022 (EUR) - B', 'lender_code' => 'UFLW', 'fund_type' => 'fixed_coupon', 'is_lender_default' => 0, 'alloc_date' => '2022-04-01', 'forex' => 3953.77, 'fe_currency_code' => 'EUR', 'alloc_amount_fc' => 10000, 'duration' => '30', 'profit_rate' => '0.0800', 'status' => 'active'],
        ]);
        DB::table('investment_txns')->insert([
            ['person_id' => ++$last_id, 'fund_code' => 'FC-APR22-EUR-A', 'txn_type' => 'investment', 'amount' => 30000, 'currency_code' => 'EUR', 'txn_date' => '2022-03-14', 'realisation_date' => '2022-04-01', 'created_at' => now()],
            ['person_id' => ++$last_id, 'fund_code' => 'FC-APR22-EUR-B', 'txn_type' => 'investment', 'amount' => 10000, 'currency_code' => 'EUR', 'txn_date' => '2022-03-24', 'realisation_date' => '2022-04-01', 'created_at' => now()],
            ['person_id' => 2533, 'fund_code' => 'FC-APR22-EUR-A', 'txn_type' => 'investment', 'amount' => 20000, 'currency_code' => 'EUR', 'txn_date' => '2022-03-10', 'realisation_date' => '2022-04-01', 'created_at' => now()],
            ['person_id' => 2536, 'fund_code' => 'FC-APR22-EUR-A', 'txn_type' => 'investment', 'amount' => 10000, 'currency_code' => 'EUR', 'txn_date' => '2022-03-31', 'realisation_date' => '2022-04-01', 'created_at' => now()],

        ]);

        DB::table('investment_txns')->update(['txn_type' => 'investment']);

        DB::table('investment_txns')->insert([
            ['person_id' => 2529, 'fund_code' => 'VC-MAY21-USD', 'txn_type' => 'payout', 'amount' => 1585, 'currency_code' => 'USD', 'realisation_date' => '2021-11-30', 'created_at' => now()],
            ['person_id' => 2530, 'fund_code' => 'VC-MAY21-EUR', 'txn_type' => 'payout', 'amount' => 1587, 'currency_code' => 'EUR', 'realisation_date' => '2021-11-30', 'created_at' => now()],
            ['person_id' => 2537, 'fund_code' => 'VC-JUN21-EUR', 'txn_type' => 'payout', 'amount' => 1419, 'currency_code' => 'EUR', 'realisation_date' => '2021-11-30', 'created_at' => now()],
            ['person_id' => 2531, 'fund_code' => 'FC-JUN21-EUR-A', 'txn_type' => 'payout', 'amount' => 468, 'currency_code' => 'EUR', 'realisation_date' => '2021-11-30', 'created_at' => now()],
            ['person_id' => 2532, 'fund_code' => 'FC-JUN21-EUR-A', 'txn_type' => 'payout', 'amount' => 468, 'currency_code' => 'EUR', 'realisation_date' => '2021-11-30', 'created_at' => now()],
            ['person_id' => 2533, 'fund_code' => 'FC-JUN21-EUR-A', 'txn_type' => 'payout', 'amount' => 468, 'currency_code' => 'EUR', 'realisation_date' => '2021-11-30', 'created_at' => now()],
            ['person_id' => 2534, 'fund_code' => 'FC-JUN21-USD', 'txn_type' => 'payout', 'amount' => 419, 'currency_code' => 'USD', 'realisation_date' => '2021-11-30', 'created_at' => now()],
            ['person_id' => 2535, 'fund_code' => 'FC-JUN21-EUR-B', 'txn_type' => 'payout', 'amount' => 419, 'currency_code' => 'EUR', 'realisation_date' => '2021-11-30', 'created_at' => now()],
            ['person_id' => 2536, 'fund_code' => 'FC-JUN21-EUR-B', 'txn_type' => 'payout', 'amount' => 419, 'currency_code' => 'EUR', 'realisation_date' => '2021-11-30', 'created_at' => now()],

        ]);

        DB::table('investment_txns')->where(['fund_code' => 'FC-JUN21-USD', 'txn_type' => 'investment'])->update(['realisation_date'=>'2021-06-03 00:00:00']);
        DB::table('investment_txns')->where(['fund_code' => 'VC-MAY21-EUR', 'txn_type' => 'investment'])->update(['realisation_date'=>'2021-05-06 00:00:00']);
        DB::table('investment_txns')->where(['fund_code' => 'VC-MAY21-USD', 'txn_type' => 'investment'])->update(['realisation_date'=>'2021-05-06 00:00:00']);
        DB::table('investment_txns')->where(['fund_code' => 'VC-JUN21-EUR', 'txn_type' => 'investment'])->update(['realisation_date'=>'2021-06-15 00:00:00']);
        DB::table('investment_txns')->where(['fund_code' => 'FC-JUN21-EUR-A', 'txn_type' => 'investment'])->update(['realisation_date'=>'2021-06-03 00:00:00']);
        DB::table('investment_txns')->where(['fund_code' => 'FC-JUN21-EUR-B', 'txn_type' => 'investment'])->update(['realisation_date'=>'2021-06-03 00:00:00']);

        DB::table('capital_funds')->where('fund_code','FC-JUN21-USD')->update(['fund_name'=>'Fixed Coupon - June 2021 (USD)']);
        DB::table('capital_funds')->where('fund_code','VC-MAY21-EUR')->update(['fund_name'=>'Variable Coupon - May 2021 (EUR)']);
        DB::table('capital_funds')->where('fund_code','VC-MAY21-USD')->update(['fund_name'=>'Variable Coupon - May 2021 (USD)']);
        DB::table('capital_funds')->where('fund_code','VC-JUN21-EUR')->update(['fund_name'=>'Variable Coupon - June 2021 (EUR)']);
        DB::table('capital_funds')->where('fund_code','FC-JUN21-EUR-A')->update(['fund_name'=>'Fixed Coupon - June 2021 (EUR) - A']);
        DB::table('capital_funds')->where('fund_code','FC-JUN21-EUR-B')->update(['fund_name'=>'Fixed Coupon - June 2021 (EUR) - B']);
    }
}
