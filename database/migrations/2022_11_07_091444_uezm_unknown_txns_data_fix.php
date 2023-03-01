<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UezmUnknownTxnsDataFix extends Migration
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
            
            /*Investment*/
            DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'investment' where cr_amt > 6000000 and EXTRACT(YEAR_MONTH from stmt_txn_date) >= '202201' and account_id = 2895 and stmt_txn_type = 'credit'");


            /*Redemption*/
            DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'redemption' where descr regexp 'TMN Cash Out.DFCU' and EXTRACT(YEAR_MONTH from stmt_txn_date) >= '202201' and account_id = 2895 and stmt_txn_type = 'debit'");


            /*Splitting Loan Txns Record*/
            DB::update("update loan_txns set txn_id = '110492244', amount = 1032000, recon_amount = 0, principal = 1032000, fee = 0 where id = 98125");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-706812667-24825',NULL,'2895',NULL,'500000.00','payment','110493767','wallet_portal','2436','2022-02-23 00:00:00',NULL,NULL,NULL,NULL,'35',NULL,'2022-02-23 13:58:59',NULL,'0','468000','32000','0','0')");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (28632224, 28636123)");


            DB::update("update loan_txns set txn_id = '111567695', amount = 1110000, recon_amount = 0, principal = 1110000, fee = 0 where id = 102790");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-752041434-48395',NULL,'2895',NULL,'422000.00','payment','111567799','wallet_portal','2436','2022-03-18 00:00:00',NULL,NULL,NULL,NULL,'35',NULL,'2022-03-18 11:27:10',NULL,'0','390000','32000','0','0')");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (30131042, 30132042)");


            /*Wrong Account Choosed*/
            DB::update("update loan_txns set to_ac_id = 3605 where id in (121478, 121486, 122071, 121474, 99999, 97660)");
            DB::update("update loan_txns set to_ac_id = 2895 where id in (110724, 110754, 111122, 112617)");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (30555175, 30577341, 30647892, 30918727)");

            DB::update("update loan_txns set to_ac_id = 1688 where id in (93051, 93110, 93535, 94173, 95260, 96626, 99397, 101365)");


            /*Penalty Paid after Waiver*/
            DB::statement("delete from loan_txns where id = 114891");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-753699104-38217',NULL,'2895',NULL,'10000.00','payment','112526639','wallet_transfer','0','2022-06-04 14:09:29',NULL,NULL,NULL,NULL,'0',NULL,'2022-06-04 14:09:29',NULL,'0.00','0.00','0','10000','0')");
            DB::update("update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 2048000 where id = 54760");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 31236030");


            DB::statement("delete from loan_txns where id = 133454");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-701892982-93162',NULL,'2895',NULL,'5000.00','payment','114951969','wallet_transfer','0','2022-04-13 14:12:51',NULL,NULL,NULL,'Col/FLOW/0753699104/RITAH/53171886','0',NULL,'2022-04-13 14:12:51',NULL,'0.00','0.00','0','10000','0')");
            DB::update("update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 1027000 where id = 46498");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 34779393");


            /*Penalty paid with Repayment but penalty is waived*/
            DB::statement("delete from loan_txns where id = 109153");
            DB::update("update loan_txns set amount = amount + 8000, penalty = 8000, recon_amount = 0 where id = 97796");
            DB::update("update loans set penalty_waived = 2000, penalty_collected = 8000, paid_amount = 1030000 where id = 41047");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 28527941");
            
            DB::statement("delete from loan_txns where id = 109131");
            DB::update("update loan_txns set amount = amount + 5000, penalty = 5000, recon_amount = 0 where id = 95238");
            DB::update("update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 517000 where id = 39878");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 27771966");

            DB::statement("delete from loan_txns where id = 109129");
            DB::update("update loan_txns set amount = amount + 5000, penalty = 5000, recon_amount = 0 where id = 95041");
            DB::update("update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 1537000 where id = 39776");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 27695037");


            DB::statement("delete from loan_txns where id = 109117");
            DB::update("update loan_txns set amount = amount + 10000, penalty = 10000, recon_amount = 0 where id = 93252");
            DB::update("update loans set penalty_waived = 0, penalty_collected = 10000, paid_amount = 1032000 where id = 38939");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 27264512");


            /*Incorrect Txn IDs*/
            DB::update("update loan_txns set txn_id = '109270285', recon_amount = 0 where id = 94043");
            DB::update("update loan_txns set txn_id = '109276000', recon_amount = 0  where id = 93955");
            DB::update("update loan_txns set txn_id = '109156634', recon_amount = 0 where id = 93446");
            DB::update("update loan_txns set txn_id = '109155974', recon_amount = 0 where id = 93447");

            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where stmt_txn_id in ('109270285', '109276000', '109156634', '109155974')");


            /*Single Repayment but captured twice*/
            DB::update("update loan_txns set recon_amount = 0, amount = 1027000, principal = 1000000, fee = 22000, penalty = 5000 where id = 125712");
            DB::statement("delete from loan_txns where id = 133011");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 33324852");

            DB::update("update loan_txns set recon_amount = 0, amount = 1027000, principal = 1000000, fee = 22000, penalty = 5000 where id = 122625");
            DB::statement("delete from loan_txns where id = 132989");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 32819507");

            DB::update("update loan_txns set recon_amount = 0, amount = 2048000, principal = 2000000, fee = 38000, penalty = 10000 where id = 121978");
            DB::statement("delete from loan_txns where id = 132986");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 32738309");

            DB::update("update loan_txns set recon_amount = 0, amount = 1027000, principal = 1000000, fee = 22000, penalty = 5000 where id = 114142");
            DB::statement("delete from loan_txns where id = 132942");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 31135222");

            DB::update("update loan_txns set recon_amount = 0, amount = 1027000, principal = 1000000, fee = 22000, penalty = 5000 where id = 111726");
            DB::update("delete from loan_txns where id = 132928");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 30742105");


            /*Duplicate Disbursal Capture*/
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (35008913, 35147948, 47253387)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-701632165-78490','2895','1977',NULL,'1500000.00','duplicate_disbursal','115059389','instant_disbursal',NULL,'2022-06-07 14:56:41',NULL,NULL,NULL,NULL,'0',NULL,'2022-06-07 14:56:41',NULL,'0.00',NULL,NULL,NULL,NULL)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-701632165-78490',NULL,'2895',NULL,'1500000.00','dup_disb_rvrsl','115095656','wallet_transfer',NULL,'2022-06-08 17:05:08',NULL,NULL,NULL,NULL,'0',NULL,'2022-06-08 17:05:08',NULL,'0.00',NULL,NULL,NULL,NULL)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-701824220-77450','2895','3090',NULL,'2000000.00','duplicate_disbursal','120276473','instant_disbursal',NULL,'2022-10-07 13:10:59',NULL,NULL,NULL,NULL,'0',NULL,'2022-10-07 13:10:59',NULL,'0.00',NULL,NULL,NULL,NULL)");

            DB::commit();

        }
        catch(Exception $e){

            DB::rollback();
            thrw($e);
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
