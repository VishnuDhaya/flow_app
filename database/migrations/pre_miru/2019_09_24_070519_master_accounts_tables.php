<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MasterAccountsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         DB::table('accounts')->insert([
         ['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'acc_prvdr_name' => 'EzeeMoney', 'acc_prvdr_code' => 'UEZM', 'acc_purpose' => 'commission', 'type' => 'wallet', 'holder_name' => 'EZEEMONEY', 'acc_number' => 999999, 'status' => 'enabled', 'created_by' => 0, 'created_at' => now()]

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
