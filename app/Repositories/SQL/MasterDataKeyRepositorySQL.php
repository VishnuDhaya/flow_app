<?php
namespace App\Repositories\SQL;
use Illuminate\Support\Facades\DB;
use App\Consts;
use App\Models\MasterDataKeys;
use Log;

class MasterDataKeyRepositorySQL extends BaseRepositorySQL{

	public function __construct()
  	{
      parent::__construct();

    }
    
	public function model(){
			return MasterDataKeys::class;
	}


	public function create(array $master_data_keys){
		
		return parent::insert_model($master_data_keys);
	}

	public function get_new_data_keys($country_code){
		return DB::select("/*$this->api_req_id*/ select data_key from master_data_keys where country_code = ? and data_key NOT IN (select distinct data_key from master_data where country_code = ?)" , [$country_code, $country_code]);
	}


	public function list($country_code){
		//return parent::get_records_by_country_code($country_code, ['data_key', 'parent_data_key', 'key_desc', 'key_group', 'status']);
		$field_values = [$country_code, "*"];
		$addl_sql_condition = " country_code in (?, ?) order by country_code, data_group";
		return parent::get_records_by_many([], $field_values,  ['data_key', 'parent_data_key', 'data_type', 'data_group', 'key_desc', 'status', 'country_code'] , "", $addl_sql_condition, false);

	}

	public function update_status($master_data_key){
		 return parent::update_record_status($master_data_key['status'], $master_data_key['id']);
	}

	public function get_parent_data_key(array $data){
        $country_code = $data['country_code'];
        unset($data['country_code']);
        $field_names = array_keys($data);
        $field_values = array_values($data);
		$field_values[] = $country_code;
	    $field_values[] = "*";
	    $addl_sql_condition = " and country_code in (?,?)";
	    $result = parent::get_records_by_many($field_names, $field_values,  $fields_arr = ["parent_data_key"] , " and ", $addl_sql_condition, false);
		if(sizeof($result) > 0){
			return $result[0]->parent_data_key;
		}else{
			return null;
		}
	}

   public function get_names($data)
   {
     $country_code = $data['country_code'];
	 return DB::select("/*$this->api_req_id*/ select country_code,data_key,data_type, data_group from master_data_keys where country_code in (?,?) order by data_group", ['*',$country_code]);
   }

}