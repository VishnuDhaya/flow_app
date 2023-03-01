<?php

use Illuminate\Database\Seeder;

class IND_AddressConfigSeeder extends Seeder
{
    /**
     * Run the fieldbase seeds.
     *
     * @return void
     */
    public function run()
    {

         DB::table('addr_config')->insert([['country_code' => 'IND','field_num' => 'field_1','field_code' => 'state', 'field_name' => 'State','status' => 'enabled', 'child_field_code' => 'district', 'field_type' => 'text', 'validation_rules' => 'required'],		['country_code' => 'IND','field_num' => 'field_2','field_code' => 'district', 'field_name' => 'District','status' => 'enabled', 'child_field_code' => 'town/village', 'field_type' => 'text', 'validation_rules' => 'required'],		['country_code' => 'IND','field_num' => 'field_3','field_code' => 'town/village', 'field_name' => 'Town/Village','status' => 'enabled', 'child_field_code' => null, 'field_type' => 'text', 'validation_rules' => 'required'],		['country_code' => 'IND','field_num' => 'field_4','field_code' => 'street_name', 'field_name' => 'Street Name','status' => 'enabled', 'child_field_code' => null, 'field_type' => 'text', 'validation_rules' => 'required'],		['country_code' => 'IND','field_num' => 'field_5','field_code' => 'plot_number', 'field_name' => 'Plot Number','status' => 'enabled', 'child_field_code' => null, 'field_type' => 'text', 'validation_rules' => 'required'],		['country_code' => 'IND','field_num' => 'field_6','field_code' => 'landmark', 'field_name' => 'Landmark','status' => 'enabled', 'child_field_code' => null, 'field_type' => 'text', 'validation_rules' => 'required'],		['country_code' => 'IND','field_num' => 'field_7','field_code' => 'pin_code', 'field_name' => 'Pincode','status' => 'enabled', 'child_field_code' => null, 'field_type' => 'text', 'validation_rules' => 'required'],		['country_code' => 'IND','field_num' => 'field_8','field_code' => 'gps', 'field_name' => 'GPS','status' => 'enabled', 'child_field_code' => null, 'field_type' => 'text', 'validation_rules' => 'required']]);

    }
}
