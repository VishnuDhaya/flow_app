<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\RMTN_audit_name_acc;

class AddUssdHolderNameCodeInAccProviders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try{

            DB::beginTransaction();

            $seeder_rmtn = new RMTN_audit_name_acc;
            $seeder_rmtn->run();
            DB::update("update acc_providers set mobile_cred_format = JSON_MERGE_PATCH(`mobile_cred_format`, '{\"ussd_holder_name_code\" : \"*182*1*1*0:recipient#\"}') where id = 8");
        
            DB::commit();
        }
        catch(\Exception $e){
            DB::rollBack();
        }
            
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acc_providers', function (Blueprint $table) {
            //
        });
    }
}
