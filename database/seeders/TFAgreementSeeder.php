<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use DB;


class TFAgreementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()


    {
        $country_code = 'UGA';
        $lender_code = "UFLW";

        DB::table('master_agreements')->update(['product_group' => "float_advance"]);
        DB::table('master_agreements')->insert(['product_group' => "terminal_financing",'aggr_type'=> 'probation','duration_type'=> 'days','aggr_duration'=> null,
        'aggr_doc_id'=> 'AGRT-UFLW-2105171422', 'status'=> 'enabled', 'name'=> 'Probation Agreement', 'country_code' => $country_code,'lender_code' =>  $lender_code]);
        DB::table('master_agreements')->insert(['product_group' => "terminal_financing",'aggr_type'=> 'terminal_financing','duration_type'=> 'days','aggr_duration'=> null,
        'aggr_doc_id'=> 'AGRT-UFLW-2105171422', 'status'=> 'enabled', 'name'=> 'Terminal Financing Agreement', 'country_code' => $country_code ,'lender_code' =>  $lender_code]);

    }
}
