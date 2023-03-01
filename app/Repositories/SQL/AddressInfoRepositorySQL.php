<?php
namespace App\Repositories\SQL;
use Illuminate\Support\Facades\DB;
use App\Models\AddressInfo;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Exceptions\FlowCustomException;
use Exception;
use Log;

class AddressInfoRepositorySQL extends BaseRepositorySQL{
	
	public function __construct()
    {
      parent::__construct();

    }

	public function model(){
		return AddressInfo::class;
	}

	private function get_addr_info($addr_obj){
		$addr_config = $this->get_addr_config_list(true, $addr_obj);
		$addr_info = array();
		foreach ($addr_config as $field_num => $value) {
			if(isset($addr_obj[$value->field_code])){
				$addr_info[$field_num]  = $addr_obj[$value->field_code];
			}
		}
		if(isset($addr_obj["id"])){
			$addr_info["id"] = $addr_obj["id"];
		}
		return $addr_info;

	}

	private function get_addr_obj($addr_info){

		$addr_config = $this->get_addr_config_list(true, ["country_code" => $addr_info->country_code]);

		$addr_obj = array();
		foreach ($addr_config as $field_num => $value) {
			$addr_obj[$value->field_code] = $addr_info->{$field_num};
		}

		if(isset($addr_info->id)){
			$addr_obj["id"] = $addr_info->id;
		}


		if(isset($addr_info->country_code)){
			$addr_obj["country_code"] = $addr_info->country_code;
		}
		return $addr_obj;

	}

	public function create(array $addr_obj){

		$this->country_code = $addr_obj['country_code'];
		$addr_info = $this->get_addr_info($addr_obj);
		$addr_info["country_code"] = $addr_obj['country_code'];
		$address_info_id = $this->insert_model($addr_info);


		return $address_info_id;
	}

	public function find($addr_info_id, $columns = ["*"]){
		$address_info = parent::find($addr_info_id, $columns);
		$addr_obj = $this->get_addr_obj($address_info);
		return $addr_obj;
	}

	public function update(array $addr_obj){
		
    $addr_obj['country_code'] = $this->country_code;
     
		//$this->country_code = $addr_obj['country_code'];
		$addr_info = $this->get_addr_info($addr_obj);
		$brwr_repo = new BorrowerRepositorySQL();
		$borrower = array();
		$borrower['biz_address_id'] = $addr_obj['id'];
		$is_update = false;
		if(array_key_exists('gps',$addr_obj)){
    		$borrower['gps'] = $addr_obj['gps'];
			$is_update = true;   	
		}
		if(array_key_exists('location',$addr_obj)){
			$borrower['location'] = $addr_obj['location'];
			$is_update = true;
		}

		if($is_update){
			$result = $brwr_repo->update_model($borrower,'biz_address_id');
		}

		
	
		return parent::update_model($addr_info);
	
	}

	public function list(){
		throw new BadMethodCallException();
	}

	

	public function get_addr_config_list($group = false, $data)
    {

    	$field_values = [$data['country_code']];
  		$fields = DB::select("/*$this->api_req_id*/ select field_num,field_code,field_name, field_type, child_field_code, validation_rules from addr_config where country_code  = ? ".status_ctn($data, $field_values) , $field_values);
    	if($group){
	    	$new_fields = [];
	    	foreach($fields as $field){
	    		$new_fields[$field->field_num] = $field;
	    		unset($field->field_num);
	    	}
	    	return $new_fields;
    	}else{
    		return $fields;
		}
	}

	public function find_addr_text($addr_info_id, $columns = ['field_10', 'field_9', 'field_8', 'field_7', 'field_6', 'field_5', 'field_4', 'field_3', 'field_2', 'field_1']){
		$address_info = parent::find($addr_info_id, $columns);

		$addr = '';
		foreach($address_info as $key => $value){
			if($key != 'id' && $value != null){
				$addr = $addr. ', '. $value;
			}
		}
		$addr = trim($addr, ', ');
		return $addr;


	}

    public function delete_model($id)
    {
		$model = $this->model();
		$table_name = $model::TABLE;
		DB::table($table_name)->delete($id);
    }
}

?>
