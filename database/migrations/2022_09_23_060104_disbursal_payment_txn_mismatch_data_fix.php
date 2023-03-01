<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Services\RepaymentService;

class DisbursalPaymentTxnMismatchDataFix extends Migration
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


            /********************************************************************************************************************************* */
            //Disbursal Transactions Mismatch

            DB::statement("DELETE FROM loan_txns where id in (165650, 165652)");    // Captured Twice


            //Duplicate Disbursals & Reversals

            DB::update("UPDATE loan_txns SET txn_type = 'duplicate_disbursal', recon_amount = 0 WHERE id = 144027");

            DB::update("UPDATE loan_txns SET txn_type = 'duplicate_disbursal', recon_amount = 0 WHERE id in (166340, 166341)");
            DB::update("UPDATE loan_txns SET txn_type = 'dup_disb_rvrsl', recon_amount = 0 WHERE id = 166363");

            DB::update("UPDATE account_stmts SET recon_status = null, loan_doc_id = null WHERE id in (37431060, 43693614, 43680646, 43680780)");


            /********************************************************************************************************************************* */
            //Payment Transactions Mismatch

            //UEZM-304570-68376
            DB::update("UPDATE loans SET paid_amount = 2046000, paid_excess = 8000 WHERE id = 37764");
            DB::update("UPDATE loan_txns SET excess = 8000 WHERE id = 90688");

            //UEZM-716818-83666
            DB::update("UPDATE loan_txns SET penalty = 0 WHERE id = 91784");
            DB::update("UPDATE loans SET penalty_collected = 0, penalty_waived = 5000 WHERE id = 38225");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UEZM-716818-83666',NULL,NULL,NULL,'5000.00','penalty_waiver',NULL,NULL,NULL,'2022-01-12 16:23:14',NULL,NULL,NULL,'32',NULL,'2022-01-12 16:23:14',NULL,'0.00',NULL,NULL,NULL,NULL)");

            //UEZM-404376-76924
            DB::update("UPDATE loans SET penalty_collected = 5000, paid_amount = 772000 WHERE id = 38870");

            //UFLO-441516-17752
            DB::update("UPDATE loan_txns SET excess = 5000 WHERE id = 96235");
            DB::update("UPDATE loans SET paid_amount = 522000, paid_excess = 5000 WHERE id = 40397");

            //UFLW-703968272-31848
            DB::update("UPDATE loan_txns SET excess = 1200 WHERE id = 103237");
            DB::update("UPDATE loans SET paid_amount = 1533200, paid_excess = 1200 WHERE id = 43840");


            //UFLW-758363620-92249
            DB::update("UPDATE loan_txns SET principal = 1000000, fee = 22000, penalty = 5000 WHERE id = 128511");
            DB::statement("DELETE FROM loan_txns WHERE id = 133027");

            //UFLW-776080669-64772
            // $this->capture_tf_txn('47810426'); // Capture TF Missing Transaction
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-776080669-64772','3683','3973',NULL,'2444.00','payment','47810426-1662015631','daily_deduction','0','2022-09-01 07:00:31',NULL,NULL,NULL,'0',NULL,'2022-09-01 23:11:19',NULL,'0.00','0','2444','0','0')");


            //UFLO-707786-98147
            DB::statement("DELETE FROM loan_txns WHERE id = 184072");
            DB::update("UPDATE loan_txns SET penalty = 0 WHERE id = 184080");
            DB::update("UPDATE loans SET penalty_collected = 0, penalty_waived = 5000 WHERE id = 81205");


            DB::commit();
        }
        catch(\Exception $e){
            DB::rollback();
            throw $e;
        }

    }

    private function capture_tf_txn($acc_number){
     
        try{

            $tf_loan = [];

            $repay_txn = DB::selectOne("SELECT stmt_txn_date, cr_amt FROM tf_repay_txn_imports WHERE from_acc_num = '$acc_number' ORDER BY id DESC LIMIT 1");

            $txn_id = $acc_number."-".(strtotime($repay_txn->stmt_txn_date));
							
            $tf_loan['amount'] = $repay_txn->cr_amt;
            $tf_loan['from_ac_id'] = 3683;
            $tf_loan['to_ac_id'] = 3973;
            $tf_loan['txn_mode'] = 'daily_deduction';
            $tf_loan['txn_id'] = $txn_id;
            $tf_loan['txn_date'] = $repay_txn->stmt_txn_date;
            $tf_loan['is_part_payment'] = true;
            $tf_loan['send_sms'] = false;
            
            (new RepaymentService())->capture_repayment($tf_loan);
    
        }
        catch(\Exception $e){
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
