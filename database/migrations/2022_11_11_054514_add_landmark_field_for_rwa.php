<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLandmarkFieldForRwa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('addr_config')->insert([
			
			['country_code' => 'RWA', 'field_num' => 'field_9', 'field_code' => 'landmark', 'field_name' => 'Landmark', 'status' => 'enabled', 'child_field_code' => null, 'field_type' => 'text', 'validation_rules' => 'required'],

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
