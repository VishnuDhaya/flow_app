<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
Use App\Scripts\php\CCAResetLoanTxns;
use App\Scripts\php\UGARecon;

class CcaBreakupLoanTxnsRecords extends Migration
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

            /*Incorrect Date Captured*/
            DB::update("update account_stmts a, loan_txns t set recon_status = '80_recon_done', txn_date = stmt_txn_date where stmt_txn_id = txn_id and txn_id in ('BWJternz', '7UXbpBl8', '2SkSWGSP', 'cffJ1EGE', 'WGrPb3Zq', 'V55D5BiU', 'r2VS1IKT', 'JtfyU3L7', 'K7G4MrnL')");

            DB::update("update loan_txns set txn_date = '2021-08-23 18:32:47' where id = 78282");

            /*Investment*/
            DB::update("update account_stmts set stmt_txn_type = 'credit', cr_amt = amount, recon_status = '80_recon_done', acc_txn_type = 'investment' where  descr regexp 'Merchant bank deposit' and account_id = 1783 and EXTRACT(YEAR_MONTH from stmt_txn_date) >= '202201' and stmt_txn_type != 'debit'");


            /*Redemption*/
            DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'redemption' where descr regexp 'Merchant bank deposit' and account_id = 1783 and EXTRACT(YEAR_MONTH from stmt_txn_date) >= '202201' and stmt_txn_type = 'debit'");


            /*Incorrect Txn */
            DB::update("update loan_txns set txn_id = 'bu65U3Rm', recon_amount = 0 where id = 91224");
            DB::update("update loan_txns set txn_id = 'IWiIheDj', recon_amount = 0 where id = 93619");
            DB::update("update loan_txns set txn_id = 'lWfWJ3Mg', recon_amount = 0  where id = 93616");
            DB::update("update loan_txns set txn_id = 'l8hqQ8pb', recon_amount = 0  where id = 93874");
            DB::update("update loan_txns set txn_id = 'QNd2tzIz', recon_amount = 0  where id = 93916");
            DB::update("update loan_txns set txn_id = 'kWmlRFWX', recon_amount = 0  where id = 138104");
            DB::update("update loan_txns set txn_id = 'xDPMpQ5M', recon_amount = 0 where id = 97761");
            DB::update("update loan_txns set txn_id = 'jSUJwAGl', recon_amount = 0 where id = 93809");
            DB::update("update loan_txns set txn_id = 'fRl3ADgz', recon_amount = 0 where id = 103301");
            DB::update("update loan_txns set txn_id = 'iIspzYyD', recon_amount = 0 where id = 94088");
            DB::update("update loan_txns set txn_id = 't8VlX6XI', amount = '1032000', recon_amount = 0, principal = '1032000', fee = 0, penalty = 0 where id = 93071");
            DB::update("update loan_txns set txn_id = 'lv6CH3Qs', recon_amount = 0 where id = 168526");
            DB::update("update loan_txns set txn_id = 'ufFsIitt', recon_amount = 0 where id = 92984");
            DB::update("update loan_txns set txn_id = 'I3gf4fIi', recon_amount = 0 where id = 93436");
            DB::update("update loan_txns set txn_id = 'y1EIn96M', recon_amount = 0 where id = 92840");
            DB::update("update loan_txns set txn_id = '6lEPzHBn', recon_amount = 0 where id = 93390");
            DB::update("update loan_txns set txn_id = 'CC-1663257307-SHARE-256-7483337', recon_amount = 0 where id = 193660");

            //27422617, 48474868, 48474870, 48474829, 48474903, 48474830, 193660
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (30236867, 26869575, 27422587, 27422581, 35869953, 31544902, 27422594, 27422563, 44413330, 46523655)");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where stmt_txn_id in ('lWfWJ3Mg', 'IWiIheDj', 'I3gf4fIi', '6lEPzHBn', 't8VlX6XI', 'ufFsIitt', 'y1EIn96M')");

            /*Duplicate Payment/Reversal */
            DB::update("update loan_txns set txn_id = '15680968118', to_ac_id = 4094 where id = 116217");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (31540179, 31544902)");

            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-756255062-14782',NULL,'1783',NULL,'512000.00','duplicate_payment','7VZUcqLn','wallet_transfer',NULL,'2022-04-20 09:53:00',NULL,NULL,NULL,NULL,'10',NULL,'2022-04-20 09:53:00',NULL,'0.00',NULL,NULL,NULL,NULL)");

            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-756255062-14782', '1783',NULL,NULL,'512000.00','duplicate_payment_reversal','utHDJtUV','wallet_transfer',NULL,'2022-04-20 11:03:00',NULL,NULL,NULL,NULL,'10',NULL,'2022-04-20 11:03:00',NULL,'0.00',NULL,NULL,NULL,NULL)");

            /*Single Repayment but captured twice*/
            DB::update("update loan_txns set recon_amount = 0, amount = 517000, principal = 500000, fee = 12000, penalty = 5000 where id = 121840");
            DB::statement("delete from loan_txns where id = 132984");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 32727568");

            DB::update("update loan_txns set recon_amount = 0, amount = 1027000, principal = 1000000, fee = 22000, penalty = 5000 where id = 120334");
            DB::statement("delete from loan_txns where id = 132976");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 32494385");

            DB::update("update loan_txns set recon_amount = 0, amount = 517000, principal = 500000, fee = 12000, penalty = 5000 where id = 119721");
            DB::statement("delete from loan_txns where id = 132975");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id = 32372316");


            /* Test amount transferred*/
            DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'test' where id in (47590350, 47590598, 47591470, 47591937, 47592864, 47613406, 47613833, 47614256, 27490799)");


            /* Duplicate Disbursal Capture*/
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (28522796)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-781882719-36774','1783','2016',NULL,'500000.00','duplicate_disbursal','xbR3CVFi','instant_disbursal',NULL,'2022-02-21 13:45:32',NULL,NULL,NULL,NULL,'0',NULL,'2022-02-21 13:45:32',NULL,'0.00',NULL,NULL,NULL,NULL)");

            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (34262307)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-755024885-63980','1783','1494',NULL,'500000.00','duplicate_disbursal','twlWErrN','instant_disbursal',NULL,'2022-05-31 14:21:44',NULL,NULL,NULL,NULL,'0',NULL,'2022-05-31 14:21:44',NULL,'0.00',NULL,NULL,NULL,NULL)");

            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (38698955)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-702458808-51915','1783','4025',NULL,'500000.00','duplicate_disbursal','Yfk1ZuW6','instant_disbursal',NULL,'2022-07-06 13:55:46',NULL,NULL,NULL,NULL,'0',NULL,'2022-07-06 13:55:46',NULL,'0.00',NULL,NULL,NULL,NULL)");

            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (40199204)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-781882719-67134','1783','2016',NULL,'500000.00','duplicate_disbursal','YWQpvjrw','instant_disbursal',NULL,'2022-07-18 10:30:49',NULL,NULL,NULL,NULL,'0',NULL,'2022-07-18 10:30:49',NULL,'0.00',NULL,NULL,NULL,NULL)");

            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (40446793)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-701490195-64966','1783','2340',NULL,'1500000.00','duplicate_disbursal','5VRUWN5q','instant_disbursal',NULL,'2022-07-19 14:09:13',NULL,NULL,NULL,NULL,'0',NULL,'2022-07-19 14:09:13',NULL,'0.00',NULL,NULL,NULL,NULL)");

            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (40660758)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-785193988-93798','1783','2599',NULL,'500000.00','duplicate_disbursal','mvI4GqiA','instant_disbursal',NULL,'2022-07-20 14:18:55',NULL,NULL,NULL,NULL,'0',NULL,'2022-07-20 14:18:55',NULL,'0.00',NULL,NULL,NULL,NULL)");

            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (41242055)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-752184996-50096','1783','3895',NULL,'1000000.00','duplicate_disbursal','URw1Lnbj','instant_disbursal',NULL,'2022-07-23 10:25:30',NULL,NULL,NULL,NULL,'0',NULL,'2022-07-23 10:25:30',NULL,'0.00',NULL,NULL,NULL,NULL)");

            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (42965419)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-785599024-32925','1783','1677',NULL,'1000000.00','duplicate_disbursal','ZKSiitvK','instant_disbursal',NULL,'2022-08-02 12:51:04',NULL,NULL,NULL,NULL,'0',NULL,'2022-08-02 12:51:04',NULL,'0.00',NULL,NULL,NULL,NULL)");

            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (45786425)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-703968272-40067','1783','2047',NULL,'1500000.00','duplicate_disbursal','MgSTpTgy','instant_disbursal',NULL,'2022-08-23 10:41:57',NULL,NULL,NULL,NULL,'0',NULL,'2022-08-23 10:41:57',NULL,'0.00',NULL,NULL,NULL,NULL)");

            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (47017295)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-756319567-29879','1783','3908',NULL,'1000000.00','duplicate_disbursal','IQGYhbP5','instant_disbursal',NULL,'2022-09-30 15:15:06',NULL,NULL,NULL,NULL,'0',NULL,'2022-09-30 15:15:06',NULL,'0.00',NULL,NULL,NULL,NULL)");

            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (47254246)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-706003564-89576','1783','2315',NULL,'1000000.00','duplicate_disbursal','96mZGGYj','instant_disbursal',NULL,'2022-10-07 13:28:32',NULL,NULL,NULL,NULL,'0',NULL,'2022-10-07 13:28:32',NULL,'0.00',NULL,NULL,NULL,NULL)");


            /*Duplicate Disbursal Reversal*/
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (28926442)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-781882719-36774',NULL,'1783',NULL,'500000.00','dup_disb_rvrsl','c7kmTB9C','wallet_transfer',NULL,'2022-02-28 10:47:23',NULL,NULL,NULL,NULL,'0',NULL,'2022-02-28 10:47:23',NULL,'0.00',NULL,NULL,NULL,NULL)");

            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (41421539)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-781882719-67134',NULL,'1783',NULL,'500000.00','dup_disb_rvrsl','t26tpJTu','wallet_transfer',NULL,'2022-07-25 09:52:01',NULL,NULL,NULL,NULL,'0',NULL,'2022-07-25 09:52:01',NULL,'0.00',NULL,NULL,NULL,NULL)");

            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (43002560)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-785599024-32925',NULL,'1783',NULL,'1000000.00','dup_disb_rvrsl','g8ZDuYg5','wallet_transfer',NULL,'2022-08-02 16:19:35',NULL,NULL,NULL,NULL,'0',NULL,'2022-08-02 16:19:35',NULL,'0.00',NULL,NULL,NULL,NULL)");

            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (47018486)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-756319567-29879',NULL,'1783',NULL,'1000000.00','dup_disb_rvrsl','CC-1664552282-SHARE-256-74810305','wallet_transfer',NULL,'2022-09-30 15:38:10',NULL,NULL,NULL,NULL,'0',NULL,'2022-09-30 15:38:10',NULL,'0.00',NULL,NULL,NULL,NULL)");

            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (45790133)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-703968272-40067',NULL,'1783',NULL,'1500000.00','dup_disb_rvrsl','hYeMxlGC','wallet_transfer',NULL,'2022-08-23 12:16:07',NULL,NULL,NULL,NULL,'0',NULL,'2022-08-23 12:16:07',NULL,'0.00',NULL,NULL,NULL,NULL)");

            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (41035904)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-785193988-93798',NULL,'1783',NULL,'500000.00','dup_disb_rvrsl','NbVub39X','wallet_transfer',NULL,'2022-07-22 09:57:21',NULL,NULL,NULL,NULL,'0',NULL,'2022-07-22 09:57:21',NULL,'0.00',NULL,NULL,NULL,NULL)");


            /* Missing Loan Txns*/
            DB::update("update loan_txns set principal = 0, fee = 38000 where id = 126122");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-758009500-31580',NULL,'1783',NULL,'2000000.00','payment','lTTpT2sf','wallet_transfer',NULL,'2022-05-20 09:44:41',NULL,NULL,NULL,NULL,'0',NULL,'2022-05-20 09:44:41',NULL,'0.00','2000000.00',NULL,NULL,NULL)");

            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','CCA-713253-31281',NULL,'1783',NULL,'510000.00','payment','14809459513','wallet_transfer',NULL,'2022-01-21 00:00:00',NULL,NULL,NULL,NULL,'0',NULL,'2022-05-20 09:44:41',NULL,'468000.00','32000.00','10000.00',NULL,NULL)");

            /*Wrong Account Choosed*/
            DB::update("update loan_txns set to_ac_id = 1783 where id in (109743, 115636,  116063, 117199, 117469, 117593, 118015, 121258, 122439, 122885, 127133, 126878)");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (30374073, 31350697, 31453746, 31788049, 31859524, 31932617, 32006491, 32539687, 32789177, 32844971, 33334118, 33469398)");

            (new CCAResetLoanTxns)->run();
            DB::commit();


            UGARecon::run([["acc_prvdr_code" => 'CCA', 'id' => 1783]]);

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
