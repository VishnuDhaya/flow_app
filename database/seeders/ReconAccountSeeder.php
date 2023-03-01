<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReconAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('accounts')->where('id', 4094)->update(['to_recon' => true, 'status' => 'enabled', 'web_cred' => json_encode(['username' => 'flow4', 'password' => 'JESUS@2020'])]);
        DB::table('accounts')->where('id', 3605)->update(['to_recon' => true, 'status' => 'enabled', 'web_cred' => json_encode(['username' => 'flow3', 'password' => 'FLOW@GLOBAL123'])]);
        DB::delete("delete from accounts where id = 4181 and acc_number = 0");
        DB::update("update accounts set web_cred = JSON_SET(web_cred, '$.timeout', 200) where cust_id is null and web_cred is not null");
    }
}
