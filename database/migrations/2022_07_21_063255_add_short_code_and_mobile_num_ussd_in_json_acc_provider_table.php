<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShortCodeAndMobileNumUssdInJsonAccProviderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         db::Statement("update acc_providers set mobile_cred_format = json_set(mobile_cred_format,'$.ussd_short_code',? ) where acc_prvdr_code = 'UMTN';",["*165*2#"]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('json_acc_provider', function (Blueprint $table) {
            //
        });
    }
}
