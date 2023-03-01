<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use TheSeer\Tokenizer\Exception;

class AddMissingTxns extends Migration
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
            DB::table('account_stmts')->insert([
                ['account_id' => '4183', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791519171', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-04 16:27:01' , 'stmt_txn_id' => '6394360810' , 'ref_account_num' => 250784440273 , 'dr_amt' => 0 , 'cr_amt' => 303000 ,'amount' => 303000],
                ['account_id' => '4183', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791519171', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-04 16:51:37' , 'stmt_txn_id' => '6394548816' , 'ref_account_num' => 250782336153 , 'dr_amt' => 0 , 'cr_amt' => 303000 ,'amount' => 303000],
                ['account_id' => '4183', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791519171', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-06 17:39:06' , 'stmt_txn_id' => '6409493732' , 'ref_account_num' => 250785149138 , 'dr_amt' => 0 , 'cr_amt' => 153000 ,'amount' => 153000],
                ['account_id' => '4183', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791519171', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'debit', 'stmt_txn_date' => '2022-05-09 12:28:10' , 'stmt_txn_id' => '38888739659' , 'ref_account_num' => 250787100000 , 'dr_amt' => 1000000 , 'cr_amt' => 0 ,'amount' => 1000000],
                ['account_id' => '4183', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791519171', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'debit', 'stmt_txn_date' => '2022-05-09 12:28:53' , 'stmt_txn_id' => '38888769038' , 'ref_account_num' => 250787100000 , 'dr_amt' => 270000 , 'cr_amt' => 0 ,'amount' => 270000],
                ['account_id' => '4183', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791519171', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-09 17:02:51' , 'stmt_txn_id' => '6429912052' , 'ref_account_num' => 250784440273 , 'dr_amt' => 0 , 'cr_amt' => 303000 ,'amount' => 303000],
                ['account_id' => '4183', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791519171', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-10 10:56:38' , 'stmt_txn_id' => '6434537785' , 'ref_account_num' => 250780107131 , 'dr_amt' => 0 , 'cr_amt' => 306000 ,'amount' => 306000],
                ['account_id' => '4183', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791519171', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-11 09:41:42' , 'stmt_txn_id' => '6441033126' , 'ref_account_num' => 250788425429 , 'dr_amt' => 0 , 'cr_amt' => 204000 ,'amount' => 204000],
                ['account_id' => '4183', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791519171', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-11 16:58:17' , 'stmt_txn_id' => '6444023786' , 'ref_account_num' => 250782336153 , 'dr_amt' => 0 , 'cr_amt' => 303000 ,'amount' => 303000],
                ['account_id' => '4183', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791519171', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-14 16:26:07' , 'stmt_txn_id' => '6464841191' , 'ref_account_num' => 250785149138 , 'dr_amt' => 0 , 'cr_amt' => 153000 ,'amount' => 153000],
                
                ['account_id' => '4184', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791516469', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'debit', 'stmt_txn_date' => '2022-05-04 09:45:28' , 'stmt_txn_id' => '38649549590' ,  'ref_account_num' => null, 'dr_amt' => 26 , 'cr_amt' => 0 ,'amount' => 26],
                ['account_id' => '4184', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791516469', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-05 16:28:52' , 'stmt_txn_id' => '6401584294' , 'ref_account_num' => 250791566249 , 'dr_amt' => 0 , 'cr_amt' => 303000 ,'amount' => 303000],
                ['account_id' => '4184', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791516469', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'debit', 'stmt_txn_date' => '2022-05-09 11:06:53' , 'stmt_txn_id' => '38885096479' , 'ref_account_num' => 250788316032 , 'dr_amt' => 305000 , 'cr_amt' => 0 ,'amount' => 305000],
                ['account_id' => '4184', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791516469', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-09 12:47:35' , 'stmt_txn_id' => '6428113248' , 'ref_account_num' => 250786081649 , 'dr_amt' => 0 , 'cr_amt' => 306000 ,'amount' => 306000],
                ['account_id' => '4184', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791516469', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-12 16:46:27' , 'stmt_txn_id' => '6450862272' , 'ref_account_num' => 250791566249 , 'dr_amt' => 0 , 'cr_amt' => 306000 ,'amount' => 306000],

                ['account_id' => '4185', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791334419', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-03 16:51:35' , 'stmt_txn_id' => '6387449119' , 'ref_account_num' => 250782521352 , 'dr_amt' => 0 , 'cr_amt' => 151500 ,'amount' => 151500],
                ['account_id' => '4185', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791334419', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-04 16:14:16' , 'stmt_txn_id' => '6394267578' , 'ref_account_num' => 250782340820 , 'dr_amt' => 0 , 'cr_amt' => 303000 ,'amount' => 303000],
                ['account_id' => '4185', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791334419', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-04 16:52:43' , 'stmt_txn_id' => '6394557017' , 'ref_account_num' => 250783940173 , 'dr_amt' => 0 , 'cr_amt' => 303000 ,'amount' => 303000],
                ['account_id' => '4185', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791334419', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-05 12:19:57' , 'stmt_txn_id' => '6399862142' , 'ref_account_num' => 250783684068 , 'dr_amt' => 0 , 'cr_amt' => 151000 ,'amount' => 151000],
                ['account_id' => '4185', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791334419', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-05 12:25:02' , 'stmt_txn_id' => '6399896815' , 'ref_account_num' => 250783684068 , 'dr_amt' => 0 , 'cr_amt' => 500 ,'amount' => 500],
                ['account_id' => '4185', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791334419', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-06 16:05:01' , 'stmt_txn_id' => '6408678959' , 'ref_account_num' => 250789967734 , 'dr_amt' => 0 , 'cr_amt' => 306000 ,'amount' => 306000],
                ['account_id' => '4185', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791334419', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-06 16:17:28' , 'stmt_txn_id' => '6408774747' , 'ref_account_num' => 250781601473 , 'dr_amt' => 0 , 'cr_amt' => 306000 ,'amount' => 306000],
                ['account_id' => '4185', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791334419', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'debit', 'stmt_txn_date' => '2022-05-09 13:24:14' , 'stmt_txn_id' => 'RMTN4185a' , 'ref_account_num' => 250788357941 , 'dr_amt' => 522000 , 'cr_amt' => 0 ,'amount' => 522000],
                ['account_id' => '4185', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791334419', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'debit', 'stmt_txn_date' => '2022-05-09 13:25:19' , 'stmt_txn_id' => 'RMTN4185b' , 'ref_account_num' => 250788357941 , 'dr_amt' => 1000000 , 'cr_amt' => 0 ,'amount' => 1000000],
                ['account_id' => '4185', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791334419', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-09 15:16:51' , 'stmt_txn_id' => '6429132647' , 'ref_account_num' => 250782340820 , 'dr_amt' => 0 , 'cr_amt' => 303000 ,'amount' => 303000],
                ['account_id' => '4185', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791334419', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-09 17:47:33' , 'stmt_txn_id' => '6430324369' , 'ref_account_num' => 250783940173 , 'dr_amt' => 0 , 'cr_amt' => 303000 ,'amount' => 303000],
                ['account_id' => '4185', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791334419', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-10 17:34:40' , 'stmt_txn_id' => '6437270640' , 'ref_account_num' => 250782521352 , 'dr_amt' => 0 , 'cr_amt' => 303000 ,'amount' => 303000],
                ['account_id' => '4185', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791334419', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-13 16:19:40' , 'stmt_txn_id' => '6457757628' , 'ref_account_num' => 250781601473 , 'dr_amt' => 0 , 'cr_amt' => 306000 ,'amount' => 306000],
                ['account_id' => '4185', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791334419', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-13 18:15:00' , 'stmt_txn_id' => '6458782234' , 'ref_account_num' => 250783940173 , 'dr_amt' => 0 , 'cr_amt' => 303000 ,'amount' => 303000],
                ['account_id' => '4185', 'acc_prvdr_code' => 'RMTN', 'acc_number' => '791334419', 'country_code'=> 'RWA', 'source' => 'stmt', 'import_id' => 999, 'stmt_txn_type' => 'credit', 'stmt_txn_date' => '2022-05-14 16:54:03' , 'stmt_txn_id' => '6465031061' , 'ref_account_num' => 250782521352 , 'dr_amt' => 0 , 'cr_amt' => 151500 ,'amount' => 151500],
            ]);

            DB::update("update account_stmts set acc_txn_type = 'withdrawal', recon_status = '80_recon_done' where stmt_txn_id in ('38888739659', '38888769038', '38885096479', 'RMTN4185a', 'RMTN4185b')");
            DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'int_transfer_dr' where country_code = 'RWA' and descr regexp '250791519171|250791516469|250791334419' and stmt_txn_type = 'debit' and account_id = 4182;");
            DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'charges' where descr like '%Charge%'"); 
            DB::update("update loan_txns set txn_type = 'duplicate_payment_reversal', txn_mode = 'wallet_transfer'  where id = 243862");
            DB::insert("INSERT INTO `flow_api`.`loan_txns` (`country_code`,`loan_doc_id`,`from_ac_id`,`to_ac_id`,`amount`,`txn_type`,`txn_id`,`txn_mode`,`txn_date`,`created_by`,`created_at`,`recon_amount`) VALUES ('RWA','RFLW-785480276-13811','6490','4184','409350.00','duplicate_payment','7864848629','instant_disbursal','2022-11-14 14:16:00','0','2022-11-14 16:01:53','0.00')");
            DB::update("UPDATE `flow_api`.`account_stmts` SET `loan_doc_id` = 'RFLW-785480276-13811' WHERE `id` = 48667312");
            DB::update("UPDATE `flow_api`.`account_stmts` SET `recon_status` = '80_recon_done',`loan_doc_id` = 'RFLW-785480276-13811' WHERE `id` = 48666417");
            DB::commit();
        }
        catch (Exception $e){
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
