<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DataIntegrityFixes extends Migration
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

            //Penalty Included in Payment Txns - Correct Penalty Txn also present
            $penalty_in_payment_fas = "'UFLW-783232580-33331','UFLW-788239129-21641','UFLW-788990561-24220','UFLW-784669842-27692','UFLW-774956241-34428','UFLW-772234329-97668','UFLW-771327995-46821','UFLW-755390468-80870','UFLW-772912683-40097','UFLW-782019399-45892','UFLW-789401017-97332','UFLO-147595-11856','UFLW-778961468-77672','UFLW-787308767-79444','UFLW-772087333-72348','UFLW-706412967-66272','UFLW-772375187-97039','UFLW-774747372-71109','UFLW-704743388-11784','UFLW-786070139-20614','UFLW-777669022-34328','UFLW-777730900-86628','UFLW-781497907-43354','UFLW-777105883-25289','UFLW-702076442-59103'";

            DB::update("UPDATE loan_txns t, loans l set amount =  amount - penalty_collected where l.loan_doc_id = t.loan_doc_id and  txn_type = 'payment' and l.loan_doc_id in ($penalty_in_payment_fas)");
            //FAs with Loan Txns missing
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`) VALUES ('UGA','UFLW-772366934-36658',NULL,'3421',NULL,'262000.00','payment','15350653123','wallet_portal','2437','2022-03-18 16:35:00',NULL,NULL,NULL,'34',NULL,'2022-03-18 16:35:00',NULL,'262000.00')");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`) VALUES ('UGA','UFLW-772366934-36658',NULL,'3421',NULL,'25000.00','penalty_payment','15350653123','wallet_portal','2437','2022-03-18 16:35:00',NULL,NULL,NULL,'34',NULL,'2022-03-18 16:35:00',NULL,'25000.00')");
            
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`) VALUES ('UGA','UFLW-788239129-36026',NULL,'3421',NULL,'222000.00','payment','15483856916','wallet_transfer','2437','2022-04-01 00:00:00',NULL,NULL,NULL,'34',NULL,'2022-04-01 13:07:01',NULL,'0.00')");
            
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`) VALUES ('UGA','UFLW-786603998-79879',NULL,'4161',NULL,'110000.00','payment','15784243880','wallet_transfer','2437','2022-04-30 00:00:00',NULL,NULL,NULL,'34',NULL,'2022-04-30 13:56:46',NULL,'0.00')");
            
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`) VALUES ('UGA','UEZM-121601-22813',NULL,'2895',NULL,'428000.00','payment','114640680','wallet_transfer','10','2022-05-26 00:00:00',NULL,NULL,NULL,'10',NULL,'2022-05-26 15:19:01',NULL,'0.00')");
            
            
            
            //Penalty Txn present in loan_txns but the same is accounted as excess in loans (Due to holidays)
            //Adding the paid_excess value to penalty_collected, setting paid_excess to 0
            $fa_ids = "'CCA-796615-99165','UEZM-856219-13153','CCA-666838-38982','UEZM-305114-42044'";
            DB::update("UPDATE loans set penalty_collected = penalty_collected + paid_excess, paid_excess = 0 where loan_doc_id in ($fa_ids)");
            
            
            
            //Duplicate Payment record
            DB::delete("DELETE from loan_txns where id = 118476");
            
            //Incorrect loan_txn amounts
            DB::update("UPDATE loan_txns set amount = 1022000, txn_id = '15773701165' where id = 132972");
            DB::update("UPDATE loan_txns set txn_id = '15773869389' where id = 119110");
            
            
            //Incorrect & Missing txns
            DB::update("UPDATE loan_txns set amount = 1027000 where id = 133551");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`) VALUES ('UGA','UFLW-787555729-14887',NULL,'4161',NULL,'10000.00','penalty_payment','16161393754','wallet_transfer','10','2022-06-04 00:00:00',NULL,NULL,NULL,'10',NULL,'2022-06-04 17:57:34',NULL,'0.00')");
            
            DB::update("UPDATE loan_txns set amount = 515000 where id = 134878");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`) VALUES ('UGA','UFLW-777170197-28770',NULL,'4161',NULL,'5000.00','penalty_payment','16199882988','wallet_transfer','10','2022-06-08 00:00:00',NULL,NULL,NULL,'10',NULL,'2022-06-08 11:13:28',NULL,'0.00')");
            
            DB::update("UPDATE loan_txns set amount = 517000 where id = 139829");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`) VALUES ('UGA','UFLW-776300889-39145',NULL,'4161',NULL,'30000.00','penalty_payment','16309480855','wallet_transfer','10','2022-06-18 00:00:00',NULL,NULL,NULL,'10',NULL,'2022-06-18 15:50:02',NULL,'0.00')");
            
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`to_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`created_by`,`created_at`,`recon_amount`) VALUES ('UGA','UFLW-787303633-38992','3421','515000.00','payment','15496936331','wallet_portal','0','2022-04-02 15:59:00','0','2022-04-02 15:59:00','0.00')");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`to_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`created_by`,`created_at`,`recon_amount`) VALUES ('UGA','UFLW-787303633-38992','3421','5000.00','penalty_payment','15496936331','wallet_portal','0','2022-04-02 15:59:00','0','2022-04-02 15:59:00','0.00')");
            
            
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`to_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_date`,`created_at`,`recon_amount`) VALUES ('UGA','UFLW-783344552-64779','4161','3055000.00','payment','15754224654','wallet_portal','2022-04-27 11:05:00','2022-04-27 11:05:00','0')");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`to_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_date`,`created_at`,`recon_amount`) VALUES ('UGA','UFLW-783344552-64779','4161','190000.00','penalty_payment','15754224654','wallet_portal','2022-04-27 11:05:00','2022-04-27 11:05:00','0')");
            
            DB::update("UPDATE `loan_txns` SET `amount` = 5090000,`txn_type` = 'payment' WHERE `id` = 131512");
            DB::insert("INSERT INTO `loan_txns` (`country_code`,`loan_doc_id`,`to_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`remarks`,`created_by`,`created_at`,`recon_amount`) VALUES ('UGA','UEZM-666502-56753','2895','15000','penalty_payment','114797043','review_n_sync','','2022-05-31 11:58:58','Col/FLOW/0701359110/JACKIE/16162973','10','2022-05-31 12:11:36','0.00')");
            
            DB::update("UPDATE `loan_txns` SET `amount` = 517000,`txn_type` = 'payment' WHERE `id` = 132906");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`) VALUES ('UGA','UFLW-786360855-49245',NULL,'4161',null,'5000.00','penalty_payment','16149173948','review_n_sync','','2022-06-03 15:13:00',NULL,NULL,'218775/REAGAN MUGOYA','10',NULL,'2022-06-03 15:51:32',NULL,'0.00')");
            
            DB::update("UPDATE `loan_txns` SET `amount` = 512000 WHERE `id` = 110399");
            DB::update("UPDATE `loan_txns` SET  txn_type = 'penalty_payment', txn_id = '9zSnKLyy' WHERE `id` = 110643");




            //Excess not present in payment_txn
            DB::update("UPDATE loans l, loan_txns t set amount = amount + paid_excess where l.loan_doc_id = t.loan_doc_id and  l.loan_doc_id in ('UFLW-772234329-81500','UFLW-705782907-52061','UFLW-787712842-11002','UFLW-755252812-40061','UFLW-772939393-76250','UFLW-782702175-66869','UFLW-782050518-34033','UFLW-787801218-65891','UFLW-787587502-97029','UFLW-781163231-26136','UFLW-787712842-32103','UFLW-786576307-40649','UFLW-789883969-69667','UFLW-706717696-71187','UFLW-757850838-52936','UFLW-783292705-68910','UFLW-776360098-71430','UFLW-756099274-47796','UFLW-783292705-33623','UFLW-781602746-99072','UFLW-706341360-97309','UFLW-782414387-30241','UFLW-782611103-18371','UFLW-783115334-73021','UFLW-753235300-36578','UFLW-785379834-96727','UFLW-705323408-69440','UFLW-776945555-10994','UFLW-777442668-25273') and txn_type = 'payment'");
            DB::update("UPDATE loan_txns set amount = 905000 where id = 136371");
            DB::update("UPDATE loan_txns set amount = 102000 where id = 151681");
            DB::update("UPDATE loan_txns set amount = 1017000 where id = 155372");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`) VALUES ('UGA','UFLW-776134646-25899',NULL,'4161',NULL,'5000.00','penalty_payment','16253589097','review_n_sync','','2022-06-13 12:31:00',NULL,NULL,'759419/OKELLO LAWRENCE','26',NULL,'2022-06-13 13:17:38',NULL,'0.00')");


            //Loan paid fields not updated
            DB::update("UPDATE loans set current_os_amount = 0, paid_principal = 1000000, paid_fee = 22000 where id = 48577");



            //Excess reversal not present
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`created_by`,`created_at`,`recon_amount`) VALUES ('UGA','UFLW-784800921-47379','3421','2000.00','excess_reversal','15446484744','wallet_transfer','10','2022-03-28 16:29:00','0','2022-03-28 16:29:00','0.00')");

            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`created_by`,`created_at`,`recon_amount`) VALUES ('UGA','UFLW-775361415-91216','3421','3000.00','excess_reversal','15518189614','wallet_transfer','10','2022-04-04 17:22:00','0','2022-04-04 17:22:00','0.00')");

            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`created_by`,`created_at`,`recon_amount`) VALUES ('UGA','UFLW-775987496-88980','3421','2000.00','excess_reversal','15518120762','wallet_transfer','10','2022-04-04 17:07:00','0','2022-04-04 17:07:00','0.00')");

            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`created_by`,`created_at`,`recon_amount`) VALUES ('UGA','UFLW-705782907-47139','3421','2000.00','excess_reversal','15518020116','wallet_transfer','10','2022-04-04 17:16:00','0','2022-04-04 17:16:00','0.00')");

            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`created_by`,`created_at`,`recon_amount`) VALUES ('UGA','UFLW-752546330-24861','3421','2000.00','excess_reversal','15554530730','wallet_transfer','10','2022-04-08 08:29:00','0','2022-04-08 08:29:00','0.00')");

            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`created_by`,`created_at`,`recon_amount`) VALUES ('UGA','UFLW-785788955-24713','3421','3000.00','excess_reversal','15595385584','wallet_transfer','10','2022-04-12 08:25:00','0','2022-04-12 08:25:00','0.00')");

            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`created_by`,`created_at`,`recon_amount`) VALUES ('UGA','UFLW-752546330-10133','3421','2000.00','excess_reversal','15621026079','wallet_transfer','10','2022-04-14 00:00:00','0','2022-04-14 00:00:00','0.00')");

            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`created_by`,`created_at`,`recon_amount`) VALUES ('UGA','UFLW-788318744-24218','3421','1000.00','excess_reversal','15621045845','wallet_transfer','10','2022-04-14 00:00:00','0','2022-04-14 00:00:00','0.00')");

            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`created_by`,`created_at`,`recon_amount`) VALUES ('UGA','UFLW-702076442-30083','3421','1000.00','excess_reversal','15621075124','wallet_transfer','10','2022-04-14 00:00:00','0','2022-04-14 00:00:00','0.00')");

            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`created_by`,`created_at`,`recon_amount`) VALUES ('UGA','UFLW-784159244-13433','3421','2000.00','excess_reversal','15621172758','wallet_transfer','10','2022-04-14 00:00:00','0','2022-04-14 00:00:00','0.00')");

            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`created_by`,`created_at`,`recon_amount`) VALUES ('UGA','UFLW-776609316-53365','3421','3000.00','excess_reversal','15675863577','wallet_transfer','10','2022-04-19 00:00:00','0','2022-04-19 00:00:00','0.00')");


            DB::commit();
        }
        catch(\Exception $e){
            DB::rollBack();
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
