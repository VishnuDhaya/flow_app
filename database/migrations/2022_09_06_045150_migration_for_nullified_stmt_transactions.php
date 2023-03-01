<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MigrationForNullifiedStmtTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try{

            DB::beginTransaction();

            //Incorrect Txn IDs 

            DB::update("UPDATE loan_txns set txn_id = '15643097292' where id = 115719");
            DB::update("UPDATE loan_txns set txn_id = '15684721455' where id = 116490");
            DB::update("UPDATE loan_txns set txn_id = '15696095253' where id = 116911");
            DB::update("UPDATE loan_txns set txn_id = '15702295073' where id = 116995");
            DB::update("UPDATE loan_txns set txn_id = '15703103406' where id = 117052");
            DB::update("UPDATE loan_txns set txn_id = '15711861691' where id = 117231");
            DB::update("UPDATE loan_txns set txn_id = '15742810792' where id = 117975");
            DB::update("UPDATE loan_txns set txn_id = '15743483455' where id = 118117");
            DB::update("UPDATE loan_txns set txn_id = '15752574827' where id = 118269");
            DB::update("UPDATE loan_txns set txn_id = '15756914511' where id = 118639");
            DB::update("UPDATE loan_txns set txn_id = '15763304324' where id = 118694");
            DB::update("UPDATE loan_txns set txn_id = '15764052571' where id = 118768");
            DB::update("UPDATE loan_txns set txn_id = '15766167264' where id = 118925");
            DB::update("UPDATE loan_txns set txn_id = '15766877763' where id = 118975");
            DB::update("UPDATE loan_txns set txn_id = '15768445445' where id = 119036");
            DB::update("UPDATE loan_txns set txn_id = '15787458846' where id = 119652");
            DB::update("UPDATE loan_txns set txn_id = '15821658116' where id = 120209");
            DB::update("UPDATE loan_txns set txn_id = '15544911189' where id = 113140");
            DB::update("UPDATE loan_txns set txn_id = '15456519160/15462714387' where id = 111044");

            //Incorrect Txn IDs and Wrong Repayment Account Choosed

            DB::update("UPDATE loan_txns set to_ac_id = 4161 where id = 114866");
            DB::update("UPDATE loan_txns set to_ac_id = 4161 where id = 115581");
            DB::update("UPDATE loan_txns set txn_id = '15639459858', to_ac_id = 4161 where id = 115511");
            DB::update("UPDATE loan_txns set txn_id = '15640238339', to_ac_id = 3421 where id = 115581");
            DB::update("UPDATE loan_txns set txn_id = '15639459858', to_ac_id = 3421 where id = 115511");
            DB::update("UPDATE loan_txns set txn_id = '15608135411', to_ac_id = 3421 where id = 114812");
            DB::update("UPDATE loan_txns set txn_id = '15609317752', to_ac_id = 3421 where id = 114866");
            DB::update("UPDATE loan_txns set txn_id = '15606452612', to_ac_id = 3421 where id = 114694");
            DB::update("UPDATE loan_txns set txn_id = '15606062591', to_ac_id = 3421 where id = 114673");
            DB::update("UPDATE loan_txns set txn_id = '15568929018', to_ac_id = 3421, amount = 1024000 where id = 113967");
            DB::update("UPDATE loan_txns set txn_id = '15569032778', to_ac_id = 3421, amount = 8000 where id = 132941");

            DB::update("UPDATE account_stmts set recon_status = '80_recon_done', loan_doc_id = 'UFLW-787652996-57039' where id in (30601249, 30642049)");


            // Update to_ac_id in loan_txns for mismatched choosed account
            DB::update("UPDATE account_stmts a, loan_txns t set to_ac_id = account_id where date(stmt_txn_date) >= '2022-01-01' and stmt_txn_type = 'credit' and recon_status is null  and stmt_txn_id = txn_id and a.country_code = 'UGA' and account_id != to_ac_id");

            //Update recon_status, loan_doc_id in account_stmts compare with loan transaction txn_id & statement account_id
            DB::update("UPDATE account_stmts a, loan_txns t set recon_status = '80_recon_done', a.loan_doc_id = t.loan_doc_id where date(stmt_txn_date) >= '2022-01-01' and stmt_txn_type = 'credit' and recon_status is null and stmt_txn_id = txn_id and account_id = to_ac_id");

            //from_ac_id is null for excess_reversal txns
            DB::update("UPDATE loan_txns set from_ac_id = 3421, to_ac_id = null where id in (120493, 120496, 123722, 125075, 125433, 126803, 127376, 127765, 128124)");

            //update non_fa_credit for investement transactions
            $acc_stmt_ids = "29876989, 31194866, 31367476, 31456733, 31566105, 31677262, 31772487, 31852700, 31941585, 32004917, 32026921, 32165368, 32232408, 32265824, 32319320, 32416196, 32430770, 31338337, 31320823, 31281242, 31235919, 31194868, 31159616, 31094587, 30991687, 30574707, 30530602, 30256056, 28855164, 26868977, 27064837, 27288543, 27375709, 27622665, 28062419, 28236712, 28504087, 28957313, 29553432, 29876996, 29877025, 29877028, 29877064, 29877076, 30159426, 30379457, 30429894, 30760047, 32154383";
            DB::update("UPDATE account_stmts set recon_status = '60_non_fa_credit' where id in ($acc_stmt_ids)");

            DB::commit();
        }
        catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
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
