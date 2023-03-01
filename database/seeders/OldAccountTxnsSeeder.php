<?php


use Illuminate\Database\Seeder;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Services\AccountService;
use App\Models\AccountTxn;
use App\Services\ImportLoanService;

class OldAccountTxnsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    
      #25000
    
   
    public function run(){
        try{
            DB::beginTransaction();
            session()->put('country_code', 'UGA');
            $this->run_capital();
            // $this->run_capital_reversal();
            DB::commit();
        }
            catch (\Exception $e){
              DB::rollback();
              Log::warning($e->getTraceAsString());
              thrw($e->getMessage());
        }
    }

    public function run_capital(){
        session()->put('country_code', 'UGA');
        $this->investments = [
          ['country_code' => 'UGA', 'acc_id' => 3, 'txn_date' => '2018-12-01', 'acc_txn_category' => 'credit',  'amount' => '2240500', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2018-12-31', 'created_by' => 0, 'created_at' => '2018-12-31'],

          ['country_code' => 'UGA', 'acc_id' => 3, 'txn_date' => '2019-01-01', 'acc_txn_category' => 'credit',  'amount' => '4428500', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2019-01-31', 'created_by' => 0, 'created_at' => '2019-01-31'],

          ['country_code' => 'UGA', 'acc_id' => 3, 'txn_date' => '2019-02-01', 'acc_txn_category' => 'credit',  'amount' => '2993000',  'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2019-02-28', 'created_by' => 0, 'created_at' => '2019-02-28'],

          ['country_code' => 'UGA', 'acc_id' => 3, 'txn_date' => '2019-03-01', 'acc_txn_category' => 'credit',  'amount' => '18000000', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2019-03-31', 'created_by' => 0,'created_at' => '2019-03-31'],

           ['country_code' => 'UGA', 'acc_id' => 3, 'txn_date' => '2019-04-01', 'acc_txn_category' => 'credit',  'amount' => '2493000',  'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2019-04-30', 'created_by' => 0, 'created_at' => '2019-04-30'],

           ['country_code' => 'UGA', 'acc_id' => 3, 'txn_date' => '2019-05-01', 'acc_txn_category' => 'credit',  'amount' => '19000000', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2019-05-31',
           'created_by' => 0, 'created_at' => '2019-05-31'],

           ['country_code' => 'UGA', 'acc_id' => 3, 'txn_date' => '2019-06-01', 'acc_txn_category' => 'credit',  'amount' => '14000000', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2019-06-30',
           'created_by' => 0,'created_at' => '2019-06-30'],

           ['country_code' => 'UGA', 'acc_id' => 3, 'txn_date' => '2019-07-01', 'acc_txn_category' => 'credit',  'amount' => '26083300', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2019-07-31',
           'created_by' => 0, 'created_at' => '2019-07-31'],

           ['country_code' => 'UGA', 'acc_id' => 3, 'txn_date' => '2019-08-01', 'acc_txn_category' => 'credit',  'amount' => '10000000',  'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2019-08-31',
           'created_by' => 0, 'created_at' =>'2019-08-31'],

            ['country_code' => 'UGA', 'acc_id' => 3, 'txn_date' => '2019-09-01', 'acc_txn_category' => 'credit',  'amount' => '29614000',  'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2019-09-30',
            'created_by' => 0, 'created_at' => '2019-09-30'],


            ['country_code' => 'UGA', 'acc_id' => 3, 'txn_date' => '2019-10-01', 'acc_txn_category' => 'credit',  'amount' => '15000000',  'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2019-10-31',
            'created_by' => 0, 'created_at' => '2019-10-31'],



            ['country_code' => 'UGA', 'acc_id' => 3, 'txn_date' => '2019-11-01', 'acc_txn_category' => 'credit',  'amount' => '16000000', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2019-11-30',
            'created_by' => 0,'created_at' => '2019-11-30'],


            ['country_code' => 'UGA', 'acc_id' => 3, 'txn_date' => '2019-12-01', 'acc_txn_category' => 'credit','amount' => '20000000',  'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2019-12-31',
            'created_by' => 0, 'created_at' => '2019-12-31'],



            ['country_code' => 'UGA', 'acc_id' => 3, 'txn_date' => '2020-01-01', 'acc_txn_category' => 'credit','amount' => '30000000',  'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2020-01-30',
            'created_by' => 0,'created_at' => '2020-01-30'],


            ['country_code' => 'UGA', 'acc_id' => 3, 'txn_date' => '2020-02-01', 'acc_txn_category' => 'credit','amount' => '25000000',  'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2020-02-21',
            'created_by' => 0,'created_at' => '2020-02-21'],

            ['country_code' => 'UGA', 'acc_id' => 3, 'txn_date' => '2020-03-01', 'acc_txn_category' => 'credit','amount' => '6000000', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2020-03-05',
            'created_by' => 0,'created_at' => '2020-03-05'],

            ['country_code' => 'UGA', 'acc_id' => 3, 'txn_date' => '2020-03-01', 'acc_txn_category' => 'credit','amount' => '20000000', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2020-03-12',
            'created_by' => 0,'created_at' => '2020-03-12'],

            ['country_code' => 'UGA', 'acc_id' => 3, 'txn_date' => '2020-03-01', 'acc_txn_category' => 'credit','amount' => '4000000', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2020-03-17',
            'created_by' => 0,'created_at' => '2020-03-17'],

            ['country_code' => 'UGA', 'acc_id' => 3, 'txn_date' => '2020-03-01', 'acc_txn_category' => 'debit', 'amount' => '21524500',  'acc_txn_type' => 'capital_investment', 'txn_mode' => 'flow_platform', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2020-03-25','ref_acc_id' => 1387,
            'created_by' => 0,'created_at' => '2018-03-25'],

            ['country_code' => 'UGA', 'acc_id' => 3, 'txn_date' => '2020-05-01', 'acc_txn_category' => 'debit', 'amount' => '22079000',  'acc_txn_type' => 'capital_investment', 'txn_mode' => 'flow_platform', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2020-05-27','ref_acc_id' => 1387,
            'created_by' => 0,'created_at' => '2020-05-27'],
        ];

        foreach($this->investments as $investment){
            (new AccountService())->create_acc_txn($investment);
        }
    }

    // public function run_capital_reversal(){
    //     $this->investments_rev = [
    //         ['country_code' => 'UGA', 'acc_id' => 3, 'txn_date' => '2020-03-25', 'acc_txn_category' => 'credit',  'amount' => 21524500,  'acc_txn_type' => 'capital_investment', 'txn_mode' => 'flow_platform', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '',
    //         'created_by' => 0,'created_at' => '2018-03-25', 'remarks' => 'debit'],

    //         ['country_code' => 'UGA', 'acc_id' => 3, 'txn_date' => '2020-05-27', 'acc_txn_category' => 'debit',  'amount' => 22079000,  'acc_txn_type' => 'capital_investment', 'txn_mode' => 'flow_platform', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '',
    //         'created_by' => 0,'created_at' => '2020-05-27', 'remarks' => ''],
             
    //       ];
    //     foreach($this->investments_rev as $investment_rev){
    //         (new AccountService())->create_acc_txn($investment_rev);
    //     }
    // }
}

