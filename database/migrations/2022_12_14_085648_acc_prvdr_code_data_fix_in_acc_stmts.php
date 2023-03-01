<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AccPrvdrCodeDataFixInAccStmts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("update account_stmts set acc_prvdr_code = 'CCA' where acc_prvdr_code is null and account_id = 1783 and EXTRACT(YEAR_MONTH from stmt_txn_date) >= '202211'");

        DB::update("update account_stmts set acc_prvdr_code = 'RMTN' where acc_prvdr_code is null and account_id = 7337 and EXTRACT(YEAR_MONTH from stmt_txn_date) >= '202211'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acc_stmts', function (Blueprint $table) {
            //
        });
    }
}
