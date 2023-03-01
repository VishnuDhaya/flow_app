<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Services\RepaymentService;

class MigrationForUnknownTxnsPhaseII extends Migration
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

            // Need to Rerun
            $acc_stmt_ids = "40202374, 34139433, 34990189";
            DB::update("UPDATE account_stmts SET recon_status = null, loan_doc_id = null where id in ($acc_stmt_ids)");


            // Wrong Account Choosed / Incorrect Data in Loan Txns

            DB::update("UPDATE loan_txns set txn_id = 'mBPzcW6u', to_ac_id = 1783, amount = 38000, principal = 0 where id = 116075");
            DB::update("UPDATE loan_txns set txn_id = 'DMgpHDTz', to_ac_id = 1783, amount = 38000, principal = 0 where id = 118065");
            DB::update("UPDATE loan_txns set txn_id = 'IpLNjNvb', to_ac_id = 1783, amount = 38000, principal = 0 where id = 125366");
            DB::update("UPDATE loan_txns set txn_id = '15978708759', principal = 0, fee = 0, penalty = 5000 where id = 125424");
            DB::update("UPDATE loan_txns set amount = '1027000', principal = 1000000, fee = 22000, penalty  = 5000  where id = 128017");
            DB::update("UPDATE loan_txns set amount = '1027000', principal = 1000000, fee = 22000, penalty  = 5000  where id = 127164");
            DB::update("UPDATE loan_txns set amount = '1027000', principal = 1000000, fee = 22000, penalty  = 5000  where id = 126334");
            DB::update("UPDATE loan_txns set amount = '1027000', principal = 1000000, fee = 22000, penalty  = 5000  where id = 125767");
            DB::update("UPDATE loan_txns set amount = '1027000', principal = 1000000, fee = 22000, penalty  = 5000  where id = 125538");
            DB::update("UPDATE loan_txns set amount = '2048000', principal = 2000000, fee = 38000, penalty  = 10000  where id = 125503");
            DB::update("UPDATE loan_txns set amount = '2048000', principal = 2000000, fee = 38000, penalty  = 10000  where id = 124303");
            DB::update("UPDATE loan_txns set amount = 517000, principal = 500000, fee = 12000, penalty  = 5000  where id = 123901");
            DB::update("UPDATE loan_txns set amount = 517000, principal = 500000, fee = 12000, penalty  = 5000  where id = 128509");
            DB::update("UPDATE loan_txns set amount = 517000, principal = 500000, fee = 12000, penalty  = 5000  where id = 116064");
            DB::update("UPDATE loan_txns set amount = 1027000, principal = 1000000, fee = 22000, penalty = 5000 where id = '121075'");
            DB::update("UPDATE loan_txns set txn_id = '15882017804', txn_mode = 'wallet_transfer', principal = 1000000, fee = 22000, penalty = 0 where id = '132985'");
            DB::update("UPDATE loan_txns set txn_id = '15882128858', principal = 0, fee = 0, penalty = 5000 where id = '121845'");
            DB::update("UPDATE loan_txns set amount = 1027000, principal = 100000, fee = 22000, penalty = 5000 where id = '122984'");
            DB::update("UPDATE loan_txns set amount = 1027000, principal = 100000, fee = 22000, penalty = 5000 where id = '123162'");
            DB::update("UPDATE loan_txns set amount = 2048000, principal = 2000000, fee = 38000, penalty = 10000 where id = '126099'");

            // Captured with Excess Amount

            DB::update("UPDATE loan_txns set amount = 1533200, recon_amount = 0 where id = 103237");
            DB::update("UPDATE loan_txns set amount = 2046000 where id = 90688");
            DB::update("UPDATE loan_txns set amount = 522000 where id = 96235");


            // Resetting Values into Loan Txns Table

            DB::update("UPDATE loan_txns set txn_id = '15956028658', principal = 0, fee = 0, penalty  = 10000  where id = 124280");
            DB::update("UPDATE loan_txns set txn_id = '15955874959', principal = 1500000, fee = 38000, penalty  = 0  where id = 133003");
            DB::update("UPDATE loan_txns set amount = 2048000, principal = 2000000, fee = 38000, penalty  = 10000  where id = 124088");
            DB::update("UPDATE loan_txns set txn_id = '7VZUcqLn', to_ac_id = 1783 where id = 116217");            
            DB::update("UPDATE loan_txns set recon_amount = 0  where id = 154606");
            DB::update("UPDATE loan_txns set amount = 767000, recon_amount = 0 where id = 91784");
            DB::update("UPDATE loan_txns set amount = 1027000 where id = 128511");
            DB::update("UPDATE loan_txns set recon_amount = 0 where id = 109230");
            DB::update("UPDATE loan_txns set txn_id  = '15763419323', recon_amount = 0 where id = 132968");
            DB::update("UPDATE loan_txns set txn_id  = '15763632493', recon_amount = 0 where id = 118718");
            DB::update("UPDATE loan_txns set recon_amount = 0 where id = 172249");
            DB::update("UPDATE loan_txns set recon_amount = 0 where id = 109314");
            DB::update("UPDATE loan_txns set recon_amount = 0 where id in (110185, 110475)");

            DB::statement("DELETE from loan_txns where id in (133025, 133023, 133017, 133012, 133008, 133006, 133004, 133001, 132999, 133026, 132952, 132979, 132992, 132994, 133013)");
    

            //Missing Penalty Loan Txns

            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-752025050-75239',NULL,'1783',NULL,'2000000.00','payment','AEWRQLek','wallet_transfer','10','2022-04-19 00:00:00',NULL,NULL,NULL,'10',NULL,'2022-04-19 13:02:26',NULL,'0.00','2000000','0','0','0')");

            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-752025050-99623',NULL,'1783',NULL,'2000000.00','payment','GedIxfLw','wallet_transfer','10','2022-04-26 00:00:00',NULL,NULL,NULL,'10',NULL,'2022-04-26 11:37:18',NULL,'0.00','2000000','0','0','0')");

            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UFLW-752025050-31428',NULL,'1783',NULL,'2000000.00','payment','Brs7kxPD','wallet_transfer','10','2022-05-18 00:00:00',NULL,NULL,NULL,'10',NULL,'2022-05-18 12:30:02',NULL,'0.00','2000000','0','0','0')");

            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`write_off_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_exec_by`,`txn_date`,`reversed_date`,`reason_for_skip`,`remarks`,`created_by`,`updated_by`,`created_at`,`updated_at`,`recon_amount`,`principal`,`fee`,`penalty`,`excess`) VALUES ('UGA','UEZM-404376-76924',NULL,'2895',NULL,'5000.00','payment','109025626','wallet_portal','10','2022-01-21 00:00:00',NULL,NULL,NULL,'10',NULL,'2022-01-21 09:07:45',NULL,'0','0','0','5000','0')");


            // Empty/Incorrect to_ac_id in Loan Txns Record

            DB::update("UPDATE loan_txns SET to_ac_id = 4161 WHERE id = 121736");
            DB::update("UPDATE loan_txns set to_ac_id = 2895 where id = 122684");
            DB::update("UPDATE loan_txns set to_ac_id = 3421, txn_id = '15978649235', txn_mode = 'wallet_transfer', principal = 1000000, fee = 22000, penalty = 0 where id = 133005");
            DB::update("UPDATE loan_txns set to_ac_id = 3421 where id = 123968");
            DB::update("UPDATE loan_txns set to_ac_id = 3421, txn_id = '15884937695/15884947023' where id = 122024");
            DB::update("UPDATE loan_txns set to_ac_id = 3421 where id = 131734");
            DB::update("UPDATE loan_txns set to_ac_id = 2895 where id = 110679");
            DB::update("UPDATE loan_txns set to_ac_id = 3421 where id = 124534");
            DB::update("UPDATE loan_txns set to_ac_id = 3421 where id in (120628, 120847,122026, 123275, 123666, 123922)");


            //Need rerun

            $stmt_txn_ids = "'mBPzcW6u', 'DMgpHDTz', 'AEWRQLek', 'GedIxfLw', 'IpLNjNvb', 'Brs7kxPD', '15978649235', '15978708759', '109025626', '15840830184', '15863542628', '15884937695', '15884947023', '16050327760', '16031998976', '16000333051', '15988355758', '15981188420', '15980604237', '15956320486', '15956028658', '15955874959', '15937081371', '15935493915', '15935512611', '15929658233', '16061043824', '16637804464', '15672923597', '16190218875', '15844095123', '15882128858', '15882017804', '15907293105', '15914777608', '15997815545', 'tlwrcnXQ', 'hyW1wpgg', '7VZUcqLn', '108149107', '108444009', '113830928', '16061068186', '16115932776', '111932296', '15959042274', '15004352488', '15395119420', '15763419323', '15763632493', '15350653123', '15396495490', '15443234172', '15423625151', '15837687976', '15839485611', '15884963807', '15915543553', '15925736600', '15934921759'";
            DB::update("update account_stmts set recon_status = null, loan_doc_id = null where stmt_txn_id in ($stmt_txn_ids)");



            //Unknown Debit Txns (Need Rerun)

            DB::update("UPDATE account_stmts SET recon_status = null, loan_doc_id = null where recon_status != '80_recon_done' and stmt_txn_type = 'debit' and date(stmt_txn_date) >= '2022-01-01' and date(stmt_txn_date) <= '2022-07-31'");


            //Query for Incorrect Partial Txn IDs

            DB::update("update loan_txns set txn_id='15828313253' where txn_id = '662546'");
            DB::update("update loan_txns set txn_id='15862876806' where txn_id = '1,022,000'");
            DB::update("update loan_txns set txn_id='15842419445' where txn_id = '10,642,835'");
            DB::update("update loan_txns set txn_id='15897141052' where txn_id = '12897118994'");
            DB::update("update loan_txns set txn_id='113830928' where txn_id = '15900362642'");
            DB::update("update loan_txns set txn_id='110599687' where txn_id = '52543580'");
            DB::update("update loan_txns set txn_id='109419004' where txn_id = '93535894'");
            DB::update("update loan_txns set txn_id='108904302' where txn_id = '4839066'");
            DB::update("update loan_txns set txn_id='16050445679' where txn_id = '160504451106'");
            DB::update("update loan_txns set txn_id='16051753846' where txn_id = '16051758197'");
            DB::update("update loan_txns set txn_id='16061138916' where txn_id = '1606112311'");
            DB::update("update loan_txns set txn_id='16124811292' where txn_id = '1612481122'");
            DB::update("update loan_txns set txn_id='16031084402' where txn_id = '16084402'");
            DB::update("update loan_txns set txn_id='15951498797' where txn_id = '1591427757'");
            DB::update("update loan_txns set txn_id='110699054',txn_date = '2022-03-01 8:13:16' where id = '98159'");
            DB::update("update loan_txns set txn_id='110514810' where txn_id = '88845787'");

            //Query for Incorrect Entire Txn IDs

            DB::update("update loan_txns set txn_id='15828036472' where txn_id = '158280036472'");
            DB::update("update loan_txns set txn_id='15837687976' where txn_id = '15837687979'");
            DB::update("update loan_txns set txn_id='15841422756' where txn_id = '15841422754'");
            DB::update("update loan_txns set txn_id='15849131488' where txn_id = '158491131488'");
            DB::update("update loan_txns set txn_id='15849824597' where txn_id = '158498245597'");
            DB::update("update loan_txns set txn_id='15851955805' where txn_id = '15851955801'");
            DB::update("update loan_txns set txn_id='15884963807' where txn_id = '15884963809'");
            DB::update("update loan_txns set txn_id='15897462621' where txn_id = '15807462621'");
            DB::update("update loan_txns set txn_id='15904031234' where txn_id = '15904031237'");
            DB::update("update loan_txns set txn_id='15916161338' where txn_id = '1591611338'");
            DB::update("update loan_txns set txn_id='15915543553' where txn_id = '15915542553'");
            DB::update("update loan_txns set txn_id='15918008548' where txn_id = '159180008548'");
            DB::update("update loan_txns set txn_id='15917145086' where txn_id = '15917144086'");
            DB::update("update loan_txns set txn_id='15925736600' where txn_id = '1592536600'");
            DB::update("update loan_txns set txn_id='15925608218' where txn_id = '1595608218'");
            DB::update("update loan_txns set txn_id='15935493915' where txn_id = '1593543915'");
            DB::update("update loan_txns set txn_id='15935098576' where txn_id = '1595098576'");
            DB::update("update loan_txns set txn_id='15934921759' where txn_id = '1593491759'");
            DB::update("update loan_txns set txn_id='15937521159' where txn_id = '1593751159'");
            DB::update("update loan_txns set txn_id='15986597048' where txn_id = '1598659708'");
            DB::update("update loan_txns set txn_id='15990736561' where txn_id = '15990736567'");
            DB::update("update loan_txns set txn_id='15999619710' where txn_id = '1599961970'");
            DB::update("update loan_txns set txn_id='15992072519' where txn_id = '15998072519'");
            DB::update("update loan_txns set txn_id='16000849029' where txn_id = '160008449029'");
            DB::update("update loan_txns set txn_id='15971151652' where txn_id = '15871151652'");
            DB::update("update loan_txns set txn_id='15967658838' where txn_id = '1596758838'");
            DB::update("update loan_txns set txn_id='15966826453' where txn_id = '1596682653'");
            DB::update("update loan_txns set txn_id='15961562931' where txn_id = '15861562931'");
            DB::update("update loan_txns set txn_id='15956376072' where txn_id = '15956376972'");
            DB::update("update loan_txns set txn_id='16039916821' where txn_id = '16039916921'");
            DB::update("update loan_txns set txn_id='15977782737' where txn_id = '1597782737'");
            DB::update("update loan_txns set txn_id='15977308067' where txn_id = '15988308067'");
            DB::update("update loan_txns set txn_id='16060908625' where txn_id = '1606090868625'");
            DB::update("update loan_txns set txn_id='16060982055' where txn_id = '1606082055'");
            DB::update("update loan_txns set txn_id='16060128349' where txn_id = '1606018349'");
            DB::update("update loan_txns set txn_id='16059263801' where txn_id = '16069263801'");
            DB::update("update loan_txns set txn_id='16122937901' where txn_id = '1622937901'");
            DB::update("update loan_txns set txn_id='16115932776' where txn_id = '1611592776'");
            DB::update("update loan_txns set txn_id='16102064608' where txn_id = '1602064608'");
            DB::update("update loan_txns set txn_id='16102046064' where txn_id = '1602046064'");
            DB::update("update loan_txns set txn_id='16101996826' where txn_id = '1610199826'");
            DB::update("update loan_txns set txn_id='16101563617' where txn_id = '1610156317'");
            DB::update("update loan_txns set txn_id='16114184482' where txn_id = '161141184482'");
            DB::update("update loan_txns set txn_id='16113899334' where txn_id = '1611389934'");
            DB::update("update loan_txns set txn_id='16113069678' where txn_id = '16113068678'");
            DB::update("update loan_txns set txn_id='16104587938' where txn_id = '16104597938'");
            DB::update("update loan_txns set txn_id='16124576772' where txn_id = '161245767772'");
            DB::update("update loan_txns set txn_id='16135547601' where txn_id = '161135520601'");
            DB::update("update loan_txns set txn_id='16134011085' where txn_id = '161340110085'");
            DB::update("update loan_txns set txn_id='16127423463' where txn_id = '161247423463'");
            DB::update("update loan_txns set txn_id='16156783853' where txn_id = '1615783853'");
            DB::update("update loan_txns set txn_id='16136525258' where txn_id = '1613652525258'");
            DB::update("update loan_txns set txn_id='16190879979' where txn_id = '161908879979'");
            DB::update("update loan_txns set txn_id='16189158162' where txn_id = '1618958162'");
            DB::update("update loan_txns set txn_id='16189062180' where txn_id = '1689062180'");
            DB::update("update loan_txns set txn_id='16189062180' where txn_id = '1689062180'");
            DB::update("update loan_txns set txn_id='16062352654' where txn_id = '1602352654'");
            DB::update("update loan_txns set txn_id='15959042274' where txn_id = '1595902274'");
            DB::update("update loan_txns set txn_id='15849651800' where txn_id = '1584964987'");
            DB::update("update loan_txns set txn_id='15832068170' where txn_id = '15832068799'");
            DB::update("update loan_txns set txn_id='16102066452' where txn_id = '1602066452'");
            DB::update("update loan_txns set txn_id='16139499585' where txn_id = '161394999585'");
            DB::update("update loan_txns set txn_id='15909164453' where txn_id = '15909144453'");
 
            DB::update("update loan_txns set txn_id = '15829104283' where txn_id = '655090'");
            DB::update("update loan_txns set txn_id = '15692236947', txn_mode = 'wallet_transfer', principal = 500000, penalty = 0 where id = 132955");
            DB::update("update loan_txns set txn_id = '15692590091', principal = 0, penalty = 5000 where id = 116694");
            DB::update("update loan_txns set txn_id = '15896544969' where id = '122578'");
            DB::update("update loan_txns set txn_id = '15926129094' where txn_id = '159261129094'");
            DB::update("update loan_txns set txn_id = '15935914149' where txn_id = '1593591419'");
            DB::update("update loan_txns set txn_id = '15955816118' where txn_id = '159558161118'");
            DB::update("update loan_txns set txn_id = '16029023465' where txn_id = '1602903465'");
            DB::update("update loan_txns set txn_id = '16029488174' where txn_id = '160294881174'");
            DB::update("update loan_txns set txn_id = '16061331488', recon_amount = 0 where id = 128547");
            DB::update("update loan_txns set txn_id = '16061329126', recon_amount = 0 where id = 128539");
            DB::update("update loan_txns set txn_id = '16100451922' where txn_id = '1600451922'");
            DB::update("update loan_txns set txn_id = '16101667638' where txn_id = '160667638'");
            DB::update("update loan_txns set txn_id = 'C1I6ANWj', to_ac_id = 1783, recon_amount = 0 where id = 101524");
            DB::update("update loan_txns set txn_id = '15290473314', recon_amount = 0 where id = 101501");
            DB::update("update loan_txns set txn_id = 'sGTUUXkJ/36TNJI48', recon_amount = 0 where id = 93903");
            DB::update("update loan_txns set txn_id = 'KqzenNBD', recon_amount = 0 where id = 96617");
            DB::update("update loan_txns set txn_id = 'dHqLVNZ5', recon_amount = 0 where id = 96616");
            DB::update("update loan_txns set txn_id = '108904302' where id = 92476");
            DB::update("update loan_txns set txn_id = '16040999198', recon_amount = 0 where id = 127572");
            DB::update("update loan_txns set txn_id = '16041005360', recon_amount = 0 where id = 127574");
            DB::update("update loan_txns set txn_id  = '15935493915/15935512611' where id = 123968");
            DB::update("update loan_txns set txn_id = '15915796160' where id = 123286");

            DB::update("UPDATE account_stmts set recon_status = '31_paid_to_different_acc' where id = 39878900");
            DB::update("UPDATE account_stmts SET recon_status = null, loan_doc_id = null where stmt_txn_id in ('15915796160', '15935493915', '16040999198', '16041005360', '108904302', 'dHqLVNZ5', 'KqzenNBD', '8sRurSWd', '36TNJI48', 'sGTUUXkJ', 'C1I6ANWj', '16101667638', '16100451922', '16061331488', '16061329126', '16029488174', '16029023465', '15955816118', '15935914149', '15926129094', '15896544969', '15692236947', '15829104283', '110193208', '15828313253','15862876806','15842419445','15897141052','113830928','110599687','109419004','108904302','16050445679','16051753846','16061138916','16124811292','16031084402','15951498797','110699054','110514810','15828036472','15837687976','15841422756','15849131488','15849824597','15851955805','15884963807','15897462621','15904031234','15916161338','15915543553','15918008548','15917145086','15925736600','15925608218','15935493915','15935098576','15934921759','15937521159','15986597048','15990736561','15999619710','15992072519','16000849029','15971151652','15967658838','15966826453','15961562931','15956376072','16039916821','15977782737','15977308067','16060908625','16060982055','16060128349','16059263801','16122937901','16115932776','16102064608','16102046064','16101996826','16101563617','16114184482','16113899334','16113069678','16104587938','16124576772','16135547601','16134011085','16127423463','16156783853','16136525258','16190879979','16189158162','16189062180','16189062180','16062352654','15959042274','15849651800','15832068170','16102066452','16139499585','15909164453')");

            DB::commit();
        }
        catch(\Exception $exc){
            DB::rollback();
            throw $exc;
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
