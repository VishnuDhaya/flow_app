<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMasterDataLeadStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data')->insert([ 
            ['country_code'=> '*', 'data_key' => 'lead_status', 'data_code' => '08_pending_dp' , 'data_value' => 'Pending Downpayment' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> '*', 'data_key' => 'lead_status', 'data_code' => '51_pending_tf_process' , 'data_value' => 'Pending TF Process' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> '*', 'data_key' => 'lead_status', 'data_code' => '07_pending_prod_sel' , 'data_value' => 'Pending Product Selection' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

   
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
