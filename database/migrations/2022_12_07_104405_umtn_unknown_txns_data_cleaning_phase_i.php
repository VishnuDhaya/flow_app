<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Repositories\SQL\LoanTransactionRepositorySQL;
use App\Repositories\SQL\AccountStmtRepositorySQL;
use App\Scripts\php\CCAResetLoanTxns;
use App\Scripts\php\UGARecon;

class UmtnUnknownTxnsDataCleaningPhaseI extends Migration
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

            session()->put('country_code', 'UGA');
            session()->put('user_id',0); 

            $serv = new CCAResetLoanTxns;
            $loan_txn_repo = new LoanTransactionRepositorySQL();
            $acc_stmt_repo = new AccountStmtRepositorySQL();
            $serv->breakup_payment_record($loan_txn_repo, $acc_stmt_repo, '4161');
            $serv->breakup_payment_record($loan_txn_repo, $acc_stmt_repo, '3074');

            /*wrong account selected*/
            DB::update("update account_stmts a, loan_txns t set t.to_ac_id = account_id, recon_status = null, t.recon_amount = 0, a.loan_doc_id = null where txn_id = stmt_txn_id and date(a.stmt_txn_date) >= '2022-01-01' and stmt_txn_type = 'credit' and account_id = 3421 and account_id != to_ac_id");
            DB::update("update account_stmts a, loan_txns t set t.to_ac_id = account_id, recon_status = null, t.recon_amount = 0, a.loan_doc_id = null where txn_id = stmt_txn_id and date(a.stmt_txn_date) >= '2022-01-01' and date(a.stmt_txn_date) <= '2022-10-31' and stmt_txn_type = 'credit' and account_id = 4161 and account_id != to_ac_id");


            /*Interest*/
            DB::update("update account_stmts set acc_txn_type = 'interest', recon_status = '80_recon_done' where id in (46486662)");


            /*Redemption*/
            DB::update('update account_stmts set recon_status = "80_recon_done", acc_txn_type = "redemption" where account_id = 4161 and recon_status != "80_recon_done" and (descr = "float transfer/FLOW UGANDA LIMITED" OR descr = "FLOW UGANDA LIMITED" OR descr is null) and dr_amt > 3500000');
            DB::update('update account_stmts set recon_status = "80_recon_done", acc_txn_type = "redemption" where account_id = 3074 and id in (26868941, 26868942, 26868946)');
            DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'redemption' where stmt_txn_id in ('14744392112', '14865139691', '14987118297', '15110247053', '15177891152', '15327616852', '15395785557', '15443709254', '15565582481')");


            /*Investment*/
            DB::update('update account_stmts set recon_status = "80_recon_done", acc_txn_type = "investment" where account_id = 4161 and recon_status != "80_recon_done" and (descr ="256787787871/SYSTEM" OR descr = "FLOW UGANDA LIMITED" or descr = "SYSTEM" OR descr = "FLOW UGANDA LIMITED" OR descr = "333/FLOW UGANDA LIMITED" OR descr = "float transfer" or descr = "float/FLOW UGANDA LIMITED") and cr_amt > 0');
            DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'investment' where stmt_txn_id = '17772056084' and account_id = 4161");
            DB::update('update account_stmts set recon_status = "80_recon_done", acc_txn_type = "investment"  where descr = "float transfer/FLOW UGANDA LIMITED" and cr_amt > 0 and recon_status !="80_recon_done" and account_id = 4161');


            /*Wrong account chosen while capturing*/
            DB::update("update loan_txns set to_ac_id = '3074' where id in (109571, 109591, 113616, 99250, 101107, 109568, 109564, 109566, 96250, 96534, 96548, 97879, 98217, 101013, 101501, 109607, 92936, 99940, 92940, 92078, 92290, 94345, 96537, 109283, 109300, 98361, 99211, 99726)");
            DB::update("update loan_txns set from_ac_id = 3421 where id in (118285, 118297, 118299, 118300, 118303, 118305, 118307)");


            /*Duplicate disbursal*/
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 43478062");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-394002199-59066', 4161, 5042, NULL, 1000000.00, 'duplicate_disbursal', '16843748331', 'instant_disbursal', NULL, '2022-08-05 12:04:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-08-05 12:04:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 46291889");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-771490607-36806', 4161, 4645, NULL, 500000.00, 'duplicate_disbursal', '17213309271', 'instant_disbursal', NULL, '2022-09-07 16:01:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-09-07 16:01:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 46324799");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-774876689-47683', 4161, 4407, NULL, 500000.00, 'duplicate_disbursal', '17223431987', 'instant_disbursal', NULL, '2022-09-08 14:04:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-09-08 14:04:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 45896731");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-773295015-77220', 4161, 4534, NULL, 500000.00, 'duplicate_disbursal', '17071782404', 'instant_disbursal', NULL, '2022-08-26 10:04:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-08-26 10:04:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 46355746");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-774073642-82132', 4161, 4302, NULL, 1000000.00, 'duplicate_disbursal', '17232609585', 'instant_disbursal', NULL, '2022-09-09 10:33:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-09-09 10:33:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 45974342");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-779955562-52844', 4161, 3801, NULL, 1000000.00, 'duplicate_disbursal', '17107406320', 'instant_disbursal', NULL, '2022-08-29 15:02:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-08-29 15:02:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 45975550");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-779955562-52844', NULL, 4161, NULL, 1000000.00, 'dup_disb_rvrsl', '17107685240', 'wallet_transfer', NULL, '2022-08-29 15:28:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-08-29 15:28:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 42344423");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-781145828-98894', 4161, 3698, NULL, 1000000.00, 'duplicate_disbursal', '16762387255', 'instant_disbursal', NULL, '2022-07-29 11:30:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-07-29 11:30:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 37471115");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-775256906-56324', 4161, 3855, NULL, 500000.00, 'duplicate_disbursal', '16409343740', 'instant_disbursal', NULL, '2022-06-27 19:22:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-06-27 19:22:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 32779292");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-754634614-30758', 4161, 3692, NULL, 1000000.00, 'duplicate_disbursal', '15888050201', 'instant_disbursal', NULL, '2022-05-09 18:14:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-05-09 18:14:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 32093978");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-773528935-58609', 4161, 3658, NULL, 1000000.00, 'duplicate_disbursal', '15754629468', 'instant_disbursal', NULL, '2022-04-27 11:47:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-04-27 11:47:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 32094176");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-773854300-10030', 4161, 4123, NULL, 1000000.00, 'duplicate_disbursal', '15754877982', 'instant_disbursal', NULL, '2022-04-27 12:12:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-04-27 12:12:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 33615151");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-771630827-69576', 4161, 4623, NULL, 1000000.00, 'duplicate_disbursal', '16031933136', 'instant_disbursal', NULL, '2022-05-23 14:20:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-05-23 14:20:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            

            /*Duplicate disbursal reversal*/
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where id = 33050595");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-774073642-82132', NULL, 4161, NULL, 300000.00, 'dup_disb_rvrsl', '17583756294', 'wallet_transfer', NULL, '2022-10-08 19:26:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-10-08 19:26:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-774073642-82132', NULL, 4161, NULL, 300000.00, 'dup_disb_rvrsl', '17951827208', 'wallet_transfer', NULL, '2022-11-08 15:50:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-11-08 15:50:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where id in (47317669, 48465532)");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where id = 32849806");
            

            /*Duplicate disbursal and reversal*/
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 46116130');        
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-786537761-91972", NULL, 4161, NULL, 500000.00, "dup_disb_rvrsl", "17151484657", "wallet_transfer", NULL, "2022-09-02 10:39:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-09-02 10:39:00", NULL, 0.00, NULL, NULL, NULL, NULL)');   
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 46115122');  
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-786537761-91972", 4161, 5744, NULL, 500000.00, "duplicate_disbursal", "17151200612", "instant_disbursal", NULL, "2022-09-02 10:14:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-09-02 10:14:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
                
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 46217387'); 
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-782796722-19271", 4161, 5970, NULL, 500000.00, "duplicate_disbursal", "17191198655", "instant_disbursal", NULL, "2022-09-05 17:21:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-09-05 17:21:00", NULL, 0.00, NULL, NULL, NULL, NULL)');   
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 46218020');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-782796722-19271", NULL, 4161, NULL, 500000.00, "dup_disb_rvrsl", "17191400786", "wallet_transfer", NULL, "2022-09-05 17:36:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-09-05 17:36:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
                
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 46301103'); 
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-774082200-55017", NULL, 4161, NULL, 1500000.00, "dup_disb_rvrsl", "17216726346", "wallet_transfer", NULL, "2022-09-07 19:51:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-09-07 19:51:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 46280747');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-774082200-55017", 4161, 4239, NULL, 1500000.00, "duplicate_disbursal", "17209419758", "instant_disbursal", NULL, "2022-09-07 10:00:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-09-07 10:00:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
                
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 46295568');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-771864693-70707", NULL, 4161, NULL, 1000000.00, "dup_disb_rvrsl", "17214324565", "wallet_transfer", NULL, "2022-09-07 17:26:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-09-07 17:26:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 46294947');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-771864693-70707", 4161, 5374, NULL, 1000000.00, "duplicate_disbursal", "17214155465", "instant_disbursal", NULL, "2022-09-07 17:13:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-09-07 17:13:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
                
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id in (45792836, 45861329, 43680780, 43693614, 43680646, 45960767, 45963842)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-778303122-22183", 4161, 4033, NULL, 500000.00, "duplicate_disbursal", "17041208341", "instant_disbursal", NULL, "2022-08-23 13:31:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-08-23 13:31:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::update('update loan_txns set txn_type = "dup_disb_rvrsl" where id in (177963, 180286)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-777586567-26371", 4161, 3313, NULL, 1500000.00, "duplicate_disbursal", "17103867366", "instant_disbursal", NULL, "2022-08-29 09:39:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-08-29 09:39:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
                
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 38816152');
            DB::update('update loan_txns set txn_type = "dup_disb_rvrsl" where id = 149241');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 38539621');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-783497775-92587", 4161, 4862, NULL, 1500000.00, "duplicate_disbursal", "16494879902", "instant_disbursal", NULL, "2022-07-05 12:12:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-07-05 12:12:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 38522981');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-703515595-53408", 4161, 5132, NULL, 500000.00, "duplicate_disbursal", "16493265986", "instant_disbursal", NULL, "2022-07-05 09:46:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-07-05 09:46:00", NULL, 0.00, NULL, NULL, NULL, NULL)');   
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 38869618');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-703515595-53408", NULL, 4161, NULL, 500000.00, "dup_disb_rvrsl", "16519436187", "wallet_transfer", NULL, "2022-07-07 15:08:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-07-07 15:08:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 39022323');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-741463930-22210", NULL, 4161, NULL, 500000.00, "dup_disb_rvrsl", "16530034097", "wallet_transfer", NULL, "2022-07-08 13:47:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-07-08 13:47:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 39016270');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-741463930-22210", 4161, 4028, NULL, 500000.00, "duplicate_disbursal", "16529718944", "instant_disbursal", NULL, "2022-07-08 13:16:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-07-08 13:16:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 39008748');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-704694116-17519", NULL, 4161, NULL, 2000000.00, "dup_disb_rvrsl", "16529259293", "wallet_transfer", NULL, "2022-07-08 12:32:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-07-08 12:32:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 38539622');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-704694116-17519", 4161, 3150, NULL, 2000000.00, "duplicate_disbursal", "16494878163", "instant_disbursal", NULL, "2022-07-05 12:12:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-07-05 12:12:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
                
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 37402718');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-777364478-52460", NULL, 4161, NULL, 1000000.00, "dup_disb_rvrsl", "16404909842", "wallet_transfer", NULL, "2022-06-27 13:08:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-06-27 13:08:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 37392758');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-777364478-52460", 4161, 3565, NULL, 1000000.00, "duplicate_disbursal", "16404239000", "instant_disbursal", NULL, "2022-06-27 12:05:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-06-27 12:05:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
                
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 34975930');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-782334354-91066", NULL, 4161, NULL, 1000000.00, "dup_disb_rvrsl", "16188984381", "wallet_transfer", NULL, "2022-06-07 10:32:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-06-07 10:32:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 34831775');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-782334354-91066", 4161, 3565, NULL, 1000000.00, "duplicate_disbursal", "16163684474", "instant_disbursal", NULL, "2022-06-04 20:08:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-06-04 20:08:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
                
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 34153413');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-788766485-82971", NULL, 4161, NULL, 1000000.00, "dup_disb_rvrsl", "16102928549", "wallet_transfer", NULL, "2022-05-30 11:42:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-05-30 11:42:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 34141617');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-788766485-82971", 4161, 4411, NULL, 1000000.00, "duplicate_disbursal", "16101860961", "instant_disbursal", NULL, "2022-05-30 09:59:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-05-30 09:59:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
                
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 34020697');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-778797238-54915", 4161, 3591, NULL, 1000000.00, "duplicate_disbursal", "16080740315", "instant_disbursal", NULL, "2022-05-28 08:45:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-05-28 08:45:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 34020951');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-778797238-54915", NULL, 4161, NULL, 1000000.00, "dup_disb_rvrsl", "16080835677", "wallet_transfer", NULL, "2022-05-28 08:56:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-05-28 08:56:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
                

            /*Duplicate payment and reversal*/
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 46943114');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-392835214-21154", 4161, NULL, NULL, 5000.00, "duplicate_payment_reversal", "17463059505", "wallet_transfer", NULL, "2022-09-28 18:06:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-09-28 18:06:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 46942232');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-392835214-21154", NULL, 4161, NULL, 5000.00, "duplicate_payment", "17462757078", "wallet_transfer", NULL, "2022-09-28 17:45:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-09-28 17:45:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 46315348');  
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-789532262-96215", NULL, 4161, NULL, 1000000.00, "duplicate_payment", "17220953673", "wallet_transfer", NULL, "2022-09-08 10:18:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-09-08 10:18:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 46320154');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-789532262-96215", 4161, NULL, NULL, 1000000.00, "duplicate_payment_reversal", "17222234040", "wallet_transfer", NULL, "2022-09-08 12:14:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-09-08 12:14:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 46789786');  
            DB::update('update loan_txns set txn_type = "duplicate_payment" where id = 199603');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 46790901');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-754873820-51979", 4161, NULL, NULL, 400000.00, "duplicate_payment_reversal", "17401033564", "wallet_transfer", NULL, "2022-09-23 13:01:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-09-23 13:01:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
                
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 46209492');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-705322549-86992", NULL, 4161, NULL, 512000.00, "duplicate_payment", "17188999110", "wallet_transfer", NULL, "2022-09-05 14:16:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-09-05 14:16:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 46213303');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-705322549-86992", 4161, NULL, NULL, 512000.00, "duplicate_payment_reversal", "17189982117", "wallet_transfer", NULL, "2022-09-05 15:44:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-09-05 15:44:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
                

            /*Test Transaction*/
            DB::update('update account_stmts SET acc_txn_type = "outward (test)", recon_status = "80_recon_done" WHERE id = 47820284');
            DB::update('update account_stmts SET acc_txn_type = "outward_reversed (test)", recon_status = "80_recon_done" WHERE id = 47821331');    
            
            DB::update('update account_stmts SET acc_txn_type = "inward(test)", recon_status = "80_recon_done" WHERE id in (38799061)');    
            DB::update('update account_stmts SET acc_txn_type = "inward_reversed(test)", recon_status = "80_recon_done" WHERE id in (39334275)');


            /*Internal Transfer*/
            DB::update("update account_stmts set acc_txn_type = 'int_transfer_dr', recon_status = '80_recon_done' where stmt_txn_id in('15956084317', '15957105102', '15957844511', '15959385899', '15959771812', '15959898167', '15959925688', '15959941812', '15968802691', '15968946983') and account_id = 3421");
            DB::update("update account_stmts set acc_txn_type = 'int_transfer_cr', recon_status = '80_recon_done' where account_id = 4161 and stmt_txn_id in ('15956084317', '15957105102', '15957844511', '15959385899', '15959771812', '15959898167', '15959925688', '15959941812', '15968802691', '15968946983')");


            /*Paid to Different Account*/
            DB::update('update account_stmts SET recon_status = "80_recon_done" WHERE id in (46241403, 46214588)');
            DB::update("update account_stmts SET recon_status = '80_recon_done', acc_txn_type = 'fa' WHERE stmt_txn_id IN ('16068947342', '16067345020')");
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UEZM 06408", 4161, 3605, NULL, 40000.00, "payment_diff_acc_int_trans", "16074054492", "wallet_transfer", NULL, "2022-05-27 15:06:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-05-27 15:06:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UEZM 06408", NULL, 4161, NULL, 20000.00, "payment_diff_acc", "16068947342", "wallet_transfer", NULL, "2022-05-26 22:41:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-05-26 22:41:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UEZM 06408", NULL, 4161, NULL, 20000.00, "payment_diff_acc", "16067345020", "wallet_transfer", NULL, "2022-05-26 22:41:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-05-26 22:41:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
    
            DB::update('update account_stmts SET recon_status = "80_recon_done" WHERE id = 45995314');
            DB::update('update account_stmts SET recon_status = "80_recon_done" WHERE id = 45971883');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "CCA-249465-63285", NULL, 4161, NULL, 100000.00, "payment_diff_acc", "17106754218", "wallet_transfer", NULL, "2022-08-29 14:03:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-08-29 14:03:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "CCA-249465-63285", 4161, 4094, NULL, 100000.00, "payment_diff_acc_int_trans", "17114345043", "wallet_transfer", NULL, "2022-08-30 07:25:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-08-30 07:25:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "CCA-249465-63285", 4161, 4094, NULL, 100000.00, "payment", "17114345043", "wallet_transfer", "0", "2022-08-30 07:25:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-08-30 07:25:00", NULL, 0.00, 100000, 0, 0, 0)');
            DB::delete('delete from loan_txns where id in (180987, 127136)');    

            DB::update('update account_stmts SET recon_status = "80_recon_done" WHERE id = 44422413');
            DB::update('update account_stmts SET recon_status = "80_recon_done" WHERE id = 44404768');
            DB::delete('delete from loan_txns where id = 169249');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UEZM-188463-40442", NULL, 4161, NULL, 60000.00, "payment_diff_acc", "16909853139", "wallet_transfer", NULL, "2022-08-11 10:36:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-08-11 10:36:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UEZM-188463-40442", 4161, 3605, NULL, 60000.00, "payment_diff_acc_int_trans", "16910731575", "wallet_transfer", NULL, "2022-08-11 11:57:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-08-11 11:57:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UEZM-188463-40442", 4161, 3605, NULL, 60000.00, "payment", "16910731575", "wallet_transfer", "0", "2022-08-11 11:57:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-08-11 11:57:00", NULL, 0.00, 60000, 0, 0, 0)');
                
            DB::update('update account_stmts SET recon_status = "80_recon_done" WHERE id = 47400618');
            DB::update('update account_stmts SET recon_status = "80_recon_done" WHERE id = 47451980');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "CCA-338099-72767", NULL, 4161, NULL, 32000.00, "payment_diff_acc", "17618906981", "wallet_transfer", NULL, "2022-10-11 18:48:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-10-11 18:48:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "CCA-338099-72767", 4161, 4094, NULL, 32000.00, "payment_diff_acc_int_trans", "17634075213", "wallet_transfer", NULL, "2022-10-13 07:12:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-10-13 07:12:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "CCA-338099-72767", 4161, 4094, NULL, 32000.00, "payment", "17634075213", "wallet_transfer", "0", "2022-10-13 07:12:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-10-13 07:12:00", NULL, 0.00, 32000, 0, 0, 0)');
                
            DB::update('update account_stmts SET recon_status = "80_recon_done" WHERE id = 45604789');
            DB::update('update account_stmts SET recon_status = "80_recon_done" WHERE id = 46618308');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "CCA-453405-29074", NULL, 4161, NULL, 48000.00, "payment_diff_acc", "16989576002", "wallet_transfer", NULL, "2022-08-18 17:58:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-08-18 17:58:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "CCA-453405-29074", 4161, 4094, NULL, 48000.00, "payment_diff_acc_int_trans", "17344620929", "wallet_transfer", NULL, "2022-09-19 06:49:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-09-19 06:49:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "CCA-453405-29074", 4161, 4094, NULL, 48000.00, "payment", "17344620929", "wallet_transfer", "0", "2022-09-19 06:49:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-09-19 06:49:00", NULL, 0.00, 48000, 0, 0, 0)');
                
            DB::update('update account_stmts SET recon_status = "80_recon_done" WHERE id = 46467159');
            DB::update('update account_stmts SET recon_status = "80_recon_done" WHERE id = 46468388');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-755273271-25598", NULL, 4161, NULL, 1027000.00, "payment_diff_acc", "17277841665", "wallet_transfer", NULL, "2022-09-13 12:30:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-09-13 12:30:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-755273271-25598", 4161, 4094, NULL, 1027000.00, "payment_diff_acc_int_trans", "17278136605", "wallet_transfer", NULL, "2022-09-13 12:59:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-09-13 12:59:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
                
            DB::update('update account_stmts SET recon_status = "80_recon_done" WHERE id = 39556272');
            DB::update('update account_stmts SET recon_status = "80_recon_done" WHERE id = 39654009');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UEZM 06446", NULL, 4161, NULL, 500000.00, "payment_diff_acc", "16586710586", "wallet_transfer", NULL, "2022-07-13 17:13:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-07-13 17:13:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UEZM 06446", 4161, 3605, NULL, 500000.00, "payment_diff_acc_int_trans", "17278136605", "wallet_transfer", NULL, "2022-07-14 08:55:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-07-14 08:55:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UEZM 06446", 4161, 3605, NULL, 500000.00, "payment", "17278136605", "wallet_transfer", "0", "2022-07-14 08:55:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-07-14 08:55:00", NULL, 0.00, 500000, 0, 0, 0)');
                
            DB::update('update account_stmts SET recon_status = "80_recon_done" WHERE id = 45242547');
            DB::update('update account_stmts SET recon_status = "80_recon_done" WHERE id = 45293309');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UEZM 06446", NULL, 4161, NULL, 500000.00, "payment_diff_acc", "16965413355", "wallet_transfer", NULL, "2022-08-16 12:48:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-08-16 12:48:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UEZM 06446", 4161, 3605, NULL, 500000.00, "payment_diff_acc_int_trans", "16968045164", "wallet_transfer", NULL, "2022-08-16 17:07:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-08-16 17:07:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UEZM 06446", 4161, 3605, NULL, 500000.00, "payment", "16968045164", "wallet_transfer", "0", "2022-08-16 17:07:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-08-16 17:07:00", NULL, 0.00, 500000, 0, 0, 0)');
                
            DB::update('update account_stmts SET recon_status = "80_recon_done" WHERE id = 46663004');
            DB::update('update account_stmts SET recon_status = "80_recon_done" WHERE id = 46695613');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UEZM 06446", NULL, 4161, NULL, 500000.00, "payment_diff_acc", "17358573204", "wallet_transfer", NULL, "2022-09-20 10:20:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-09-20 10:20:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UEZM 06446", 4161, 3605, NULL, 500000.00, "payment_diff_acc_int_trans", "17371130465", "wallet_transfer", NULL, "2022-09-21 08:07:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-09-21 08:07:00", NULL, 0.00, NULL, NULL, NULL, NULL)');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UEZM 06446", 4161, 3605, NULL, 500000.00, "payment", "17371130465", "wallet_transfer", "0", "2022-09-21 08:07:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-09-21 08:07:00", NULL, 0.00, 500000, 0, 0, 0)');
            

            /*Penalty waived but paid*/
            DB::delete('delete from loan_txns where id = 146560');
            DB::update('update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 1027000 where id = 61069');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 38197432');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-782702175-50600", NULL, 4161, NULL, 5000.00, "payment", "16459078710", "wallet_transfer", NULL, "2022-07-02 09:08:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-07-02 09:08:00", NULL, 0.00, NULL, NULL, 5000, NULL)');
            
            DB::delete('delete from loan_txns where id = 194651');
            DB::update('update loans set penalty_waived = 0, penalty_collected = 1000, paid_amount = 257000 where id = 85621');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 46572754');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-774780313-29104", NULL, 4161, NULL, 1000.00, "payment", "17318979604", "wallet_transfer", NULL, "2022-09-16 18:56:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-09-16 18:56:00", NULL, 0.00, NULL, NULL, 1000, NULL)');
                
            DB::delete('delete from loan_txns where id = 199799');
            DB::update('update loans set penalty_waived = 0, penalty_collected = 10000, paid_amount = 1032000 where id = 87673');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 46814751');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-394827672-30888", NULL, 4161, NULL, 2000.00, "payment", "17412294326", "wallet_transfer", NULL, "2022-09-24 09:17:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-09-24 09:17:00", NULL, 0.00, NULL, NULL, 2000, NULL)');
                
            DB::delete('delete from loan_txns where id = 184233');
            DB::update('update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 1027000 where id = 81281');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 46159496');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-780553555-67113", NULL, 4161, NULL, 5000.00, "payment", "17163359377", "wallet_transfer", NULL, "2022-09-03 10:40:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-09-03 10:40:00", NULL, 0.00, NULL, NULL, 5000, NULL)');    
        
            DB::delete("delete from loan_txns where id = 155816");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-775275149-23044', NULL, 4161, NULL, 5000.00, 'payment', '16659863315', 'wallet_transfer', NULL, '2022-07-20 10:45:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-07-20 10:45:00', NULL, 0.00, NULL, NULL, 5000, NULL)");
            DB::update("update loans set penalty_collected = '10000.00', penalty_waived = '0.00', paid_amount = '1542000.00' where id = 68105");
            DB::update("UPDATE account_stmts set recon_status = null, loan_doc_id = null where stmt_txn_id = '16659863315' and account_id = 4161");

            DB::delete("delete from loan_txns where id = 187315");
            DB::update("update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 517000 where id = 82720");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 46282471");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-782995738-57169', NULL, 4161, NULL, 5000.00, 'payment', '17210298390', 'wallet_transfer', NULL, '2022-09-07 11:20:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-09-07 11:20:00', NULL, 0.00, NULL, NULL, 5000, NULL)");
            
            DB::delete("delete from loan_txns where id = 184917");
            DB::update("update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 1027000 where id = 81768");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 46198582");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-774097837-38519', NULL, 4161, NULL, 5000.00, 'payment', '17186124502', 'wallet_transfer', NULL, '2022-09-05 10:08:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-09-05 10:08:00', NULL, 0.00, NULL, NULL, 5000, NULL)");
            
            DB::delete('delete from loan_txns where id = 108786');
            DB::update('update loans set penalty_waived = 0, penalty_collected = 10000, paid_amount = 1542000 where id = 31193');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 43354003');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "CCA-638375-80916", NULL, 4161, NULL, 10000.00, "payment", "16834853522", "wallet_transfer", NULL, "2022-08-04 16:48:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-08-04 16:48:00", NULL, 0.00, NULL, NULL, 10000, NULL)');

            DB::delete('delete from loan_txns where id = 176134');
            DB::update('update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 517000 where id = 77447');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 45770918');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-778407242-40211", NULL, 4161, NULL, 5000.00, "payment", "17035562760", "wallet_transfer", NULL, "2022-08-22 20:17:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-08-22 20:17:00", NULL, 0.00, NULL, NULL, 5000, NULL)');
                
            DB::delete('delete from loan_txns where id = 123596');
            DB::update('update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 517000 where id = 50796');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 32958411');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-781299800-92571", NULL, 4161, NULL, 5000.00, "payment", "15925308928", "wallet_transfer", NULL, "2022-05-13 09:43:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-05-13 09:43:00", NULL, 0.00, NULL, NULL, 5000, NULL)');
                
            DB::delete('delete from loan_txns where id = 192134');
            DB::update('update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 1027000 where id = 84824');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 46488849');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-772055224-98764", NULL, 4161, NULL, 5000.00, "payment", "17287494463", "wallet_transfer", NULL, "2022-09-14 09:58:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-09-14 09:58:00", NULL, 0.00, NULL, NULL, 5000, NULL)');
                
            DB::delete('delete from loan_txns where id = 120434');
            DB::update('update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 1027000 where id = 49069');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 32505600');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-785283744-28608", NULL, 4161, NULL, 5000.00, "payment", "15829918202", "wallet_transfer", NULL, "2022-05-04 12:39:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-05-04 12:39:00", NULL, 0.00, NULL, NULL, 5000, NULL)');
                
            DB::delete('delete from loan_txns where id = 129147');
            DB::update('update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 261000 where id = 53335');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 33967361');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-783289369-81554", NULL, 4161, NULL, 5000.00, "payment", "16074585875", "wallet_transfer", NULL, "2022-05-27 15:58:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-05-27 15:58:00", NULL, 0.00, NULL, NULL, 5000, NULL)');
                
            DB::delete('delete from loan_txns where id = 220059');
            DB::update('update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 1027000 where id = 98373');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 47648972');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-760915762-38808", NULL, 4161, NULL, 5000.00, "payment", "17701887975", "wallet_transfer", NULL, "2022-10-19 09:02:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-10-19 09:02:00", NULL, 0.00, NULL, NULL, 5000, NULL)');
                
            DB::delete('delete from loan_txns where id = 207341');
            DB::update('update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 517000 where id = 92384');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 47123369');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-753235300-33682", NULL, 4161, NULL, 5000.00, "payment", "17528247273", "wallet_transfer", NULL, "2022-10-04 09:32:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-10-04 09:32:00", NULL, 0.00, NULL, NULL, 5000, NULL)');
                
            DB::delete('delete from loan_txns where id = 192213');
            DB::update('update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 1537000 where id = 84817');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 46489445');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-779249603-90421", NULL, 4161, NULL, 5000.00, "payment", "17287804516", "wallet_transfer", NULL, "2022-09-14 10:29:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-09-14 10:29:00", NULL, 0.00, NULL, NULL, 5000, NULL)');
                
            DB::delete('delete from loan_txns where id = 151898');
            DB::update('update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 1027000 where id = 66077');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 39480811');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-392662110-23811", NULL, 4161, NULL, 5000.00, "payment", "16582337209", "wallet_transfer", NULL, "2022-07-13 10:05:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-07-13 10:05:00", NULL, 0.00, NULL, NULL, 5000, NULL)');
                
            DB::delete('delete from loan_txns where id = 164702');
            DB::update('update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 1027000 where id = 72114');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 43302035');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-392662110-93215", NULL, 4161, NULL, 5000.00, "payment", "16831484013", "wallet_transfer", NULL, "2022-08-04 12:12:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-08-04 12:12:00", NULL, 0.00, NULL, NULL, 5000, NULL)');
                
            DB::delete('delete from loan_txns where id = 169016');
            DB::update('update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 517000 where id = 74232');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 44424911');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-781358444-74750", NULL, 4161, NULL, 5000.00, "payment", "16910857368", "wallet_transfer", NULL, "2022-08-11 12:09:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-08-11 12:09:00", NULL, 0.00, NULL, NULL, 5000, NULL)');
                
            DB::delete('delete from loan_txns where id = 172579');
            DB::update('update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 1027000 where id = 75805');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 45390989');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-789559973-20256", NULL, 4161, NULL, 5000.00, "payment", "16974726143", "wallet_transfer", NULL, "2022-08-17 10:59:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-08-17 10:59:00", NULL, 0.00, NULL, NULL, 5000, NULL)');
                
            DB::delete('delete from loan_txns where id = 131810');
            DB::update('update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 1027000 where id = 54058');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 34341216');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-786110500-43803", NULL, 4161, NULL, 5000.00, "payment", "16123195219", "wallet_transfer", NULL, "2022-06-01 09:09:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-06-01 09:09:00", NULL, 0.00, NULL, NULL, 5000, NULL)');
                
            DB::delete('delete from loan_txns where id = 155601');
            DB::update('update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 517000 where id = 67891');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 40533114');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-771864880-20335", NULL, 4161, NULL, 5000.00, "payment", "16656090998", "wallet_transfer", NULL, "2022-07-19 20:40:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-07-19 20:40:00", NULL, 0.00, NULL, NULL, 5000, NULL)');
                
            DB::delete('delete from loan_txns where id = 181707');
            DB::update('update loans set penalty_waived = 0, penalty_collected = 10000, paid_amount = 1542000 where id = 80325');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 46035875');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-772375187-98626", NULL, 4161, NULL, 10000.00, "payment", "17126363214", "wallet_transfer", NULL, "2022-08-31 09:06:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-08-31 09:06:00", NULL, 0.00, NULL, NULL, 10000, NULL)');
                
            DB::delete('delete from loan_txns where id = 223909');
            DB::update('update loans set penalty_waived = 0, penalty_collected = 5000, paid_excess = 10000, paid_amount = 1037000 where id = 99579');
            DB::update('update account_stmts set recon_status = null, loan_doc_id = null where id = 47828971');
            DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ("UGA", "UFLW-771322371-91377", NULL, 4161, NULL, 10000.00, "payment", "17760222872", "wallet_transfer", NULL, "2022-10-24 11:04:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-10-24 11:04:00", NULL, 0.00, NULL, NULL, NULL, 10000)');
                
            DB::delete("delete from loan_txns where id = 117770");
            DB::update("update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 1027000 where id = 47873");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 31951858");
            DB::INSERT("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-709254341-67486', NULL, 4161, NULL, 5000.00, 'payment', '15734769425', 'wallet_transfer', NULL, '2022-04-25 13:22:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-25 13:22:00', NULL, 0.00, NULL, NULL, 5000, NULL)");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 46510025");

            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-782881093-74584', NULL, 4161, NULL, 5000.00, 'payment', '17727185720', 'wallet_transfer', NULL, '2022-10-21 12:25:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-21 12:25:00', NULL, 0.00, NULL, NULL, 5000, NULL)");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where stmt_txn_id = '17727185720' and account_id = 4161");
            DB::update("update loans set penalty_waived = 0, penalty_collected = 5000, paid_amount = 1027000 where id = 99194");
            DB::delete("delete from loan_txns where id = 187315");

            /*Wrong transaction ID entered in loan_txns*/
            DB::update("update loan_txns set amount = '810985.00', txn_id = '14735580086', principal = '810985.00', fee = '0.00', penalty = '0.00', recon_amount = 0 where id = 91836");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'CCA-575676-14649', NULL, 3074, NULL, 216015.00, 'payment', '14735622885', 'wallet_transfer', '0', '2022-01-13 10:17:08', NULL, NULL, NULL, NULL, 0, NULL, '2022-01-13 10:17:08', NULL, 0.00, 189045, 22000, 5000, 0)");
            
            DB::update("update loan_txns set amount = '762000.00', txn_id = '14996907719', principal = '750000.00', fee = '12000.00', penalty = '0.00', recon_amount = 0 where id = 95995");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'CCA-292118-12491', NULL, 3074, NULL, 5000.00, 'payment', '14997123791', 'wallet_transfer', '0', '2022-02-10 11:10:49', NULL, NULL, NULL, NULL, 0, NULL, '2022-02-10 11:10:49', NULL, 0.00, 0, 5000, 0, 0)");
            
            DB::update("update loan_txns set amount = '305500.00', txn_id = '14713090575', principal = '305500.00', fee = '0.00', penalty = '0.00', recon_amount = 0 where id = 91487"); 
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'CCA-718903-99480', NULL, 3074, NULL, 2749500.00, 'payment', '14713114395', 'wallet_transfer', '0', '2022-01-11 10:57:46', NULL, NULL, NULL, NULL, 0, NULL, '2022-01-11 10:57:46', NULL, 0.00, 2694500, 55000, 0, 0)");
            DB::update("update account_stmts set recon_status = null, recon_desc = null, loan_doc_id = null where id in(27115240, 27115241)");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 32953962");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15920472792' where id = 123529");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 32387116");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15789683450' where id = 119715");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 32491067");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15822463770' where id = 120238");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 32727826");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15866867217' where id = 121816");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 32832172");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15898633652' where id = 122638");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 32953968");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15919693387' where id = 123525");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 33135688");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15961404499' where id = 124600");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 31351163");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15641133742' where id = 115627");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 37431060");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 33247342");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15617539892' where id = 115037");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 31292334");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15622553071' where id = 115334");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 31275037");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15619754530' where id = 115206");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 33247342");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15640820981' where id = 115603");
            
            DB::update("update loan_txns set txn_id = '14653913331' where id = 90774");
            DB::delete("delete from loan_txns where id = 133021");
            DB::update("update loan_txns set amount = '517000.00', principal = '500000.00', fee = '12000.00', penalty = '5000.00' where id = 126897");
            
            DB::delete("delete from loan_txns where id = 133020");
            DB::update("update loan_txns set amount = '517000.00', principal = '500000.00', fee = '12000.00', penalty = '5000.00' where id = 126827");
            DB::update("update loan_txns set txn_id = '16012053915', penalty = '5000.00', principal = '0.00' where id = 126796");
            
            DB::update("update loan_txns set txn_id = '16011338454', amount = '1022000.00', principal = '100000.00', fee = '22000.00', penalty = '0.00' where id = 133019");
            DB::update("update account_stmts set recon_status = null where id in (33540669, 33537429)");
            
            DB::delete("delete from loan_txns where id = 133018");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 126711");
            
            DB::delete("delete from loan_txns where id = 133016");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 126230");
            
            DB::delete("delete from loan_txns where id = 133015");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 126223");
            
            DB::delete("delete from loan_txns where id = 133014");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 126157");
            
            DB::delete("delete from loan_txns where id = 133010");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 125705");
            
            DB::delete("delete from loan_txns where id = 133009");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 125628");
            
            DB::delete("delete from loan_txns where id = 133000");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 124071");
            
            DB::delete("delete from loan_txns where id = 132991");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 122753");
            
            DB::delete("delete from loan_txns where id = 123454");
            DB::delete("delete from loan_txns where id = 132996");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 123455");
            DB::update("update loans set penalty_waived = '0.00' where id = 50268");
            
            DB::delete("delete from loan_txns where id = 132987");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 122093");
            
            DB::delete("delete from loan_txns where id = 132980");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 121091");
            
            DB::delete("delete from loan_txns where id = 132981");
            DB::update("update loan_txns set amount = '1032000.00', principal = '1000000.00', fee = '22000.00', penalty = '10000.00' where id = 121132");
            
            DB::delete("delete from loan_txns where id = 132982");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 121425");
            
            DB::delete("delete from loan_txns where id = 132983");
            DB::update("update account_stmts set recon_status = null, recon_desc = null where id = 32706458");
            
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 121722");
            DB::delete("delete from loan_txns where id = 132954");
            
            DB::update("update loan_txns set amount = '1052000.00', principal = '1000000.00', fee = '22000.00', penalty = '30000.00' where id = 116564");
            
            DB::update("update loan_txns set amount = '1022000.00', principal = '1000000.00', fee = '22000.00', penalty = '0.00', to_ac_id = 3421 where id = 116962");
            
            DB::update("update loan_txns set amount = '5000.00', principal = '0.00', fee = '0.00', penalty = '5000.00', to_ac_id = 3421, txn_id = '15701739566' where id = 132956");
            
            DB::update("update loan_txns set amount = '1022000.00', principal = '1000000.00', fee = '22000.00', penalty = '0.00', to_ac_id = 3421 where id = 115272");
            
            DB::update("update loan_txns set amount = '10000.00', principal = '0.00', fee = '0.00', penalty = '5000.00', excess = '5000.00', to_ac_id = 3421, txn_id = '15621029989' where id = 132949");
            DB::update("update loans set penalty_collected = '5000.00', paid_excess = '5000.00' where id = 46625");
            
            DB::delete("delete from loan_txns where id = 132970");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 119102");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15773741508' and account_id = 3421");
            
            DB::delete("delete from loan_txns where id = 132971");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 119105");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15773767274' and account_id = 3421");
            
            DB::delete("delete from loan_txns where id = 132973");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 119135");
            
            DB::delete("delete from loan_txns where id = 132977");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 120595");
            
            DB::delete("delete from loan_txns where id = 132951");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 115825");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15666500630' and account_id = 3421");
            
            DB::delete("delete from loan_txns where id = 132978");
            DB::update("update loan_txns set amount = '522000.00', principal = '500000.00', fee = '12000.00', penalty = '10000.00' where id = 120986");
            
            DB::delete("delete from loan_txns where id = 132988");
            DB::update("update loan_txns set amount = '517000.00', principal = '500000.00', fee = '12000.00', penalty = '5000.00' where id = 122185");
            
            DB::delete("delete from loan_txns where id = 132990");
            DB::update("update loan_txns set amount = '517000.00', principal = '500000.00', fee = '12000.00', penalty = '5000.00' where id = 122728");
            
            DB::delete("delete from loan_txns where id = 132960");
            DB::update("update loan_txns set amount = '517000.00', principal = '500000.00', fee = '12000.00', penalty = '5000.00' where id = 117644");
            
            DB::delete("delete from loan_txns where id = 132967");
            DB::update("update loan_txns set amount = '517000.00', principal = '500000.00', fee = '12000.00', penalty = '5000.00' where id = 118596");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = '15753924156' and account_id = 3421");
            
            DB::delete("delete from loan_txns where id = 124255");
            DB::delete("delete from loan_txns where id = 133002");
            DB::update("update loan_txns set amount = '540000.00', principal = '500000.00', fee = '12000.00', penalty = '28000.00' where id = 124257");
            DB::update("update loans set penalty_waived = '14000.00' where id = 48547");
            
            DB::update("update loan_txns set txn_id = '16019534025', recon_amount = 0 where id = 133020");
            DB::update("update account_stmts set recon_status = null, recon_desc = null, loan_doc_id = null where id = 33592625");
            
            DB::delete("delete from loan_txns where id = 132958");
            DB::update("update loan_txns set amount = '1552000.00', principal = '1500000.00', fee = '32000.00', penalty = '10000.00', excess = '10000.00' where id = 117146");
            DB::update("update loans set paid_excess = '10000.00', penalty_collected = '10000.00' where id = 47385");
            DB::update("update account_stmts set recon_status = null, recon_desc = null, loan_doc_id = null where stmt_txn_id = '15704800010' and account_id = 3421");
            
            DB::delete("delete from loan_txns where id = 132997");
            DB::update("update loan_txns set amount = '1542000.00', principal = '1500000.00', fee = '32000.00', penalty = '10000.00' where id = 123486");
            
            DB::delete("delete from loan_txns where id = 132953");
            DB::update("update loan_txns set amount = '517000.00', principal = '500000.00', fee = '12000.00', penalty = '5000.00' where id = 116213");
            DB::update("update account_stmts set recon_status = null, recon_desc = null, loan_doc_id = null where stmt_txn_id = '15681433754' and account_id = 3421");
            
            DB::delete("delete from loan_txns where id = 132959");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 117391");
            DB::update("update account_stmts set recon_status = null, recon_desc = null, loan_doc_id = null where stmt_txn_id = '15703401072' and account_id = 3421");
            
            DB::delete("delete from loan_txns where id = 132957");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 117078");
            
            DB::delete("delete from loan_txns where id = 132961");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 117654");
            
            DB::update("update loan_txns set amount = '1022000.00', principal = '1000000.00', fee = '22000.00', penalty = '0.00', txn_id = '15733353752', to_ac_id = 3421 where id = 132962");
            
            DB::update("update loan_txns set amount = '5000.00', principal = '0.00', fee = '0.00', penalty = '5000.00', txn_id = '15733489209', to_ac_id = 3421 where id = 117687");
            DB::update("update account_stmts set recon_status = null, recon_desc = null, loan_doc_id = null where stmt_txn_id = '15733489209' and account_id = 3421");
            
            DB::delete("delete from loan_txns where id = 132964");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 118253");
            
            DB::delete("delete from loan_txns where id = 132966");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 118334");
            
            DB::delete("delete from loan_txns where id = 132974");
            DB::update("update loan_txns set amount = '1032000.00', principal = '1000000.00', fee = '22000.00', penalty = '10000.00' where id = 119648");
            DB::update("update account_stmts set recon_status = null, recon_desc = null, loan_doc_id = null where stmt_txn_id = '15787539035' and account_id = 3421");
            
            DB::delete("delete from loan_txns where id = 132998");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 123656");
            
            DB::delete("delete from loan_txns where id = 132995");
            DB::update("update loan_txns set amount = '1047000.00', principal = '1000000.00', fee = '22000.00', penalty = '25000.00' where id = 123397");
            DB::update("update loan_txns set amount = '1000000.00', txn_id = '15643421480', principal = '1000000.00', fee = '0.00', to_ac_id = 3421 where id = 115731");
            
            DB::update("update loan_txns set txn_id = '15638319056', to_ac_id = 3421 where id = 115428");
            
            DB::update("update loan_txns set txn_id = '15734539895', to_ac_id = 3421 where id = 117759");
            
            DB::update("update loan_txns set txn_id = '15734943550', to_ac_id = 3421 where id = 117793");
            
            DB::update("update loan_txns set txn_id = '15640188184', to_ac_id = 3421 where id = 115568");
            
            DB::update("update loan_txns set txn_id = '15664684541', to_ac_id = 3421 where id = 115817");
            
            DB::update("update loan_txns set txn_id = '15787176241', to_ac_id = 3421 where id = 119642");
            
            DB::update("update loan_txns set txn_id = '15778056192', to_ac_id = 3421 where id = 119372");
            
            DB::update("update loan_txns set txn_id = '15674919972', to_ac_id = 3421 where id = 116160");
            
            DB::update("update loan_txns set txn_id = '15681247950', to_ac_id = 3421 where id = 116208");
            
            DB::update("update loan_txns set txn_id = '15702179951', to_ac_id = 3421 where id = 116986");
            DB::update("update loan_txns set txn_id = '15702969974', to_ac_id = 3421 where id = 117045");
            DB::update("update loan_txns set txn_id = '15703998313', to_ac_id = 3421 where id = 117109");
            DB::update("update loan_txns set txn_id = '15712101024', to_ac_id = 3421 where id = 117250");
            DB::update("update loan_txns set txn_id = '15716580977', to_ac_id = 3421 where id = 117537");
            DB::update("update loan_txns set txn_id = '15735162665', to_ac_id = 3421 where id = 117797");
            DB::update("update loan_txns set txn_id = '15753659121', to_ac_id = 3421 where id = 118381");
            DB::update("update loan_txns set txn_id = '15753965114', to_ac_id = 3421 where id = 118398");
            DB::update("update loan_txns set txn_id = '15754538519', to_ac_id = 3421 where id = 118472");
            DB::update("update loan_txns set txn_id = '15762950527', to_ac_id = 3421 where id = 118678");
            DB::update("update loan_txns set txn_id = '15763498525', to_ac_id = 3421 where id = 118705");
            DB::update("update loan_txns set txn_id = '15766459869', to_ac_id = 3421 where id = 118942");
            DB::update("update loan_txns set txn_id = '15773553393', to_ac_id = 3421 where id = 119077");
            DB::update("update loan_txns set txn_id = '15850753062', to_ac_id = 3421 where id = 121220");
            DB::update("update loan_txns set txn_id = '15044974906' where id = 96748");
            DB::update("update loan_txns set txn_id = '14737135659', to_ac_id = 3074 where id = 92279");
            DB::update("update loan_txns set txn_id = '15143748287' where id = 98979");
            DB::update("update loan_txns set txn_id = '15744827928' where id = 118149");
            DB::update("update loan_txns set txn_id = '16101553795' where id = 129719");
            DB::update("update loan_txns set txn_id = '15937320825' where id = 124103");

            DB::update("update loan_txns set txn_id = '15906576404', recon_amount = 0 where id = 122913");
            DB::update("update loan_txns set txn_id = '15753563151', to_ac_id = 3421 where id = 118352");
            DB::update("update loan_txns set txn_id = '14981237472' where id = 95834");
            DB::update("update loan_txns set txn_id = '15639733293' where id = 115552");
            
            DB::update("update loan_txns set txn_id = '15639745753', to_ac_id = 3421 where id = 115553");
            DB::update("update account_stmts set recon_desc = null, recon_status = null, loan_doc_id = null where stmt_txn_id = 15639745753 and account_id = 3421");

            DB::update("update loan_txns set txn_id = '14809354662' where id = 93079");
            DB::update("update loan_txns set txn_id = '14837450610' where id = 93304");
            DB::update("update loan_txns set txn_id = '14903032258' where id = 94359"); 

            DB::update("update loan_txns set txn_id = '15066223147' where id = 97160");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (32837488, 32837487)");

            DB::update("update loan_txns set txn_id = '15904622131', recon_amount = 0 where id = 122762");
            DB::update("update loan_txns set txn_id = '15904606717', recon_amount = 0 where id = 122757");
            DB::update("update loan_txns set txn_id = '14773965432' where id = 88729");


            DB::update("update loan_txns set txn_id = '15674565012' where id = 116144");
            DB::update("update loan_txns set txn_id = '15683238185' where id = 116395");
            DB::update("update loan_txns set txn_id = '15764636281' where id = 118818");
            DB::update("update loan_txns set txn_id = '15937840190' where id = 124146");
            DB::update("update loan_txns set txn_id = '16040765950' where id = 127537");
            DB::update("update loan_txns set txn_id = '16029033420' where id = 126906");


            /*Excess paid*/
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-753235300-29691', NULL, 4161, NULL, 5000.00, 'payment', '16252783324', 'wallet_transfer', NULL, '2022-06-13 11:13:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-06-13 11:13:00', NULL, 0.00, NULL, NULL, NULL, 5000)");
            DB::update("update loans set paid_excess = '5000.00' where id = 56139");
            DB::update("UPDATE account_stmts set recon_status = null, loan_doc_id = null where stmt_txn_id = '16252783324' and account_id = 4161");

            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-777470831-61291', NULL, 4161, NULL, 2000.00, 'payment', '16252276882', 'wallet_transfer', NULL, '2022-06-13 10:25:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-06-13 10:25:00', NULL, 0.00, NULL, NULL, NULL, 2000)");
            DB::update("update loans set paid_excess = '2000.00' where id = 56357");
            DB::update("UPDATE account_stmts set recon_status = null, loan_doc_id = null where stmt_txn_id = '16252276882' and account_id = 4161");

            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-773419126-78413', NULL, 4161, NULL, 5000.00, 'payment', '16582178602', 'wallet_transfer', NULL, '2022-07-13 09:49:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-07-13 09:49:00', NULL, 0.00, NULL, NULL, NULL, 5000)");
            DB::update("update loans set paid_excess = '5000.00' where id = 66442");
            DB::update("UPDATE account_stmts set recon_status = null, loan_doc_id = null where stmt_txn_id = '16582178602' and account_id = 4161");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 39341854");
            DB::INSERT("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-789628485-97934', NULL, 4161, NULL, 1.00, 'payment', '16572176326', 'wallet_transfer', NULL, '2022-07-12 11:27:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-07-12 11:27:00', NULL, 0.00, NULL, NULL, NULL, 1)");
            DB::update("update loans set paid_excess = 1 where id = 66473");

            DB::INSERT("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-774876689-47683', NULL, 4161, NULL, 512000.00, 'dup_disb_rvrsl', '17300682155', 'wallet_transfer', NULL, '2022-09-15 10:41:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-09-15 10:41:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            DB::update("update loans set paid_excess = 0 where id = 85874");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 46529484");
            
            DB::INSERT("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-771490607-36806', NULL, 4161, NULL, 512000.00, 'dup_disb_rvrsl', '17305415341', 'wallet_transfer', NULL, '2022-09-15 17:59:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-09-15 17:59:00', NULL, 0.00, NULL, NULL, NULL, 12000)");
            DB::update("update loans set paid_excess = 0 where id = 85538");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 32424578");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15818142956' where id = 119931");


            /*Wrong information in loan_txns*/
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-788006336-50184', NULL, 3421, NULL, 22000.00, 'payment', '15643454789', 'wallet_transfer', '0', '2022-04-16 15:49:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-04-16 15:49:00', NULL, 0.00, 0, 22000, 0, 0)");
            DB::update("update account_stmts set recon_status = null, recon_desc = null, loan_doc_id = null where stmt_txn_id = '15643421480' and account_id = 3421");
            DB::update("update loan_txns set amount = '1000000.00', txn_id = '15886937351', principal = '1000000.00', fee = '0.00', to_ac_id = 3421 where id = 122122");
            
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-757211591-88499', NULL, 3421, NULL, 22000.00, 'payment', '15886950262', 'wallet_transfer', '0', '2022-05-09 16:46:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-05-09 16:46:00', NULL, 0.00, 0, 22000, 0, 0)");
            DB::update("update account_stmts set recon_status = null, recon_desc = null, loan_doc_id = null where id in(32766622, 32766621)");
            DB::update("update loan_txns set amount = '1002000.00', txn_id = '15755516221', principal = '1000000.00', fee = '2000.00', to_ac_id = 3421 where id = 118551");
            
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-740315020-35660', NULL, 3421, NULL, 20000.00, 'payment', '15755541942', 'wallet_transfer', '0', '2022-04-27 13:19:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-04-27 13:19:00', NULL, 0.00, 0, 20000, 0, 0)");
            DB::update("update loan_txns set amount = '1032000.00', txn_id = '16101628104', principal = '1000000.00', fee = '32000.00', recon_amount = 0 where id = 129809");
            
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-759446935-60199', NULL, 4161, NULL, 500000.00, 'payment', '16102089427', 'wallet_transfer', '0', '2022-05-30 10:22:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-05-30 10:22:00', NULL, 0.00, 500000, 0, 0, 0)");
            DB::update("update account_stmts set recon_status = null, recon_desc = null, loan_doc_id = null where id in(34139433, 34144503)");
            DB::update("update loan_txns set txn_id = '15685391664' where id = 116533");
            
            DB::update("update loan_txns set txn_id = '15619428948', to_ac_id = 3421 where id = 115188");
            DB::update("update loan_txns set amount = '500000.00', txn_id = '15665770609', principal = '500000.00', fee = '0.00', recon_amount = 0, to_ac_id = 3421 where id = 115823");
            
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-782459588-69952', NULL, 3421, NULL, 12000.00, 'payment', '15670413343', 'wallet_transfer', '0', '2022-04-19 07:52:00', NULL, NULL, NULL, NULL, 0, NULL, ' 2022-04-19 07:52:00', NULL, 0.00, 0, 12000, 0, 0)");
            DB::update("update loan_txns set txn_id = '15628078109', to_ac_id = 3421 where id = 115357");
            DB::update("update loan_txns set txn_id = '15628064706' where id = 115356");
            DB::update("update account_stmts set recon_status = null, recon_desc = null, loan_doc_id = null where stmt_txn_id = '15628078109' and account_id = 3421");
            
            DB::delete("delete from loan_txns where id = 118301");
            DB::update("update loan_txns set txn_id = '15753237112', amount = '517000.00', principal = '500000.00' where id = 132965");
            DB::update("update loan_txns set txn_id = '14809459513', to_ac_id = '3074' where id = 258616");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 33184573");
            DB::INSERT("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-752160116-60565', 4161, 3540, NULL, 500000.00, 'duplicate_disbursal', '15971212850', 'instant_disbursal', NULL, '2022-05-17 17:13:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-05-17 17:13:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 33187088");
            DB::INSERT("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-752160116-60565', NULL, 4161, NULL, 500000.00, 'dup_disb_rvrsl', '15971449618', 'wallet_transfer', NULL, '2022-05-17 17:34:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-05-17 17:34:00', NULL, 0.00, NULL, NULL, NULL, NULL)");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 32839564");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15904896658' where id = 122806");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 32839563");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15904910052' where id = 122812");
        
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 31366627");
            DB::delete("delete from loan_txns where id = 115769");
            DB::update("update loan_txns set amount = 517000, principal = 500000, fee = 12000, penalty = 5000, txn_id = '15644562492' where id = 132950");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 31364000");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15644243645' where id = 115756");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 31652805");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15690976761' where id = 116569");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 31933094");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15732523046' where id = 117603");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 31933908");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15732654421' where id = 117624");
            
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 31950897");
            DB::update("update loan_txns set recon_amount = 0, txn_id = '15734677700' where id = 117774");

            /*Wrong txn record updated*/
            DB::delete("delete from loan_txns where id = 119422");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-753830221-33552', NULL, 4161, NULL, 122000.00, 'payment', '15784273770', 'wallet_transfer', '0', '2022-04-30 09:08:00', NULL, NULL, NULL, NULL, 0, NULL, '2022-04-30 09:08:00', NULL, 0.00, 122000, 0, 0, 0)");
            DB::update("update loan_txns set recon_amount = 0, amount = '900000.00', principal = '680000.00', fee = '22000.00' where loan_doc_id = 'UFLW-753830221-33552' and txn_id = '15784290079'");


            /*Re run*/ 
            $data = [["acc_prvdr_code" => 'UMTN', "id" => 3074], ["acc_prvdr_code" => "UMTN", "id" => 4161], ["acc_prvdr_code" => 'UMTN', "id" => 3421]];
            UGARecon::run($data);
            

            /*Break payment record for 810986*/
            $serv->breakup_payment_record($loan_txn_repo, $acc_stmt_repo, '3421');


            /*wrong account selected - 810986*/
            DB::update("update account_stmts a, loan_txns t set t.from_ac_id = account_id, recon_status = null, t.recon_amount = null, a.loan_doc_id = null where txn_id = stmt_txn_id and date(a.stmt_txn_date) >= '2022-01-01' and stmt_txn_type = 'debit' and account_id = 3421 and account_id != from_ac_id");


            /*Internal Transfer*/
            DB::update("update account_stmts set acc_txn_type = 'int_transfer_dr', recon_status = '80_recon_done' where stmt_txn_id in (15673597144, 15684338863, 15644661783, 15636285428, 15693788178, 15755357527, 15818945920, 15765090907, 15714562264, 15564956352, 15704303667, 15733682536, 15505025824, 15743072618, 15839355479, 15915742878, 15864540381, 15786261290, 15906062202, 15745527107, 15851464601, 15829149124, 15583229725, 15777004581, 15894250877, 15773502199, 15969042223, 15436298702, 15492011475, 15605928879, 15596059722, 15840783259, 15978952597, 16008006857, 15884464709, 15997343147, 15451902240, 15620743670, 15298487960, 15816650027, 15955663609, 15367666908, 15628617740, 16418118416, 15924460640, 15895294897, 15887717931, 15926424619, 15830656712, 16063089931, 15595150709, 15982156163, 15842884134, 15530935942, 16023591483, 16186734468, 16121743998, 15832084799) and account_id = 3421");
            DB::update("update account_stmts set acc_txn_type = 'int_transfer_cr', recon_status = '80_recon_done' where account_id in (4161, 3074, 3728) and stmt_txn_id in (15673597144, 15684338863, 15644661783, 15636285428, 15693788178, 15755357527, 15818945920, 15765090907, 15714562264, 15564956352, 15704303667, 15733682536, 15505025824, 15743072618, 15839355479, 15915742878, 15864540381, 15786261290, 15906062202, 15745527107, 15851464601, 15829149124, 15583229725, 15777004581, 15894250877, 15773502199, 15969042223, 15436298702, 15492011475, 15605928879, 15596059722, 15840783259, 15978952597, 16008006857, 15884464709, 15997343147, 15451902240, 15620743670, 15298487960, 15816650027, 15955663609, 15367666908, 15628617740, 16418118416, 15924460640, 15895294897, 15887717931, 15926424619, 15830656712, 16063089931, 15595150709, 15982156163, 15842884134, 15530935942, 16023591483, 16186734468, 16121743998, 15832084799)");

            DB::update("update account_stmts set acc_txn_type = 'int_transfer_cr', recon_status = '80_recon_done' where stmt_txn_id in (15148267438) and account_id = 3421");
            DB::update("update account_stmts set acc_txn_type = 'int_transfer_dr', recon_status = '80_recon_done' where account_id in (4161, 3074, 3728) and stmt_txn_id in (15148267438)");

            
            /*Investment*/
            DB::update("update account_stmts set acc_txn_type = 'investment', recon_status = '80_recon_done' where stmt_txn_id in (14671732802, 15172248795, 14930846730, 15006744378, 14846279303, 15100918136, 14800945275, 14704922340, 14874606683, 15044486698) and account_id = 3421");


            /*wrong txn id entered - 810986*/
            DB::update("update loan_txns set txn_id = '14635213104', to_ac_id = 3421 where id = 90374");
            
            DB::update("update loan_txns set txn_id = '14662874977' where id = 90939");
            
            DB::update("update loan_txns set txn_id = '14664126155' where id = 90981");
            
            DB::update("update loan_txns set txn_id = '14664592282' where id = 90994");
            
            DB::update("update loan_txns set txn_id = '14674263629' where id = 91135");
            
            DB::update("update loan_txns set txn_id = '14725465968' where id = 91735");
            
            DB::update("update loan_txns set txn_id = '14727445493' where id = 91799");
            
            DB::update("update loan_txns set txn_id = '14748513295' where id = 92195");
            
            DB::update("update loan_txns set txn_id = '14771785017' where id = 92262");
            
            DB::update("update loan_txns set txn_id = '14846420518' where id = 93477");
            
            DB::delete("delete from loan_txns where id = 132927");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 111724");

            DB::delete("delete from loan_txns where id = 132929");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 111992");

            DB::delete("delete from loan_txns where id = 132931");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 112697");

            DB::delete("delete from loan_txns where id = 132933");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 113114");

            DB::delete("delete from loan_txns where id = 132934");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 113130");

            DB::delete("delete from loan_txns where id = 132943");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 114192");

            DB::delete("delete from loan_txns where id = 132937");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 113289");

            DB::delete("delete from loan_txns where id = 132946");
            DB::update("update loan_txns set amount = '1037000.00', principal = '1000000.00', fee = '22000.00', penalty = '15000.00' where id = 114462");

            DB::delete("delete from loan_txns where id = 132947");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 114749");

            DB::delete("delete from loan_txns where id = 132948");
            DB::update("update loan_txns set amount = '1027000.00', principal = '1000000.00', fee = '22000.00', penalty = '5000.00' where id = 114781");

            DB::delete("delete from loan_txns where id = 132935");
            DB::update("update loan_txns set amount = '517000.00', principal = '500000.00', fee = '12000.00', penalty = '5000.00' where id = 113236");

            DB::delete("delete from loan_txns where id = 132936");
            DB::update("update loan_txns set amount = '517000.00', principal = '500000.00', fee = '12000.00', penalty = '5000.00' where id = 113258");

            DB::delete("delete from loan_txns where id = 132930");
            DB::update("update loan_txns set amount = '1022000.00', principal = '1000000.00', fee = '22000.00', penalty = '0.00', txn_id = '15523707386', recon_amount = '0.00' where id = 112521");
            DB::update("update loan_txns set amount = '5000.00', principal = '0.00', fee = '0.00', penalty = '5000.00' where id = 259056");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null, recon_desc = null where id = 30915077");

            DB::update("update loan_txns set txn_id = '15597143210' where id = 114491");

            DB::update("update loan_txns set txn_id = '15587292580' where id = 114211");

            DB::update("update loan_txns set txn_id = '15586351217' where id = 114128");

            DB::update("update loan_txns set txn_id = '15585877218' where id = 114072");

            DB::update("update loan_txns set txn_id = '15566123332' where id = 113851");

            DB::update("update loan_txns set txn_id = '15557243632' where id = 113646");

            DB::update("update loan_txns set txn_id = '15556780284' where id = 113631");

            DB::update("update loan_txns set txn_id = '15555576919' where id = 113517");

            DB::update("update loan_txns set txn_id = '15555527597' where id = 113513");

            DB::update("update loan_txns set txn_id = '15555388096' where id = 113498");

            DB::update("update loan_txns set txn_id = '15555084126' where id = 113473");

            DB::update("update loan_txns set txn_id = '15544518574' where id = 113109");

            DB::update("update loan_txns set txn_id = '15526079595' where id = 112665");

            DB::update("update loan_txns set txn_id = '15526679258' where id = 112699");

            DB::update("update loan_txns set txn_id = '15527138185' where id = 112733");

            DB::update("update loan_txns set txn_id = '15481584642' where id = 111560");
            DB::update("update account_stmts set loan_doc_id = null, recon_desc = null, recon_status = null  where id = 30735371");

            DB::update("update loan_txns set txn_id = '15492780703' where id = 111990");

            DB::update("update loan_txns set txn_id = '15494727281' where id = 112081");

            DB::update("update loan_txns set txn_id = '15481567307' where id = 111558");

            DB::update("update loan_txns set txn_id = '15513225047' where id = 112191");

            DB::update("update loan_txns set txn_id = '15513393669' where id = 112216");

            DB::update("update loan_txns set txn_id = '15514653754' where id = 112312");

            DB::update("update loan_txns set txn_id = '14674753946' where id = 91159");

            DB::update("update loan_txns set txn_id = '14702181045' where id = 91344");

            DB::update("update loan_txns set txn_id = '14774306645' where id = 92353");

            DB::update("update loan_txns set txn_id = '15177565611' where id = 99177");

            DB::update("update loan_txns set txn_id = '15179671520' where id = 99298");

            DB::update("update loan_txns set txn_id = '15191568593' where id = 99507");

            DB::update("update loan_txns set txn_id = '15200876198' where id = 99792");

            DB::update("update loan_txns set txn_id = '15207903396' where id = 99894");

            DB::update("update loan_txns set txn_id = '15208149602' where id = 99918");

            DB::update("update loan_txns set txn_id = '15218024065' where id = 100331");

            DB::update("update loan_txns set txn_id = '15241625581' where id = 100500");

            DB::update("update loan_txns set txn_id = '15260407231' where id = 100709");

            DB::update("update loan_txns set txn_id = '15473452065' where id = 111409");

            DB::update("update loan_txns set txn_id = '15472970162' where id = 111362");

            DB::update("update loan_txns set txn_id = '15471520003' where id = 111244");

            DB::update("update loan_txns set txn_id = '15465553750' where id = 111211");

            DB::update("update loan_txns set txn_id = '15465407437' where id = 111203");

            DB::update("update loan_txns set txn_id = '15453670577' where id = 110892");

            DB::update("update loan_txns set txn_id = '15454845097' where id = 110889");

            DB::update("update loan_txns set txn_id = '15453575964' where id = 110833");

            DB::update("update loan_txns set txn_id = '15453052646' where id = 110783");

            DB::update("update loan_txns set txn_id = '15413485383' where id = 109794");

            DB::update("update loan_txns set txn_id = '15413670440' where id = 109797");

            DB::update("update loan_txns set txn_id = '15413791776' where id = 109810");

            DB::update("update loan_txns set txn_id = '15413959561' where id = 109826");

            DB::update("update loan_txns set txn_id = '15414252374' where id = 109854");

            DB::update("update loan_txns set txn_id = '15414387334' where id = 109869");

            DB::update("update loan_txns set txn_id = '15415380682' where id = 109981");

            DB::update("update loan_txns set txn_id = '15307320684' where id = 101643");

            DB::update("update loan_txns set txn_id = '15308378727' where id = 101749");

            DB::update("update loan_txns set txn_id = '15317202642' where id = 101924");

            DB::update("update loan_txns set txn_id = '15327465109' where id = 102126");

            DB::update("update loan_txns set txn_id = '15329302467' where id = 102248");

            DB::update("update loan_txns set txn_id = '15338077469' where id = 102441");

            DB::update("update loan_txns set txn_id = '15338485500' where id = 102470");

            DB::update("update loan_txns set txn_id = '15346280474' where id = 102622");

            DB::update("update loan_txns set txn_id = '15346460237' where id = 102639");

            DB::update("update loan_txns set txn_id = '15346535985' where id = 102651");

            DB::update("update loan_txns set txn_id = '14921013395' where id = 94642");

            DB::update("update loan_txns set txn_id = '14923813386' where id = 94745");

            DB::update("update loan_txns set txn_id = '14938812980' where id = 95018");

            DB::update("update loan_txns set txn_id = '14939867476' where id = 95058");

            DB::update("update loan_txns set txn_id = '14941255671' where id = 95143");

            DB::update("update loan_txns set txn_id = '14949008564' where id = 95222");

            DB::update("update loan_txns set txn_id = '14999447690' where id = 96125");

            DB::update("update loan_txns set txn_id = '14999904423' where id = 96143");

            DB::update("update loan_txns set txn_id = '15015533924' where id = 96415");

            DB::update("update loan_txns set txn_id = '15034984541' where id = 96574");

            DB::update("update loan_txns set txn_id = '15062563322' where id = 96898");

            DB::update("update loan_txns set txn_id = '15072672251' where id = 97266");

            DB::update("update loan_txns set txn_id = '15075235789' where id = 97427");

            DB::update("update loan_txns set txn_id = '15083659723' where id = 97572");

            DB::update("update loan_txns set txn_id = '15085029831' where id = 97609");

            DB::update("update loan_txns set txn_id = '15098829177' where id = 97635");

            DB::update("update loan_txns set txn_id = '15101104489' where id = 97691");

            DB::update("update loan_txns set txn_id = '15102603016' where id = 97744");

            DB::update("update loan_txns set txn_id = '15112691111' where id = 97955");

            DB::update("update loan_txns set txn_id = '15113762566' where id = 97987");

            DB::update("update loan_txns set txn_id = '15114428388' where id = 98012");

            DB::update("update loan_txns set txn_id = '15149326877' where id = 98861");

            DB::update("update loan_txns set txn_id = '15147896941' where id = 98783");

            DB::update("update loan_txns set txn_id = '15139607087' where id = 98608");

            DB::update("update loan_txns set txn_id = '15138763790' where id = 98578");

            DB::update("update loan_txns set txn_id = '15138810071', recon_amount = 0 where id = 98575");
            DB::update("update account_stmts set loan_doc_id = null, recon_status = null, recon_desc = null where id in (28757485, 28629997)");
            DB::update("update loan_txns set txn_id = '15138818838', recon_amount = 0 where id = 98541");

            DB::update("update loan_txns set txn_id = '15129245072' where id = 98207");

            DB::update("update loan_txns set txn_id = '15129299539' where id = 98212");

            DB::update("update loan_txns set txn_id = '15138513419' where id = 98499");

            DB::update("update loan_txns set txn_id = '15138748495' where id = 98523");

            DB::update("update loan_txns set txn_id = '15121231082', amount = '1000000.00', principal = '1000000.00', fee = '0.00', recon_amount = 0 where id = 98096");
            DB::update("update loan_txns set txn_id = '15121257378', amount = '22000.00', principal = '0.00', fee = '22000.00', recon_amount = 0 where txn_id = '15121257378' and loan_doc_id = 'UFLW-752153701-43830'");

            DB::update("update loan_txns set txn_id = '15085484855', recon_amount = 0, amount = '1022000.00', principal = '1000000.00', fee = '22000.00' where id = 97624");
            
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-788990561-11591', NULL, 3421, NULL, 5000.00, 'payment', '15085611025', 'wallet_transfer', NULL, '2022-02-19 16:05:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-02-19 16:05:00', NULL, 0.00, NULL, NULL, 5000, NULL)");

            DB::update("update loan_txns set txn_id = '15394690274' where id = 109206");

            DB::update("update loan_txns set txn_id = '15398163128' where id = 109427");

            DB::update("update loan_txns set txn_id = '15398352385' where id = 109438");

            DB::update("update loan_txns set txn_id = '15405724855', recon_amount = 0 where id = 109586");

            DB::update("update loan_txns set txn_id = '15410022420' where id = 109786");

            DB::update("update loan_txns set txn_id = '15415757368' where id = 110022");

            DB::update("update loan_txns set txn_id = '15441792445' where id = 110370");

            DB::update("update loan_txns set txn_id = '15452148412' where id = 110733");

            DB::update("update loan_txns set txn_id = '15385693779' where id = 103479");

            DB::update("update loan_txns set txn_id = '15386196363' where id = 103505");

            DB::update("update loan_txns set txn_id = '15389501087' where id = 103665");

            DB::update("update loan_txns set txn_id = '15389640791' where id = 103668");

            DB::update("update loan_txns set txn_id = '15346864651' where id = 102716");

            DB::update("update loan_txns set txn_id = '15347326103' where id = 102761");

            DB::update("update loan_txns set txn_id = '15376963687' where id = 103282");

            DB::update("update loan_txns set txn_id = '15289874480' where id = 101513");

            DB::update("update loan_txns set txn_id = '14903332894' where id = 94368");

            DB::update("update loan_txns set txn_id = '14884871569' where id = 94196");

            DB::update("update loan_txns set txn_id = '14862184796' where id = 93706");

            DB::update("update loan_txns set txn_id = '14847354039' where id = 93544");

            DB::update("update loan_txns set txn_id = '14792305678' where id = 92731");

            DB::update("update loan_txns set txn_id = '14791769373' where id = 92706");
            DB::update("update account_stmts set recon_status = NULL, recon_desc = NULL, loan_doc_id = NULL where stmt_txn_id = '14791769373' and account_id = '3421'");

            DB::update("update loan_txns set txn_id = '14785854484' where id = 92652");

            DB::update("update loan_txns set txn_id = '14774899477' where id = 92384");

            DB::update("update loan_txns set txn_id = '14791755691' where id = 92701");

            DB::update("update loan_txns set txn_id = '14723582601' where id = 91638");

            DB::update("update loan_txns set txn_id = '14737486245' where id = 91936");

            DB::update("update loan_txns set txn_id = '14776409677' where id = 92425");

            DB::update("update loan_txns set txn_id = '14808037272' where id = 93029");

            DB::update("update loan_txns set txn_id = '15690920553', amount = '1000000.00', principal = '1000000.00', fee = '0.00', recon_amount = 0 where id = 116569");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-777702263-44291', NULL, 4161, NULL, 22000.00, 'payment', '15690976761', 'wallet_transfer', NULL, '2022-04-21 08:13:30', NULL, NULL, NULL, NULL, NULL, NULL, '2022-04-21 08:13:30', NULL, 0.00, NULL, 22000, NULL, NULL)");

            DB::update("update loan_txns set txn_id = '15268503275' where id = 100806");

            DB::update("update loan_txns set txn_id = '15268641235' where id = 100816");

            DB::update("update loan_txns set txn_id = '15268728187' where id = 100819");

            DB::update("update loan_txns set txn_id = '15269391618' where id = 100858");

            DB::update("update loan_txns set txn_id = '15279474059' where id = 101243");
            DB::update("update account_stmts set loan_doc_id = null, recon_desc = null, recon_status = null where stmt_txn_id = '15279474059' and account_id = 3421");

            DB::update("update loan_txns set txn_id = '15281044358', recon_amount = 0 where id = 101335");
            DB::update("update account_stmts set loan_doc_id = null, recon_desc = null, recon_status = null where stmt_txn_id = '15281044358' and account_id = 3421");

            DB::update("update loan_txns set txn_id = '15281034440', recon_amount = 0 where id = 101334");
            DB::update("update account_stmts set loan_doc_id = null, recon_desc = null, recon_status = null where stmt_txn_id = '15281034440' and account_id = 3421");

            DB::update("update loan_txns set txn_id = '15528727799', recon_amount = 0 where id = 112767");
            DB::update("update account_stmts set loan_doc_id = null, recon_desc = null, recon_status = null where stmt_txn_id = '15528727799' and account_id = 3421");

            DB::update("update loan_txns set txn_id = '15279474044' where id = 101204");
            DB::update("update account_stmts set loan_doc_id = null, recon_desc = null, recon_status = null where stmt_txn_id = '15279474044' and account_id = 3421");

            DB::update("update loan_txns set txn_id = '15513302115' where id = 112203");

            DB::update("update loan_txns set txn_id = '15526965762' where id = 112715");

            DB::update("update loan_txns set txn_id = '15535800071' where id = 112929");

            DB::update("update loan_txns set txn_id = '15544944428' where id = 113141");

            DB::update("update loan_txns set txn_id = '15377554230' where id = 103325");

            DB::update("update loan_txns set txn_id = '15261446462', amount = '1536000.00', principal = '1500000.00', fee = '32000.00', excess = '4000.00' where id = 100753");
            DB::update("update loans set paid_excess = '4000.00' where loan_doc_id = 'UFLW-785714275-49003'");
            DB::update("update account_stmts set loan_doc_id = null, recon_desc = null, recon_status = null where stmt_txn_id = '15261446462' and account_id = 3421");

            DB::update("update loan_txns set txn_id = '15197815391' where id = 99588");

            DB::update("update loan_txns set txn_id = '14848480574' where id = 93600");

            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLO-252225-27476', NULL, 3421, NULL, 5000.00, 'payment', '14987083816', 'wallet_transfer', NULL, '2022-02-09 10:03:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-02-09 10:03:00', NULL, 0.00, NULL, NULL, 5000, NULL)");
            DB::update("update account_stmts set loan_doc_id = NULL, recon_status = NULL, recon_desc = NULL where id = 27927116");

            DB::update("update loan_txns set amount = '1022000.00' where id = 110588");

            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-788239129-36026', NULL, 3421, NULL, 100000.00, 'payment', '15458960301', 'wallet_transfer', NULL, '2022-03-29 19:45:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-29 19:45:00', NULL, 0.00, '100000.00', NULL, NULL, NULL)");

            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLO-359880-53760', NULL, 3421, NULL, 5000.00, 'payment', '14711316583', 'wallet_transfer', NULL, '2022-01-11 08:22:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-01-11 08:22:00', NULL, 0.00, NULL, NULL, 5000, NULL)");
            DB::update("update account_stmts set loan_doc_id = NULL, recon_desc = NULL, recon_status = NULL where  id = 27110388");


            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLO-768280-62990', NULL, 3421, NULL, 5000.00, 'payment', '14724009864', 'wallet_transfer', NULL, '2022-01-12 09:50:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-01-12 09:50:00', NULL, 0.00, NULL, NULL, 5000, NULL)");
            DB::update("update account_stmts set loan_doc_id = NULL, recon_desc = NULL, recon_status = NULL where  id = 27138645");

            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-772912683-19545', NULL, 3421, NULL, 5000.00, 'payment', '15259240855', 'wallet_transfer', NULL, '2022-03-09 10:31:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-09 10:31:00', NULL, 0.00, NULL, NULL, 5000, NULL)");
            DB::update("update account_stmts set loan_doc_id = NULL, recon_desc = NULL, recon_status = NULL where  id = 29530640");


            DB::update("update loan_txns set recon_amount = '0.00', fee = '5400.00' where txn_id = '14988245351' and loan_doc_id = 'UFLO-378623-85725' and to_ac_id = 3421");
            DB::update("update loan_txns set recon_amount = '0.00', fee = '6000.00', principal = '25000.00' where txn_id = '14988234982' and to_ac_id = 3421");
            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLO-378623-85725', NULL, 3421, NULL, 225000.00, 'payment', '14988345035', 'wallet_transfer', NULL, '2022-02-09 12:27:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-02-09 12:27:00', NULL, 0.00, '225000.00', NULL, NULL, NULL)");

            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLW-759503503-10109', NULL, 3421, NULL, 1000000.00, 'payment', '15199888297', 'wallet_transfer', NULL, '2022-03-03 13:39:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-03 13:39:00', NULL, 0.00, '1000000.00', NULL, NULL, NULL)");

            DB::insert("INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES ('UGA', 'UFLO-989573-30900', NULL, 3421, NULL, 12000.00, 'payment', '14979760920', 'wallet_transfer', NULL, '2022-02-08 13:55:00', NULL, NULL, NULL, NULL, NULL, NULL, '2022-02-08 13:55:00', NULL, 0.00, NULL, '12000.00', NULL, NULL)");

            DB::update("update account_stmts set loan_doc_id = NULL, recon_desc = NULL, recon_status = NULL where  stmt_txn_id = '15545633115' and account_id = 3421");
            DB::update("update loan_txns set txn_id = '15545683218', recon_amount = 0, amount = '2037000.00', principal = ' 2000000.00', fee = '37000.00' where loan_doc_id = 'UFLW-787963376-17803' and txn_id = '15545633115' and to_ac_id = 3421");
            DB::update("update loan_txns set recon_amount = 0, amount = '1000.00', principal = ' 0.00', fee = '1000.00' where loan_doc_id = 'UFLW-787963376-17803' and txn_id = '15545858856' and to_ac_id = 3421");

            
            /*Re run*/
            $data = [["acc_prvdr_code" => 'UMTN', "id" => 3421]];
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
