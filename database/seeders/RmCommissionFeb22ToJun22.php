<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RmCommissionFeb22ToJun22 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        db::insert("insert into commissions (country_code,month,total_paid) values ('UGA',202202,3835000)");
        db::insert("insert into commissions (country_code,month,total_paid) values ('UGA',202203,4073500)");
        db::insert("insert into commissions (country_code,month,total_paid) values ('UGA',202204,6431409)");
        db::insert("insert into commissions (country_code,month,total_paid) values ('UGA',202205,10318911)");
        db::insert("insert into commissions (country_code,month,total_paid) values ('UGA',202206,8405211)");
    }
}
