
<?php

use Illuminate\Database\Seeder;
use App\Services\AccountService;
use App\Repositories\SQL\AccountRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;

class MissingChapChapAccounts extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

	

	private function import_missing_account($borrower_data){
		$data['country_code'] = "UGA";
		$data['cust_id'] = $borrower_data->cust_id;
		$data['acc_prvdr_name'] = "ChapChap";
		$data['acc_prvdr_code'] = "CCA";
		$data['type'] = "Wallet";
		$data['holder_name'] = (new PersonRepositorySQL())->full_name($borrower_data->owner_person_id);
		$data['acc_number'] = $borrower_data->data_prvdr_cust_id;
		$data['is_primary_acc'] = true;
		$data['created_by'] = $borrower_data->created_by;
		$data['created_at'] = $borrower_data->created_at;

		(new AccountRepositorySQL())->create($data);
		
	}
    public function run(){
		try{
			DB::beginTransaction();
			session()->put('country_code', 'UGA');
			$missing_data_prvdr_datas = DB::select("select data_prvdr_cust_id,cust_id,created_by,created_at, owner_person_id from borrowers where cust_id not in (select cust_id from accounts where cust_id is not null) and data_prvdr_code = 'CCA'");

			foreach($missing_data_prvdr_datas as $missing_data_prvdr_data){
				$this->import_missing_account($missing_data_prvdr_data);
			}
			DB::commit();
		}
		catch(Exception $e){
			DB::rollback();
			Log::warning($e->getTraceAsString());
      		thrw($e->getMessage());
		}
	}
}
