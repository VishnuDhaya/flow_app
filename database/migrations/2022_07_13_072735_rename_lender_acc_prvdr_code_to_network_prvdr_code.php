<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameLenderAccPrvdrCodeToNetworkPrvdrCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->renameColumn('lender_acc_prvdr_code', 'network_prvdr_code');
        });

        Schema::table('account_stmts', function (Blueprint $table) {
            $table->renameColumn('lender_acc_prvdr_code', 'network_prvdr_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->renameColumn('network_prvdr_code', 'lender_acc_prvdr_code');
        });

        Schema::table('account_stmts', function (Blueprint $table) {
            $table->renameColumn('network_prvdr_code', 'lender_acc_prvdr_code');
        });
    }
}
