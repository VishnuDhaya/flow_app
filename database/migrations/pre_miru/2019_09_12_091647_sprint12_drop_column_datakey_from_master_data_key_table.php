<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Sprint12DropColumnDatakeyFromMasterDataKeyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       
     DB::table('master_data')->where('data_key','csf_model')->delete();
     DB::table('master_data_keys')->where('data_key','csf_model')->delete();
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_data_key', function (Blueprint $table) {
            //
        });
    }
}
