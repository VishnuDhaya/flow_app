<?php

use App\Scripts\php\RWARecon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RwaDataFixes1Cleanup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //https://docs.google.com/document/d/1gJOKrGVks1fBohHRLENXydTFr5aPS9NMDetcQ_8N6Z4/
        
        //Individual Fixes
        try{
            DB::beginTransaction();

            DB::update("update loan_txns set txn_id = '6576692910', recon_amount = 0, from_ac_id = 4185 where id = 130132;");
            DB::update("update loan_txns set txn_id = 'FT222204VL87', recon_amount = 0 where id = 167147;");
            DB::delete("delete from loan_txns where id in (166043, 172015, 176926);");
            DB::update("update loan_txns set txn_id = 'FT221157CYVC', recon_amount = 0 where id = 117883;");
            DB::update("update loan_txns set txn_id = 'FT22220RXD24', recon_amount = 0 where id = 167147;");
            DB::update("update loan_txns set recon_amount = 0 where txn_id in ('FT22206L6K4K', 'FT22206S9CJP', 'FT22206R1MRV');");
            DB::update("update loan_txns set txn_id = '7102678372', from_ac_id = 4185 where id = 168586;");
            DB::update("update loan_txns set txn_id = '7102404134', from_ac_id = 4183 where id = 168590;");
            DB::update("update loan_txns set txn_id = 'FT22140BJX23', recon_amount = 0 where id = 126478;");
            DB::update("update loan_txns set txn_id = 'FT221860QV5H', recon_amount = 0 where id = 148279;");
            DB::update("update loan_txns set txn_id = 'FT221406WS8Z', recon_amount = 0 where id = 126480;");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where stmt_txn_id in ('7102404134', '7102678372', 'FT221933BW7R', 'FT22196S2RKJ', 'FT22195HLR2X', 'FT22196LX9NT', 'FT22196989V1', 'FT221616X9K5', 'FT22161BY7Y9', 'FT22244JQ7CT', 'FT2224403BRQ', 'FT222455HRLR', 'FT22245099YH', 'FT2222177T78', 'FT22221LVCXT', 'FT222235H7CW', 'FT22223Q12DJ', 'FT222299ZBWS', 'FT222292TRXW', 'FT22238L2FYG', 'FT222384VP16', 'FT22243PRWM3', 'FT22243922G5', 'FT2224362HDZ', 'FT22243T643W', 'FT221406WS8Z', 'FT221860QV5H', 'FT22140BJX23', '6576692910', 'FT222238HM5D', 'FT22223XYV31');");
            DB::update("update loan_txns set from_ac_id = 4182, recon_amount = 0 where id in (151700, 153563);");
            DB::update("update loan_txns set from_ac_id = 4182, recon_amount = 0 where id in (152819, 153380, 153485);");
            DB::update("update loan_txns set to_ac_id = 4182, amount = '500100', txn_id = 'FT22161BY7Y9', fee = 100, recon_amount = 0 where id in (135727);");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (43651628, 45251088, 45807163, 34222700, 34222738, 32524018, 43865917, 41491946, 41491951, 41491947);");
            DB::update("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('RWA','RFLW-781601259-53237',NULL,'4182',NULL,'10000','payment','FT221616X9K5','wallet_transfer',0,'2022-06-10 00:00:00',NULL,NULL,NULL,NULL,0,NULL,'2022-10-16 17:16:26',NULL,NULL,NULL,'9900',NULL,'100');");
            DB::update("UPDATE `flow_api`.`loan_txns` SET `amount` = 500000.00,`txn_id` = 'FT22244JQ7CT',`recon_amount` = 0 WHERE `id` = 183095;");
            DB::update("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_date`,`created_by`,`created_at`,`recon_amount`) VALUES ('RWA','RFLW-780442421-57783','4292','5594','300000.00','disbursal','FT2224403BRQ','internet_banking','2022-09-01 00:00:00','58','2022-09-01 15:44:49','0');");
            DB::update("UPDATE `flow_api`.`loan_txns` SET `amount` = 500000.00,`txn_id` = 'FT222455HRLR',`recon_amount` = 0 WHERE `id` = 183977;");
            DB::update("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_date`,`created_by`,`created_at`,`recon_amount`) VALUES ('RWA','RFLW-781601259-85384','4292','4566','300000.00','disbursal','FT22245099YH','internet_banking','2022-09-02 00:00:00','58','2022-09-02 16:21:52','0');");
            DB::update("UPDATE `flow_api`.`loan_txns` SET `amount` = 500000.00,`txn_id` = 'FT2222177T78',`recon_amount` = 0 WHERE `id` = 167840;");
            DB::update("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_date`,`created_by`,`created_at`,`recon_amount`) VALUES ('RWA','RFLW-787808115-59432','4292','4539','500000.00','disbursal','FT22221LVCXT','internet_banking','2022-08-09 00:00:00','58','2022-08-09 15:20:55','0');");
            DB::update("UPDATE `flow_api`.`loan_txns` SET `amount` = 500000.00,`txn_id` = 'FT222235H7CW',`recon_amount` = 0 WHERE `id` = 169455;");
            DB::update("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_date`,`created_by`,`created_at`,`recon_amount`) VALUES ('RWA','RFLW-781601259-31025','4292','4566','300000.00','disbursal','FT22223Q12DJ','internet_banking','2022-08-11 00:00:00','58','2022-08-12 08:46:02','0');");
            DB::update("UPDATE `flow_api`.`loan_txns` SET `amount` = 500000.00,`txn_id` = 'FT222299ZBWS',`recon_amount` = 0 WHERE `id` = 172927;");
            DB::update("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_date`,`created_by`,`created_at`,`recon_amount`) VALUES ('RWA','RFLW-787808115-21155','4292','4539','500000.00','disbursal','FT222292TRXW','internet_banking','2022-08-17 00:00:00','58','2022-08-17 13:52:28','0');");
            DB::update("UPDATE `flow_api`.`loan_txns` SET `amount` = 500000.00,`txn_id` = 'FT22238L2FYG',`recon_amount` = 0 WHERE `id` = 179096;");
            DB::update("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_date`,`created_by`,`created_at`,`recon_amount`) VALUES ('RWA','RFLW-781601259-38218','4292','4566','300000.00','disbursal','FT222384VP16','wallet_portal','2022-08-26 00:00:00','58','2022-08-26 13:54:04','0');");
            DB::update("UPDATE `flow_api`.`loan_txns` SET `amount` = 500000.00,`txn_id` = 'FT22243PRWM3',`recon_amount` = 0 WHERE `id` = 181927;");
            DB::update("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_date`,`created_by`,`created_at`,`recon_amount`) VALUES ('RWA','RFLW-787808115-11579','4292','4539','500000.00','disbursal','FT22243922G5','internet_banking','2022-08-31 00:00:00','58','2022-08-31 10:08:04','0');");
            DB::update("UPDATE `flow_api`.`loan_txns` SET `amount` = 500000.00,`txn_id` = 'FT2224362HDZ',`recon_amount` = 0 WHERE `id` = 182274;");
            DB::update("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_date`,`created_by`,`created_at`,`recon_amount`) VALUES ('RWA','RFLW-785818371-20429','4292','5574','500000.00','disbursal','FT22243T643W','internet_banking','2022-08-31 00:00:00','58','2022-08-31 14:17:35','0');");
            DB::update("UPDATE `flow_api`.`loan_txns` SET `amount` = 506000.00,`txn_id` = 'FT22223XYV31',`recon_amount` = 0,`fee` = 6000 WHERE `id` = 169025;");
            DB::update("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`to_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`remarks`,`created_by`,`created_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('RWA','RFLW-781888020-73065','4292','4000','payment','FT222238HM5D','wallet_transfer','3570','2022-08-11 00:00:00','the customer paid the FA of 500000RWF with a fee of 10000RWF. he paid on time.','55','2022-08-11 11:22:11','0','0','4000','0','0');");
            DB::update("UPDATE `flow_api`.`loan_txns` SET `principal` = 1000000,`fee` = 22000,`penalty` = 2000 WHERE `id` = 113967;");
            DB::update("UPDATE `flow_api`.`loan_txns` SET `principal` = 0,`fee` = 0,`penalty` = 8000 WHERE `id` = 132941;");
            DB::update("UPDATE `flow_api`.`loan_txns` SET `principal` = 1000000 WHERE `id` = 122984;");
            DB::update("UPDATE `flow_api`.`loan_txns` SET `principal` = 1000000 WHERE `id` = 123162;");
            DB::update("UPDATE `flow_api`.`loan_txns` SET `fee` = 32000 WHERE `id` = 133003;");
            DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'withdrawal' where stmt_txn_id in ('6488880313','6488887798','6490849116','6490853952','6495890760','6537364440','6537368856','6537397922')");
            DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'commission' where stmt_txn_id in ('6537761541', '6601603027')");
            DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'deposit' where stmt_txn_id in ('TT22129YWMCC','TT22129XN932','TT22129JQS0M','FT221389H3GK','TT22138SHTP7','FT22139CRJNM','FT22145B5WBH')");
            // DB::update("update loan_txns set txn_type = 'dup_disb_rvrsl' where txn_id in ('7068481517', '7147938551', '7205060908');");
            
            //Cleanup Fixes
            //Wrong txn ID
            DB::update("update loan_txns set recon_amount = 0, txn_id = '7042346171' where id = 163980");
            DB::update("update loan_txns set recon_amount = 0, txn_id = 'TT2229934K69' where id = 226927");
            DB::update("update loan_txns set recon_amount = 0, txn_id = 'FT22299J068Y' where id = 226914");
            DB::update("UPDATE `flow_api`.`loan_txns` SET `amount` = 200000.00,`txn_id` = 'FT22181QF58F',`recon_amount` = 200000.00,`fee` = 0 WHERE `id` = 145711;");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`to_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`remarks`,`created_by`,`created_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('RWA','RFLW-791222527-14723','4292','4000.00','payment','FT221815DZ1J','wallet_transfer','3570','2022-06-30 00:00:00','the customer paid the FA of 200000RWF with the fee of 4000RWF.he  paid on time.','55','2022-06-30 12:04:09',4000.0,'0','4000','0','0');");
            DB::update("UPDATE `flow_api`.`loan_txns` SET `amount` = 500000.00,`txn_id` = 'FT22171FX6R2',`recon_amount` = 500000.00,`fee` = 0 WHERE `id` = 140540;");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`to_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`remarks`,`created_by`,`created_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('RWA','RFLW-786127194-47449','4292','5000.00','payment','FT221719L596','wallet_transfer','3570','2022-06-20 00:00:00','the customer paid the FA of 500000RWF with the fee of 5000RWF. he paid on time','55','2022-06-20 18:03:07','5000.00','0','5000','0','0');");
            DB::update("UPDATE `flow_api`.`loan_txns` SET `txn_id` = 'TT22294KBX2W' WHERE `id` = 222796;");
            
            //Dup Disbs
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-781837097-87850','7376300310',400000,'duplicate_disbursal','2022-09-13 13:56:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-782641494-32296','7341951577',150000,'duplicate_disbursal','2022-09-09 09:18:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-782586333-64711','7328525932',150000,'duplicate_disbursal','2022-09-07 15:37:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-781908470-65258','7319129470',300000,'duplicate_disbursal','2022-09-06 12:54:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-781023072-83234','FT22244BN90T',300000,'duplicate_disbursal','2022-09-01 00:00:00')");
            
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-780244030-40239','7204758424',200000,'duplicate_disbursal','2022-08-23 17:59:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-783237807-27863','7147344464',200000,'duplicate_disbursal','2022-08-16 11:16:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-788436714-38267','FT22220SPX9X',1000000,'duplicate_disbursal','2022-08-08 00:00:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-780929087-35809','7068042323',200000,'duplicate_disbursal','2022-08-06 09:59:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-782573603-22534','7068015264',400000,'duplicate_disbursal','2022-08-06 09:55:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-782585171-54908','7042583170',300000,'duplicate_disbursal','2022-08-03 09:38:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-790966071-28346','7037580486',150000,'duplicate_disbursal','2022-08-02 15:55:00')");
            
            
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-791432747-60375','6957800690',200000,'duplicate_disbursal','2022-07-22 09:48:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-780919381-61933','6957790447',150000,'duplicate_disbursal','2022-07-22 10:04:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-791572118-85480','FT22200GXSM2',500000,'duplicate_disbursal','2022-07-19 00:00:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-789967717-14094','6911116603',500000,'duplicate_disbursal','2022-07-15 11:13:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-783940173-12397','6589748084',500000,'duplicate_disbursal','2022-06-01 13:45:00')");

            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-785149138-48287','6573038754',300000,'duplicate_disbursal','2022-05-30 09:10:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-781601473-86693','FT22133KWYQY',300000,'duplicate_disbursal','2022-05-13 00:00:00')");
            
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-780128485-87303','FT22109P0VXM',150000,'duplicate_disbursal','2022-04-18 00:00:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-782100744-73424','FT222972DK0R',300000,'duplicate_disbursal','2022-10-24 00:00:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-783723352-88289','FT22241D72GB',500000,'duplicate_disbursal','2022-08-29 00:00:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-781097711-35227','FT22216XPF1R',500000,'duplicate_disbursal','2022-08-04 00:00:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-788361699-46635','FT22194KP70P',500000,'duplicate_disbursal','2022-07-13 00:00:00')");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = 'RFLW-782585171-54908' where stmt_txn_id = '7042583170' ");
            
            //Dup Disb Rvrsl
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-781023072-83234','FT222455XLC3',300000,'dup_disb_rvrsl','2022-09-02 00:00:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-781837097-87850','7376359241',400000,'dup_disb_rvrsl','2022-09-13 14:03:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-782641494-32296','7342157852',150000,'dup_disb_rvrsl','2022-09-09 09:44:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-782586333-64711','7328578330',150000,'dup_disb_rvrsl','2022-09-07 15:44:00')");

            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-780244030-40239','7205060908',200000,'dup_disb_rvrsl','2022-08-23 18:22:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-783237807-27863','7147938551',200000,'dup_disb_rvrsl','2022-08-16 12:32:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-788436714-38267','FT222204VL87',1000000,'dup_disb_rvrsl','2022-08-08 00:00:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-782573603-22534','7068481517',400000,'dup_disb_rvrsl','2022-08-06 10:58:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-782585171-54908','7043309678',300000,'dup_disb_rvrsl','2022-08-03 11:07:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-791572118-85480','FT22201D71CG',500000,'dup_disb_rvrsl','2022-07-19 00:00:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-789967717-14094','6911624220',500000,'dup_disb_rvrsl','2022-07-15 12:18:00')");
            
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-783940173-12397','FT221529VF3F',500000,'dup_disb_rvrsl','2022-06-01 00:00:00')");


            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-785149138-48287','FT2215022NKB',300000,'dup_disb_rvrsl','2022-05-30 00:00:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-781601473-86693','FT22136HR08T',300000,'dup_disb_rvrsl','2022-05-13 00:00:00')");

            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-780128485-87303','FT22109MGM3H',150000,'dup_disb_rvrsl','2022-04-19 00:00:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-782100744-73424','FT22298MZB5G',300000,'dup_disb_rvrsl','2022-10-24 00:00:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-781097711-35227','FT22216W1QMM',500010,'dup_disb_rvrsl','2022-08-04 00:00:00')");
            //Test
            DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'test' where stmt_txn_id = 'FT222480VJGR'");
            DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'test' where stmt_txn_id = 'FT221263P68P'");
            DB::update("update account_stmts set acc_txn_type = 'duplicate_payment_reversal', recon_status = '80_recon_done' where stmt_txn_id = '7174368192' ");
            DB::update("update account_stmts set acc_txn_type = 'duplicate_payment', recon_status = '80_recon_done' where stmt_txn_id = '7174412084' ");
            DB::update("update account_stmts set acc_txn_type = 'duplicate_payment_reversal', recon_status = '80_recon_done' where stmt_txn_id = '7709130067' ");
            DB::update("update account_stmts set acc_txn_type = 'duplicate_payment_reversal', recon_status = '80_recon_done' where stmt_txn_id = '7391820962' "); 
            DB::update("update account_stmts set acc_txn_type = 'duplicate_payment', recon_status = '80_recon_done' where stmt_txn_id = '7766989333' ");
            DB::update("update account_stmts set acc_txn_type = 'duplicate_payment', recon_status = '80_recon_done' where stmt_txn_id = '7446645772' ");
            
            DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'test' where stmt_txn_id = 'FT221517JGF1'");
            DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'test' where stmt_txn_id = 'FT22157YHVWD'");
            DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'test' where stmt_txn_id = 'FT221095Z3QS'");
            DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'test' where stmt_txn_id = 'FT221098BXJ7'");
            DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'test' where stmt_txn_id = 'FT22109VTZTY'");
            
            
            //Dup Payment
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-781111781-17038','7051111170',156000,'duplicate_payment','2022-08-04 10:05:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-788427941-79009','6953402241',275400,'duplicate_payment','2022-07-21 15:31:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-788427941-79009','6953384463',30600,'duplicate_payment','2022-07-21 15:28:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-783341122-95173','6941557974',305000,'duplicate_payment','2022-07-19 17:01:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-791527125-97795','7547282480',220000,'duplicate_payment','2022-10-04 16:30:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-789949400-20940','7721945765',511000,'duplicate_payment','2022-10-27 09:25:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-789517236-98646','FT22297PLN9K',205000,'duplicate_payment','2022-10-24 00:00:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-785546986-82949','FT221865LPVH',61000,'duplicate_payment','2022-07-05 00:00:00')");
            //Dup Payment Rvrsl
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-781111781-17038','7051231180',156000,'duplicate_payment_reversal','2022-08-04 10:19:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-788427941-79009','6953650296',306000,'duplicate_payment_reversal','2022-07-21 16:10:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-783341122-95173','6941578746',305000,'duplicate_payment_reversal','2022-07-19 17:05:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-791527125-97795','7547359413',220000,'duplicate_payment_reversal','2022-10-04 16:39:00')"); 
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-789949400-20940','7722188509',511000,'duplicate_payment_reversal','2022-10-27 09:54:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-789517236-98646','FT22297M3604',205000,'duplicate_payment_reversal','2022-10-24 00:00:00')");
            DB::insert("INSERT INTO loan_txns (loan_doc_id, txn_id, amount, txn_type, txn_date) values ('RFLW-785546986-82949','FT22186YNHW6',61000,'duplicate_payment_reversal','2022-07-05 00:00:00')");
            //Misc
            DB::update("update account_stmts set recon_status = '80_recon_done', loan_doc_id = 'RFLW-787380398-75830' where stmt_txn_id = 'FT22126SYZ7L' ");	
            DB::update("update loan_txns set txn_id = 'FT22126SYZ7L' where id = 121168");
            DB::update("update account_stmts set recon_status = '80_recon_done', loan_doc_id = 'RFLW-780128485-53371' where stmt_txn_id = 'FT221254MSRP' ");	
            
            DB::update("update loan_txns set amount = 513000, principal = 500000, fee = 10000, penalty = 1650, excess = 1350 where id = 127077");	
            DB::update("update loans set paid_amount = 513000, paid_excess = 1350, reversed_excess = 1350 where loan_doc_id = 'RFLW-787380398-81412' ");	
            DB::update("update loan_txns set from_ac_id = 4182, to_ac_id = 4220, amount = 1350, txn_type = 'excess_reversal', txn_id = 'FT22143R7BSL', principal = fee = penalty = excess = NULL where id = 133022 ");	
            DB::update("update loan_txns set amount = 550000, principal = 500000, fee = 5000, penalty = 0, excess = 45000 where id = 126478");	
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`created_at`) VALUES ('RWA','RFLW-788702635-68209','4182','5000','excess_reversal','FT22140QSQCT','wallet_transfer','3570','2022-05-20 00:00:00','2022-11-08 16:44:33');");		
            DB::update("update loans set paid_amount = 550000, paid_excess = 45000, reversed_excess = 45000 where loan_doc_id = 'RFLW-788702635-68209' "); 	
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`created_at`) VALUES ('RWA','RFLW-788702635-68209','4182','40000','excess_reversal','FT22140F85BX','wallet_transfer','3570','2022-05-20 00:00:00','2022-11-08 16:44:33');");		
            
            
            DB::update("update loan_txns set recon_amount=0, txn_id = 'FT22118H0XBF' where loan_doc_id = 'RFLW-788820759-17513' and txn_type = 'disbursal'");
            DB::update("update loan_txns set recon_amount=0, txn_id = 'FT2211960S94' where loan_doc_id = 'RFLW-788365122-99847' and txn_type = 'disbursal'");
            DB::update("update loan_txns set recon_amount=0, txn_id = 'FT22123853ML' where loan_doc_id = 'RFLW-788399591-92952' and txn_type = 'disbursal'");
            DB::update("update loan_txns set recon_amount=0, txn_id = 'FT22124SLT2G' where loan_doc_id = 'RFLW-788702635-36917' and txn_type = 'disbursal'");
            
            DB::update("update loan_txns set txn_id = 'FT222298GYV7', txn_type = 'payment', penalty = 1650, principal = fee = excess = 0 where id = 172520");	
            DB::update("update loans set penalty_waived = 0, penalty_collected = 1650, paid_amount = paid_amount + 1650 where loan_doc_id = 'RFLW-788776743-94886'");
            
            DB::update("UPDATE `flow_api`.`loan_txns` SET `amount` = 200000.00,`txn_id` = '6597993589',`fee` = 0, recon_amount = 0 WHERE `id` = 132775");	
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`remarks`,`created_by`,`created_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('RWA','RFLW-791565990-48034','2000.00','payment','6597995834','wallet_transfer','3570','2022-06-02 00:00:00','the customer paid the FA of 200000RWF with the fee of 2000RWF.she  paid on time.','55','2022-06-02 15:09:28','0.00','0','2000','0','0')");


            
            DB::update("update loan_txns set txn_id = 'FT22109J1BTJ', recon_amount = 0 where id = 115814;");
            DB::update("update loan_txns set txn_id = 'FT22109KR2B4', recon_amount = 0 where id = 116175;");
            DB::update("update loan_txns set txn_id = 'FT221090MNWV', recon_amount = 0 where id = 116146;");
            DB::update("update loan_txns set txn_id = 'FT22109WWDKY', recon_amount = 0 where id = 116030;");
            DB::update("update loan_txns set txn_id = 'FT22111WJHL1', recon_amount = 0 where id = 116643;");
            DB::update("update loan_txns set txn_id = 'FT22111JLLSM', recon_amount = 0 where id = 116714;");
            DB::update("update loan_txns set txn_id = 'FT221118ZDHS', recon_amount = 0 where id = 116741;");
            DB::update("update loan_txns set txn_id = 'FT22115RSYLK', recon_amount = 0 where id = 117673;");
            DB::update("update loan_txns set txn_id = 'FT22115W597K', recon_amount = 0 where id = 117679;");
            DB::update("update loan_txns set txn_id = 'FT22116LM0Q4', recon_amount = 0 where id = 117990;");
            DB::update("update loan_txns set txn_id ='FT22116PFMGC', recon_amount = 0 where id = 118143;");
            DB::update("update loan_txns set txn_id = 'FT221157CYVC', recon_amount = 0 where id = 117883");
            
            DB::delete("DELETE FROM `flow_api`.`loan_txns` WHERE `id` = 161519;");  

            //Wrong Account Capture Fix
            DB::update("update account_stmts a, loan_txns t set t.from_ac_id = account_id,  t.recon_amount = 0, recon_status = null, a.loan_doc_id = null where (a.recon_status not in ('10_capture_payment_pending','80_recon_done','60_non_fa_credit') or recon_status is null) and txn_id = stmt_txn_id and stmt_txn_type = 'debit' and account_id = 4183;");
            DB::update("update account_stmts a, loan_txns t set t.from_ac_id = account_id, t.recon_amount = 0, recon_status = null, a.loan_doc_id = null where (a.recon_status not in ('10_capture_payment_pending','80_recon_done','60_non_fa_credit') or recon_status is null) and txn_id = stmt_txn_id and stmt_txn_type = 'debit' and account_id = 4184;");
            DB::update("update account_stmts a, loan_txns t set t.from_ac_id = account_id, t.recon_amount = 0, recon_status = null, a.loan_doc_id = null where (a.recon_status not in ('10_capture_payment_pending','80_recon_done','60_non_fa_credit') or recon_status is null) and txn_id = stmt_txn_id and stmt_txn_type = 'debit' and account_id = 4185;");
            DB::update("update account_stmts a, loan_txns t set t.from_ac_id = account_id, t.recon_amount = 0, recon_status = null, a.loan_doc_id = null where (a.recon_status not in ('10_capture_payment_pending','80_recon_done','60_non_fa_credit') or recon_status is null) and txn_id = stmt_txn_id and stmt_txn_type = 'debit' and account_id = 4182 and (from_ac_id != 4292 or from_ac_id is null);");
            DB::update("update account_stmts a, loan_txns t set t.to_ac_id = account_id, t.recon_amount = 0, recon_status = null, a.loan_doc_id = null where (a.recon_status not in ('10_capture_payment_pending','80_recon_done','60_non_fa_credit') or recon_status is null) and txn_id = stmt_txn_id and stmt_txn_type = 'credit' and account_id = 4182 and (to_ac_id != 4292 or to_ac_id is null);");
            DB::update("update account_stmts a, loan_txns t set t.to_ac_id = account_id, t.recon_amount = 0, recon_status = null, a.loan_doc_id = null where (a.recon_status not in ('10_capture_payment_pending','80_recon_done','60_non_fa_credit') or recon_status is null) and txn_id = stmt_txn_id and stmt_txn_type = 'credit' and account_id = 4183;");
            DB::update("update account_stmts a, loan_txns t set t.to_ac_id = account_id, t.recon_amount = 0, recon_status = null, a.loan_doc_id = null where (a.recon_status not in ('10_capture_payment_pending','80_recon_done','60_non_fa_credit') or recon_status is null) and txn_id = stmt_txn_id and stmt_txn_type = 'credit' and account_id = 4184;");
            DB::update("update account_stmts a, loan_txns t set t.to_ac_id = account_id, t.recon_amount = 0, recon_status = null, a.loan_doc_id = null where (a.recon_status not in ('10_capture_payment_pending','80_recon_done','60_non_fa_credit') or recon_status is null) and txn_id = stmt_txn_id and stmt_txn_type = 'credit' and account_id = 4185;");
            //Reinitiate Recon
            DB::update("update loan_txns t, loans l set t.country_code = l.country_code where l.loan_doc_id = t.loan_doc_id;");
            // DB::update("update account_stmts a, loan_txns t set recon_status = null, a.loan_doc_id = null, recon_amount = null where a.country_code = 'RWA' and (recon_status != '80_recon_done' or recon_status is null) and txn_id = stmt_txn_id;");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where country_code = 'RWA' and (recon_status != '80_recon_done' or recon_status is null) and stmt_txn_type = 'debit';");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where country_code = 'RWA' and (recon_status not in ('80_recon_done', '10_capture_payment_pending') or recon_status is null) and stmt_txn_type = 'credit';");
            
            
            //Match Investments, Internal Transfers. Charges
            DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'int_transfer_dr' where country_code = 'RWA' and descr regexp '250791519171|250791516469|250791334419' and stmt_txn_type = 'debit' and account_id = 4182;");
            DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'int_transfer_cr' where country_code = 'RWA' and descr like '%Fund-transfer to%' and stmt_txn_type = 'credit' and account_id in (4183, 4184, 4185);");
            DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'investment' where descr regexp 'IB Account transfer / wallet top up|Investment' and country_code = 'RWA' and account_id = 4182;");
            DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'charges' where descr like '%Charge%'"); 
            
            foreach([4182, 4183, 4184, 4185] as $acc){
                DB::update("update account_stmts a, loan_txns t set t.to_ac_id = account_id,  t.recon_amount = 0, recon_status = null, a.loan_doc_id = null where  txn_id = stmt_txn_id and stmt_txn_type = 'credit' and account_id = ? and (to_ac_id != account_id or to_ac_id is null)", [$acc]);
                DB::update("update account_stmts a, loan_txns t set t.from_ac_id = account_id,  t.recon_amount = 0, recon_status = null, a.loan_doc_id = null where  txn_id = stmt_txn_id and stmt_txn_type = 'debit' and account_id = ? and (from_ac_id != account_id or from_ac_id is null)", [$acc]);
            }
            RWARecon::run();
            
            DB::update("update account_stmts a, loan_txns t set is_future_txn = true where stmt_txn_id = txn_id and date(txn_date) != date(stmt_txn_date) and account_id = 4182");
            DB::update("update account_stmts set value_date = null where is_future_txn = false and account_id = 4182");
            DB::update("update loan_txns set to_ac_id = null where id in ('120223','120602','120604','120605','120607','120958','121074','121432','121434','121542','122030','122103','122150','122151','122412','122633','122797','123061','123582','123903','123916','124225','124232')");

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
