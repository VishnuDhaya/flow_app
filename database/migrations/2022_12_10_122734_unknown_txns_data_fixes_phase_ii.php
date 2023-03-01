<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Scripts\php\UGARecon;
use App\Scripts\php\PaidtoDifferentAccountScript;

class UnknownTxnsDataFixesPhaseIi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        try{

            DB::beginTransaction();

            DB::update("update loan_txns set txn_id = '16150859472' where txn_id = '16150859472.'");

            (new PaidtoDifferentAccountScript)->run();

            //797904 Fix
            DB::Update("update loan_txns set txn_id = '15980526410', recon_amount = 0 where id = 125507");
            DB::Update("update loan_txns set txn_id = '16042170718', recon_amount = 0 where id = 127693");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (49875886, 49875894)");


            //Test Transactions
            DB::update("update account_stmts set acc_txn_type = 'inward (test)', recon_status = '80_recon_done' where id = 46620341");
            DB::update("update account_stmts set acc_txn_type = 'inward_reversed (test)', recon_status = '80_recon_done' where id = 46619307");


            //Incorrect Data in Loan Txns
            DB::update("update loan_txns set to_ac_id = 1688 where id = 110707");
            DB::update("update loan_txns set principal = '1352000', fee = 0, amount = '1352000.00' where id = 124171");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES
            ('UGA', 'UFLW-706812667-11540', NULL, 3605, NULL, 180000.00, 'payment', '16083195275', 'wallet_transfer', NULL, '2022-05-28 12:48:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-28 12:48:00', NULL, 0.00, '148000', '32000', NULL, NULL)");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 34040399");
            DB::update("update loans set paid_amount = '3245000', paid_fee = '55000', paid_principal = '3000000', penalty_collected = '190000' where id = 45303");

            //Recon Rerun
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (32387115, 32953961)");
            

            //Internal Transfers
            DB::update("update account_stmts set acc_txn_type = 'int_transfer_dr', recon_status = '80_recon_done' where stmt_txn_id in(15822427832, 15822459185) and account_id = 3421");
            DB::update("update account_stmts set acc_txn_type = 'int_transfer_cr', recon_status = '80_recon_done' where account_id = 4161 and stmt_txn_id in (15822427832, 15822459185)");

            DB::update("update loan_txns set txn_id = '15755376903', to_ac_id = 3421 where id = 118523");


            LOG::warning("============================");
            //Incorrect Txn ID
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (30526800, 31062274, 31062275, 31062286, 33781301, 49875861, 49876720, 49876719, 49876813, 49876812, 15006874217, 15006109803)");
            DB::update("update loan_txns set txn_id = '15445642242', to_ac_id = 3421, recon_amount = 0 where id = 110658");
            DB::update("update loan_txns set txn_id = '15558152346', recon_amount = 0 where id = 113697");
            DB::update("update loan_txns set txn_id = '15558156598', recon_amount = 0, to_ac_id = 3421 where id = 113698");
            DB::update("update loan_txns set amount = '517000', principal = '500000', fee = '12000', penalty = '5000', recon_amount = 0 where id = 113674");
            DB::update("update loan_txns set amount = '1027000', principal = '1000000', fee = '22000', penalty = '5000', recon_amount = 0 where id = 127912");
            DB::update("update loan_txns set recon_amount = 0 where id = 268315");
            DB::update("update loan_txns set to_ac_id = 3728 where id = 113562");
            DB::update("update loan_txns set txn_id = '15671140283', recon_amount = 0 where id = 116381");
            DB::update("update loan_txns set txn_id = '15670858934', recon_amount = 0 where id = 115888");
            DB::update("update loan_txns set txn_id = '15828008650', recon_amount = 0 where id = 120291");
            DB::update("update loan_txns set txn_id = '15828033339', recon_amount = 0 where id = 120317");
            DB::update("update loan_txns set txn_id = '15006874217', recon_amount = 0 where id = 96251");
            DB::update("update loan_txns set txn_id = '15006109803', recon_amount = 0 where id = 96249");
            DB::statement("delete from loan_txns where id in (132938, 133024)");

            Log::warning("==========================");
            DB::update("update loan_txns set recon_amount = 0, amount = '51200.00', principal = '39200.00', fee = '12000.00' where id = 92903");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLO-525017-50922', NULL, 3421, NULL, 460800.00, 'payment', '14801412886', 'wallet_transfer', '0', '2022-01-20 10:58:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-01-20 10:58:00', NULL, 0.00, 460800, 0, 0, 0)");

            DB::update("update loan_txns set recon_amount = 0, amount = '500000.00', principal = '500000.00', fee = '0.00' where id = 95855");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLO-407163-11116', NULL, 3421, NULL, 12000.00, 'payment', '14988190278', 'wallet_transfer', '0', '2022-02-09 12:09:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-02-09 12:09:00', NULL, 0.00, 0.00, 12000, 0, 0)");

            DB::update("update loan_txns set txn_id = '14847728550' where id = 93558");
            DB::update("update account_stmts set recon_status = NULL, recon_desc = null, loan_doc_id = null where id = 27293036");

            DB::update("update loan_txns set txn_id = '14793414004' where id = 92775");
            DB::update("update account_stmts set recon_status = NULL, recon_desc = null, loan_doc_id = null where stmt_txn_id = '14793414004' and account_id = 3421");

            DB::update("update loan_txns set txn_id = '14998424597' where id = 96080");
            DB::update("update account_stmts set recon_status = NULL, recon_desc = null, loan_doc_id = null where stmt_txn_id = '14998424597' and account_id = 3421");

            DB::update("update loan_txns set txn_id = '14672450095' where id = 91064");
            DB::update("update account_stmts set recon_status = NULL, recon_desc = null, loan_doc_id = null where stmt_txn_id = '14672450095' and account_id = 3421");

            DB::update("update loan_txns set txn_id = '14674083468' where id = 91128");
            DB::update("update account_stmts set recon_status = NULL, recon_desc = null, loan_doc_id = null where stmt_txn_id = '14674083468' and account_id = 3421");

            DB::update("update loan_txns set txn_id = '14776435497' where id = 92420");
            DB::update("update account_stmts set recon_status = NULL, recon_desc = null, loan_doc_id = null where stmt_txn_id = '14776435497' and account_id = 3421");

            DB::update("update loan_txns set txn_id = '15140733330' where id = 98686");
            DB::update("update account_stmts set recon_status = NULL, recon_desc = null, loan_doc_id = null where stmt_txn_id = '15140733330' and account_id = 3421");

            DB::update("update loan_txns set txn_id = '15607120150' where id = 114730");
            DB::update("update account_stmts set recon_status = NULL, recon_desc = null, loan_doc_id = null where stmt_txn_id = '15607120150' and account_id = 3421");

            DB::update("update loan_txns set txn_id = '15597269958' where id = 114496");
            DB::update("update account_stmts set recon_status = NULL, recon_desc = null, loan_doc_id = null where stmt_txn_id = '15597269958' and account_id = 3421");

            DB::update("update loan_txns set txn_id = '15493922059' where id = 112047");
            DB::update("update account_stmts set recon_status = NULL, recon_desc = null, loan_doc_id = null where stmt_txn_id = '15493922059' and account_id = 3421");

            DB::update("update loan_txns set txn_id = '15445473816' where id = 110638");
            DB::update("update account_stmts set recon_status = NULL, recon_desc = null, loan_doc_id = null where stmt_txn_id = '15445473816' and account_id = 3421");

            DB::update("update loan_txns set txn_id = '15405464447' where id = 109530");
            DB::update("update account_stmts set recon_status = NULL, recon_desc = null, loan_doc_id = null where stmt_txn_id = '15405464447' and account_id = 3421");

            DB::update("update loan_txns set txn_id = '15310726076' where id = 101869");
            DB::update("update account_stmts set recon_status = NULL, recon_desc = null, loan_doc_id = null where stmt_txn_id = '15310726076' and account_id = 3421");

            DB::update("update loan_txns set txn_id = '15239835178' where id = 100439");
            DB::update("update account_stmts set recon_status = NULL, recon_desc = null, loan_doc_id = null where stmt_txn_id = '15239835178' and account_id = 3421");

            DB::update("update loan_txns set txn_id = '15217585516' where id = 100149");
            DB::update("update account_stmts set recon_status = NULL, recon_desc = null, loan_doc_id = null where stmt_txn_id = '15217585516' and account_id = 3421");

            DB::update("update loan_txns set txn_id = '15140852650' where id = 98701");
            DB::update("update account_stmts set recon_status = NULL, recon_desc = null, loan_doc_id = null where stmt_txn_id = '15140852650' and account_id = 3421");

            DB::update("update loan_txns set txn_id = '15142092489' where id = 98754");
            DB::update("update account_stmts set recon_status = NULL, recon_desc = null, loan_doc_id = null where stmt_txn_id = '15142092489' and account_id = 3421");

            DB::update("update loan_txns set recon_amount = 0, amount = '900000.00', principal = '900000.00', fee = 0 where id = 96247");
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLO-176289-27250", NULL, 3421, NULL, 122000.00, "payment", "15006775986", "wallet_transfer", "0", "2022-02-11 11:09:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-02-11 11:09:00", NULL, 0.00, 100000.00, 22000.00, 0, 0)');

            DB::update("update loan_txns set recon_amount = 0, amount = '22000.00', principal = '0', fee = '22000.00' where id = 91577");
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLO-450531-57657", NULL, 3421, NULL, 1000000.00, "payment", "14714665338", "wallet_transfer", "0", "2022-01-11 13:29:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-01-11 13:29:00", NULL, 0.00, 1000000.00, 0.00, 0, 0)');

            DB::update("update loan_txns set recon_amount = 0, amount = '22000.00', principal = '0', fee = '22000.00' where id = 93607");
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLO-450531-13619", NULL, 3421, NULL, 1000000.00, "payment", "14848632703", "wallet_transfer", "0", "2022-01-25 14:36:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-01-25 14:36:00", NULL, 0.00, 1000000.00, 0.00, 0, 0)');

            DB::update("update loan_txns set txn_id = '15928586990', txn_date = '2022-05-13 15:21:00' where id = 121256");
            DB::update("update account_stmts set recon_status = NULL, recon_desc = null, loan_doc_id = null where stmt_txn_id = '15928586990' and account_id = 3421");

            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 33316336");
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-709342866-29915", 3421, NULL, NULL, 1022000.00, "duplicate_payment_reversal", "15969395564", "wallet_transfer", NULL, "2022-05-18 15:46:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-05-18 15:46:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 33316284");
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-709342866-29915", NULL, 3421, NULL, 1022000.00, "duplicate_payment", "15969350235", "wallet_transfer", NULL, "2022-05-17 13:59:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-05-17 13:59:00", NULL, 0.00, NULL, NULL, NULL, NULL)');

            DB::update("update loan_txns set to_ac_id = 3421, txn_id = '15486281349', amount = '1020000.00', principal = '1000000.00', fee = '20000.00' where id = 111911");
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-784500366-12814", NULL, 3421, NULL, 2000.00, "payment", "15486321741", "wallet_transfer", "0", "2022-04-01 16:24:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-04-01 16:24:00", NULL, 0.00, 0.00, 2000.00, 0, 0)');

            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-751025208-20410', NULL, 3421, NULL, 5.00, 'payment', '15492758449', 'wallet_transfer', NULL, '2022-04-02 09:09:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-02 09:09:00', NULL, 0.00, NULL, NULL, NULL, 5)");
            DB::update("update loans set paid_excess = '5.00' where id = 45360");

            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where stmt_txn_id = '16157789578' and account_id = 3421");
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-784794965-41743", 3421, NULL, NULL, 1022000.00, "duplicate_payment_reversal", "16157914836", "wallet_transfer", NULL, "2022-06-04 11:57:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-06-04 11:57:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where stmt_txn_id = '16157914836' and account_id = 3421");
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-784794965-41743", NULL, 3421, NULL, 1022000.00, "duplicate_payment", "16157789578", "wallet_transfer", NULL, "2022-06-04 11:45:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-06-04 11:45:00", NULL, 0.00, NULL, NULL, NULL, NULL)');

            DB::update("update account_stmts set acc_txn_type = 'int_transfer_dr', recon_status = '80_recon_done' where stmt_txn_id in (16270154311, 16246849207, 16192849509, 16165455778, 16126077173, 16143316897, 16032623602, 16042623978, 16043749848, 16076288316, 16111500126, 16090116909, 16079572774) and account_id = 3421");
            DB::update("update account_stmts set acc_txn_type = 'int_transfer_cr', recon_status = '80_recon_done' where account_id in (4161, 3074, 3728) and stmt_txn_id in (16270154311, 16246849207, 16192849509, 16165455778, 16126077173, 16143316897, 16032623602, 16042623978, 16043749848, 16076288316, 16111500126, 16090116909, 16079572774)");

            DB::update("update account_stmts set acc_txn_type = 'int_transfer_cr', recon_status = '80_recon_done' where stmt_txn_id in (15142503303, 15332082274) and account_id = 3421");
            DB::update("update account_stmts set acc_txn_type = 'int_transfer_dr', recon_status = '80_recon_done' where account_id = 3728 and stmt_txn_id in (15142503303, 15332082274)");

            DB::update("update account_stmts set acc_txn_type = 'int_transfer_dr', recon_status = '80_recon_done' where stmt_txn_id in ('16968012243') and account_id = 4161");
            DB::update("update account_stmts set acc_txn_type = 'int_transfer_cr', recon_status = '80_recon_done' where account_id = 3605 and stmt_txn_id in ('16968012243')");

            DB::update("update account_stmts set acc_txn_type = 'int_transfer_dr', recon_status = '80_recon_done' where stmt_txn_id in ('16968037397') and account_id = 3605");
            DB::update("update account_stmts set acc_txn_type = 'int_transfer_cr', recon_status = '80_recon_done' where account_id = 4161 and stmt_txn_id in ('16968037397')");

            DB::update("update loan_txns set to_ac_id = 3421 where id = 117275");

            DB::update("update loan_txns set amount = '102200.00', principal = '80200.00', fee = '22000.00', recon_amount = 0 where id = 117496");
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-770686147-32070", NULL, 3421, NULL, 919800.00, "payment", "15715714729", "wallet_transfer", "0", "2022-04-23 14:28:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-04-23 14:28:00", NULL, 0.00, 919800.00, 0.00, 0, 0)');

            DB::update("update loan_txns set amount = '122000.00', principal = '100000.00', fee = '22000.00', recon_amount = 0 where id = 100815");
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-750121515-40127", NULL, 3421, NULL, 900000.00, "payment", "15268672466", "wallet_transfer", "0", "2022-03-10 09:21:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-03-10 09:21:00", NULL, 0.00, 900000.00, 0.00, 0, 0)');

            DB::update("update loans set penalty_collected = '5000.00' where id = 40671");
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLO-267676-44458", NULL, 3421, NULL, 5000.00, "payment", "15063586153", "wallet_transfer", NULL, "2022-02-17 10:13:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-02-17 10:13:00", NULL, 0.00, NULL, NULL, 5000, NULL)');

            DB::update("update loan_txns set txn_id = '15207283101', to_ac_id = 3421 where id = 99843");

            DB::update("update loan_txns set txn_id = '15558896846', to_ac_id = 3421 where id = 113741");

            DB::update("update loan_txns set to_ac_id = 3421 where id = 117859");

            DB::update("update loan_txns set txn_id = '15547942528', to_ac_id = 3421 where id = 113352");

            DB::update("update loan_txns set txn_id = '15714934319', to_ac_id = 3421 where id = 117453");

            DB::update("update loan_txns set txn_id = '15416728578', to_ac_id = 3421 where id = 110072");

            DB::update("update loan_txns set txn_id = '15558415943', to_ac_id = 3421 where id = 113714");

            DB::update("update loan_txns set txn_id = '15517258501', to_ac_id = 3421 where id = 112446");


            /*Incorrect amount in FA*/
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15641179138' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15641179138' where id = 115635");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15641185923' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15641185923' where id = 115638");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15785796244' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15785796244' where id = 119555");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15785764278' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15785764278' where id = 119553");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15745414445' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15745414445' where id = 118179");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15745440529' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15745440529' where id = 118192");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15742746007' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15742746007' where id = 117970");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15742770925' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15742770925' where id = 117972");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15682195765' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15682195765' where id = 116309");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15682234493' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15682234493' where id = 116311");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15681651767' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15681651767' where id = 116241");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15681642786' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15681642786' where id = 116240");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15673514280' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15673514280' where id = 116112");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15673501767' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15673501767' where id = 116110");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15671981783' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15671981783' where id = 115953");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15672003839' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15672003839' where id = 115957");

            DB::delete("delete from loan_txns where id = 132945");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.0', penalty = '5000.00', recon_amount = 0 where id = 114289");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15588565935' and account_id = 3421");

            DB::delete("delete from loan_txns where id = 132944");
            DB::update("update loan_txns set amount = '1032000.00', principal = '1000000.00', fee = '22000.0', penalty = '10000.00', recon_amount = 0 where id = 114285");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15588506080' and account_id = 3421");

            DB::delete("delete from loan_txns where id = 132940");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.0', penalty = '5000.00', recon_amount = 0 where id = 113933");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15567882377' and account_id = 3421");

            DB::update("update loan_txns set amount = '1024000.00', principal = '1000000.00', fee = '22000.0', penalty = '0.00', excess = '2000.00', recon_amount = 0 where id = 111079");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15463174884' and account_id = 3421");
            DB::update("update loans set paid_excess = '2000.00', paid_amount = '1024000.00' where id = 44979");

            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.0', penalty = '0.00', excess = '5000.00', recon_amount = 0 where id = 100132");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15211767700' and account_id = 3421");
            DB::update("update loans set paid_excess = '5000.00', paid_amount = '1027000.00' where id = 42295");

            DB::delete("delete from loan_txns where id = 109173");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.0', penalty = '5000.00', recon_amount = 0 where id = 99701");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15199090487' and account_id = 3421");
            DB::update("update loans set penalty_waived = '0.00', penalty_collected = '5000.00', paid_amount = '1027000.00' where id = 42018");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15328866065' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15328866065' where id = 102225");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15328857609' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15328857609' where id = 102222");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15417274229' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15417274229' where id = 110104");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15417332798' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15417332798' where id = 110107");

            DB::update("update loan_txns set amount = '1024000.00', principal = '1000000.00', fee = '22000.0', penalty = '0.00', excess = '2000.00', recon_amount = 0 where id = 109439");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15398412080' and account_id = 3421");
            DB::update("update loans set paid_excess = '2000.00', paid_amount = '1024000.00' where id = 44122");

            DB::delete("delete from loan_txns where id = 109200");
            DB::update("update loan_txns set amount = '516000.00', principal = '500000.00', fee = '12000.0', penalty = '4000.00', recon_amount = 0 where id = 103509");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15386214456' and account_id = 3421");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-787652996-88488', NULL, 3421, NULL, 1000.00, 'payment', '15386330712', 'wallet_transfer', NULL, '2022-03-22 10:06:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-22 10:06:00', NULL, 0.00, NULL, NULL, '1000.00', NULL)");
            DB::update("update loans set penalty_waived = '0.00', penalty_collected = '5000.00', paid_amount = '517000.00' where id = 43866");

            DB::update("update loan_txns set amount = '513000.00', principal = '500000.00', fee = '12000.0', penalty = '0.00', excess = '1000.00', recon_amount = 0 where id = 100748");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15261273471' and account_id = 3421");
            DB::update("update loans set paid_excess = '1000.00', paid_amount = '513000.00' where id = 42680");

            DB::update("update loan_txns set amount = '517000.00', principal = '500000.00', fee = '12000.0', penalty = '0.00', excess = '5000.00', recon_amount = 0 where id = 102741");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15347068432' and account_id = 3421");
            DB::update("update loans set paid_excess = '5000.00', paid_amount = '517000.00' where id = 43627");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15378151550' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15378151550' where id = 103358");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15378177215' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15378177215' where id = 103387");

            DB::delete("delete from loan_txns where id = 109176");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.0', penalty = '5000.00', recon_amount = 0 where id = 100160");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15217952050' and account_id = 3421");
            DB::update("update loans set penalty_waived = '0.00', penalty_collected = '5000.00', paid_amount = '1027000.00' where id = 42237");

            DB::update("update loan_txns set amount = '2138000.00', principal = '2000000.00', fee = '38000.0', penalty = '0.00', excess = '100000.00', recon_amount = 0 where id = 99517");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15196853903' and account_id = 3421");
            DB::update("update loans set paid_excess = '100000.00', paid_amount = '2138000.00' where id = 42110");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '14791345085' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '14791345085' where id = 92680");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '14791357637' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '14791357637' where id = 92682");

            DB::update("update loan_txns set amount = '1536000.00', principal = '1500000.00', fee = '32000.0', penalty = '0.00', excess = '4000.00', recon_amount = 0 where id = 109290");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15396092371' and account_id = 3421");
            DB::update("update loans set paid_excess = '4000.00', paid_amount = '1536000.00' where id = 44066");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15337430864' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15337430864' where id = 102383");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15337422819' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15337422819' where id = 102381");

            DB::update("update loan_txns set amount = '1536000.00', principal = '1500000.00', fee = '32000.0', penalty = '0.00', excess = '4000.00', recon_amount = 0 where id = 102138");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15327666415' and account_id = 3421");
            DB::update("update loans set paid_excess = '4000.00', paid_amount = '1536000.00' where id = 43357");

            DB::update("update loan_txns set amount = '1024000.00', principal = '1000000.00', fee = '22000.0', penalty = '0.00', excess = '2000.00', recon_amount = 0 where id = 102235");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15329032546' and account_id = 3421");
            DB::update("update loans set paid_excess = '2000.00', paid_amount = '1024000.00' where id = 43319");

            DB::update("update loan_txns set amount = '1024000.00', principal = '1000000.00', fee = '22000.0', penalty = '0.00', excess = '2000.00', recon_amount = 0 where id = 101557");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15290630784' and account_id = 3421");
            DB::update("update loans set paid_excess = '2000.00', paid_amount = '1024000.00' where id = 43001");

            DB::update("update loan_txns set amount = '1025000.00', principal = '1000000.00', fee = '22000.0', penalty = '0.00', excess = '3000.00', recon_amount = 0 where id = 101545");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15290480256' and account_id = 3421");
            DB::update("update loans set paid_excess = '3000.00', paid_amount = '1025000.00' where id = 42951");

            DB::update("update loan_txns set amount = '515000.00', principal = '500000.00', fee = '12000.0', penalty = '0.00', excess = '3000.00', recon_amount = 0 where id = 99442");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15189932269' and account_id = 3421");
            DB::update("update loans set paid_excess = '3000.00', paid_amount = '515000.00' where id = 41986");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15279625369' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15279625369' where id = 101259");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15279638890' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15279638890' where id = 101265");

            DB::update("update loan_txns set amount = '1024000.00', principal = '1000000.00', fee = '22000.0', penalty = '0.00', excess = '2000.00', recon_amount = 0 where id = 100961");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15270304974' and account_id = 3421");
            DB::update("update loans set paid_excess = '2000.00', paid_amount = '1024000.00' where id = 42853");

            DB::update("update loan_txns set amount = '1024000.00', principal = '1000000.00', fee = '22000.0', penalty = '0.00', excess = '2000.00', recon_amount = 0 where id = 100678");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15259893881' and account_id = 3421");
            DB::update("update loans set paid_excess = '2000.00', paid_amount = '1024000.00' where id = 42672");

            DB::update("update loan_txns set amount = '515000.00', principal = '500000.00', fee = '12000.0', penalty = '0.00', excess = '3000.00', recon_amount = 0 where id = 96828");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15046871627' and account_id = 3421");
            DB::update("update loans set paid_excess = '3000.00', paid_amount = '515000.00' where id = 40758");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15180531157' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15180531157' where id = 99318");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15180574784' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15180574784' where id = 99319");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15279766769' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15279766769' where id = 101273");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15279781614' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15279781614' where id = 101274");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15109988186' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15109988186' where id = 97821");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15109997092' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15109997092' where id = 97810");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15044307235' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15044307235' where id = 96695");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15044356829' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15044356829' where id = 96699");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '14996394366' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '14996394366' where id = 95940");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '14996400613' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '14996400613' where id = 95944");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '14970002106' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '14970002106' where id = 95422");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '14970004634' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '14970004634' where id = 95423");

            DB::update("update loan_txns set amount = '520000.00', principal = '500000.00', fee = '12000.0', penalty = '0.00', excess = '8000.00', recon_amount = 0 where id = 95664");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '14980258698' and account_id = 3421");
            DB::update("update loans set paid_excess = '8000.00', paid_amount = '520000.00' where id = 40299");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '14950688241' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '14950688241' where id = 95271");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '14950602794' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '14950602794' where id = 95270");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '14933545741' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '14933545741' where id = 94991");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '14933559706' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '14933559706' where id = 94992");

            DB::update("update loan_txns set amount = '1024000.00', principal = '1000000.00', fee = '22000.0', penalty = '0.00', excess = '2000.00', recon_amount = 0 where id = 95348");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '14968203529' and account_id = 3421");
            DB::update("update loans set paid_excess = '2000.00', paid_amount = '1024000.00' where id = 40072");

            DB::update("update loan_txns set amount = '1024000.00', principal = '1000000.00', fee = '22000.0', penalty = '0.00', excess = '2000.00', recon_amount = 0 where id = 94316");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '14902132761' and account_id = 3421");
            DB::update("update loans set paid_excess = '2000.00', paid_amount = '1024000.00' where id = 39559");

            DB::update("update loan_txns set amount = '1024000.00', principal = '1000000.00', fee = '22000.0', penalty = '0.00', excess = '2000.00', recon_amount = 0 where id = 93792");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '14865188974' and account_id = 3421");
            DB::update("update loans set paid_excess = '2000.00', paid_amount = '1024000.00' where id = 39333");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '14820062372' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '14820062372' where id = 93220");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '14820065259' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '14820065259' where id = 93221");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '14703724852' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '14703724852' where id = 91390");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '14703803032' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '14703803032' where id = 91402");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '14711993618' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '14711993618' where id = 91568");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '14713262194' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '14713262194' where id = 91449");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '14653700480' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '14653700480' where id = 90773");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '14653652903' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '14653652903' where id = 90772");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '14791748840' and account_id = 3421");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15850753062' and account_id = 3421");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15621029989' and account_id = 3421");

            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15767775002' and account_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15767775002' where id = 116959");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '15701739566' and account_id = 3421");


            DB::delete("delete from loan_txns where id = 110168");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-772926524-50567', NULL, 3421, NULL, 5000.00, 'payment', '15423251675', 'wallet_transfer', NULL, '2022-03-26 08:23:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-26 08:23:00', NULL, 0.00, NULL, NULL, '5000.00', NULL)");
            DB::update("update loans set penalty_waived = '0.00', penalty_collected = '5000.00', paid_amount = '1027000.00' where id = 44386");

            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-778765481-20398', NULL, 3421, NULL, 5000.00, 'payment', '15686724457', 'wallet_transfer', NULL, '2022-04-20 18:14:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-20 18:14:00', NULL, 0.00, NULL, NULL, NULL, '5000.00')");
            DB::update("update loans set paid_excess = '5000.00', paid_amount = '1027000.00' where id = 47414");

            DB::update("update loan_txns set amount = '1533000.00', principal = '500000.00', fee = '32000.0', penalty = '0.00', excess = '1000.00', recon_amount = 0 where id = 109949");
            DB::update("update loans set paid_amount = '1533000.00', paid_excess = '1000.00', reversed_excess = '1000.00' where id = 44367");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES
            ('UGA', 'UFLW-752895533-60084', 3421, NULL, NULL, 1000.00, 'excess_reversal', '15423302315', 'wallet_transfer', NULL, '2022-03-26 08:30:00', NULL, NULL, NULL, NULL, 10, NULL, '2022-03-26 08:30:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            DB::update("update account_stmts set recon_status = '80_recon_done', loan_doc_id = 'UFLW-752895533-60084' where stmt_txn_id = '15423302315' and account_id = 3421");

            DB::update("update account_stmts SET acc_txn_type = 'inward(test)', recon_status = '80_recon_done' WHERE id in (27033522, 49878483)");

            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-773070062-41956', NULL, 3421, NULL, 2000.00, 'payment', '15640050811', 'wallet_transfer', NULL, '2022-04-16 11:11:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-16 11:11:00', NULL, 0.00, NULL, NULL, NULL, '2000.00')");
            DB::update("update loans set paid_excess = '2000.00', paid_amount = '1024000.00' where id = 47232");

            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-774097837-62229', NULL, 3421, NULL, 2000.00, 'payment', '15212004471', 'wallet_transfer', NULL, '2022-03-04 17:02:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-04 17:02:00', NULL, 0.00, NULL, '2000.00', NULL, NULL)");
            DB::update("update loan_txns set recon_amount = 0, amount = '1020000.00', principal = '1000000.00', fee = '200000.00' where id = 100134");

            DB::update("update loan_txns set recon_amount = 0, amount = '510000.00', principal = '500000.00', fee = '10000.00' where id = 101741");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-750495977-43548', NULL, 3421, NULL, 2000.00, 'payment', '15308453148', 'wallet_transfer', NULL, '2022-03-14 11:16:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-14 11:16:00', NULL, 0.00, NULL, '2000.00', NULL, NULL)");

            DB::update("update loan_txns set recon_amount = 0, amount = '1020000.00', principal = '1000000.00', fee = '200000.00' where id = 102179");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-740315020-20174', NULL, 3421, NULL, 2000.00, 'payment', '15328695157', 'wallet_transfer', NULL, '2022-03-16 12:03:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-16 12:03:00', NULL, 0.00, NULL, '2000.00', NULL, NULL)");

            DB::update("update loan_txns set txn_id = '17726619937' where id = 222729");

            DB::update("update account_stmts set acc_txn_type = 'inward(test)', recon_status = '80_recon_done' where stmt_txn_id in (15743652864, 15743800135, 15897118994) and account_id = 4161");          

            DB::update("update loan_txns set amount = '1020000.00', principal = '1000000.00', fee = '20000.00' where id = 119534");
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-785805937-21656", NULL, 4161, NULL, 2000.00, "payment", "15785737974", "wallet_transfer", NULL, "2022-04-30 11:28:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-04-30 11:28:00", NULL, 0.00, NULL, 2000, NULL, NULL)');

            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-773699517-67041', 4161, 4911, NULL, 3000.00, 'duplicate_disbursal', '16081140911', 'instant_disbursal', NULL, '2022-05-28 09:29:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-05-28 09:29:00', NULL, 0.00, NULL, NULL, NULL, NULL)");

            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-773699517-80123', 4161, 4911, NULL, 3000.00, 'duplicate_disbursal', '16177513188', 'instant_disbursal', NULL, '2022-06-06 09:44:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-06-06 09:44:00', NULL, 0.00, NULL, NULL, NULL, NULL)");

            DB::update("update account_stmts set recon_status = NULL, recon_desc = NULL, loan_doc_id = NULL where stmt_txn_id in (16177513188, 16081140911, 16177633606) and account_id = 4161");

            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-773699517-80123', NULL, 4161, NULL, 6000.00, 'dup_disb_rvrsl', '16177633606', 'wallet_transfer', NULL, '2022-06-06 09:56:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-06-06 09:56:00', NULL, 0.00, NULL, NULL, NULL, NULL)");

            DB::delete("delete from loan_txns where id = 109235");
            DB::update("update loan_txns set amount = '477000.00', fee = '22000.00', penalty = '5000.00', principal = '450000.00' where id = 109236");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-788239129-21641', NULL, 3421, NULL, 100000.00, 'payment', '15356573401', 'wallet_transfer', '0', '2022-03-19 09:17:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-03-19 09:17:00', NULL, 0.00, 100000, 0, 0, 0)");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-788239129-21641', NULL, 3421, NULL, 100000.00, 'payment', '15376211468', 'wallet_transfer', '0', '2022-03-21 09:10:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-03-21 09:10:00', NULL, 0.00, 100000, 0, 0, 0)");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-788239129-21641', NULL, 3421, NULL, 100000.00, 'payment', '15357233797', 'wallet_transfer', '0', '2022-03-19 10:27:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-03-19 10:27:00', NULL, 0.00, 100000, 0, 0, 0)");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-788239129-21641', NULL, 3421, NULL, 250000.00, 'payment', '15377023778', 'wallet_transfer', '0', '2022-03-21 10:36:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-03-21 10:36:00', NULL, 0.00, 250000, 0, 0, 0)");

            DB::update("update loan_txns set amount = '22000.00', fee = '22000.00', penalty = '0.00', principal = '0.00' where id = 110588");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-777001698-43824', NULL, 3421, NULL, 1000000.00, 'payment', '15444356550', 'wallet_transfer', '0', '2022-03-28 12:35:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-03-28 12:35:00', NULL, 0.00, 1000000, 0, 0, 0)");

            DB::update("update loan_txns set amount = '1050000.00', fee = '0.00', penalty = '0.00', principal = '1050000.00' where id = 109314");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-788990561-24220', NULL, 3421, NULL, 72000.00, 'payment', '15396891980', 'wallet_transfer', '0', '2022-03-23 12:31:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-03-23 12:31:00', NULL, 0.00, 0, 0, 72000, 0)");
            DB::delete("delete from loan_txns where id = 109313");
            DB::update("update loans set paid_amount = '1122000.00', paid_principal = '1000000.00', paid_fee = '22000.00', penalty_collected =  '100000.00', penalty_waived = '0' where id = 41759");
            DB::update("update loan_txns set principal = '1000000.00', fee = '22000.00', penalty = '28000.00' where id = 109314");

            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLO-446910-41502', NULL, 3421, NULL, 30000.00, 'payment', '16289907891', 'wallet_transfer', '0', '2022-06-16 18:49:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-06-16 18:49:00', NULL, 0.00, 30000, 0, 0, 0)");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLO-446910-41502', NULL, 3421, NULL, 50000.00, 'payment', '16792720038', 'wallet_transfer', '0', '2022-07-31 22:55:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-07-31 22:55:00', NULL, 0.00, 50000, 0, 0, 0)");

            DB::delete("delete from loan_txns where id = 109177");
            DB::update("update loan_txns set amount = 3065000, penalty = 10000 where id = 100543");
            DB::update("update loans set paid_amount = 3065000, penalty_waived = 0, penalty_collected = 10000 where id = 42262");
            DB::update("update account_stmts set loan_doc_id = null, recon_status = null, recon_desc = null where stmt_txn_id = '15248681205' and account_id = 3074");
            
            DB::update("update account_stmts set loan_doc_id = null, recon_status = null, recon_desc = null where stmt_txn_id in ('15672923597', '16751145813', '16805868674',  '16834853522', '15937840190', '16029033420', '16040765950', '16101553795') and account_id = 4161");
            DB::update("update loan_txns set recon_amount = 0 where id in (160743, 116064, 163244, 268428, 124146, 126906, 127537, 129719)");
            
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '16040002900' and account_id = 4161");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '16040002900' where id = 127418");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '16040000991' and account_id = 4161");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '16040000991' where id = 127415");
            
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '16040164458' and account_id = 4161");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '16040164458' where id = 127457");
            DB::update("update account_stmts set recon_desc = 0, recon_status = null, loan_doc_id = null where stmt_txn_id = '16040303106' and account_id = 4161");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '16040303106' where id = 127438");
            
            DB::update("update account_stmts set recon_desc = null, loan_doc_id = null, recon_status = null where stmt_txn_id in ('17421238074', '17729796124', '16834853522') and account_id = 4161");
            
            DB::update("update loan_txns set amount = 100000, penalty = 10000, excess = 90000 where id = 268428");
            DB::update("update loans set paid_amount = 1632000, paid_excess = 90000 where id = 31193");
            
            DB::update("update account_stmts SET acc_txn_type = 'inward(test)', recon_status = '80_recon_done' WHERE id in (27037445, 49876033)");
            
            DB::update("update account_stmts SET acc_txn_type = 'inward(test)', recon_status = '80_recon_done' WHERE stmt_txn_id in (14701064413) and account_id = 3074");
            DB::update("update account_stmts SET acc_txn_type = 'inward_reversed(test)', recon_status = '80_recon_done' WHERE stmt_txn_id in (14701084002) and account_id = 3074");
            DB::update("update account_stmts SET acc_txn_type = 'outward(test)', recon_status = '80_recon_done' WHERE stmt_txn_id in (14913275591) and account_id = 3074");
            DB::update("update account_stmts SET acc_txn_type = 'outward_reversed(test)', recon_status = '80_recon_done' WHERE stmt_txn_id in (14913283088) and account_id = 3074");
            
            DB::update("update account_stmts set acc_txn_type = 'int_transfer_cr', recon_status = '80_recon_done' where stmt_txn_id = '14902758958' and account_id = 3074");
            DB::update("update account_stmts set acc_txn_type = 'int_transfer_dr', recon_status = '80_recon_done' where stmt_txn_id = '14902758958' and account_id = 3421");
            
            DB::update("update account_stmts SET acc_txn_type = 'inward(test)', recon_status = '80_recon_done' , recon_desc = 'Internally transferred to 215010' WHERE stmt_txn_id in (14848538931, 14647961047) and account_id = 3074");
            DB::update("update account_stmts SET acc_txn_type = 'inward_reversed(test)', recon_status = '80_recon_done', recon_desc = 'Internally transferred to 215010' WHERE stmt_txn_id in (14848551952, 14848547011, 14700062539) and account_id = 3074");
            
            DB::update("update account_stmts SET acc_txn_type = 'outward(test)', recon_status = '80_recon_done'  WHERE stmt_txn_id in (14644690217) and account_id = 3074");
            
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-773854300-10030', NULL, 4161, NULL, '1000000.00', 'dup_disb_rvrsl', '15756571790', 'wallet_transfer', NULL, '2022-04-27 15:05:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-04-27 15:05:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-772090903-55527', NULL, 4161, NULL, '500000.00', 'dup_disb_rvrsl', '15821167229', 'wallet_transfer', NULL, '2022-05-03 15:57:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-05-03 15:57:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            DB::update("update loan_txns set amount = 1000000 where id =119717");
            DB::update("update loans set paid_amount = 1012000 where id = 50148");
            DB::update("update account_stmts set recon_desc = null, loan_doc_id = null, recon_status = null where stmt_txn_id in ('15790155320') and account_id = 4161");
            
            DB::update("update loan_txns set txn_id = '15562779969' , to_ac_id = 3421 where id = 113616");
            DB::insert("INSERT INTO `loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-759241926-84965',NULL,'4161',NULL,'1022000.00','payment_diff_acc','15555876765','wallet_transfer',NULL,'2022-04-08 10:50:17',NULL,NULL,NULL,NULL,'0',NULL,'2022-04-08 10:50:17',NULL,'0.00',NULL,NULL,NULL,NULL)");
            DB::insert("INSERT INTO `loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-759241926-84965','4161','3421',NULL,'1022000.00','payment_diff_acc_int_trans','15562779969','wallet_transfer',NULL,'2022-04-08 20:50:00',NULL,NULL,NULL,NULL,'0',NULL,'2022-04-08 20:50:00',NULL,'0.00',NULL,NULL,NULL,NULL)");
            DB::update("UPDATE account_stmts SET recon_status = '80_recon_done', acc_txn_type = 'fa', cust_id = 'UFLW-759241926', loan_doc_id = 'UFLW-759241926-84965' where stmt_txn_id in ('15562779969', '15555876765')");
            
            DB::update("update loan_txns set txn_type = 'duplicate_disbursal', loan_doc_id = 'UFLW-758672580-66918' where id = 119299");
            DB::update("update loan_txns set txn_type = 'dup_disb_rvrsl', loan_doc_id = 'UFLW-758672580-66918' where id = 123258");
            
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-754634614-30758', NULL, 3421, NULL, 1000000.00, 'dup_disb_rvrsl', '15888348963', 'wallet_transfer', NULL, '2022-05-09 18:35:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-05-09 18:35:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            DB::update("update account_stmts set recon_desc = null, loan_doc_id = null, recon_status = null where stmt_txn_id in ('15888348963') and account_id = 3421");
            
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-783845866-92104', NULL, 4161, NULL, 500000.00, 'duplicate_payment', '16650186814', 'wallet_transfer', NULL, '2022-07-19 13:03:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-07-19 13:03:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-783845866-92104', 4161, NULL, NULL, 500000.00, 'duplicate_payment_reversal', '16663207192', 'wallet_transfer', NULL, '2022-07-20 16:11:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-07-20 16:11:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            DB::update("update account_stmts set recon_desc = null, loan_doc_id = null, recon_status = null where stmt_txn_id in ('16663207192', '16650186814') and account_id = 4161");
            
            DB::update("update loan_txns set txn_id = '17314144535' where id = 268407");
            DB::update("update account_stmts set recon_status = null, recon_desc = null, loan_doc_id = null where stmt_txn_id = '16989576002'");
            DB::update("update account_stmts set recon_status = '80_recon_done', loan_doc_id = 'CCA-453405-29074' where stmt_txn_id = '17314144535'");

            DB::update("update loan_txns set txn_id = '16592609396' where txn_id = '17278136605' and loan_doc_id = 'UEZM 06446'");

            DB::update("update account_stmts a, loan_txns t set recon_status = null, a.loan_doc_id = null, recon_amount = 0 where a.id in (32387112, 43517443, 43517441, 46551238, 46604892, 46619391, 46781192, 46802037, 46858128, 46879042, 46980006, 47012896, 47043588, 47142783, 47185554) and stmt_txn_id = txn_id and account_id = from_ac_id and stmt_txn_type = 'debit'");
            DB::update("update account_stmts a, loan_txns t set recon_status = null, a.loan_doc_id = null, recon_amount = 0 where stmt_txn_id = txn_id and account_id = to_ac_id and a.id in ('49876353','49876406','49876407','49876408','49876409','49876410','49876412','49876413','49876414','49876415','49876416','49876417','49876418','49876419','49876420','49876440','49876719','49876812')");
            // DB::update("update account_stmts a, loan_txns t set recon_amount = 0, recon_status = null, a.loan_doc_id = null where stmt_txn_id = txn_id and (recon_status != '80_recon_done' or recon_status is null) and stmt_txn_type = 'credit' and account_id = to_ac_id and EXTRACT(YEAR_MONTH from stmt_txn_date) >= '202201' and EXTRACT(YEAR_MONTH from stmt_txn_date) <= '202210'");
            // DB::update("update account_stmts a, loan_txns t set recon_amount = 0, recon_status = null, a.loan_doc_id = null where stmt_txn_id = txn_id and (recon_status != '80_recon_done' or recon_status is null) and stmt_txn_type = 'debit' and account_id = from_ac_id and EXTRACT(YEAR_MONTH from stmt_txn_date) >= '202201' and EXTRACT(YEAR_MONTH from stmt_txn_date) <= '202210'");

            DB::update("update account_stmts set recon_status = '80_recon_done', loan_doc_id = 'CCA-453405-29074' where stmt_txn_id = '17344620929'");
            DB::update("update account_stmts set recon_status = '80_recon_done', loan_doc_id = 'CCA-338099-72767' where stmt_txn_id = '17634075213'");
            DB::update("update account_stmts set recon_status = '80_recon_done', loan_doc_id = 'UEZM 06446' where stmt_txn_id in ('16592609396', '16968045164', '17371130465')");
            


            //ChapChap Fixes
            DB::update("update loan_txns set recon_amount = amount, to_ac_id = 3074 where id = 268063");
            DB::update("update loan_txns set to_ac_id = 1687 where id in (101415, 101418, 101987)");

            
            //Rerun
            $data = [["acc_prvdr_code" => "UMTN", "id" => 3421], ["acc_prvdr_code" => "UMTN", "id" => 3605], ["acc_prvdr_code" => "UMTN", "id" => 4094], ["acc_prvdr_code" => "UMTN", "id" => 3728], ["acc_prvdr_code" => "UMTN", "id" => 4161], ["acc_prvdr_code" => "UMTN", "id" => 3074]];
            UGARecon::run($data);


            DB::commit();
        }
        catch(\Exception $e){
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
