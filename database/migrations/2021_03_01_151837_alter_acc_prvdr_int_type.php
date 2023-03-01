<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAccPrvdrIntType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acc_providers', function (Blueprint $table) {
            
            $table->string('int_type',5)->nullable()->after("org_id");
            $table->double('transfer_limit',15,2)->nullable()->after("int_type");
            $table->json('api_cred_format')->nullable()->after('transfer_limit');
            $table->json('web_cred_format')->nullable()->after('api_cred_format');
       });

       DB::update("update acc_providers set int_type = 'api' where acc_prvdr_code = 'CCA'");
       DB::update("update acc_providers set int_type = 'web' where acc_prvdr_code = 'UEZM'");

       DB::update("update acc_providers set api_url = 'http://20.80.160.35:7601/api/thirdparty' where acc_prvdr_code = 'CCA'");
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
