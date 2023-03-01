<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedMasterDataTxnCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       DB::table('master_data')->where('data_key','transaction_mode')->delete();
       DB::table('master_data')->insert([
    
     ['data_key'=>'transaction_mode','data_code' => 'wallet_portal','data_value' => 'Wallet Portal','data_type' => 'common','status' => 'enabled','created_at'=> now()],
     ['data_key'=>'transaction_mode','data_code' => 'wallet_transfer','data_value' => 'Wallet Transfer','data_type' => 'common','status' => 'enabled','created_at'=> now()],


     ['data_key'=>'transaction_mode','data_code' => 'internet_banking','data_value' => 'Internet Banking','data_type' => 'common','status' => 'enabled','created_at'=> now()],
     ['data_key'=>'transaction_mode','data_code' => 'bank_counter','data_value'=> 'Bank Counter','data_type' => 'common','status' => 'enabled','created_at'=> now()],
     ['data_key'=>'transaction_mode','data_code' => 'others','data_value'=> 'Others','data_type' => 'common','status' => 'enabled','created_at'=> now()],
       ['data_key'=>'transaction_mode','data_code' => 'flow_platform','data_value'=> 'Flow Platform','data_type' => 'common','status' => 'enabled','created_at'=> now()],
       ['data_key'=>'transaction_mode','data_code' => 'data_provider_transfer','data_value'=> 'Data Provider Transfer','data_type' => 'common','status' => 'enabled','created_at'=> now()]
     ]);
        }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_data', function (Blueprint $table) {
            //
        });
    }
}
