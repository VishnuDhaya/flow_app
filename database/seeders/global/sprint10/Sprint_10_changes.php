<?php

use Illuminate\Database\Seeder;

class Sprint_10_changes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('addr_config')->where('field_code','gps')->update(['field_type'=> NULL]);
        $country = ['India','Rwanda','Uganda','United Kingdom'];

          DB::table('countries')->whereIn('country',$country)
          ->update([       
             'status' => 'enabled'
          ]);


    }
}
