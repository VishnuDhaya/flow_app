<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class OctoberForexSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('forex_rates')->insert([
            ['base' => 'USD', 'quote' => 'EUR', 'forex_rate' => 0.8624042623, 'forex_date' =>  '2021-10-31 17:56:18'],
            ['base' => 'USD', 'quote' => 'UGX', 'forex_rate' => 3539.1507060072, 'forex_date' =>  '2021-10-31 17:56:18'],
            ['base' => 'EUR', 'quote' => 'USD', 'forex_rate' => 1.1595490000, 'forex_date' =>  '2021-10-31 17:56:18'],
            ['base' => 'EUR', 'quote' => 'UGX', 'forex_rate' => 4103.8186620000, 'forex_date' =>  '2021-10-31 17:56:18'],
            ['base' => 'UGX', 'quote' => 'USD', 'forex_rate' => 0.0002825537, 'forex_date' =>  '2021-10-31 17:56:18'],
            ['base' => 'UGX', 'quote' => 'EUR', 'forex_rate' => 0.0002436755, 'forex_date' =>  '2021-10-31 17:56:18'],
        ]);
    }
}
