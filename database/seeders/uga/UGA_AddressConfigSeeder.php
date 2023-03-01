<?php

use Illuminate\Database\Seeder;

class UGA_AddressConfigSeeder extends Seeder
{
    /**
     * Run the fieldbase seeds.
     *
     * @return void
     */
    public function run()
    {
    	$country_code= 'UGA';
    	//DB::table('addr_config')->truncate();
         DB::table('addr_config')->insert([
		['country_code' => $country_code,'field_num' => 'field_1','field_code' => 'region', 'field_name' => 'Region','status' => 'enabled', 'child_field_code' => 'district', 'field_type' => 'select', 'validation_rules' => 'required'],

		['country_code' => $country_code,'field_num' => 'field_2','field_code' => 'district', 'field_name' => 'District','status' => 'enabled', 'child_field_code' => 'county', 'field_type' => 'select', 'validation_rules' => 'required'],
		['country_code' => $country_code,'field_num' => 'field_3','field_code' => 'county', 'field_name' => 'County','status' => 'enabled', 'child_field_code' => 'sub_county', 'field_type' => 'select', 'validation_rules' => 'required'],
		['country_code' => $country_code,'field_num' => 'field_4','field_code' => 'sub_county', 'field_name' => 'Sub County','status' => 'enabled', 'child_field_code' => null, 'field_type' => 'text', 'validation_rules' => 'required'],
		['country_code' => $country_code,'field_num' => 'field_5','field_code' => 'parish', 'field_name' => 'Parish','status' => 'enabled', 'child_field_code' => null, 'field_type' => 'text', 'validation_rules' => 'required'],
		['country_code' => $country_code,'field_num' => 'field_6','field_code' => 'village', 'field_name' => 'Village','status' => 'enabled', 'child_field_code' => null, 'field_type' => 'text', 'validation_rules' => 'required'],
		['country_code' => $country_code,'field_num' => 'field_7','field_code' => 'state', 'field_name' => 'State','status' => 'enabled', 'child_field_code' => null, 'field_type' => 'text', 'validation_rules' => 'required'],
		['country_code' => $country_code,'field_num' => 'field_8','field_code' => 'plot_number', 'field_name' => 'Plot Number','status' => 'enabled', 'child_field_code' => null, 'field_type' => 'text', 'validation_rules' => 'required'],
		['country_code' => $country_code,'field_num' => 'field_9','field_code' => 'landmark', 'field_name' => 'Landmark','status' => 'enabled', 'child_field_code' => null, 'field_type' => 'text', 'validation_rules' => 'required'],
		['country_code' => $country_code,'field_num' => 'field_10','field_code' => 'gps', 'field_name' => 'GPS','status' => 'enabled', 'child_field_code' => null, 'field_type' => 'text', 'validation_rules' => 'required'],


	
    ]);

    }
}
