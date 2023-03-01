<?php

use App\Repositories\SQL\CapitalFundRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Services\RepaymentService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrationForDataFix extends Migration
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

            //Terminal Financing FA (Insert Missing Loan Txns)
            set_app_session('UGA');
            $loan_repo = new LoanRepositorySQL();
            DB::statement("DELETE from loan_txns where id = '134063'"); //Incorrect Data

            $loan_doc_id = 'UFLW-789651743-18693';
            $acc_number = '89435481';
            $fund_code = 'VC-JUN21-EUR';

            (new CapitalFundRepositorySQL)->increment_by_code('os_amount', $fund_code, '117348');
            $loan_repo->update_model_by_code(['loan_doc_id' => $loan_doc_id, 'current_os_amount' => '1022190', 'paid_amount' => '257810', 'paid_excess' => '0', 'paid_fee' => '0', 'paid_principal' => '257810']);
            
            $tf_loan = $loan_repo->find_by_code($loan_doc_id, ['acc_number','cust_acc_id','loan_doc_id','cust_id','disbursal_date']);
    
            $tf_transactions = DB::select("SELECT stmt_txn_date, cr_amt from tf_repay_txn_imports where from_acc_num = ? and date(created_at) >= ?",[$acc_number, '2022-06-06']);

            if($tf_transactions){

                foreach($tf_transactions as $tf_transaction){
										
                    $txn_id = $acc_number."-".(strtotime($tf_transaction->stmt_txn_date));
                    
                    $tf_loan->amount = $tf_transaction->cr_amt;
                    $tf_loan->from_ac_id = $tf_loan->cust_acc_id;
                    $tf_loan->to_ac_id = '3973';    // TF Repayment Account
                    $tf_loan->txn_mode = 'daily_deduction';
                    $tf_loan->txn_id = $txn_id;
                    $tf_loan->txn_date = $tf_transaction->stmt_txn_date;
                    $tf_loan->is_part_payment = true;
                    $tf_loan->send_sms = false;
                    
                    (new RepaymentService)->capture_repayment((array)$tf_loan);
                }

            }

            //Duplicate Disbursal Txn
            DB::delete("DELETE from loan_txns where id = 140521");

            //Change Status to paid_to_different_acc
            $acc_stmt_ids = "'35861040', '34899929'";
            DB::update("update account_stmts set recon_status = ?, updated_at = ? where id in ($acc_stmt_ids)", ['31_paid_to_different_acc', now()]);


            //Missing Loan Txns (Partial Payments)
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`) VALUES ('UGA','UFLW-754369120-60874',NULL,'4161',NULL,'799511.00','payment','16028906032','wallet_portal','0','2022-05-23 09:15:00',NULL,NULL,NULL,'0',NULL,'2022-08-12 00:00:00',NULL,0)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`) VALUES ('UGA','UFLW-771377595-44973',NULL,'4161',NULL,'942000.00', 'payment','16050172691','wallet_transfer','0','2022-05-25 00:00:00',NULL,NULL,NULL,'0',NULL,'2022-10-12 00:00:00',NULL,0)");


            //Incorect Data in Loan Txns
            DB::update("UPDATE loan_txns set txn_id = ?, amount = ?, updated_at = ? where id = ?", ['16028990798', '222489.00', now(), '126900']);
            DB::update("UPDATE loan_txns set txn_id = ?, amount = ?, updated_at = ? where id = ?", ['16052473747', '80000.00', now(), '128143']);
            DB::update("UPDATE loan_txns set txn_id = ?, updated_at = ? where id = ?", ['15132269677', now(), '98431']);

            DB::update("UPDATE account_stmts set recon_status = '80_recon_done', loan_doc_id = 'UFLW-787016067-82369' where id = '28710876'");
            DB::update("UPDATE account_stmts set recon_status = '80_recon_done', loan_doc_id = 'UFLW-787884204-40829' where id = '28710548'");


            //Missing Loan Txns for Voided Loan
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`) VALUES ('UGA','UFLW-787948196-87442','4161',NULL,NULL,'1500000.00', 'disbursal','16072868783','instant_disbursal','0','2022-05-27 00:00:00',NULL,NULL,NULL,'0',NULL,'2022-08-12 00:00:00',NULL,0)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`) VALUES ('UGA','UFLW-787948196-87442',NULL,'4161',NULL,'1500000.00', 'disbursal_reversal','16072990067','wallet_transfer','0','2022-05-27 00:00:00',NULL,NULL,NULL,'0',NULL,'2022-08-12 00:00:00',NULL,0)");
            DB::update("UPDATE account_stmts set recon_status = ?, loan_doc_id = ?, updated_at = ? where stmt_txn_id = ?", ['80_recon_done', 'UFLW-787948196-87442', now(), '16072990067']);


            //Terminal Financing FAs
            $tf_fa_txn_ids = "'113586147','113401108','113326583','113249137','113025907','113017974','112566645','112477376','112441969','112192471','111926732','111827568','111677106','111567624','111204315','111078463'";
            DB::update("UPDATE account_stmts set recon_status = null, loan_doc_id = null where stmt_txn_id in ($tf_fa_txn_ids)"); 


            //Incorrect Account choosed
            DB::update("UPDATE loan_txns set to_ac_id = ?, updated_at = ? where id = ?", [1783, now(), '126122']);
            DB::update("UPDATE loan_txns set to_ac_id = ?, updated_at = ? where id = ?", [3421, now(), '120952']);


            //Need rerun
            $acc_stmt_ids = "'30326262', '30326041', '33596801', '33782286', '28555877', '29342579', '29447567', '29787405', '30327624', '30468678', '30965192', '33787243', '34358222', '34358220', '35315876', '35315877', '33843393', '33843391', '33417913', '33417914', '32567492'";
            DB::update("UPDATE account_stmts set recon_status = null, loan_doc_id = null where id in ($acc_stmt_ids)");
            
            DB::commit();
        }
        catch(\Exception $ex){
            DB::rollBack();
            throw $ex;
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
