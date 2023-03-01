<?php
namespace App\Repositories\SQL;
//namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Repositories\SQL\OrgRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\AddressInfoRepositorySQL;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Consts;
use App\Models\Lender;
use App\Exceptions\FlowCustomException;
use Exception;
use Log;
use Illuminate\Database\QueryException;

class LenderRepositorySQL extends BaseRepositorySQL implements BaseRepositoryInterface{

	public function __construct()
    {
      parent::__construct();

    }
    
	public function model(){
			return Lender::class;
	}

	public function create(array $lender){
		try
		{
        DB::beginTransaction();
			$person_repo = new PersonRepositorySQL();
			$org_repo = new OrgRepositorySQL();
			if(isset($lender['org']['id']))
			{
				$org_id = $lender['org']['id'];
			}
			else
			{
				$org_id = $org_repo->create($lender['org']);
			}
			$contact_person_id = $person_repo->create($lender['contact_person']);
			$lender['contact_person_id'] = $contact_person_id;
			$lender['org_id'] = $org_id;
			
			parent::insert_model($lender);
			mount_entity_file("lenders", $lender, $lender['lender_code'], 'lender_logo');
        DB::commit();
		}
		catch (\Exception $e) {
			DB::rollback();
			if ($e instanceof QueryException){
			throw $e;
			}else{
			thrw($e->getMessage());
			}
		}
		return $lender['lender_code'];

	}
	public function view($lender_code, array $models = ["*"], array $columns = ["lender" => ["*"],"org" => ["*"], "address" => ["*"], "contact_person" => ["*"]]){
		if(in_array("*", $models) || in_array("lender", $models)){
			$lender = parent::find_by_code( $lender_code, $columns["lender"]);
		}
        if(in_array("*", $models) || in_array("org", $models)){
			$org_id = $lender->org_id;
			$org_details = (new OrgRepositorySQL())->view($org_id,$columns["org"]); 
			$lender->org = $org_details;
	    }
        /*if(in_array("*", $models) || in_array("address", $models)){
        	
			$addr_id = $org_details->reg_address_id;
			$address_details = (new AddressInfoRepositorySQL())->find($addr_id,$columns["address"]);
			$org_details->reg_address = $address_details;
	    }*/
		if(in_array("*", $models) || in_array("contact_person", $models)){
			$person_id = $lender->contact_person_id;
			$person_details = (new PersonRepositorySQL())->find($person_id,$columns["contact_person"]);
			$lender->contact_person = $person_details;
			if($person_details){
				#$lender->contact_person->file_rel_path = file_rel_path();
			}
        } 
		#$lender->file_rel_path = file_rel_path();
		return $lender;
	}

	public function update(array $data){
		/*if(array_key_exists('lender_code', $data)){
			$data['lender_code'] = $this->country_code[0].$data['lender_code'];
		}*/
		$result = parent::update_model($data);
		if(array_key_exists('lender_logo', $data)){
			$data['country_code'] = $this->country_code;
			$result  = parent::get_record_by('id',$data['id'],['lender_code']);
			mount_entity_file("lenders", $data, $result->lender_code, 'lender_logo');
		}
		//mount_entity_file("lenders", $lender, $lender['lender_code'], 'lender_logo');
		return $result;
	}

	public function list(array $data){
		return parent::get_records_by_country_code(["*"], $data['status']);
	}

	public function delete($id){
		throw new BadMethodCallException();

	}
	public function get_lender_name($lender_code)
	{
        $lender =  parent::get_record_by('lender_code', $lender_code, ['name']);
        return $lender;
	}
}


