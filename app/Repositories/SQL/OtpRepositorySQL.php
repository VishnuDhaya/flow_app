<?php
 namespace App\Repositories\SQL;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Models\Otp;
use App\Consts;

class OtpRepositorySQL extends BaseRepositorySQL implements BaseRepositoryInterface{

	public function __construct()
    {
      parent::__construct();

    }

	public function model(){
			return Otp::class;
	}

	public function create(array $otp)
 	{
 		$id = parent::insert_model($otp);
 		return $id;
 	}

    public function update_entity_id($entity_id, $id){
        $this->update_model(["id" => $id,  "entity" => $entity_id]);
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


