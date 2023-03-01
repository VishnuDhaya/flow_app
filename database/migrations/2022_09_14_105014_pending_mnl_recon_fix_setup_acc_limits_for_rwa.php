<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PendingMnlReconFixSetupAccLimitsForRwa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("UPDATE accounts set upper_limit = 30000000, lower_limit = 5000000 where id = 4182 and status = 'enabled'");
        DB::update("UPDATE accounts set upper_limit = 3000000 where id in (4183, 4184, 4185) and status = 'enabled'");

        DB::UPDATE("UPDATE loan_txns set from_ac_id = 2895, to_ac_id = null where id = 125009");
        DB::update("UPDATE account_stmts a, loan_txns t set recon_status = null, a.loan_doc_id = null, recon_amount = 0 where txn_id = stmt_txn_id and reason_for_skip = 'statement_import_failed'");

        DB::update("UPDATE account_stmts a, loan_txns t set recon_status = null, a.loan_doc_id = null, recon_amount = 0 where txn_type = 'excess_reversal' and txn_id = stmt_txn_id and (recon_status != '80_recon_done' or recon_status is null)");
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
