<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RMTN_Update_AccBranches extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::update("update accounts set branch = 'gasabo' where acc_number = '791519171'");
        DB::update("update accounts set branch = 'nyarugenge' where acc_number = '791516469'");
        DB::update("update accounts set branch = 'kicukiro' where acc_number = '791334419'");
    }
}
