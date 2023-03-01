<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMasterDataForPreApprIgnrReason extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        {
            DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'pre_appr_ignr_reasons', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
    
    
            DB::table('master_data')->insert([ 
                
                ['country_code'=> '*', 'data_key' => 'pre_appr_ignr_reasons', 'data_code' => 'low_score' , 'data_value' => 'Low Credit Score' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
    
                ['country_code'=> '*', 'data_key' => 'pre_appr_ignr_reasons', 'data_code' => 'repaid_late_recently' , 'data_value' => 'Recently FAs Paid Late' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
    
                ['country_code'=> '*', 'data_key' => 'pre_appr_ignr_reasons', 'data_code' => 'higher_fa' , 'data_value' => 'Higher FA Amount' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'],
    
            ]);
        }
    
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
