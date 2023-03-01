
<?php


use Illuminate\Database\Seeder;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Services\AccountService;
use App\Models\AccountTxn;
use App\Services\ImportLoanService;

class InvestmentChapChap extends Seeder
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
    	$acc_id = $this->get_account();
        session()->put('country_code', 'UGA');
        $this->investments = [

          ['country_code' => 'UGA', 'acc_id' =>$acc_id , 'txn_date' => '2020-05-29', 'acc_txn_category' => 'credit',  'amount' => '15000000', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2020-05-29', 'created_by' => 0, 'created_at' => '2020-05-29'],

          ['country_code' => 'UGA', 'acc_id' => $acc_id, 'txn_date' => '2020-06-10', 'acc_txn_category' => 'credit',  'amount' => '4000000', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2020-06-10', 'created_by' => 0, 'created_at' => '2020-06-10'],

          ['country_code' => 'UGA', 'acc_id' =>$acc_id , 'txn_date' => '2020-06-16', 'acc_txn_category' => 'credit',  'amount' => '25000000', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2020-06-16', 'created_by' => 0, 'created_at' => '2020-06-16'],

          ['country_code' => 'UGA', 'acc_id' => $acc_id, 'txn_date' => '2020-06-29', 'acc_txn_category' => 'credit',  'amount' => '15000000', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2020-06-29', 'created_by' => 0, 'created_at' => '2020-06-29'],

          ['country_code' => 'UGA', 'acc_id' =>$acc_id , 'txn_date' => '2020-07-01', 'acc_txn_category' => 'debit',  'amount' => '396000', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2020-07-01', 'created_by' => 0, 'created_at' => '2020-07-01'],

          ['country_code' => 'UGA', 'acc_id' => $acc_id, 'txn_date' => '2020-07-07', 'acc_txn_category' => 'credit',  'amount' => '10000000', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2020-07-07', 'created_by' => 0, 'created_at' => '2020-07-07'],


          ['country_code' => 'UGA', 'acc_id' => $acc_id, 'txn_date' => '2020-07-28', 'acc_txn_category' => 'credit',  'amount' => '47800000', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2020-07-28', 'created_by' => 0, 'created_at' => '2020-07-28'],

          ['country_code' => 'UGA', 'acc_id' => $acc_id, 'txn_date' => '2020-08-01', 'acc_txn_category' => 'credit',  'amount' => '20000000', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2020-08-01', 'created_by' => 0, 'created_at' => '2020-08-01'],

          ['country_code' => 'UGA', 'acc_id' => $acc_id, 'txn_date' => '2020-08-01', 'acc_txn_category' => 'debit',  'amount' => '754000', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2020-08-01', 'created_by' => 0, 'created_at' => '2020-08-01'],

          ['country_code' => 'UGA', 'acc_id' =>$acc_id , 'txn_date' => '2020-08-11', 'acc_txn_category' => 'credit',  'amount' => '25000000', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2020-08-11', 'created_by' => 0, 'created_at' => '2020-08-11'],

          ['country_code' => 'UGA', 'acc_id' => $acc_id, 'txn_date' => '2020-08-18', 'acc_txn_category' => 'credit',  'amount' => '30000000', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2020-08-18', 'created_by' => 0, 'created_at' => '2020-08-18'],

          ['country_code' => 'UGA', 'acc_id' => $acc_id, 'txn_date' => '2020-08-24', 'acc_txn_category' => 'credit',  'amount' => '25000000', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2020-08-24', 'created_by' => 0, 'created_at' => '2020-08-24'],

          ['country_code' => 'UGA', 'acc_id' => $acc_id, 'txn_date' => '2020-08-27', 'acc_txn_category' => 'credit',  'amount' => '30000000', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2020-08-27', 'created_by' => 0, 'created_at' => '2020-08-27'],

          ['country_code' => 'UGA', 'acc_id' => $acc_id, 'txn_date' => '2020-09-08', 'acc_txn_category' => 'credit',  'amount' => '40000000', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2020-09-08', 'created_by' => 0, 'created_at' => '2020-09-08'],

          ['country_code' => 'UGA', 'acc_id' => $acc_id, 'txn_date' => '2020-09-16', 'acc_txn_category' => 'credit',  'amount' => '30000000', 'acc_txn_type' => 'capital_investment', 'txn_mode' => 'internet_banking', 'txn_exec_by' => 3, 'txn_id' => '','remarks' => '2020-09-16', 'created_by' => 0, 'created_at' => '2020-09-16'],

        ];

        foreach($this->investments as $investment){
            (new AccountService())->create_acc_txn($investment);
        }
    }
    public function get_account(){
		$bank_accounts = DB::selectOne("select id from accounts where lender_code='UFLW' and lender_data_prvdr_code ='CCA'");
		

		return $bank_accounts->id;
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
