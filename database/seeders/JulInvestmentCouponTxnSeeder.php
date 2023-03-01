<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JulInvestmentCouponTxnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::insert("insert into investment_txns (person_id, fund_code,txn_type,amount,currency_code,txn_date,realisation_date,created_at) values (2529, 'VC-MAY21-USD','payout',2059,'USD','44749','2022-06-30',now())");
        DB::insert("insert into investment_txns (person_id, fund_code,txn_type,amount,currency_code,txn_date,realisation_date,created_at) values (2530, 'VC-MAY21-EUR','payout',2057,'EUR','44750','2022-06-30',now())");
        DB::insert("insert into investment_txns (person_id, fund_code,txn_type,amount,currency_code,txn_date,realisation_date,created_at) values (2531, 'FC-JUN21-EUR-A','payout',640.33,'EUR','44743','2022-06-30',now())");
        DB::insert("insert into investment_txns (person_id, fund_code,txn_type,amount,currency_code,txn_date,realisation_date,created_at) values (2532, 'FC-JUN21-EUR-A','payout',640.33,'EUR','44743','2022-06-30',now())");
        DB::insert("insert into investment_txns (person_id, fund_code,txn_type,amount,currency_code,txn_date,realisation_date,created_at) values (2533, 'FC-JUN21-EUR-A','payout',561.17,'EUR','44743','2022-06-30',now())");
        DB::insert("insert into investment_txns (person_id, fund_code,txn_type,amount,currency_code,txn_date,realisation_date,created_at) values (2534, 'FC-JUN21-USD','payout',689.33,'USD','44743','2022-06-30',now())");
        DB::insert("insert into investment_txns (person_id, fund_code,txn_type,amount,currency_code,txn_date,realisation_date,created_at) values (2535, 'FC-JUN21-EUR-B','payout',610.17,'EUR','44743','2022-06-30',now())");
        DB::insert("insert into investment_txns (person_id, fund_code,txn_type,amount,currency_code,txn_date,realisation_date,created_at) values (2536, 'FC-JUN21-EUR-B','payout',501.83,'EUR','44743','2022-06-30',now())");
        DB::insert("insert into investment_txns (person_id, fund_code,txn_type,amount,currency_code,txn_date,realisation_date,created_at) values (2537, 'VC-JUN21-EUR','payout',2057,'EUR','44804','2022-06-30',now())");
        DB::insert("insert into investment_txns (person_id, fund_code,txn_type,amount,currency_code,txn_date,realisation_date,created_at) values (3454, 'FC-APR22-EUR-A','payout',637.5,'EUR','44743','2022-06-30',now())");
        DB::insert("insert into investment_txns (person_id, fund_code,txn_type,amount,currency_code,txn_date,realisation_date,created_at) values (3455, 'FC-APR22-EUR-B','payout',225,'EUR','44743','2022-06-30',now())");
        DB::insert("insert into investment_txns (person_id, fund_code,txn_type,amount,currency_code,txn_date,realisation_date,created_at) values (2533, 'FC-APR22-EUR-A','payout',425,'EUR','44743','2022-06-30',now())");
        DB::insert("insert into investment_txns (person_id, fund_code,txn_type,amount,currency_code,txn_date,realisation_date,created_at) values (3747, 'FC-MAY22-EUR-A','payout',150,'EUR','44743','2022-06-30',now())");
        DB::insert("insert into investment_txns (person_id, fund_code,txn_type,amount,currency_code,txn_date,realisation_date,created_at) values (3748, 'FC-MAY22-EUR-B','payout',133.33,'EUR','44743','2022-06-30',now())");
    }
}
