<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMasterDataBizPropsTypeDrugsShop extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data')->insert([ 
            ['country_code'=> '*', 'data_key' => 'biz_addr_prop_type', 'data_code' => 'drug_shop' , 'data_value' => 'Drug Shop' , 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'], 
            
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
