<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use DB;

class AgreementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_agreements')->truncate();
        DB::table('master_agreements')->insert([['country_code'=> 'UGA',  'name' => 'Float Advance Agreement', 'aggr_doc_id' => 'AGRT-CCA-UFLW-2105171421', 'product_group' => 'float_advance', 'lender_code' => 'UFLW', 'duration_type' => 'days', 'aggr_duration' => '180', 'aggr_type' => 'onboarded', 'status' => 'enabled','created_at' => now()]]);

        DB::table('master_agreements')->insert([['country_code'=> 'UGA',  'name' => 'Float Advance Agreement', 'aggr_doc_id' => 'AGRT-CCA-UFLW-2108231124', 'product_group' => 'float_advance', 'lender_code' => 'UFLW', 'duration_type' => 'fas', 'aggr_duration' => '8', 'aggr_type' => 'condonation', 'status' => 'enabled','created_at' => now()]]);

        DB::table('master_agreements')->insert([['country_code'=> 'UGA',  'name' => 'Float Advance Agreement', 'aggr_doc_id' => 'AGRT-CCA-UFLW-2105171420', 'product_group' => 'float_advance', 'lender_code' => 'UFLW', 'duration_type' => 'fas', 'aggr_duration' => '15', 'aggr_type' => 'probation', 'status' => 'enabled','created_at' => now()]]);

        DB::table('master_agreements')->insert([['country_code'=> 'UGA',  'name' => 'Terminal Financing Agreement', 'aggr_doc_id' => 'AGRT-UEZM-UFLW-2105171422', 'product_group' => 'terminal_financing', 'lender_code' => 'UFLW', 'aggr_type' => 'probation', 'status' => 'enabled','created_at' => now()]]);

        DB::table('master_agreements')->insert([['country_code'=> 'UGA',  'name' => 'Terminal Financing Agreement', 'aggr_doc_id' => 'AGRT-UEZM-UFLW-2105171421', 'product_group' => 'terminal_financing', 'lender_code' => 'UFLW', 'aggr_type' => 'terminal_finance', 'status' => 'enabled','created_at' => now()]]);
    }
}
