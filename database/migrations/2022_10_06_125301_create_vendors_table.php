<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {

            $table->increments('id');
            $table->string('country_code', 4);
            $table->string('vendor_type', 10)->nullable();
            $table->string('vendor_code', 5)->nullable();
            $table->string('vendor_name', 100)->nullable();
            $table->string('status', 20)->nullable();
            $table->json('credentials')->nullable();
            $table->unsignedInteger('balance')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->dateTime('updated_at')->nullable();

        });

        try{
            DB::beginTransaction();

            $user_id = 0; #session('user_id');
            $current_datetime = datetime_db();
    
            $uga_uait_username = env('UAIT_UGA_USERNAME');
            $uga_uait_api_key = env('UAIT_UGA_API_KEY');
            $rwa_uait_username = env('UAIT_RWA_USERNAME');
            $rwa_uait_api_key = env('UAIT_RWA_API_KEY');
    
            $uga_usis_username = "info@flowglobal.net";
            $ugs_usis_password = "password123";
    
            DB::insert("INSERT into vendors (`country_code`, `vendor_type`, `vendor_code`, `vendor_name`, `status`, `credentials`, `created_by`, `created_at`) VALUES ('UGA', 'sms', 'UAIT', 'AfricasTalking', 'enabled', '{\"username\" : \"$uga_uait_username\", \"api_key\" : \"$uga_uait_api_key\"}', '$user_id', '$current_datetime')");
            DB::insert("INSERT into vendors (`country_code`, `vendor_type`, `vendor_code`, `vendor_name`, `status`, `credentials`, `created_by`, `created_at`) VALUES ('RWA', 'sms', 'UAIT', 'AfricasTalking', 'enabled', '{\"username\" : \"$rwa_uait_username\", \"api_key\" : \"$rwa_uait_api_key\"}', '$user_id', '$current_datetime')");
            DB::insert("INSERT into vendors (`country_code`, `vendor_type`, `vendor_code`, `vendor_name`, `status`, `credentials`, `created_by`, `created_at`) VALUES ('UGA', 'sms', 'USIS', 'SimplySMS', 'enabled', '{\"username\" : \"$uga_usis_username\", \"password\" : \"$ugs_usis_password\"}', '$user_id', '$current_datetime')");

            DB::commit();
        }
        catch(\Exception $e){
            DB::rollback();
            thrw($e);
        }

    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendors');
    }
}
