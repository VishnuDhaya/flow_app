<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Scripts\php\UGARecon;
use App\Scripts\php\PaidtoDifferentAccountScript;

class UmtnDataCleaningPhaseV extends Migration
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

            (new PaidtoDifferentAccountScript)->run();

            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLO-175467-25465', 3421, NULL, NULL, 1022000.00, 'excess_reversal', '14998245798', 'wallet_transfer', NULL, '2022-02-10 13:18:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-02-10 13:18:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            DB::update("update account_stmts set recon_status = null, recon_desc = null, loan_doc_id = null where id = 27985233");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLO-175467-25465', NULL, 3421, NULL, 1022000.00, 'payment', '14997388959', 'wallet_transfer', NULL, '2022-02-10 11:40:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-02-10 11:40:00', NULL, 0.00, NULL, NULL, NULL, 1022000)");
            DB::update("update loans set paid_amount = '2044000.00',   paid_excess = '1022000.00', reversed_excess = '1022000.00' where id = 40419");

            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLO-359880-18620', 3421, NULL, NULL, 100000.00, 'excess_reversal', '15199536673', 'wallet_transfer', NULL, '2022-03-03 13:00:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-03 13:00:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            DB::update("update loans set reversed_excess = '100000.00' where id = 42110");
            DB::update("update account_stmts set recon_status = null, recon_desc = null, loan_doc_id = null where id = 29148857");

            DB::update("update loan_txns set  amount = '522000.00', excess = '10000.00' where id = 102979");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-776352725-69415', 3421, NULL, NULL, 10000.00, 'excess_reversal', '15363372215', 'wallet_transfer', NULL, '2022-03-19 20:01:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-19 20:01:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            DB::update("update account_stmts set recon_status = null, recon_desc = null, loan_doc_id = null where id = 30220399");
            DB::update("update loans set paid_amount = '522000.00',   paid_excess = '10000.00', reversed_excess = '10000.00' where id = 43710");

            DB::update("update account_stmts set acc_txn_type = 'inward_reversed(test)', recon_status = '80_recon_done' where stmt_txn_id in ('14705124873', '15773567534')");
            DB::update("update account_stmts set acc_txn_type = 'outward(test)', recon_status = '80_recon_done' where stmt_txn_id in ('14776629404')");

            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-760521735-29785', NULL, 3421, NULL, 1.00, 'payment', '16305673099', 'wallet_transfer', NULL, '2022-06-18 09:33:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-06-18 09:33:00', NULL, 0.00, NULL, NULL, NULL, 1)");
            DB::update("update account_stmts set recon_status = null, recon_desc = null, loan_doc_id = null where id = 49878621");
            DB::update("update loans set paid_amount = '1022001.00',   paid_excess = '1.00' where id = 57784");
        
            DB::update('update account_stmts SET recon_status = "80_recon_done" WHERE id in (32802321, 49875885, 33247342)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-755505629-27602", NULL, 4161, NULL, 100000.00, "payment_diff_acc", "15896335810", "wallet_transfer", NULL, "2022-05-10 14:32:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-05-10 14:32:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-755505629-27602", 4161, 4094, NULL, 100000.00, "payment_diff_acc_int_trans", "15978983509", "wallet_transfer", NULL, "2022-05-18 12:47:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-05-18 12:47:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-755505629-27602", 4161, 4094, NULL, 100000.00, "payment", "15978983509", "wallet_transfer", "0", "2022-05-18 12:47:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-05-18 12:47:00", NULL, 0.00, 100000, 0, 0, 0)');

            DB::update('update account_stmts SET recon_status = "80_recon_done" WHERE id in (37898221, 38120173, 38120283, 46046288, 45293309, 46618308, 46695613, 39654009, 47451980)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "CCA-338099-72767", NULL, 4161, NULL, 30000.00, "payment_diff_acc", "16438312155", "wallet_transfer", NULL, "2022-05-10 14:32:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-05-10 14:32:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "CCA-338099-72767", 4161, 4094, NULL, 30000.00, "payment_diff_acc_int_trans", "16452904007", "wallet_transfer", NULL, "2022-07-01 17:24:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-07-01 17:24:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "CCA-338099-72767", 4161, 4094, NULL, 30000.00, "payment", "16452904007", "wallet_transfer", "0", "2022-07-01 17:24:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-07-01 17:24:00", NULL, 0.00, 30000, 0, 0, 0)');

            DB::update('update account_stmts SET recon_status = "80_recon_done" WHERE stmt_txn_id in ("15143092452")');

            $data = [["acc_prvdr_code" => "UMTN", "id" => 3421]];
            UGARecon::run($data);

            DB::commit();

        }
        catch(\Exception $e){

            DB::rollBack();
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
