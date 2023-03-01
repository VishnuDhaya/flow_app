<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ScheduleFromSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $updated_at = datetime_db();
        DB::table('field_visits')->update(['sch_from' => 'rm', 'updated_at' => $updated_at]);
    }
}
