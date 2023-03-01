<?php

namespace Database\Seeders;

use App\Repositories\SQL\PersonRepositorySQL;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvestorDataMaySeeder extends Seeder
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
            ['first_name'=>"Rike", "last_name"=>"Draeger", "gender"=>"Male", "email_id"=>"rike.draeger@posteo.de", "designation"=>"investor", "dob"=>" 2022-04-30", "country_code"=>"UGA"],
            ['first_name'=>"Roger", "last_name"=>"Bymolt", "gender"=>"Male", "email_id"=>"rogerbymolt@gmail.com", "designation"=>"investor", "dob"=>" 2022-04-30", "country_code"=>"UGA"]
        ]);
        DB::table('capital_funds')->insert([
            ['country_code' => 'UGA', 'fund_code' => 'FC-MAY22-EUR-A', 'fund_name' => 'Fixed Coupon - May 2022 (EUR) - A', 'lender_code' => 'UFLW', 'fund_type' => 'fixed_coupon', 'is_lender_default' => 0, 'alloc_date' => '2022-05-01', 'forex' => 3748.52, 'fe_currency_code' => 'EUR', 'alloc_amount_fc' => 10000, 'duration' => '30', 'profit_rate' => '0.0800', 'status' => 'active'],
            ['country_code' => 'UGA', 'fund_code' => 'FC-MAY22-EUR-B', 'fund_name' => 'Fixed Coupon - May 2022 (EUR) - B', 'lender_code' => 'UFLW', 'fund_type' => 'fixed_coupon', 'is_lender_default' => 0, 'alloc_date' => '2022-05-01', 'forex' => 3748.52, 'fe_currency_code' => 'EUR', 'alloc_amount_fc' => 10000, 'duration' => '18', 'profit_rate' => '0.0800', 'status' => 'active']
        ]);
        DB::table('investment_txns')->insert([
            ['person_id' => ++$last_id, 'fund_code' => 'FC-MAY22-EUR-A', 'txn_type' => 'investment', 'amount' => 10000, 'currency_code' => 'EUR', 'txn_date' => '2022-04-20', 'realisation_date' => '2022-05-01', 'created_at' => now()],
            ['person_id' => ++$last_id, 'fund_code' => 'FC-MAY22-EUR-B', 'txn_type' => 'investment', 'amount' => 10000, 'currency_code' => 'EUR', 'txn_date' => '2022-04-25', 'realisation_date' => '2022-05-01', 'created_at' => now()],
            ]);
    }
}
