<?php

use Illuminate\Database\Seeder;

class Sprint_10_master_data extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_data_keys')->insert([
        ['country_code' => '*','data_key' => 'comment_type', 'parent_data_key' => NULL, 'status' => 'enabled'], 
        [ 'country_code' => '*', 'data_key' => 'comment_type', 'parent_data_key' => null, 'status' => 'enabled']
        ]);

        DB::table('master_data')->insert([
        ['country_code' => '*', 'data_key' => 'comment_type', 'data_value' => 'New Task', 'data_code' => 'new_task',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'],

        ['country_code' => '*', 'data_key' => 'comment_type', 'data_value' => 'General Comment', 'data_code' => 'gen_comment',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common'],
        ]);

    }
}
