<?php

use Illuminate\Database\Seeder;

class UGA_FlowIdsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $country_code= 'UGA';

        DB::statement("insert into flow_ids (country_code, id_type, id_value)  (select '$country_code' as country_code,'loan_appl' as id_type, id as id_value from master_seq_nums where id >= 10000 and id <= 99999 order by rand())");

        DB::statement("insert into flow_ids (country_code, id_type, id_value)  (select '$country_code' as country_code,'customer' as id_type, id as id_value from master_seq_nums where id >= 100000 and id <= 999999 order by rand())");
    }
}
