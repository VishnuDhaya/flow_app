<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AlterAccountsTableLowerUpperLimit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->double('upper_limit',15,2)->nullable()->after('to_recon');
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->double('lower_limit',15,2)->nullable()->after('to_recon');
        });

        DB::update("update accounts set upper_limit = 30000000, lower_limit = 10000000 where id in (1783, 4161) and status = 'enabled'");
        DB::update("update accounts set upper_limit = 20000000 where id in (3605, 4094) and status = 'enabled'");
        DB::update("update accounts set upper_limit = 50000000, lower_limit = 10000000 where id = 2895 and status = 'enabled'");
        DB::update("update accounts set upper_limit = 5000000 where id = 3973");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table){
        $table->dropColumn('lower_limit');
        $table->dropColumn('upper_limit');
        });
    }
}
