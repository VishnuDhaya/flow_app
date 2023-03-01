<?php


namespace App\Scripts\php;

use App\Consts;
use App\Repositories\SQL\AccountStmtRepositorySQL;
use App\Repositories\SQL\LoanTransactionRepositorySQL;
use App\Services\LoanService;
use App\Services\RepaymentService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaidtoDifferentAccountScript{

    public function run(){

        try{

            DB::beginTransaction();

            session()->put('country_code', 'UGA');
            session()->put('user_id',0);

            $loan_txn_repo = new LoanTransactionRepositorySQL();
            $repayment_serv = new RepaymentService();

            /*$acc_txns = [

                0 => [  
                    'loan_doc_id' => 'UEZM-121601-57122', 
                    'cust_id' => 'UEZM-121601',
                    'cr_amount' => '2038000.00', 
                    'from_acc_id' => 3605, 
                    'to_acc_id' => 4161, 
                    'stmt_txn_id' => '16254019368', 
                    'stmt_txn_date' => '2022-06-13 13:14:00', 
                    'txn_id' => '16254444808', 
                    'txn_date' => '2022-06-13 13:57:00',
                    'principal' => '2000000.00',
                    'fee' => '38000.00',
                    'penalty' => 0
                    ],
                1 => [  
                    'loan_doc_id' => 'UFLW-759017358-96536', 
                    'cust_id' => 'UFLW-759017358',
                    'cr_amount' => '1011000.00', 
                    'from_acc_id' => 3605, 
                    'to_acc_id' => 4161, 
                    'stmt_txn_id' => '16878151228', 
                    'stmt_txn_date' => '2022-08-08 13:01:00', 
                    'txn_id' => '16878324378', 
                    'txn_date' => '2022-08-08 13:18:00',
                    'principal' => '1000000.00',
                    'fee' => '11000.00',
                    'penalty' => 0
                ],
                2 => [  
                    'loan_doc_id' => 'UFLW-782198421-13940', 
                    'cust_id' => 'UFLW-782198421',
                    'cr_amount' => '1022000.00', 
                    'from_acc_id' => 3605, 
                    'to_acc_id' => 4161, 
                    'stmt_txn_id' => '16975963169', 
                    'stmt_txn_date' => '2022-08-17 12:57:00', 
                    'txn_id' => '16976468657', 
                    'txn_date' => '2022-08-17 13:47:00',
                    'principal' => '1000000.00',
                    'fee' => '22000.00',
                    'penalty' => 0
                ],
                3 => [  
                    'loan_doc_id' => 'UFLW-708226459-51830', 
                    'cust_id' => 'UFLW-708226459',
                    'cr_amount' => '1532000.00', 
                    'from_acc_id' => 3605, 
                    'to_acc_id' => 4161, 
                    'stmt_txn_id' => '17494349919', 
                    'stmt_txn_date' => '2022-10-01 12:13:00', 
                    'txn_id' => '17495120667', 
                    'txn_date' => '2022-10-01 13:16:00',
                    'principal' => '1500000.00',
                    'fee' => '32000.00',
                    'penalty' => 0
                ],
                4 => [  
                    'loan_doc_id' => 'UFLW-758014173-37390', 
                    'cust_id' => 'UFLW-758014173',
                    'cr_amount' => '1022000.00', 
                    'from_acc_id' => 3605, 
                    'to_acc_id' => 4094, 
                    'stmt_txn_id' => '16891042742', 
                    'stmt_txn_date' => '2022-08-09 15:52:00', 
                    'txn_id' => '16891214129', 
                    'txn_date' => '2022-08-09 16:08:00',
                    'principal' => '1000000.00',
                    'fee' => '22000.00',
                    'penalty' => 0
                ],
                5 => [
                    'loan_doc_id' => 'CCA-638375-73370', 
                    'cust_id' => 'UFLW-701822650',
                    'cr_amount' => '100000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 4094, 
                    'stmt_txn_id' => '17760680482', 
                    'stmt_txn_date' => '2022-10-24 11:47:00', 
                    'txn_id' => '17768668413', 
                    'txn_date' => '2022-10-24 22:52:00',
                    'principal' => '100000.00',
                    'fee' => '0',
                    'penalty' => 0
                ],
                6 => [
                    'loan_doc_id' => 'UEZM 06408',
                    'cust_id' => 'UFLW-782470680', 
                    'cr_amount' => '20000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 3605, 
                    'stmt_txn_id' => '17582750689', 
                    'stmt_txn_date' => '2022-10-08 18:33:00', 
                    'txn_id' => '17600727288', 
                    'txn_date' => '2022-10-10 09:28:00',
                    'principal' => '20000.00',
                    'fee' => '0',
                    'penalty' => 0
                ],
                7 => [
                    'loan_doc_id' => 'CCA-638375-73370', 
                    'cust_id' => 'UFLW-701822650',
                    'cr_amount' => '100000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 4094, 
                    'stmt_txn_id' => '17231851502', 
                    'stmt_txn_date' => '2022-09-09 09:19:00', 
                    'txn_id' => '17247986756', 
                    'txn_date' => '2022-09-10 17:14:00',
                    'principal' => '100000.00',
                    'fee' => '0',
                    'penalty' => 0
                ],
                8 => [
                    'loan_doc_id' => 'UEZM 06408', 
                    'cust_id' => 'UFLW-782470680', 
                    'cr_amount' => '20000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 3605, 
                    'stmt_txn_id' => '17190373428', 
                    'stmt_txn_date' => '2022-09-05 16:18:00', 
                    'txn_id' => '17198391633', 
                    'txn_date' => '2022-09-06 11:00:00',
                    'principal' => '20000.00',
                    'fee' => '0',
                    'penalty' => 0
                ],
                9 => [
                    'loan_doc_id' => 'CCA-139017-23139', 
                    'cust_id' => 'UFLW-701396589',
                    'cr_amount' => '50000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 4094, 
                    'stmt_txn_id' => '16954323995', 
                    'stmt_txn_date' => '2022-08-15 12:26:00', 
                    'txn_id' => '16963368252', 
                    'txn_date' => '2022-08-16 09:27:00',
                    'principal' => '50000.00',
                    'fee' => '0',
                    'penalty' => 0
                ],
                10 => [
                    'loan_doc_id' => 'CCA-186085-49077', 
                    'cust_id' => 'UFLW-754855194',
                    'cr_amount' => '50000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 4094, 
                    'stmt_txn_id' => '16954295974', 
                    'stmt_txn_date' => '2022-08-15 12:23:00', 
                    'txn_id' => '16963362104', 
                    'txn_date' => '2022-08-16 09:27:00',
                    'principal' => '50000.00',
                    'fee' => '0',
                    'penalty' => 0
                ],
                11 => [
                    'loan_doc_id' => 'UFLW-702201948-31815', 
                    'cust_id' => 'UFLW-702201948',
                    'cr_amount' => '2038000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 3605, 
                    'stmt_txn_id' => '16806478122', 
                    'stmt_txn_date' => '2022-08-02 10:15:00', 
                    'txn_id' => '16808046492', 
                    'txn_date' => '2022-08-16 09:27:00',
                    'principal' => '2000000.00',
                    'fee' => '38000.00',
                    'penalty' => 0
                ],
                12 => [
                    'loan_doc_id' => 'UFLW-754704239-64785', 
                    'cust_id' => 'UFLW-754704239',
                    'cr_amount' => '1022000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 3605, 
                    'stmt_txn_id' => '16606115684', 
                    'stmt_txn_date' => '2022-07-15 12:50:00', 
                    'txn_id' => '16607009116', 
                    'txn_date' => '2022-07-15 14:18:00',
                    'principal' => '1022000.00',
                    'fee' => '22000.00',
                    'penalty' => 0
                ],
                13 => [
                    'loan_doc_id' => 'CCA-999353-38876', 
                    'cust_id' => 'UFLW-755010899',
                    'cr_amount' => '32000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 4094, 
                    'stmt_txn_id' => '16123208976', 
                    'stmt_txn_date' => '2022-06-01 09:11:00', 
                    'txn_id' => '16135639925', 
                    'txn_date' => '2022-06-02 11:13:00',
                    'principal' => '32000.00',
                    'fee' => '0.00',
                    'penalty' => 0
                ],
                14 => [
                    'loan_doc_id' => 'UEZM 06446',
                    'cust_id' => 'UEZM-818083', 
                    'cr_amount' => '500000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 3605, 
                    'stmt_txn_id' => '16180060472', 
                    'stmt_txn_date' => '2022-06-06 13:49:00', 
                    'txn_id' => '16180487164', 
                    'txn_date' => '2022-06-06 14:33:00',
                    'principal' => '500000.00',
                    'fee' => '0.00',
                    'penalty' => 0
                ],
                15 => [
                    'loan_doc_id' => 'UFLW-777105883-18697', 
                    'cust_id' => 'UFLW-777105883',
                    'cr_amount' => '1027000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 4094, 
                    'stmt_txn_id' => '16323566423', 
                    'stmt_txn_date' => '2022-06-19 20:30:00', 
                    'txn_id' => '16330503827', 
                    'txn_date' => '2022-06-20 16:12:00',
                    'principal' => '1000000.00',
                    'fee' => '27000.00',
                    'penalty' => '5000.00'
                ],
                16 => [
                    'loan_doc_id' => 'UEZM 06408', 
                    'cust_id' => 'UFLW-782470680', 
                    'cr_amount' => '20000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 3605, 
                    'stmt_txn_id' => '16403204410', 
                    'stmt_txn_date' => '2022-06-27 10:30:00', 
                    'txn_id' => '16404696541', 
                    'txn_date' => '2022-06-27 12:48:00',
                    'principal' => '20000.00',
                    'fee' => '0.00',
                    'penalty' => 0
                ],
                17 => [  
                    'loan_doc_id' => 'UFLW-778161888-33847', 
                    'cust_id' => 'UFLW-778161888',
                    'cr_amount' => '1022000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 3421, 
                    'stmt_txn_id' => '15268835683', 
                    'stmt_txn_date' => '2022-03-10 09:39:00', 
                    'txn_id' => '15270163240', 
                    'txn_date' => '2022-03-10 12:00:00',
                    'principal' => '1000000.00',
                    'fee' => '22000.00',
                    'penalty' => 0
                ],
                18 => [  
                    'loan_doc_id' => 'UFLW-778161888-90526', 
                    'cust_id' => 'UFLW-778161888',
                    'cr_amount' => '1022000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 3421, 
                    'stmt_txn_id' => '15337997686', 
                    'stmt_txn_date' => '2022-03-17 11:04:00', 
                    'txn_id' => '15339249947', 
                    'txn_date' => '2022-03-17 13:23:00',
                    'principal' => '1000000.00',
                    'fee' => '22000.00',
                    'penalty' => 0
                ],
                19 => [  
                    'loan_doc_id' => 'UFLO-696559-57101', 
                    'cust_id' => 'UFLW-777606060',
                    'cr_amount' => '1022000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 3421, 
                    'stmt_txn_id' => '15337540796', 
                    'stmt_txn_date' => '2022-03-17 10:16:00', 
                    'txn_id' => '15337716329', 
                    'txn_date' => '2022-03-17 10:34:00',
                    'principal' => '1000000.00',
                    'fee' => '22000.00',
                    'penalty' => 0
                ],
                20 => [  
                    'loan_doc_id' => 'UFLO-141342-85813', 
                    'cust_id' => 'UFLW-787989603',
                    'cr_amount' => '512000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 3421, 
                    'stmt_txn_id' => '15008214034', 
                    'stmt_txn_date' => '2022-02-11 13:55:00', 
                    'txn_id' => '15008241738', 
                    'txn_date' => '2022-02-11 13:58:00',
                    'principal' => '500000.00',
                    'fee' => '12000.00',
                    'penalty' => 0
                ],
                21 => [  
                    'loan_doc_id' => 'UFLW-787989603-38386', 
                    'cust_id' => 'UFLW-787989603',
                    'cr_amount' => '512000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 3421, 
                    'stmt_txn_id' => '15084827816', 
                    'stmt_txn_date' => '2022-02-19 14:39:00', 
                    'txn_id' => '15085513754', 
                    'txn_date' => '2022-02-19 15:55:00',
                    'principal' => '500000.00',
                    'fee' => '12000.00',
                    'penalty' => 0
                ],
                22 => [  
                    'loan_doc_id' => 'UFLW-778161888-85276', 
                    'cust_id' => 'UFLW-778161888',
                    'cr_amount' => '1022000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 3421, 
                    'stmt_txn_id' => '15191091371', 
                    'stmt_txn_date' => '2022-03-02 15:53:00', 
                    'txn_id' => '15191139803', 
                    'txn_date' => '2022-03-02 15:58:00',
                    'principal' => '1000000.00',
                    'fee' => '22000.00',
                    'penalty' => 0
                ],
                23 => [  
                    'loan_doc_id' => 'UFLW-708942574-77272', 
                    'cust_id' => 'UFLW-708942574',
                    'cr_amount' => '1532000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 3421, 
                    'stmt_txn_id' => '15414080147', 
                    'stmt_txn_date' => '2022-03-25 09:00:00', 
                    'txn_id' => '15414630126', 
                    'txn_date' => '2022-03-25 10:03:00',
                    'principal' => '1500000.00',
                    'fee' => '32000.00',
                    'penalty' => 0
                ],
                24 => [  
                    'loan_doc_id' => 'UFLW-786823347-22356', 
                    'cust_id' => 'UFLW-786823347',
                    'cr_amount' => '1022000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 3728, 
                    'stmt_txn_id' => '15596927186', 
                    'stmt_txn_date' => '2022-04-12 11:06:00', 
                    'txn_id' => '15597485263', 
                    'txn_date' => '2022-04-12 12:01:00',
                    'principal' => '1000000.00',
                    'fee' => '22000.00',
                    'penalty' => 0
                ],
                25 => [  
                    'loan_doc_id' => 'UFLW-751137144-65156', 
                    'cust_id' => 'UFLW-751137144',
                    'cr_amount' => '1022000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 3421, 
                    'stmt_txn_id' => '15258683446', 
                    'stmt_txn_date' => '2022-03-09 09:30:00', 
                    'txn_id' => '15259119832', 
                    'txn_date' => '2022-03-09 10:18:00',
                    'principal' => '1000000.00',
                    'fee' => '22000.00',
                    'penalty' => 0
                ],
                26 => [  
                    'loan_doc_id' => 'CCA-673421-90846', 
                    'cust_id' => 'UFLW-783055904',
                    'cr_amount' => '1022000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 3421, 
                    'stmt_txn_id' => '15258692798', 
                    'stmt_txn_date' => '2022-03-09 09:31:00', 
                    'txn_id' => '15259128484', 
                    'txn_date' => '2022-03-09 10:19:00',
                    'principal' => '1000000.00',
                    'fee' => '22000.00',
                    'penalty' => 0
                ],
                27 => [  
                    'loan_doc_id' => 'UFLW-776561283-98194', 
                    'cust_id' => 'UFLW-776561283',
                    'cr_amount' => '1022000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 3421, 
                    'stmt_txn_id' => '15207238564', 
                    'stmt_txn_date' => '2022-03-04 08:35:00', 
                    'txn_id' => '15207343995', 
                    'txn_date' => '2022-03-04 08:48:00',
                    'principal' => '1000000.00',
                    'fee' => '22000.00',
                    'penalty' => 0
                ],
                28 => [  
                    'loan_doc_id' => 'UFLW-777105883-20427', 
                    'cust_id' => 'UFLW-777105883',
                    'cr_amount' => '1027000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 4094, 
                    'stmt_txn_id' => '16603856060', 
                    'stmt_txn_date' => '2022-07-15 09:20:00', 
                    'txn_id' => '16604289323', 
                    'txn_date' => '2022-07-15 10:02:00',
                    'principal' => '1000000.00',
                    'fee' => '27000.00',
                    'penalty' => '5000.00',
                ],
                29 => [
                    'loan_doc_id' => 'UFLW-755455020-84860',
                    'cust_id' => 'UFLW-755455020',
                    'cr_amount' => '2038000.00',
                    'from_acc_id' => 4094,
                    'to_acc_id' => 4161,
                    'stmt_txn_id' => '15742489707',
                    'stmt_txn_date' => '2022-04-26 09:10:00',
                    'txn_id' => '15742593277',
                    'txn_date' => '2022-04-26 09:22:00',
                    'principal' => '2000000.00',
                    'fee' => '38000.00',
                    'penalty' => 0
                ],
                30 => [
                    'loan_doc_id' => 'UFLW-756099274-72359',
                    'cust_id' => 'UFLW-756099274',
                    'cr_amount' => '1022000.00',
                    'from_acc_id' => 4094,
                    'to_acc_id' => 3605,
                    'stmt_txn_id' => '15766310525', 
                    'stmt_txn_date' => '2022-04-28 13:59:00',
                    'txn_id' => '15766726047',
                    'txn_date' => '2022-04-28 14:43:00',
                    'principal' => '1000000.00',
                    'fee' => '22000.00',
                    'penalty' => 0,
                ],
                31 => [  
                    'loan_doc_id' => 'UFLO-721017-27695', 
                    'cust_id' => 'UFLW-788238252',
                    'cr_amount' => '1022000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 3421, 
                    'stmt_txn_id' => '14645517087', 
                    'stmt_txn_date' => '2022-01-04 12:24:00', 
                    'txn_id' => '14645751697', 
                    'txn_date' => '2022-01-04 12:50:00',
                    'principal' => '1000000.00',
                    'fee' => '22000.00',
                    'penalty' => 0
                ],
                32 => [
                    'loan_doc_id' => 'UFLW-708226459-69192',
                    'cust_id' => 'UFLW-708226459',
                    'cr_amount' => '1532000.00',
                    'from_acc_id' => 4094,
                    'to_acc_id' => 3605,
                    'stmt_txn_id' => '16049724944',
                    'stmt_txn_date' => '2022-05-25 09:38:00',
                    'txn_id' => '16050938186',
                    'txn_date' => '2022-05-25 11:39:00',
                    'principal' => '1500000.00',
                    'fee' => '32000.00',
                    'penalty' => 0
                ],
                33 => [
                    'loan_doc_id' => 'UFLW-753829194-73073',
                    'cust_id' => 'UFLW-753829194',
                    'cr_amount' => '1532000.00',
                    'from_acc_id' => 3605,
                    'to_acc_id' => 3421,
                    'stmt_txn_id' => '15475202337',
                    'stmt_txn_date' => '2022-03-31 15:05:00',
                    'txn_id' => '15476074769',
                    'txn_date' => '2022-03-31 16:39:00',
                    'principal' => '1500000.00',
                    'fee' => '32000.00',
                    'penalty' => 0
                ],
                34 => [
                    'loan_doc_id' => 'CCA-462556-59333',
                    'cust_id' => 'UFLW-703773380',
                    'cr_amount' => '100000.00',
                    'from_acc_id' => 3605,
                    'to_acc_id' => 4094,
                    'stmt_txn_id' => '15644695575',
                    'stmt_txn_date' => '2022-04-16 17:40:00',
                    'txn_id' => '15681029639',
                    'txn_date' => '2022-04-20 08:41:00',
                    'principal' => '100000.00',
                    'fee' => 0,
                    'penalty' => 0
                ]];*/

            //     $acc_txns = [
            //         0 => [  
            //         'loan_doc_id' => 'UFLW-703662466-71113', 
            //         'cust_id' => 'UFLW-703662466',
            //         'cr_amount' => '1022000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16762284339', 
            //         'stmt_txn_date' => '2022-07-29 11:21:00', 
            //         'txn_id' => '16763403356', 
            //         'txn_date' => '2022-07-29 13:01:00',
            //         'principal' => '1000000.00',
            //         'fee' => '22000.00',
            //         'penalty' => 0
            //         ],
            //     1 => [  
            //         'loan_doc_id' => 'CCA-859297-14359', 
            //         'cust_id' => 'UFLW-785314582',
            //         'cr_amount' => '2038000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '14970120115', 
            //         'stmt_txn_date' => '2022-02-07 13:31:00', 
            //         'txn_id' => '14977719899', 
            //         'txn_date' => '2022-02-08 10:06:00',
            //         'principal' => '2000000.00',
            //         'fee' => '38000.00',
            //         'penalty' => 0
            //     ],
            //     2 => [  
            //         'loan_doc_id' => 'CCA-392356-97760', 
            //         'cust_id' => 'CCA-392356',
            //         'cr_amount' => '500000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '14944238830', 
            //         'stmt_txn_date' => '2022-02-04 18:13:00', 
            //         'txn_id' => '14977164672', 
            //         'txn_date' => '2022-02-08 09:02:00',
            //         'principal' => '500000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            //     3 => [  
            //         'loan_doc_id' => 'UFLW-784723338-86515', 
            //         'cust_id' => 'UFLW-784723338',
            //         'cr_amount' => '1022000.00',
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16760320293', 
            //         'stmt_txn_date' => '2022-07-29 08:15:00', 
            //         'txn_id' => '16760469974', 
            //         'txn_date' => '2022-07-29 08:33:00',
            //         'principal' => '1000000.00',
            //         'fee' => '22000.00',
            //         'penalty' => 0
            //     ],
            //     4 => [  
            //         'loan_doc_id' => 'UFLW-703608562-18122', 
            //         'cust_id' => 'UFLW-703608562',
            //         'cr_amount' => '1022000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16740676226', 
            //         'stmt_txn_date' => '2022-07-27 14:43:00', 
            //         'txn_id' => '16740698647', 
            //         'txn_date' => '2022-07-27 14:45:00',
            //         'principal' => '1000000.00',
            //         'fee' => '22000.00',
            //         'penalty' => 0
            //     ],
            //     5 => [
            //         'loan_doc_id' => 'UFLW-786350822-40893', 
            //         'cust_id' => 'UFLW-786350822',
            //         'cr_amount' => '512000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16714214179', 
            //         'stmt_txn_date' => '2022-07-25 08:28:00', 
            //         'txn_id' => '16714241296', 
            //         'txn_date' => '2022-07-25 08:31:00',
            //         'principal' => '500000.00',
            //         'fee' => '12000.00',
            //         'penalty' => 0
            //     ],
            //     6 => [
            //         'loan_doc_id' => 'UFLW-786268773-77227',
            //         'cust_id' => 'UFLW-786268773', 
            //         'cr_amount' => '1022000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16696430281', 
            //         'stmt_txn_date' => '2022-07-23 15:08:00', 
            //         'txn_id' => '16696490830', 
            //         'txn_date' => '2022-07-23 15:14:00',
            //         'principal' => '1000000.00',
            //         'fee' => '22000.00',
            //         'penalty' => 0
            //     ],
            //     7 => [
            //         'loan_doc_id' => 'UFLW-783489327-89940', 
            //         'cust_id' => 'UFLW-783489327',
            //         'cr_amount' => '512000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16692513228', 
            //         'stmt_txn_date' => '2022-07-23 08:56:00', 
            //         'txn_id' => '16692612979', 
            //         'txn_date' => '2022-07-23 09:06:00',
            //         'principal' => '500000.00',
            //         'fee' => '12000.00',
            //         'penalty' => 0
            //     ],
            //     8 => [
            //         'loan_doc_id' => 'UFLW-757704160-44984', 
            //         'cust_id' => 'UFLW-757704160', 
            //         'cr_amount' => '2038000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16683507530', 
            //         'stmt_txn_date' => '2022-07-22 12:36:00', 
            //         'txn_id' => '16683991155', 
            //         'txn_date' => '2022-07-22 13:23:00',
            //         'principal' => '2000000.00',
            //         'fee' => '38000.00',
            //         'penalty' => 0
            //     ],
            //     9 => [
            //         'loan_doc_id' => 'UFLW-759689495-52956', 
            //         'cust_id' => 'UFLW-759689495',
            //         'cr_amount' => '1022000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16570599589', 
            //         'stmt_txn_date' => '2022-07-12 08:54:00', 
            //         'txn_id' => '16571033452', 
            //         'txn_date' => '2022-07-12 09:39:00',
            //         'principal' => '1000000.00',
            //         'fee' => '22000.00',
            //         'penalty' => 0
            //     ],
            //     10 => [
            //         'loan_doc_id' => 'UFLW-705769295-98880', 
            //         'cust_id' => 'UFLW-705769295',
            //         'cr_amount' => '1022000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16584121125', 
            //         'stmt_txn_date' => '2022-07-13 12:57:00', 
            //         'txn_id' => '16584138120', 
            //         'txn_date' => '2022-07-13 12:59:00',
            //         'principal' => '1000000.00',
            //         'fee' => '22000.00',
            //         'penalty' => 0
            //     ],
            //     11 => [
            //         'loan_doc_id' => 'UFLW-700749533-79136', 
            //         'cust_id' => 'UFLW-700749533',
            //         'cr_amount' => '512000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16595183686', 
            //         'stmt_txn_date' => '2022-07-14 13:02:00', 
            //         'txn_id' => '16595251172', 
            //         'txn_date' => '2022-07-14 13:08:00',
            //         'principal' => '500000.00',
            //         'fee' => '12000.00',
            //         'penalty' => 0
            //     ],
            //     12 => [
            //         'loan_doc_id' => 'UFLW-706188467-11915', 
            //         'cust_id' => 'UFLW-706188467',
            //         'cr_amount' => '1022000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16597067922', 
            //         'stmt_txn_date' => '2022-07-14 16:14:00', 
            //         'txn_id' => '16597093023', 
            //         'txn_date' => '2022-07-14 16:16:00',
            //         'principal' => '1000000.00',
            //         'fee' => '22000.00',
            //         'penalty' => 0
            //     ],
            //     13 => [
            //         'loan_doc_id' => 'UFLW-740315020-76757', 
            //         'cust_id' => 'UFLW-740315020',
            //         'cr_amount' => '1022000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16605750984', 
            //         'stmt_txn_date' => '2022-07-15 12:16:00', 
            //         'txn_id' => '16605976324', 
            //         'txn_date' => '2022-07-15 12:37:00',
            //         'principal' => '1000000.00',
            //         'fee' => '22000.00',
            //         'penalty' => 0
            //     ],
            //     14 => [
            //         'loan_doc_id' => 'UFLW-788812115-39225',
            //         'cust_id' => 'UFLW-788812115', 
            //         'cr_amount' => '1022000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16492995082', 
            //         'stmt_txn_date' => '2022-07-05 09:20:00', 
            //         'txn_id' => '16493224842', 
            //         'txn_date' => '2022-07-05 09:42:00',
            //         'principal' => '1000000.00',
            //         'fee' => '22000.00',
            //         'penalty' => 0
            //     ],
            //     15 => [
            //         'loan_doc_id' => 'UFLW-780246907-37573', 
            //         'cust_id' => 'UFLW-780246907',
            //         'cr_amount' => '512000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16504725931', 
            //         'stmt_txn_date' => '2022-07-06 09:47:00', 
            //         'txn_id' => '16504831117', 
            //         'txn_date' => '2022-07-06 09:57:00',
            //         'principal' => '500000.00',
            //         'fee' => '12000.00',
            //         'penalty' => '0.00'
            //     ],
            //     16 => [
            //         'loan_doc_id' => 'UFLW-787135377-64934', 
            //         'cust_id' => 'UFLW-787135377', 
            //         'cr_amount' => '2048000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16527402160', 
            //         'stmt_txn_date' => '2022-07-08 09:46:00', 
            //         'txn_id' => '16527421495', 
            //         'txn_date' => '2022-07-08 09:48:00',
            //         'principal' => '2000000.00',
            //         'fee' => '38000.00',
            //         'penalty' => '10000.00'
            //     ],
            //     17 => [  
            //         'loan_doc_id' => 'UFLW-789490483-50704', 
            //         'cust_id' => 'UFLW-789490483',
            //         'cr_amount' => '1022000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16448788393', 
            //         'stmt_txn_date' => '2022-07-01 11:18:00', 
            //         'txn_id' => '16448812017', 
            //         'txn_date' => '2022-07-01 11:20:00',
            //         'principal' => '1000000.00',
            //         'fee' => '22000.00',
            //         'penalty' => 0
            //     ],
            //     18 => [  
            //         'loan_doc_id' => 'UFLW-774639462-87367', 
            //         'cust_id' => 'UFLW-774639462',
            //         'cr_amount' => '1022000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16458782679', 
            //         'stmt_txn_date' => '2022-07-02 08:36:00', 
            //         'txn_id' => '16459198771', 
            //         'txn_date' => '2022-07-02 09:20:00',
            //         'principal' => '1000000.00',
            //         'fee' => '22000.00',
            //         'penalty' => 0
            //     ],
            //     19 => [  
            //         'loan_doc_id' => 'UFLW-786268773-56514', 
            //         'cust_id' => 'UFLW-786268773',
            //         'cr_amount' => '1022000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16451332347', 
            //         'stmt_txn_date' => '2022-07-01 15:09:00', 
            //         'txn_id' => '16451361162', 
            //         'txn_date' => '2022-07-01 15:11:00',
            //         'principal' => '1000000.00',
            //         'fee' => '22000.00',
            //         'penalty' => 0
            //     ],
            //     20 => [  
            //         'loan_doc_id' => 'UFLO-446910-41502', 
            //         'cust_id' => 'UFLW-701262404',
            //         'cr_amount' => '50000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16685149655', 
            //         'stmt_txn_date' => '2022-07-22 15:21:00', 
            //         'txn_id' => '16685176241', 
            //         'txn_date' => '2022-07-22 15:23:00',
            //         'principal' => '50000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            //     21 => [  
            //         'loan_doc_id' => 'UFLW-703292316-32583', 
            //         'cust_id' => 'UFLW-703292316',
            //         'cr_amount' => '512000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16715454362', 
            //         'stmt_txn_date' => '2022-07-25 10:31:00', 
            //         'txn_id' => '16718212741', 
            //         'txn_date' => '2022-07-25 14:48:00',
            //         'principal' => '500000.00',
            //         'fee' => '12000.00',
            //         'penalty' => 0
            //     ],
            //     22 => [  
            //         'loan_doc_id' => 'UFLW-786268773-49651', 
            //         'cust_id' => 'UFLW-786268773',
            //         'cr_amount' => '1022000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16617516107', 
            //         'stmt_txn_date' => '2022-07-16 13:01:00', 
            //         'txn_id' => '16617532604', 
            //         'txn_date' => '2022-07-16 13:03:00',
            //         'principal' => '1000000.00',
            //         'fee' => '22000.00',
            //         'penalty' => 0
            //     ],
            //     23 => [  
            //         'loan_doc_id' => 'UFLW-703292316-34385', 
            //         'cust_id' => 'UFLW-703292316',
            //         'cr_amount' => '512000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16559737355', 
            //         'stmt_txn_date' => '2022-07-11 09:24:00', 
            //         'txn_id' => '16559761302', 
            //         'txn_date' => '2022-07-11 09:26:00',
            //         'principal' => '500000.00',
            //         'fee' => '12000.00',
            //         'penalty' => 0
            //     ],
            //     24 => [  
            //         'loan_doc_id' => 'UFLW-773069113-23296', 
            //         'cust_id' => 'UFLW-773069113',
            //         'cr_amount' => '1022000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16582891502', 
            //         'stmt_txn_date' => '2022-07-13 10:58:00', 
            //         'txn_id' => '16584022242', 
            //         'txn_date' => '2022-07-13 12:47:00',
            //         'principal' => '1000000.00',
            //         'fee' => '22000.00',
            //         'penalty' => 0
            //     ],
            //     25 => [  
            //         'loan_doc_id' => 'UFLW-785082463-13382', 
            //         'cust_id' => 'UFLW-785082463',
            //         'cr_amount' => '1022000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16530990797', 
            //         'stmt_txn_date' => '2022-07-08 15:19:00', 
            //         'txn_id' => '16532693348', 
            //         'txn_date' => '2022-07-08 17:42:00',
            //         'principal' => '1000000.00',
            //         'fee' => '22000.00',
            //         'penalty' => 0
            //     ],
            //     26 => [  
            //         'loan_doc_id' => 'UFLW-786268773-18327', 
            //         'cust_id' => 'UFLW-786268773',
            //         'cr_amount' => '1022000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16528542892', 
            //         'stmt_txn_date' => '2022-07-08 11:29:00', 
            //         'txn_id' => '16528838895', 
            //         'txn_date' => '2022-07-08 11:55:00',
            //         'principal' => '1000000.00',
            //         'fee' => '22000.00',
            //         'penalty' => 0
            //     ],
            //     27 => [  
            //         'loan_doc_id' => 'UFLW-785082463-78123', 
            //         'cust_id' => 'UFLW-785082463',
            //         'cr_amount' => '1022000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16447867690', 
            //         'stmt_txn_date' => '2022-07-01 09:57:00', 
            //         'txn_id' => '16447992476', 
            //         'txn_date' => '2022-07-01 10:08:00',
            //         'principal' => '1000000.00',
            //         'fee' => '22000.00',
            //         'penalty' => 0
            //     ],
            //     28 => [  
            //         'loan_doc_id' => 'UFLW-740529449-58698', 
            //         'cust_id' => 'UFLW-740529449',
            //         'cr_amount' => '512000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16488414272', 
            //         'stmt_txn_date' => '2022-07-04 19:21:00', 
            //         'txn_id' => '16489033021', 
            //         'txn_date' => '2022-07-04 19:52:00',
            //         'principal' => '500000.00',
            //         'fee' => '12000.00',
            //         'penalty' => '0.00',
            //     ],
            //     29 => [
            //         'loan_doc_id' => 'UFLW-752721772-68363',
            //         'cust_id' => 'UFLW-752721772',
            //         'cr_amount' => '100000.00',
            //         'from_acc_id' => 3421,
            //         'to_acc_id' => 4161,
            //         'stmt_txn_id' => '16532616638',
            //         'stmt_txn_date' => '2022-07-08 17:36:00',
            //         'txn_id' => '16532750761',
            //         'txn_date' => '2022-07-08 17:46:00',
            //         'principal' => '100000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            //     30 => [
            //         'loan_doc_id' => 'UFLO-446910-41502',
            //         'cust_id' => 'UFLW-701262404',
            //         'cr_amount' => '30000.00',
            //         'from_acc_id' => 3421,
            //         'to_acc_id' => 4161,
            //         'stmt_txn_id' => '16531676266', 
            //         'stmt_txn_date' => '2022-07-08 16:21:00',
            //         'txn_id' => '16532731094',
            //         'txn_date' => '2022-07-08 17:45:00',
            //         'principal' => '30000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0,
            //     ],
            //     31 => [  
            //         'loan_doc_id' => 'UFLO-922516-57906', 
            //         'cust_id' => 'UFLW-786353202',
            //         'cr_amount' => '50000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16444845366', 
            //         'stmt_txn_date' => '2022-06-30 21:17:00', 
            //         'txn_id' => '16447307867', 
            //         'txn_date' => '2022-07-01 09:04:00',
            //         'principal' => '50000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            //     32 => [
            //         'loan_doc_id' => 'UFLO-446910-41502',
            //         'cust_id' => 'UFLW-701262404',
            //         'cr_amount' => '50000.00',
            //         'from_acc_id' => 3421,
            //         'to_acc_id' => 4161,
            //         'stmt_txn_id' => '16613482802',
            //         'stmt_txn_date' => '2022-07-15 23:08:00',
            //         'txn_id' => '16614444585',
            //         'txn_date' => '2022-07-16 08:06:00',
            //         'principal' => '50000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            //     33 => [  
            //         'loan_doc_id' => 'UEZM 06493', 
            //         'cust_id' => 'UFLW-780958290',
            //         'cr_amount' => '100000.00', 
            //         'from_acc_id' => 4161, 
            //         'to_acc_id' => 3605, 
            //         'stmt_txn_id' => '17460143940', 
            //         'stmt_txn_date' => '2022-09-28 13:44:00', 
            //         'txn_id' => '17465987168', 
            //         'txn_date' => '2022-09-28 20:56:00',
            //         'principal' => '100000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //         ],
            //     34 => [  
            //         'loan_doc_id' => 'UEZM 06493', 
            //         'cust_id' => 'UFLW-780958290',
            //         'cr_amount' => '250000.00', 
            //         'from_acc_id' => 4161, 
            //         'to_acc_id' => 3605, 
            //         'stmt_txn_id' => '16738666424', 
            //         'stmt_txn_date' => '2022-07-27 11:31:00', 
            //         'txn_id' => '16746748383', 
            //         'txn_date' => '2022-07-27 22:08:00',
            //         'principal' => '250000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            //     35 => [  
            //         'loan_doc_id' => 'CCA-224790-87419', 
            //         'cust_id' => 'UFLW-753977103',
            //         'cr_amount' => '50000.00', 
            //         'from_acc_id' => 4161, 
            //         'to_acc_id' => 4094, 
            //         'stmt_txn_id' => '16887910599', 
            //         'stmt_txn_date' => '2022-08-09 10:50:00', 
            //         'txn_id' => '16910923554', 
            //         'txn_date' => '2022-08-11 12:15:00',
            //         'principal' => '50000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            //     36 => [  
            //         'loan_doc_id' => 'CCA-742733-62890', 
            //         'cust_id' => 'UFLW-759838406',
            //         'cr_amount' => '20000.00', 
            //         'from_acc_id' => 4161, 
            //         'to_acc_id' => 4094, 
            //         'stmt_txn_id' => '16836668864', 
            //         'stmt_txn_date' => '2022-08-04 18:53:00', 
            //         'txn_id' => '16844522822', 
            //         'txn_date' => '2022-08-05 13:09:00',
            //         'principal' => '20000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            //     37 => [  
            //         'loan_doc_id' => 'CCA-351101-53106', 
            //         'cust_id' => 'UFLW-782286633',
            //         'cr_amount' => '100000.00', 
            //         'from_acc_id' => 4161, 
            //         'to_acc_id' => 4094, 
            //         'stmt_txn_id' => '16985837662', 
            //         'stmt_txn_date' => '2022-08-18 12:12:00', 
            //         'txn_id' => '16986319503', 
            //         'txn_date' => '2022-08-18 12:58:00',
            //         'principal' => '100000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            //     38 => [
            //         'loan_doc_id' => 'CCA-453254-28786', 
            //         'cust_id' => 'UFLW-752603112',
            //         'cr_amount' => '40000.00', 
            //         'from_acc_id' => 4161, 
            //         'to_acc_id' => 4094, 
            //         'stmt_txn_id' => '16676699154', 
            //         'stmt_txn_date' => '2022-07-21 19:02:00', 
            //         'txn_id' => '16684027955', 
            //         'txn_date' => '2022-07-22 13:27:00',
            //         'principal' => '40000.00',
            //         'fee' => '0',
            //         'penalty' => 0
            //     ],
            //     39 => [
            //         'loan_doc_id' => 'CCA-453254-28786',
            //         'cust_id' => 'UFLW-752603112', 
            //         'cr_amount' => '40000.00', 
            //         'from_acc_id' => 4161, 
            //         'to_acc_id' => 4094, 
            //         'stmt_txn_id' => '16859667557', 
            //         'stmt_txn_date' => '2022-08-06 18:08:00', 
            //         'txn_id' => '16910915927', 
            //         'txn_date' => '2022-08-11 12:14:00',
            //         'principal' => '40000.00',
            //         'fee' => '0',
            //         'penalty' => 0
            //     ],
            //     40 => [
            //         'loan_doc_id' => 'CCA-453254-28786', 
            //         'cust_id' => 'UFLW-752603112',
            //         'cr_amount' => '30000.00', 
            //         'from_acc_id' => 4161, 
            //         'to_acc_id' => 4094, 
            //         'stmt_txn_id' => '17352251540', 
            //         'stmt_txn_date' => '2022-09-19 18:54:00', 
            //         'txn_id' => '17356120511', 
            //         'txn_date' => '2022-09-19 22:51:00',
            //         'principal' => '30000.00',
            //         'fee' => '0',
            //         'penalty' => 0
            //     ],
            //     41 => [
            //         'loan_doc_id' => 'UFLW-702458808-97237', 
            //         'cust_id' => 'UFLW-702458808',
            //         'cr_amount' => '512000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '15523850994', 
            //         'stmt_txn_date' => '2022-04-05 09:09:00', 
            //         'txn_id' => '15535000311', 
            //         'txn_date' => '2022-04-06 10:27:00',
            //         'principal' => '500000.00',
            //         'fee' => '12000.00',
            //         'penalty' => 0
            //     ],
            //     42 => [
            //         'loan_doc_id' => 'UFLO-446910-41502', 
            //         'cust_id' => 'UFLW-701262404',
            //         'cr_amount' => '30000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '16469167110', 
            //         'stmt_txn_date' => '2022-07-03 00:38:00', 
            //         'txn_id' => '16489047739', 
            //         'txn_date' => '2022-07-04 19:53:00',
            //         'principal' => '30000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            //     43 => [
            //         'loan_doc_id' => 'CCA-966945-68807', 
            //         'cust_id' => 'UFLW-776406170',
            //         'cr_amount' => '425000.00', 
            //         'from_acc_id' => 3421, 
            //         'to_acc_id' => 4161, 
            //         'stmt_txn_id' => '14876805471', 
            //         'stmt_txn_date' => '2022-01-28 16:29:00', 
            //         'txn_id' => '14904625226', 
            //         'txn_date' => '2022-01-31 16:35:00',
            //         'principal' => '425000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            //     44 => [
            //         'loan_doc_id' => 'CCA-390480-82196', 
            //         'cust_id' => 'UFLW-776491582',
            //         'cr_amount' => '30000.00', 
            //         'from_acc_id' => 4161, 
            //         'to_acc_id' => 4094, 
            //         'stmt_txn_id' => '17335130275', 
            //         'stmt_txn_date' => '2022-09-18 09:42:00', 
            //         'txn_id' => '17355733723', 
            //         'txn_date' => '2022-09-19 22:19:00',
            //         'principal' => '30000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            //     45 => [
            //         'loan_doc_id' => 'CCA-390480-82196', 
            //         'cust_id' => 'UFLW-776491582',
            //         'cr_amount' => '40000.00', 
            //         'from_acc_id' => 4161, 
            //         'to_acc_id' => 4094, 
            //         'stmt_txn_id' => '17003793369', 
            //         'stmt_txn_date' => '2022-08-19 21:20:00', 
            //         'txn_id' => '17027775928', 
            //         'txn_date' => '2022-08-22 09:44:00',
            //         'principal' => '40000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            //     46 => [
            //         'loan_doc_id' => 'CCA-390480-82196', 
            //         'cust_id' => 'UFLW-776491582',
            //         'cr_amount' => '20000.00', 
            //         'from_acc_id' => 4161, 
            //         'to_acc_id' => 4094, 
            //         'stmt_txn_id' => '16926511404', 
            //         'stmt_txn_date' => '2022-08-12 18:44:00', 
            //         'txn_id' => '16952322259', 
            //         'txn_date' => '2022-08-15 09:19:00',
            //         'principal' => '20000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            //     47 => [
            //         'loan_doc_id' => 'UEZM 06408', 
            //         'cust_id' => 'UFLW-782470680',
            //         'cr_amount' => '20000.00', 
            //         'from_acc_id' => 4161, 
            //         'to_acc_id' => 3605, 
            //         'stmt_txn_id' => '15198460728', 
            //         'stmt_txn_date' => '2022-03-03 11:06:00', 
            //         'txn_id' => '15198562274', 
            //         'txn_date' => '2022-03-03 11:17:00',
            //         'principal' => '20000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            //     48 => [
            //         'loan_doc_id' => 'CCA-338099-72767', 
            //         'cust_id' => 'UFLW-751169719',
            //         'cr_amount' => '30000.00', 
            //         'from_acc_id' => 4161, 
            //         'to_acc_id' => 4094, 
            //         'stmt_txn_id' => '16986397716', 
            //         'stmt_txn_date' => '2022-08-18 13:06:00', 
            //         'txn_id' => '16987689815', 
            //         'txn_date' => '2022-08-18 15:11:00',
            //         'principal' => '30000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            //     49 => [
            //         'loan_doc_id' => 'CCA-838546-78889', 
            //         'cust_id' => 'UFLW-705093316',
            //         'cr_amount' => '150000.00', 
            //         'from_acc_id' => 4161, 
            //         'to_acc_id' => 4094, 
            //         'stmt_txn_id' => '16347993322', 
            //         'stmt_txn_date' => '2022-06-22 09:18:00', 
            //         'txn_id' => '16352509476', 
            //         'txn_date' => '2022-06-22 16:37:00',
            //         'principal' => '150000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            //     50 => [
            //         'loan_doc_id' => 'UEZM-237450-50313', 
            //         'cust_id' => 'UFLW-754644112',
            //         'cr_amount' => '50000.00', 
            //         'from_acc_id' => 4161, 
            //         'to_acc_id' => 3605, 
            //         'stmt_txn_id' => '16932705539', 
            //         'stmt_txn_date' => '2022-08-13 11:38:00', 
            //         'txn_id' => '16952316406', 
            //         'txn_date' => '2022-08-15 09:18:00',
            //         'principal' => '50000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            //     51 => [
            //         'loan_doc_id' => 'CCA-863301-43030', 
            //         'cust_id' => 'UFLW-772066253',
            //         'cr_amount' => '100000.00', 
            //         'from_acc_id' => 4161, 
            //         'to_acc_id' => 4094, 
            //         'stmt_txn_id' => '17518825500', 
            //         'stmt_txn_date' => '2022-10-03 13:09:00', 
            //         'txn_id' => '17526047889', 
            //         'txn_date' => '2022-10-03 21:58:00',
            //         'principal' => '100000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            //     52 => [
            //         'loan_doc_id' => 'UEZM 06493', 
            //         'cust_id' => 'UFLW-780958290',
            //         'cr_amount' => '100000.00', 
            //         'from_acc_id' => 4161, 
            //         'to_acc_id' => 3605, 
            //         'stmt_txn_id' => '16136754263', 
            //         'stmt_txn_date' => '2022-06-02 13:01:00', 
            //         'txn_id' => '16150859472', 
            //         'txn_date' => '2022-06-03 17:48:00',
            //         'principal' => '100000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            //     53 => [
            //         'loan_doc_id' => 'CCA-390480-82196', 
            //         'cust_id' => 'UFLW-776491582',
            //         'cr_amount' => '50000.00', 
            //         'from_acc_id' => 4161, 
            //         'to_acc_id' => 4094, 
            //         'stmt_txn_id' => '16128616801', 
            //         'stmt_txn_date' => '2022-06-01 17:43:00', 
            //         'txn_id' => '16135647603', 
            //         'txn_date' => '2022-06-02 11:14:00',
            //         'principal' => '50000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            //     54 => [
            //         'loan_doc_id' => 'CCA-366843-37699', 
            //         'cust_id' => 'UFLW-703179459',
            //         'cr_amount' => '100000.00', 
            //         'from_acc_id' => 4161, 
            //         'to_acc_id' => 4094, 
            //         'stmt_txn_id' => '17192280610', 
            //         'stmt_txn_date' => '2022-09-05 18:35:00', 
            //         'txn_id' => '17198412032', 
            //         'txn_date' => '2022-09-06 11:02:00',
            //         'principal' => '100000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            //     55 => [
            //         'loan_doc_id' => 'CCA-970900-96309', 
            //         'cust_id' => 'UFLW-786231235',
            //         'cr_amount' => '20000.00', 
            //         'from_acc_id' => 4161, 
            //         'to_acc_id' => 4094, 
            //         'stmt_txn_id' => '16883258487', 
            //         'stmt_txn_date' => '2022-08-08 19:54:00', 
            //         'txn_id' => '16910907265', 
            //         'txn_date' => '2022-08-11 12:14:00',
            //         'principal' => '20000.00',
            //         'fee' => '0.00',
            //         'penalty' => 0
            //     ],
            // ];

            $acc_txns = [
                0 => [
                    'loan_doc_id' => 'CCA-338099-72767', 
                    'cust_id' => 'UFLW-751169719',
                    'cr_amount' => '30000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 4094, 
                    'stmt_txn_id' => '17685065126', 
                    'stmt_txn_date' => '2022-10-17 17:34:00', 
                    'txn_id' => '17691799354', 
                    'txn_date' => '2022-10-18 10:35:00',
                    'principal' => '30000.00',
                    'fee' => '0.00',
                    'penalty' => 0
                ],
                1 => [
                    'loan_doc_id' => 'UFLW-755505629-27602', 
                    'cust_id' => 'UFLW-755505629',
                    'cr_amount' => '80000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 4094, 
                    'stmt_txn_id' => '16499395645', 
                    'stmt_txn_date' => '2022-07-05 18:51:00', 
                    'txn_id' => '16502671358', 
                    'txn_date' => '2022-07-05 22:45:00',
                    'principal' => '80000.00',
                    'fee' => '0.00',
                    'penalty' => 0
                ],
                2 => [
                    'loan_doc_id' => 'CCA-390480-82196', 
                    'cust_id' => 'UFLW-776491582',
                    'cr_amount' => '40000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 4094, 
                    'stmt_txn_id' => '16363997645', 
                    'stmt_txn_date' => '2022-06-23 17:31:00', 
                    'txn_id' => '16370498009', 
                    'txn_date' => '2022-06-24 10:26:00',
                    'principal' => '40000.00',
                    'fee' => '0.00',
                    'penalty' => 0
                ],
                3 => [
                    'loan_doc_id' => 'CCA-586093-78365', 
                    'cust_id' => 'UFLW-702106999',
                    'cr_amount' => '100000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 4094, 
                    'stmt_txn_id' => '16283326614', 
                    'stmt_txn_date' => '2022-06-16 08:25:00', 
                    'txn_id' => '16295911457', 
                    'txn_date' => '2022-06-17 11:25:00',
                    'principal' => '100000.00',
                    'fee' => '0.00',
                    'penalty' => 0
                ],
                4 => [
                    'loan_doc_id' => 'UEZM-237450-50313', 
                    'cust_id' => 'UFLW-754644112',
                    'cr_amount' => '50000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 4094, 
                    'stmt_txn_id' => '16358435248', 
                    'stmt_txn_date' => '2022-06-23 08:22:00', 
                    'txn_id' => '16370536506', 
                    'txn_date' => '2022-06-24 10:30:00',
                    'principal' => '50000.00',
                    'fee' => '0.00',
                    'penalty' => 0
                ],
                5 => [
                    'loan_doc_id' => 'CCA-504282-10843', 
                    'cust_id' => 'UFLW-759922407',
                    'cr_amount' => '30000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 4094, 
                    'stmt_txn_id' => '17088406002', 
                    'stmt_txn_date' => '2022-08-27 18:21:00', 
                    'txn_id' => '17129255013', 
                    'txn_date' => '2022-08-31 13:34:00',
                    'principal' => '30000.00',
                    'fee' => '0.00',
                    'penalty' => 0
                ],
                6 => [
                    'loan_doc_id' => 'UEZM-215820-18668', 
                    'cust_id' => 'UFLW-702919956',
                    'cr_amount' => '10000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 3605, 
                    'stmt_txn_id' => '17122547350', 
                    'stmt_txn_date' => '2022-08-30 19:28:00', 
                    'txn_id' => '17129244169', 
                    'txn_date' => '2022-08-31 13:33:00',
                    'principal' => '10000.00',
                    'fee' => '0.00',
                    'penalty' => 0
                ],
                7 => [
                    'loan_doc_id' => 'CCA-854992-89509', 
                    'cust_id' => 'UFLW-757027973',
                    'cr_amount' => '40000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 4094, 
                    'stmt_txn_id' => '16609118355', 
                    'stmt_txn_date' => '2022-07-15 17:32:00', 
                    'txn_id' => '16612973696', 
                    'txn_date' => '2022-07-15 21:51:00',
                    'principal' => '40000.00',
                    'fee' => '0.00',
                    'penalty' => 0
                ],
                8 => [
                    'loan_doc_id' => 'CCA-406453-51655', 
                    'cust_id' => 'UFLW-782706908',
                    'cr_amount' => '50000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 4094, 
                    'stmt_txn_id' => '16608782243', 
                    'stmt_txn_date' => '2022-07-15 17:05:00', 
                    'txn_id' => '16612970238', 
                    'txn_date' => '2022-07-15 21:51:00',
                    'principal' => '50000.00',
                    'fee' => '0.00',
                    'penalty' => 0
                ],
                9 => [
                    'loan_doc_id' => 'UEZM-172160-50536', 
                    'cust_id' => 'UFLW-750378084',
                    'cr_amount' => '212000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 3605, 
                    'stmt_txn_id' => '15142558577', 
                    'stmt_txn_date' => '2022-02-25 16:39:00', 
                    'txn_id' => '15167293848', 
                    'txn_date' => '2022-02-28 09:20:00',
                    'principal' => '212000.00',
                    'fee' => '0.00',
                    'penalty' => 0
                ],
                10 => [
                    'loan_doc_id' => 'CCA-338099-72767', 
                    'cust_id' => 'UFLW-751169719',
                    'cr_amount' => '50000.00', 
                    'from_acc_id' => 4161, 
                    'to_acc_id' => 4094, 
                    'stmt_txn_id' => '16989576002', 
                    'stmt_txn_date' => '2022-08-18 17:58:00', 
                    'txn_id' => '17248189706', 
                    'txn_date' => '2022-09-10 17:31:00',
                    'principal' => '50000.00',
                    'fee' => '0.00',
                    'penalty' => 0
                ],
            ];

            foreach($acc_txns as $acc_txn){
                
                $loan_txn_record = $loan_txn_repo->get_record_by_many(['loan_doc_id', 'txn_type', 'txn_id'], [$acc_txn['loan_doc_id'], 'payment', $acc_txn['txn_id']], ['id']);

                $loan_txn_data['loan_txn_id'] = $loan_txn_record->id;

                $this->reverse_payment($loan_txn_data);

                $this->capture_payment_diff_txn($acc_txn); // payment_diff_acc

                $this->capture_payment_diff_int_trans_txn($acc_txn); // payment_diff_acc_int_trans

                $this->capture_payment($acc_txn); // payment

                $cust_id = $acc_txn['cust_id'];
                $loan_doc_id = $acc_txn['loan_doc_id'];

                $stmt_txn_id = $acc_txn['stmt_txn_id'];
                $txn_id = $acc_txn['txn_id'];

                $addl_sql = "stmt_txn_id in ('$stmt_txn_id', '$txn_id')";
                DB::update("UPDATE account_stmts SET recon_status = '80_recon_done', acc_txn_type = 'fa', cust_id = '$cust_id', loan_doc_id = '$loan_doc_id' WHERE $addl_sql");                
            }

            $reverse_payment_ids = [152479, 215212, 195334, 197416, 175418];

            foreach ($reverse_payment_ids as $reverse_payment_id){
                $loan_txn_data['loan_txn_id'] = $reverse_payment_id;
                $this->reverse_payment($loan_txn_data);
            }

            //Individual Fix for Paid to Diff Acc Case

            // $data['loan_txn_id'] = 117885;
            // $repayment_serv->reverse_payment($data);
            // DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES
            // ("UGA", "UEZM 06493", NULL, 4161, NULL, 100000.00, "payment_diff_acc", "15733698852", "wallet_transfer", NULL, "2022-04-25 11:33:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-04-25 11:33:00", NULL, 0.00, NULL, NULL, NULL, NULL)');

            // DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES
            // ("UGA", "UEZM 06493", 4161, 3605, NULL, 100000.00, "payment_diff_acc_int_trans", "15736276849", "wallet_transfer", NULL, "2022-04-25 15:54:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-04-25 15:54:00", NULL, 0.00, NULL, NULL, NULL, NULL)');

            // DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES
            // ("UGA", "UEZM 06493", NULL, 3605, NULL, 100000.00, "payment", "15736276849", "wallet_transfer", "0", "2022-04-25 15:54:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-04-25 15:54:00", NULL, 100000, 100000, 0, 0, 0)');

            // DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'fa', loan_doc_id = 'UEZM 06493', cust_id = 'UFLW-780958290'  where id in (31941674, 31968989, 49876759)");


            // $data['loan_txn_id'] = 121927;
            // $repayment_serv->reverse_payment($data);
            // DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES
            // ("UGA", "UEZM 06446", NULL, 4161, NULL, 500000.00, "payment_diff_acc", "15746525216", "wallet_transfer", NULL, "2022-04-26 15:43:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-04-26 15:43:00", NULL, 0.00, NULL, NULL, NULL, NULL)');

            // DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES
            // ("UGA", "UEZM 06446", 4161, 3605, NULL, 500000.00, "payment_diff_acc_int_trans", "15754720245", "wallet_transfer", NULL, "2022-04-27 11:56:00", NULL, NULL, NULL, NULL, NULL, NULL, "2022-04-27 11:56:00", NULL, 0.00, NULL, NULL, NULL, NULL)');

            // DB::insert('INSERT INTO `loan_txns` (`country_code`, `loan_doc_id`, `from_ac_id`, `to_ac_id`, `write_off_id`, `amount`, `txn_type`, `txn_id`, `txn_mode`, `txn_exec_by`, `txn_date`, `reversed_date`, `reason_for_skip`, `photo_transaction_proof`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `recon_amount`, `principal`, `fee`, `penalty`, `excess`) VALUES
            // ("UGA", "UEZM 06446", NULL, 3605, NULL, 500000.00, "payment", "15754720245", "wallet_transfer", "0", "2022-04-27 11:56:00", NULL, NULL, NULL, NULL, 0, NULL, "2022-04-27 11:56:00", NULL, 500000, 500000, 0, 0, 0)');

            // DB::update("update account_stmts set recon_status = '80_recon_done', acc_txn_type = 'fa', loan_doc_id = 'UEZM 06446', cust_id = 'UEZM-818083' where id in (32041303, 32094004, 49876774)");

            DB::commit();
        }

        catch(\Exception $e){

            DB::rollBack();
            thrw($e);
        }
    }

    private function reverse_payment($data){

        Log::warning("REVERSE PAYMENT");

        $loan_txn = (new LoanTransactionRepositorySQL)->find($data['loan_txn_id'], ['loan_doc_id', 'from_ac_id', 'to_ac_id', 'amount', 'txn_type', 'txn_id', 'txn_date']);

        $today_date = Carbon::now();
        DB::update("update loan_txns set reversed_date = ?, txn_type = ? where id = ? ",[$today_date, Consts::LOAN_PAYMENT_REVERSED, $data['loan_txn_id']]);

        (new AccountStmtRepositorySQL)->update_model_by_code(['stmt_txn_id' => $loan_txn->txn_id, 'loan_doc_id' => null, 'recon_status' => '90_payment_reversed', 'recon_desc' => null, 'cust_id' => null]);

    }

    private function capture_payment_diff_txn($data){

        Log::warning("PAYMENT DIFF ACCOUNT");
        Log::warning((array)$data);

        $loan_txn_repo = new LoanTransactionRepositorySQL();

        $loan_txn['loan_doc_id'] = $data['loan_doc_id'];
        $loan_txn['amount'] = $data['cr_amount'];
        $loan_txn['txn_type'] = Consts::LOAN_TXN_PAYMENT_DIFF_ACC;
        $loan_txn['to_ac_id'] = $data['from_acc_id'];
        $loan_txn['txn_id'] = $data['stmt_txn_id'];
        $loan_txn['txn_mode'] = 'wallet_transfer';
        $loan_txn['txn_date'] = $data['stmt_txn_date'];

        $loan_txn_repo->create($loan_txn);

    }

    private function capture_payment_diff_int_trans_txn($data){

        Log::warning("PAYMENT DIFF INT TRANSFER ACCOUNT");
        Log::warning((array)$data);

        $loan_txn_repo = new LoanTransactionRepositorySQL();

        $loan_txn['loan_doc_id'] = $data['loan_doc_id'];
        $loan_txn['amount'] = $data['cr_amount'];
        $loan_txn['txn_type'] = Consts::LOAN_TXN_PAYMENT_DIFF_ACC_INT_TRANS;
        $loan_txn['from_ac_id'] = $data['from_acc_id'];
        $loan_txn['to_ac_id'] = $data['to_acc_id'];
        $loan_txn['txn_id'] = $data['txn_id'];
        $loan_txn['txn_mode'] = 'wallet_transfer';
        $loan_txn['txn_date'] = $data['txn_date'];

        $loan_txn_repo->create($loan_txn);

    }

    private function capture_payment($data){

        Log::warning("PAYMENT");
        Log::warning((array)$data);

        $req_data = (new LoanService())->get_req_data_for_capture($data);
        $req_data['txn_type'] = "payment";
        $req_data['principal'] = $data['principal'];
        $req_data['fee'] = $data['fee'];
        $req_data['penalty'] = $data['penalty'];

        (new LoanTransactionRepositorySQL)->create($req_data);

    }
}




?>