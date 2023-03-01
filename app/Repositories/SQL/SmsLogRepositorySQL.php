<?php
namespace App\Repositories\SQL;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Models\SmsLog;
use App\Consts;

class SmsLogRepositorySQL extends BaseRepositorySQL implements BaseRepositoryInterface{

	public function __construct()
    {
      parent::__construct();

    }

	public function model(){
			return SmsLog::class;
	}

	public function create(array $sms)
 	{
 		$id = parent::insert_model($sms);
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

}


