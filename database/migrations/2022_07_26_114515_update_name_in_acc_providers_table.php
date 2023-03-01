<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateNameInAccProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("update acc_providers set name = 'MTN' where name = 'MTN UGANDA' or name = 'MTN RWANDA' ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("update acc_providers set name = 'MTN UGANDA' where name = 'MTN' and country_code = 'UGA'");
        DB::statement("update acc_providers set name = 'MTN RWANDA' where name = 'MTN' and country_code = 'RWA'");
    }
}
