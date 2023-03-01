<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateHolderNameAndStatusInAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        db::statement("update accounts set holder_name = 'FLOW UGANDA LIMITED' where acc_number in ('810985', '797904', '797903')");
        db::statement("update accounts set status = 'disabled' where acc_number = '1063626247612' and country_code = 'UGA'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            //
        });
    }
}
