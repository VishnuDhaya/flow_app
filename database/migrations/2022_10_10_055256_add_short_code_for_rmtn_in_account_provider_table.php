<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShortCodeForRmtnInAccountProviderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        db::Statement("update acc_providers set mobile_cred_format = json_set(mobile_cred_format,'$.ussd_short_code',? ) where acc_prvdr_code = 'RMTN';",["*184*1"]);
        db::Statement("update accounts set type = 'bank' where acc_prvdr_code = 'RBOK' and cust_id is null");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_provider', function (Blueprint $table) {
            //
        });
    }
}
