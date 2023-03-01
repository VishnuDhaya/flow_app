<?php

namespace Database\Seeders;

use App\Repositories\SQL\PersonRepositorySQL;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class addNewBondsJune2022 extends Seeder
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
            ['first_name'=>"Daniel", "last_name"=>"Schuett", "gender"=>"male", "email_id"=>"dajo.schuett@gmail.com", "designation"=>"investor", "dob"=>" 2022-10-12", "country_code"=>"UGA"],
            ['first_name'=>"Helmut", "last_name"=>"Higl", "gender"=>"male", "email_id"=>"helmuthigl@gmx.at", "designation"=>"investor", "dob"=>" 2022-10-12", "country_code"=>"UGA"],
            ['first_name'=>"Johannes", "last_name"=>"Steiner", "gender"=>"male", "email_id"=>"johannes@flowglobal.net", "designation"=>"investor", "dob"=>" 2022-10-12", "country_code"=>"UGA"]
        ]);
        DB::table('capital_funds')->insert([
            ['country_code' => 'UGA', 'fund_code' => 'FC-JUL22-EUR-A', 'fund_name' => 'Fixed Coupon - JUL 2022 (EUR) - A', 'lender_code' => 'UFLW', 'fund_type' => 'fixed_coupon', 'is_lender_default' => 0, 'alloc_date' => '2022-07-01', 'forex' => 3931.26, 'fe_currency_code' => 'EUR', 'alloc_amount_fc' => 10000, 'alloc_amount' => 39312600, 'duration' => '18', 'profit_rate' => '0.0800', 'status' => 'active', 'created_at' => now()],
            ['country_code' => 'UGA', 'fund_code' => 'FC-JUL2-EUR-B', 'fund_name' => 'Fixed Coupon - JUL 2022 (EUR) - B', 'lender_code' => 'UFLW', 'fund_type' => 'fixed_coupon', 'is_lender_default' => 0, 'alloc_date' => '2022-07-01', 'forex' => 3931.26, 'fe_currency_code' => 'EUR', 'alloc_amount_fc' => 30000, 'alloc_amount' => 117937800, 'duration' => '30', 'profit_rate' => '0.1550', 'status' => 'active', 'created_at' => now()],
            ['country_code' => 'UGA', 'fund_code' => 'FC-SEP22-EUR-A', 'fund_name' => 'Fixed Coupon - SEP 2022 (EUR) - A', 'lender_code' => 'UFLW', 'fund_type' => 'fixed_coupon', 'is_lender_default' => 0, 'alloc_date' => '2022-09-01', 'forex' => 3810.99, 'fe_currency_code' => 'EUR', 'alloc_amount_fc' => 10000, 'alloc_amount' => 38109900, 'duration' => '30', 'profit_rate' => '0.0900', 'status' => 'active', 'created_at' => now()],
            ['country_code' => 'UGA', 'fund_code' => 'FC-SEP22-EUR-B', 'fund_name' => 'Fixed Coupon - SEP 2022 (EUR) - B', 'lender_code' => 'UFLW', 'fund_type' => 'fixed_coupon', 'is_lender_default' => 0, 'alloc_date' => '2022-09-01', 'forex' => 3810.99, 'fe_currency_code' => 'EUR', 'alloc_amount_fc' => 25000, 'alloc_amount' => 95274750, 'duration' => '30', 'profit_rate' => '0.1550', 'status' => 'active', 'created_at' => now()],
            ['country_code' => 'UGA', 'fund_code' => 'FC-JUN22-USD', 'fund_name' => 'Fixed Coupon - JUN 2022 (USD)', 'lender_code' => 'UFLW', 'fund_type' => 'fixed_coupon', 'is_lender_default' => 0, 'alloc_date' => '2022-06-01', 'forex' => 3777.32, 'fe_currency_code' => 'USD', 'alloc_amount_fc' => 10000, 'alloc_amount' => 37773200, 'duration' => '30', 'profit_rate' => '0.0900', 'status' => 'active', 'created_at' => now()],
            ['country_code' => 'UGA', 'fund_code' => 'FC-JUL22-USD', 'fund_name' => 'Fixed Coupon - JUL 2022 (USD)', 'lender_code' => 'UFLW', 'fund_type' => 'fixed_coupon', 'is_lender_default' => 0, 'alloc_date' => '2022-07-01', 'forex' => 3752.23, 'fe_currency_code' => 'USD', 'alloc_amount_fc' => 25000, 'alloc_amount' => 93805750, 'duration' => '30', 'profit_rate' => '0.1550', 'status' => 'active', 'created_at' => now()],
        ]);

        DB::table('investment_txns')->insert([
            ['person_id' => ++$last_id, 'fund_code' => 'FC-JUL22-EUR-A', 'txn_type' => 'investment', 'amount' => 10000, 'currency_code' => 'EUR', 'txn_date' => '2022-06-02', 'realisation_date' => '2022-07-01', 'created_at' => now()],
            ['person_id' => ++$last_id, 'fund_code' => 'FC-SEP22-EUR-A', 'txn_type' => 'investment', 'amount' => 10000, 'currency_code' => 'EUR', 'txn_date' => '2022-08-02', 'realisation_date' => '2022-09-01', 'created_at' => now()],
            ['person_id' => ++$last_id, 'fund_code' => 'FC-JUN22-USD', 'txn_type' => 'investment', 'amount' => 10000, 'currency_code' => 'USD', 'txn_date' => '2022-05-27', 'realisation_date' => '2022-06-01', 'created_at' => now()],
            ['person_id' => 2531, 'fund_code' => 'FC-SEP22-EUR-B', 'txn_type' => 'investment', 'amount' => 25000, 'currency_code' => 'EUR', 'txn_date' => '2022-08-01', 'realisation_date' => '2022-09-01', 'created_at' => now()],
            ['person_id' => 2536, 'fund_code' => 'FC-JUL22-EUR-B', 'txn_type' => 'investment', 'amount' => 30000, 'currency_code' => 'EUR', 'txn_date' => '2022-06-14', 'realisation_date' => '2022-07-01', 'created_at' => now()],
            ['person_id' => 2534, 'fund_code' => 'FC-JUL22-USD', 'txn_type' => 'investment', 'amount' => 25000, 'currency_code' => 'USD', 'txn_date' => '2022-06-13', 'realisation_date' => '2022-07-01', 'created_at' => now()],
            ]);

        DB::delete("delete from investment_txns where person_id = 2536 and fund_code = 'FC-APR22-EUR-A'");
        DB::update("update capital_funds set alloc_amount_fc = 50000, alloc_amount = 50000 * forex where fund_code = 'FC-APR22-EUR-A'");
    }
}
