<?php
namespace App\Repositories\SQL;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Models\DisbursalAttempt;
use App\Consts;

class DisbursalAttemptRepositorySQL extends BaseRepositorySQL implements BaseRepositoryInterface{

	public function __construct()
    {
      parent::__construct();

    }

	public function model(){
			return DisbursalAttempt::class;
	}

	public function create(array $otp)
 	{
 		$id = parent::insert_model($otp);
 		return $id;
 	}

 	public function update(array $req){
 	    throw new BadMethodCallException();
	}
	public function delete($id){
		throw new BadMethodCallException();
	}

	public function list($id){
	   throw new BadMethodCallException();
	}

	public function get_first_disburse_attempt($loan_doc_id,$fields_arr = ['*']){
	
		$fields_str = parent::get_field_str($fields_arr);
		return DB::selectOne("select $fields_str from disbursal_attempts where `loan_doc_id` = '$loan_doc_id' order by id limit 1 ");

	}
	public function get_last_disburse_attempt($loan_doc_id, $fields_arr = ['*']){
		if(in_array('count',$fields_arr)){
			$disb_attempts = DB::selectOne("select count(*) as count from disbursal_attempts where `loan_doc_id` = '$loan_doc_id'");
			$fields_arr = array_diff($fields_arr, array('count'));
		}
		$fields_str = parent::get_field_str($fields_arr);
		$attempt = DB::selectOne("select $fields_str from disbursal_attempts where `loan_doc_id` = '$loan_doc_id' order by id desc limit 1");
		if (isset($disb_attempts) && isset($attempt)){
			$attempt->count = $disb_attempts->count;
		}
		return $attempt;
	}


}


