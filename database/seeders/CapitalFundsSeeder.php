<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use DB;

class CapitalFundsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('capital_funds')->where('fund_code', 'VC-MAY21-USD')->update(['forex' => 3545, 'license_rate'=> 0.15, 'profit_rate' => 0.4,'floor_rate' => 0.025, 'duration' => 36]);
        DB::table('capital_funds')->where('fund_code', 'VC-MAY21-EUR')->update(['forex' => 4262, 'license_rate'=> 0.15, 'profit_rate' => 0.4,'floor_rate' => 0.025, 'duration' => 36]);
        DB::table('capital_funds')->where('fund_code', 'VC-JUN21-EUR')->update(['forex' => 4244, 'license_rate'=> 0.15, 'profit_rate' => 0.4,'floor_rate' => 0.025, 'duration' => 30]);
        DB::table('capital_funds')->where('fund_code', 'FC-JUN21-USD')->update(['forex' => 3525,'profit_rate' => 0.085, 'duration' => 18]);
        DB::table('capital_funds')->where('fund_code', 'FC-JUN21-EUR-A')->update(['forex' => 4310,'profit_rate' => 0.095, 'duration' => 30]);
        DB::table('capital_funds')->where('fund_code', 'FC-JUN21-EUR-B')->update(['forex' => 4310,'profit_rate' => 0.085, 'duration' => 18]);
    }
}
