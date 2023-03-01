<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterDataForRelationWithOwner extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'relation_with_owner', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);

       DB::table('master_data')->insert([ 
            ['country_code'=> '*', 'data_key' => 'relation_with_owner', 'data_code' => 'employee' , 'data_value' => 'Employee' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> '*', 'data_key' => 'relation_with_owner', 'data_code' => 'son/daughter' , 'data_value' => 'Son / Daughter' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> '*', 'data_key' => 'relation_with_owner', 'data_code' => 'parent' , 'data_value' => 'Parent' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> '*', 'data_key' => 'relation_with_owner', 'data_code' => 'spouse' , 'data_value' => 'Spouse' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> '*', 'data_key' => 'relation_with_owner', 'data_code' => 'sibling' , 'data_value' => 'Sibling' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> '*', 'data_key' => 'relation_with_owner', 'data_code' => 'other_relative' , 'data_value' => 'Other Relative' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> '*', 'data_key' => 'relation_with_owner', 'data_code' => 'others' , 'data_value' => 'Others' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_data_for_relation_with_owner');
    }
}
