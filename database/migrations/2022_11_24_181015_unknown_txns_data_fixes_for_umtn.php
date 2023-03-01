<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Scripts\php\UGARecon;
use App\Scripts\php\PaidtoDifferentAccountScript;

class UnknownTxnsDataFixesForUmtn extends Migration
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

            $data = [["acc_prvdr_code" => 'UMTN', "id" => 3728], ["acc_prvdr_code" => "UMTN", "id" => 3605], ["acc_prvdr_code" => 'UMTN', "id" => 4094]];
            UGARecon::run($data);

            /*Missing Account Statement*/

            DB::insert("INSERT INTO `flow_api`.`account_stmts` (`account_id`,`acc_prvdr_code`,`network_prvdr_code`,`cust_id`,`ref_account_id`,`ref_account_num`,`ref_account_terminal`,`acc_number`,`stmt_txn_date`,`stmt_txn_type`,`acc_txn_type`,`descr`,`dr_amt`,`cr_amt`,`amount`,`balance`,`stmt_txn_id`,`data_prvdr_cust_id`,`is_reversal`,`is_future_txn`,`descr_contains`,`recon_id`,`recon_status`,`recon_desc`,`review_reason`,`loan_doc_id`,`country_code`,`import_id`,`source`,`sms_import_status`,`sms_log_id`,`loan_txn_type`,`sms_content`,`created_at`,`created_by`,`updated_at`,`updated_by`,`value_date`) VALUES ('3605','UMTN','UEZM',NULL,NULL,NULL,NULL,'797903','2022-05-25 11:39:00','credit',NULL,'Ms Trust Academy','0.00','1532000.00','1532000.00','12553000.00','16050938186',NULL,'0','0',NULL,NULL,NULL,NULL,NULL,NULL,'UGA',0,'stmt',NULL,NULL,NULL,NULL,NULL,NULL,'2022-05-25 11:39:00',NULL,NULL)");

            (new PaidtoDifferentAccountScript)->run();

            /*Wrong Account Choosed*/
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (35861038)");
            DB::update("update loan_txns set to_ac_id = 3605, recon_amount = 0 where id = 136934");

            DB::update("update account_stmts a, loan_txns t set to_ac_id = account_id, recon_status = null, a.loan_doc_id = null, recon_amount = 0 where stmt_txn_id = txn_id and account_id != to_ac_id and stmt_txn_id in ('152072831101','15408459382','15408574982','15408704790','15409601164','15416419221','15416435139','15416541515','15416583332','15416542090','154167285578','15416735443',
            '15416783031','15417030674','15417332798','15417274229','15417342323','15417415607','15417441680','15417526409','15417549299','15417650340','15417989943','15418061778',
            '15418265483','15418277579','15418527804','15418635428','15418845026','15555642242','15445694228','15445694876','15445710649','15445715258','15445776466','15445868668',
            '15445980721','15446088620','15446029504','15446197498','15446203911','15446261140','15446291236','15446344252','15446631684','15446678123','15456728414','15475599532',
            '15475698024','15478148914','15485362496','15485501936','15485561302','15485606281','15485674400','15485705876','15485711835','15485727142','15485737767','15485759676',
            '15485778777','15485784381','15485785253','15485822057','15485868907','15485869397','15485909065','15485934213','15485974907','15486157071','15486170386','15486184828',
            '15486233772','15486559056','15486638731','15486775201','15486843760','15487302006','15515985482','15515994234','15516086029','15515964157',
            '15516190127','15516222644','15516271583','15516271926','15516327562','15516400628','15516536082','15515630283','15516436639','15516840474','15517148213','15517203848',
            '15517258510','15517324611','15517374846','15517262805','15517436734','15517481386','15517570961','15517898492','15518112535','15518273599','15547718648','15547903383',
            '15547936863','15547942428','15548043270','15548131575','15548328007','15548376033','15548488664','15548517602','15548817139','15548889260','155555348551','1555152346',
            '15558415953','15558774962','15558813853','1555889684','15558839724','15559028826','15559039058','15559179428','15559490817','15559632950','15560257156','15559908135',
            '15560354068','15561246096','15567543255','15567550838','15567732542','15567829270','15567861921','15567882377','15567882811','15568191927','15568242366','15568489323',
            '15568652839','15568907434','15568964237','15569080117','15569212470','15569272045','15569637413','15569689363','15570521262','15570507249','15588367812','15588461757',
            '15588265922','15588471392','15588499669','15588506080','15588514172','15588529004','15588565935','15588625267','15588684850','15588714796','15588784991','15588820121',
            '15588864821','15588871052','15588902300','15588869350','15589052988','15589086113','15589098741','15589186726','15589216030','15589274621','15589324793','15589353019',
            '15589467049','15589681039','15589771986','15589843951','15589874901','15589608264','15598714502','15598732888','15598737409','15598811295','15598818552','15598822554',
            '15598854992','15598879511','15598887665','15598977133','15598987990','15599177672','15599197887','15599238819','15599381847','15599631023','15599777102','15600019403',
            '15598962110','15599887229','15600278789','15600343496','15600480095','15600677673','15609653884','15609719143','15610081869','15610542815','fmWrTKSC','iFLzHRr2',
            '15681464428','P4WGFqRs','zzPmw7tI','cCJFPDDv','7nrjs9cN','15753155434','15808738144','dbLxZzkn','15838130550','sBgiKJV8','7vIhsynh','15968571067','1590526410','5p4kIIQp',
            'kBMHqNSL','160421717469','16050007763','16128116167')");
            
            DB::update("update account_stmts a, loan_txns t set recon_status = null, a.loan_doc_id = null, to_ac_id = account_id where (a.recon_status not in ('10_capture_payment_pending','80_recon_done','60_non_fa_credit')  or recon_status is null) and txn_id = stmt_txn_id and date(a.stmt_txn_date) >= '2022-01-01' and stmt_txn_type = 'credit'  and account_id = 3605 and  account_id != to_ac_id");


            /*Incorrect Txn ID*/
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (31017855, 29876995, 30379455, 30528464, 35860151, 35860155, 31058979)");
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where stmt_txn_id in ('15753179287', '15843735404', '15909656758', '15123458160', '15181562520', '15587245620', '15009058817', '15082558586', '15139196983', '15258502804', '15269052877', '15280997048', '15307575810', '15357280745', '15375929579', '16157669066', '16051033601', '16040809997', '15980290286', '15956945152', '15464970123', '15516770533', '15555654720', '15558624424', '15610122580', '15642443577', '15644162256', '15672728350', '15683465052', '15684697273', '15787287297', '15787377320', '15788713412', '15892805640', '15903623642', '15048184369', '15925047438', '15927507075', '15765984682', '15735714500', '15567394043', '15526913839', '15567836198', '15980938661', '15907625907', '15774656787', '15454054070', '15415873065', '15494776849') and account_id = 3605");

            DB::update("update loan_txns set txn_id = '15546567961', to_ac_id = 3728 where id = 113256");
            DB::update("update loan_txns set txn_id = '15288099558', recon_amount = 0 where id = 101436");
            DB::update("update loan_txns set txn_id = '15408328207', recon_amount = 0 where id = 109756");
            DB::update("update loan_txns set txn_id = '15445791851', recon_amount = 0 where id = 110655");
            DB::update("update loan_txns set txn_id = '16274782004', recon_amount = 0 where id = 137994");
            DB::update("update loan_txns set txn_id = '16274459194', recon_amount = 0 where id = 137970");
            DB::update("update loan_txns set txn_id = '15555534855', recon_amount = 0 where id = 113562");

            DB::update("update loan_txns set txn_id = '15753179287', recon_amount = 0 where id = 118290");
            DB::update("update loan_txns set txn_id = '15843735404', recon_amount = 0 where id = 122071");
            DB::update("update loan_txns set txn_id = '15909656758', recon_amount = 0 where id = 123062");
            DB::update("update loan_txns set txn_id = '15123458160', recon_amount = 0 where id = 98170");
            DB::update("update loan_txns set txn_id = '15181562520', recon_amount = 0 where id = 99330");
            DB::update("update loan_txns set txn_id = '15587245620', recon_amount = 0 where id = 114208");

            DB::update("update loan_txns set txn_id = '15009058817', recon_amount = 0 where id = 96374");
            DB::update("update loan_txns set txn_id = '15082558586', recon_amount = 0 where id = 97522");
            DB::update("update loan_txns set txn_id = '15139196983', recon_amount = 0 where id = 98964");
            DB::update("update loan_txns set txn_id = '15258502804', recon_amount = 0 where id = 100588");
            DB::update("update loan_txns set txn_id = '15269052877', recon_amount = 0 where id = 100827");
            DB::update("update loan_txns set txn_id = '15280997048', recon_amount = 0 where id = 101327");
            DB::update("update loan_txns set txn_id = '15307575810', recon_amount = 0 where id = 101639");
            DB::update("update loan_txns set txn_id = '15357280745', recon_amount = 0 where id = 103031");
            DB::update("update loan_txns set txn_id = '15375929579', recon_amount = 0 where id = 103188");
            DB::update("update loan_txns set txn_id = '16157669066', recon_amount = 0 where id = 133343");
            DB::update("update loan_txns set txn_id = '16051033601', recon_amount = 0 where id = 128030");
            DB::update("update loan_txns set txn_id = '16040809997', recon_amount = 0 where id = 127547");
            DB::update("update loan_txns set txn_id = '15980290286', recon_amount = 0 where id = 125468");
            DB::update("update loan_txns set txn_id = '15956945152', recon_amount = 0 where id = 124380"); //
            DB::update("update loan_txns set txn_id = '15464970123', recon_amount = 0 where id = 111182");
            DB::update("update loan_txns set txn_id = '15516770533', recon_amount = 0 where id = 112431");
            DB::update("update loan_txns set txn_id = '15555654720', recon_amount = 0 where id = 113524");
            DB::update("update loan_txns set txn_id = '15558624424', recon_amount = 0 where id = 113747");
            DB::update("update loan_txns set txn_id = '15610122580', recon_amount = 0 where id = 114920");
            DB::update("update loan_txns set txn_id = '15642443577', recon_amount = 0 where id = 115697");
            DB::update("update loan_txns set txn_id = '15644162256', recon_amount = 0 where id = 115755");
            DB::update("update loan_txns set txn_id = '15672728350', recon_amount = 0 where id = 116050");
            DB::update("update loan_txns set txn_id = '15683465052', recon_amount = 0 where id = 116398");
            DB::update("update loan_txns set txn_id = '15684697273', recon_amount = 0 where id = 116529");
            DB::update("update loan_txns set txn_id = '15787287297', recon_amount = 0 where id = 119631");
            DB::update("update loan_txns set txn_id = '15787377320', recon_amount = 0 where id = 119646");
            DB::update("update loan_txns set txn_id = '15788713412', recon_amount = 0 where id = 121921");
            DB::update("update loan_txns set txn_id = '15892805640', recon_amount = 0 where id = 122158");
            DB::update("update loan_txns set txn_id = '15903623642', recon_amount = 0 where id = 122714");
            DB::update("update loan_txns set txn_id = '15048184369', recon_amount = 0 where id = 96872"); //
            DB::update("update loan_txns set txn_id = '15925047438', recon_amount = 0 where id = 123578");
            DB::update("update loan_txns set txn_id = '15927507075', recon_amount = 0 where id = 123801");
            DB::update("update loan_txns set txn_id = '15774656787', recon_amount = 0 where id = 119157");
            DB::update("update loan_txns set txn_id = '15454054070', recon_amount = 0 where id = 110859");
            DB::update("update loan_txns set txn_id = '15494776849', recon_amount = 0 where id = 112073");

            DB::statement("DELETE from loan_txns where id = 132932");
            DB::update("update loan_txns set amount = '3085000.00', recon_amount = 0, principal = '3000000', fee = '55000', penalty = '30000' where id = 112711");

            DB::update("update loan_txns set txn_id = '15567836198', recon_amount = 0, penalty = '10000' where id = 113929");
            DB::update("update loan_txns set txn_id = '15567394043', recon_amount = 0, principal = '1500000', fee = '32000', penalty = '0' where id = 132939");

            DB::statement("DELETE from loan_txns where id = 132963");
            DB::update("update loan_txns set amount = '772000.00', txn_id = '15735714500', recon_amount = 0, principal = '750000', fee = '17000', penalty = '5000' where id = 117836");

            DB::statement("DELETE from loan_txns where id = 132969");
            DB::update("update loan_txns set amount = '3110000.00', txn_id = '15765984682', recon_amount = 0, principal = '3000000', fee = '55000', penalty = '0' where id = 118921");

            DB::statement("DELETE from loan_txns where id = 133007");
            DB::update("update loan_txns set amount = '1542000.00', txn_id = '15980938661', recon_amount = 0, principal = '1500000', fee = '32000', penalty = '10000' where id = 125520");

            DB::statement("DELETE from loan_txns where id = 132993");
            DB::update("update loan_txns set amount = '3070000.00', txn_id = '15907625907', recon_amount = 0, principal = '3000000', fee = '55000', penalty = '15000' where id = 123001");

            DB::update("update loan_txns set amount = '500000', principal = '500000', fee = 0 where id = 110015");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-703121599-23406',NULL,'3605',NULL,'12000.00','payment','15415873065','wallet_transfer','2436','2022-03-25 12:18:00',NULL,NULL,NULL,NULL,'0',NULL,'2022-03-25 12:18:00',NULL,'0.00','0','12000','0','0')");

            /*Internal Transfer*/
            DB::update("update account_stmts set acc_txn_type = 'int_transfer_dr', recon_status = '80_recon_done' where stmt_txn_id = '15638418693' and account_id = 4161");
            DB::update("update account_stmts set acc_txn_type = 'int_transfer_cr', recon_status = '80_recon_done' where account_id = 4094 and stmt_txn_id = '15638418693'");

            DB::update("update account_stmts set acc_txn_type = 'int_transfer_dr', recon_status = '80_recon_done' where stmt_txn_id = '16043854102' and account_id = 4161");
            DB::update("update account_stmts set acc_txn_type = 'int_transfer_cr', recon_status = '80_recon_done' where account_id = 4094 and stmt_txn_id = '16043854102'");

            DB::update("update account_stmts set acc_txn_type = 'int_transfer_dr', recon_status = '80_recon_done' where stmt_txn_id = '15556901827' and account_id = 3728");
            DB::update("update account_stmts set acc_txn_type = 'int_transfer_cr', recon_status = '80_recon_done' where account_id = 3421 and stmt_txn_id = '15556901827'");

            DB::update("update account_stmts set acc_txn_type = 'int_transfer_dr', recon_status = '80_recon_done' where stmt_txn_id = '15907325381' and account_id = 4094");
            DB::update("update account_stmts set acc_txn_type = 'int_transfer_cr', recon_status = '80_recon_done' where account_id = 4161 and stmt_txn_id = '15907325381'");

            DB::update("update account_stmts set acc_txn_type = 'int_transfer_dr', recon_status = '80_recon_done' where stmt_txn_id = '15908844496' and account_id = 4094");
            DB::update("update account_stmts set acc_txn_type = 'int_transfer_cr', recon_status = '80_recon_done' where account_id = 4161 and stmt_txn_id = '15908844496'");

            DB::update("update account_stmts set acc_txn_type = 'int_transfer_dr', recon_status = '80_recon_done' where stmt_txn_id = '16920867929' and account_id = 4094");
            DB::update("update account_stmts set acc_txn_type = 'int_transfer_cr', recon_status = '80_recon_done' where account_id = 3605 and stmt_txn_id = '16920867929'");

            DB::update("update account_stmts set acc_txn_type = 'int_transfer_dr', recon_status = '80_recon_done' where stmt_txn_id = '15262052824' and account_id = 3728");
            DB::update("update account_stmts set acc_txn_type = 'int_transfer_cr', recon_status = '80_recon_done' where account_id = 3421 and stmt_txn_id = '15262052824'");

            DB::update("update account_stmts set acc_txn_type = 'int_transfer_dr', recon_status = '80_recon_done' where stmt_txn_id = '16061495436' and account_id = 3605");
            DB::update("update account_stmts set acc_txn_type = 'int_transfer_cr', recon_status = '80_recon_done' where account_id = 4161 and stmt_txn_id = '16061495436'");


            /*Duplicate Disbursal Capture*/
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where stmt_txn_id in ('15569900649')");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-772008484-21577','3728','3176',NULL,'1500000.00','duplicate_disbursal','15569900649','instant_disbursal',NULL,'2022-04-09 16:30:14',NULL,NULL,NULL,NULL,'0',NULL,'2022-04-09 16:30:14',NULL,'0.00',NULL,NULL,NULL,NULL)");

            
            /*Duplicate Disbursal Reversal*/
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (41635951)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-752184996-50096',NULL,'4094',NULL,'1000000.00','dup_disb_rvrsl','16727214316','wallet_transfer',NULL,'2022-07-26 11:06:00',NULL,NULL,NULL,NULL,'0',NULL,'2022-07-26 11:06:00',NULL,'0.00',NULL,NULL,NULL,NULL)");

            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (41943276)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-701490195-64966',NULL,'4094',NULL,'1500000.00','dup_disb_rvrsl','16741715310','wallet_transfer',NULL,'2022-07-27 16:23:00',NULL,NULL,NULL,NULL,'0',NULL,'2022-07-27 16:23:00',NULL,'0.00',NULL,NULL,NULL,NULL)");

            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (38977292)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-702458808-51915',NULL,'4094',NULL,'500000.00','dup_disb_rvrsl','16526969353','wallet_transfer',NULL,'2022-07-08 09:03:00',NULL,NULL,NULL,NULL,'0',NULL,'2022-07-08 09:03:00',NULL,'0.00',NULL,NULL,NULL,NULL)");

            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where id in (47258880)");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`photo_transaction_proof`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-706003564-89576',NULL,'4094',NULL,'1000000.00','dup_disb_rvrsl','17567789159','wallet_transfer',NULL,'2022-10-07 15:04:00',NULL,NULL,NULL,NULL,'0',NULL,'2022-10-07 15:04:00',NULL,'0.00',NULL,NULL,NULL,NULL)");


            /*Test Transactions*/
            DB::update("update account_stmts set acc_txn_type = 'outward (test)', recon_status = '80_recon_done' where stmt_txn_id in ('16853269225', '16855650348', '17263664665', '17263665817', '17758308377', '17758329038', '17758344564')");
            DB::update("update account_stmts set acc_txn_type = 'outward_reversed (test)', recon_status = '80_recon_done' where stmt_txn_id in ('16853283284', '16855671772', '17263671954', '17263673184', '17758316633', '17758337649', '17758663524')");

            DB::update("update account_stmts set acc_txn_type = 'inward(test)', recon_status = '80_recon_done' where stmt_txn_id in ('15773538775', '16515020635', '16515028901', '16515036809', '15813981068', '15836654455', '15773559654', '15773484284')");
            DB::update("update account_stmts set acc_txn_type = 'inward_reversed(test)', recon_status = '80_recon_done' where stmt_txn_id in ('15773553844', '16515760598', '16515751657', '16569186193', '15817193056', '15840028684', '15773570969', '15773503282')");


            /*Charges*/
            DB::update("update account_stmts set acc_txn_type = 'charges', recon_status = '80_recon_done' where id = '47424359'");
            DB::update("update account_stmts set acc_txn_type = 'charges', recon_status = '80_recon_done' where stmt_txn_id in ('17625722581', '15611807535')");


            /*Momo Interest*/
            DB::update("update account_stmts set acc_txn_type = 'interest', recon_status = '80_recon_done' where id in (46486640, 46486666)");


            /*Investment*/
            DB::update("update account_stmts set acc_txn_type = 'investment', recon_status = '80_recon_done' where descr regexp 'float transfer|256776909759' and account_id in (3728) and stmt_txn_type = 'credit'and EXTRACT(YEAR_MONTH from stmt_txn_date) >= '202201' and EXTRACT(YEAR_MONTH from stmt_txn_date) <= '202210'");


            /*Redemption*/
            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where (descr regexp 'DDEMBELYO TELCOMS LIMITED|float transfer|transfer|test|flow' and dr_amt >= 3000000) and account_id in (3605, 4094, 3728) and stmt_txn_type = 'debit' and EXTRACT(YEAR_MONTH from stmt_txn_date) >= '202201' and EXTRACT(YEAR_MONTH from stmt_txn_date) <= '202210'");

            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '15671950537' and account_id = 4094");
            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '15756137051' and account_id = 4094");
            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '15764374799' and account_id = 4094");
            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '15987877080' and account_id = 4094");

            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '15063575548' and account_id = 3605");
            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '15198224979' and account_id = 3605");
            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '15307237636' and account_id = 3605");
            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '15385971631' and account_id = 3605");
            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '15443767418' and account_id = 3605");
            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '15513852517' and account_id = 3605");
            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '15565680476' and account_id = 3605");
            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '15617333956' and account_id = 3605");
            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '15681050415' and account_id = 3605");
            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '15713174246' and account_id = 3605");
            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '15742491483' and account_id = 3605");
            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '15763662076' and account_id = 3605");
            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '15817129838' and account_id = 3605");
            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '15849752683' and account_id = 3605");
            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '15914414286' and account_id = 3605");
            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '15956147950' and account_id = 3605");
            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '15998122010' and account_id = 3605");
            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '16040168131' and account_id = 3605");
            DB::update("update account_stmts set acc_txn_type = 'redemption', recon_status = '80_recon_done' where stmt_txn_id = '16111760800' and account_id = 3605");

            
            //Rerun
            $data = [["acc_prvdr_code" => 'UMTN', "id" => 3728], ["acc_prvdr_code" => "UMTN", "id" => 3605], ["acc_prvdr_code" => "UMTN", "id" => 4094]];
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
