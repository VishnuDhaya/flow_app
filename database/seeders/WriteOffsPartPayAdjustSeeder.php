<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WriteOffsPartPayAdjustSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::update("update loan_txns set txn_date = '2021-01-01' where txn_type = 'payment' and txn_date <= '2020-12-31' and loan_doc_id in (select loan_doc_id from loan_write_off where year = '2020') ");
        DB::update("update loan_txns set txn_date = '2022-01-01' where txn_type = 'payment' and txn_date <= '2021-12-31' and loan_doc_id in (select loan_doc_id from loan_write_off where year = '2021') ");
    }
}
