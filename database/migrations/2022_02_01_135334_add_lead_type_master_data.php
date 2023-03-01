<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeadTypeMasterData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'lead_type', 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);

        DB::table('master_data')->insert([
            ['country_code'=> '*',  'data_type' => 'common', 'data_key' => 'lead_type', 'data_code' => 'kyc', 'data_value' => 'New KYC', 'status' => 'enabled','created_at' => now()],
            ['country_code'=> '*',  'data_type' => 'common', 'data_key' => 'lead_type', 'data_code' => 're_kyc', 'data_value' => 'ReKYC', 'status' => 'enabled','created_at' => now()]
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
