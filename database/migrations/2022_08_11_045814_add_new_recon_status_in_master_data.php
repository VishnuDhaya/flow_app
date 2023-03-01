<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewReconStatusInMasterData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            DB::table('master_data')->insert([ 
                
                ['country_code'=> '*', 'data_key' => 'recon_status', 'data_code' => '31_paid_to_different_acc' , 'data_value' => 'Paid To Different Account' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
            
                ['country_code'=> '*', 'data_key' => 'recon_status', 'data_code' => '90_payment_reversed' , 'data_value' => 'Payment Reversed' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
            
                ['country_code'=> '*', 'data_key' => 'recon_status', 'data_code' => '71_pending_manual_recon' , 'data_value' => 'Pending Manual Recon' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] , 
                     
    
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
