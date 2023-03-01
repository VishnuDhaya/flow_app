<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssessmentAccPurpose extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data')->insert([
            ['country_code'=> '*',  'data_type' => 'common', 'data_key' => 'account_purpose', 'data_code' => 'assessment', 'data_value' => 'Assessment', 'status' => 'enabled', 'parent_data_code' => 'customer', 'created_at' => now()],
            ['country_code'=> '*',  'data_type' => 'common', 'data_key' => 'account_purpose', 'data_code' => 'terminal_financing', 'data_value' => 'Terminal Financing', 'status' => 'enabled', 'parent_data_code' => 'customer', 'created_at' => now()],
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
