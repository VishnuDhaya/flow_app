<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMasterDataForAccountBranches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert(['country_code' => 'RWA', 'data_key' => 'RMTN_branches', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
        
        DB::table('master_data')->insert([ 
           ['country_code'=> 'RWA', 'data_key' => 'RMTN_branches', 'data_code' => 'gasabo' , 'data_value' => 'Gasabo' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
           ['country_code'=> 'RWA', 'data_key' => 'RMTN_branches', 'data_code' => 'nyarugenge' , 'data_value' => 'Nyarugenge' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
           ['country_code'=> 'RWA', 'data_key' => 'RMTN_branches', 'data_code' => 'kicukiro' , 'data_value' => 'Kicukiro' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

           ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
