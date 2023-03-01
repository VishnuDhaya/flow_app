<?php
namespace App\Repositories\SQL;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Exceptions\FlowCustomException;
use Illuminate\Database\Query\Expression;

class BaseRepositorySQL {
	
	 public function __construct()
    {
    	
    	$this->country_code = session('country_code');
		$this->add_country_code(true);    	
    	$this->api_req_id = session('api_req_id_n_path');
		Log::warning($this->api_req_id);
    	//Log::warning("BaseRepositorySQL");
    	//Log::warning($this->api_req_id);
    	//Log::warning($this->country_code);

    	if(!$this->country_code){
    		thrw("ERROR: COUNTRY CODE IS NULL");
    	}
    }

	protected function get_table_name(){
		$model = $this->model();
		return $model::TABLE;
	}

	protected function add_country_code($value){
		return $this->add_country_code = $value;
	}
	public function get_last_id(){
		$model = $this->model();
		$table_name = $model::TABLE;
		$result = DB::select("/*$this->api_req_id*/ SELECT max(id) as id from $table_name");
		return $result[0]->id;
		
	}

	public function get_id($code){
		$model = $this->model();
		$code_name = $model::CODE_NAME;
		$record = $this->get_record_by($code_name, $code, ["id"]);
		
		if($record){
			return $record->id;
		}else{
			
			return null;
		}
	}

	public function get_code($id){
		$model = $this->model();
		$code_name = $model::CODE_NAME;
		$record = $this->get_record_by("id", $id, [$code_name]);
			
		if($record){
			return $record->{$code_name};
		}else{
			
			return null;
		}
	}

	public function find($id, $fields_arr = ["*"]){
		
		$records_arr = $this->get_records_by("id",$id, $fields_arr);

		if(sizeof($records_arr) == 1){
			//$address = collect($address_details[0])->toArray();
 			return $records_arr[0];
 		}
 		if(sizeof($records_arr) == 0){
			return null;
		
		}else{
			thrw("ERROR : No or more than one {$this->model()} returned for id = '$id'", 9999);
			//return null;
		}
	}

	public function find_by_code($field_value, $fields_arr = ["*"]){
		$model = $this->model();
		 $field_name =  $model::CODE_NAME;
		if(!defined("$model::CODE_NAME")){
			thrw("Constant CODE_NAME not defined in the class $model.", 9999);
		}

		return $this->get_record_by($field_name,  $field_value, $fields_arr);
	}

	public function get_records_by($field_name,  $field_value, $fields_arr = ["*"], $status = null, $addtn_condn =""){
		if(is_array($field_value)){
			return $this->get_records_by_in($field_name, $field_value, $fields_arr = $fields_arr, $status);
		}

		$model = $this->model();
		$table_name = $model::TABLE;
		$fields_str = $this->get_field_str($fields_arr);
		
		$field_values = [$field_value];
		$country_ctn = country_ctn($this->country_code, $field_values);
		$status_ctn = status_ctn($status, $field_values);
		
		$results = DB::select("/*$this->api_req_id*/ select $fields_str from $table_name where `$field_name` = ?  " . $country_ctn . $status_ctn . " $addtn_condn" , $field_values);
        $this->decode_json_fields($results);
        return $results;
	}

	public function get_records_by_in($field_name, array $field_values, $fields_arr = ["*"], $status = null, $addtn_condn = ""){
		
		$model = $this->model();
		$table_name = $model::TABLE;
		$fields_str = $this->get_field_str($fields_arr);
		$field_values_csv = csv($field_values);
		$field_values = array();
		$country_ctn = country_ctn($this->country_code, $field_values);
		$status_ctn = status_ctn($status, $field_values);
		
		
		$results = DB::select("/*$this->api_req_id*/ select $fields_str from $table_name where `$field_name` in ($field_values_csv)" . $country_ctn . $status_ctn . $addtn_condn, $field_values);
        $this->decode_json_fields($results);
        return $results;
	}


	protected function get_field_str($fields_arr){
		
		$fields_str = implode(", ",$fields_arr);
		if ($fields_str != "*" && (!str_contains($fields_str, "id,") || !in_array('id', $fields_arr))){
			$fields_str = "`id`, " .  $fields_str;
		}
		return $fields_str;
	}


	public function get_record_by_many($field_names,  $field_values, $fields_arr = ["*"], $condition = "and", $addl_sql_condition = ""){
		$result = $this->get_records_by_many($field_names,  $field_values, $fields_arr, $condition, $addl_sql_condition);


		if(sizeof($result) == 1){
			return $result[0];
 		}
		if(sizeof($result) == 0){
			return null;
		}else{
			thrw("ERROR : More than one {$this->model()} returned", 9999);
			//return null;
		}

	}

	public function get_json_by($json_col_name, $json_condition, $fields_arr = ["*"], $addl_sql = null){
		$result = $this->get_jsons_by($json_col_name, $json_condition, $fields_arr, $addl_sql);
	
		if(sizeof($result) == 1){
			return $result[0];
 		}
		if(sizeof($result) == 0){
			return null;
		}else{
			thrw("ERROR : More than one {$this->model()} returned for $json_col_name", 9999);
			//return null;
		}

	}

	public function get_jsons_by($json_col_name, $json_condition, $fields_arr = ['*'], $addl_sql= null){
		$model = $this->model();
		$table_name = $model::TABLE;
		$fields_str = $this->get_field_str($fields_arr);
		$json_condition = json_encode($json_condition);
		$json_condition = "json_contains($json_col_name, '$json_condition')";
		return DB::select("select $fields_str from $table_name where $json_condition {$addl_sql}");

		
	}

	public function get_record_by($field_name,  $field_value, $fields_arr = ["*"]){
		$result = $this->get_records_by($field_name,  $field_value, $fields_arr);
	
		if(sizeof($result) == 1){
			return $result[0];
 		}
		if(sizeof($result) == 0){
			return null;
		}else{
			thrw("ERROR : More than one {$this->model()} returned for $field_name = '$field_value'", 9999);
			//return null;
		}

	}
/*
	public function get_records_by_many(array $field_names,  array $field_values ,array $fields_arr = ["*"], $condition = "and", $addl_sql_condition = ""){
		$model = $this->model();
		$table_name = $model::TABLE;
		$fields_str = $this->get_field_str($fields_arr);

		$where_str = "";
		
		for ($i = 0; $i < sizeof($field_names); $i++) {
			$where_str .= " `$field_names[$i]` = ?";
			
			if ($i < sizeof($field_names) - 1){
				$where_str .= " $condition ";
			}
		}	
		$sql = "select $fields_str from $table_name where $where_str $addl_sql_condition";
		
		return DB::select($sql, $field_values);
	}
*/
	private function add_date_fields(&$field_values,  &$field_name, $i, &$op){
		//var_dump(user());
		if(Str::endsWith($field_name , "__from")){
			$field_name = Str::replaceLast("__from" , "", $field_name);
			$op = ">=";
			$from_date = parse_date($field_values[$i])->startOfDay();
			$field_values[$i] = $from_date;
		}else  if(Str::endsWith($field_name , "__to")){
			$field_name = Str::replaceLast("__to" , "", $field_name);
			$op = "<=";
			$to_date = parse_date($field_values[$i])->endOfDay();
			$field_values[$i] = $to_date;
		}else  if(Str::endsWith($field_name , "__today")){
			$field_name = Str::replaceLast("__today" , "", $field_name);
			$field_name = "DATE($field_name)";
			$op = "=";
			$field_values[$i] = date_db();
		}
	}

	private function remove_null_status(&$field_names,  &$field_values){	
			
			$index = array_search("status", $field_names);	
			if($index !== false){
				if($field_values[$index] === null){
					unset($field_names[$index]);
					$field_names = array_values($field_names);
					unset($field_values[$index]);
					$field_values = array_values($field_values);
				}
			}
	
	}	

	public function pluck_by_many(array $field_names,  array $field_values ,$field, $condition = " and ", $addl_sql_condition = ""){
		$obj_arr = $this->get_records_by_many($field_names,  $field_values, $field, $condition , $addl_sql_condition);
		
		return pluck($obj_arr, $field[0]);
	}

	public function get_records_by_many(array $field_names,  array $field_values ,array $fields_arr = ["*"], $condition = "and", $addl_sql_condition = "", $add_country = true){
		
				

		$model = $this->model();
		$table_name = $model::TABLE;
		$fields_str = $this->get_field_str($fields_arr);
		
		$where_str = "";
		
		$this->remove_null_status($field_names, $field_values);
		

		for ($i = 0; $i < sizeof($field_names); $i++) {
			$op = '=';

			$field_name = $field_names[$i];
			
			$this->add_date_fields( $field_values,  $field_name, $i, $op);

			$where_str .= " $field_name $op ?";
			
			if ($i < sizeof($field_names) - 1){ // Append and for all but last
				$where_str .= " $condition ";
			}
		}


		if ($where_str != "" && $addl_sql_condition!=""){
			$addl_sql_condition = "$addl_sql_condition";
		}
		if($add_country && $this->country_code != 'global'){
			if($where_str != ""){
				$where_str = "($where_str) and country_code = ?";
				
			}else{
				$where_str = "country_code = ?";
				if($addl_sql_condition){
					$condition_array = explode(" ",$addl_sql_condition);
					if($condition_array[0] != "and"){
						$where_str .= " and ";	
					}
					
				}
			}
			$field_values[] = $this->country_code;
		}
		// $addl_sql_condition = str_replace("and  and", replace, subject)


		$sql = "/*$this->api_req_id HERE*/ select $fields_str from $table_name where $where_str $addl_sql_condition";
		$result =  DB::select($sql, $field_values);
        $this->decode_json_fields($result);
        return $result;
	}

	public function get_records_by_any(array $field_names,  array $field_values ,array $fields_arr = ["*"], $addl_sql_condition = "", $add_country = true){
		return $this->get_records_by_many($field_names, $field_values , $fields_arr, "or", $addl_sql_condition,  $add_country);
	}

	public function pluck_by_any(array $field_names,  array $field_values ,$field, $addl_sql_condition = '')
	{
		if($this->country_code != 'global'){
			$addl_sql_condition =  " and country_code = '{$this->country_code}' " . $addl_sql_condition; 	
		}
		
		return $this->pluck_by_many($field_names, $field_values , $field, "or", $addl_sql_condition);
	}
	



	public function get_records_by_country_code($fields_arr = ["*"], $status= null){

		return $this->get_records_by("country_code",  $this->country_code, $fields_arr, $status);

	}

	public function get_name_list($fields = ["id", "name"], $first_item_arr = null, $status= null){

		$model = $this->model();
		$table_name = $model::TABLE;
		//$field_values = [$country_code];
		$field_values = [$this->country_code];
		
		$result_arr = DB::select("/*$this->api_req_id*/ select $fields[0] as id, $fields[1] as name from $table_name where country_code = ? ".status_ctn($status, $field_values),$field_values);

		if($first_item_arr){
			$first_item = array();
			$first_item['id'] = $first_item_arr[0];
			$first_item['name'] = $first_item_arr[1];
			array_unshift($result_arr,$first_item);
		}
		
		return $result_arr;
	}
	public function get_record_status_by_code($code){
		$model = $this->model();
		return $this->get_record_by($model::CODE_NAME, $code, ['status']);
	}
	public function update_record_status($new_status, $id, $status_field = 'status'){

		$model = $this->model();
		$table_name = $model::TABLE;
		$record = $this->find($id,[$status_field]);
		$curr_status = $record->{$status_field};

		if($curr_status == $new_status){
			return true;//throw new FlowCustomException("Record already in '$new_status' status");
		}
		$result = DB::update("/*$this->api_req_id*/ update $table_name set `updated_at` = ? , `updated_by` = ? ,`$status_field` = ? where `id` = ?" , [datetime_db(),  session('user_id'), $new_status, $id]);
		return ($result == 1);

	}


	public function increment_by_code($field_name, $id, $incr_val = 1){
		$model = $this->model();
		return $this->increment($field_name, $id, $model::CODE_NAME, $incr_val);
	}	

	public function increment($field_name, $id, $incr_by_field = 'id', $incr_val = 1){
		$model = $this->model();
		$query_str = "`$field_name` = `$field_name` + $incr_val , ";

		//if($model::TOUCH){
			$query_str .= "`updated_at` = ? ,";
			$query_values[] = datetime_db();
			$query_str .= "`updated_by` = ? ";
			$query_values[] = session('user_id');
		//}
		$query_str .= " where $incr_by_field = ?";

		$query_values[] = $id;

		$table_name = $model::TABLE;
		$query_str = "/*$this->api_req_id*/ update `$table_name` set $query_str";
		//echo $records_modified; 
		$records_modified = DB::update($query_str, $query_values);
		//echo $records_modified;
		return $records_modified;

	}



	public function update_model_by_code(array $data){
		$model = $this->model();
		$update_by_field =  $model::CODE_NAME;
		if(array_key_exists($update_by_field, $data)){
			return $this->update_model($data, $update_by_field);
		}else{
			thrw("[$model::CODE_NAME] not exist in the parameter");
		}
		
	}	

	private function construct_json($keys_arr, $value) {
	
		$no_of_keys = sizeof($keys_arr);

		if ($no_of_keys == 1) {
			return [array_shift($keys_arr) => $value];
		}
		else {
			return [array_shift($keys_arr) => $this->construct_json($keys_arr,$value)];
		}
	
		
	}
	



	public function update_json_arr($json_col_name,$fields_arr,$id){

		$table_name = $this->get_table_name();
        $addl_sql = $this->get_json_update_sql($fields_arr);
        return DB::update("update {$table_name} set $json_col_name = JSON_MERGE_PATCH(`$json_col_name`, '$addl_sql') where id = ?", [$id]);
	}
		


	public function update_model(array $data, $update_by_field = 'id'){
		
		$model = $this->model();

		if(sizeof($model::UPDATABLE) == 0){

			throw new Exception("Constant UPDATABLE not defined in the class $model.");
		}

		if(!defined("$model::TABLE")){

			throw new Exception("Constant TABLE not defined in the class $model.");
		}
		//$update_by_value = $data[$update_by_field];
		//unset($data[$update_by_field]);
		$keys = array_keys($data);
		$query_str = "";
		$query_values = array();
		foreach($keys as $key){
			if(in_array($key,$model::UPDATABLE)) {
				$value = $data[$key];
				if($value instanceof  Expression){
					$query_str .= "`$key` = $value, " ;	
				}else{
                    if($this->is_json($key)){
                        $query_str.= "`$key` = JSON_MERGE_PATCH(`$key`, ?), " ;
                        $query_values[] = $this->get_json_update_sql($data[$key]);
                    }
                    else{
                        $query_str .= "`$key` = ?, " ;
                        $query_values[] = $data[$key];
                    }
				}
				
			}
			else{
				if($key != $update_by_field){
					Log::warning("BASE_REPO_WARN : $key not included for update");
					//print_r("$key not included for update\n");
				}
				continue;			
			}
		}
		//if($model::TOUCH){
			$query_str .= "`updated_at` = ? ,";
			$query_values[] = datetime_db();
			$query_str .= "`updated_by` = ? ";
			$query_values[] = session('user_id');
		//}
		$query_str .= " where $update_by_field = ?";
		$query_values[] = $data[$update_by_field];

		$table_name = $model::TABLE;
		$query_str = "/*$this->api_req_id*/ update $table_name set $query_str";
		$records_modified = DB::update($query_str, $query_values);
		return $records_modified;

	}


	public function insert_model(array $data){

		$model = $this->model();
		
		if(sizeof($model::INSERTABLE) == 0){
			throw new Exception("Constant $model::INSERTABLE not defined in class $model.");
		}
		if(!defined("$model::TABLE")){
			throw new Exception("Constant $model::TABLE not defined in class $model");
		}
		
		$keys = array_keys($data);
		$column_str = "";
		$param_str = "";
		$query_values = array();
		$insert_arr = array();

		foreach($keys as $index=>$key){
       		if(in_array($key,$model::INSERTABLE)) {
			
				$column_str .= "`$key`, " ;
				$param_str  .= "?, ";
                if($this->is_json($key)){
					$json_data = json_encode($data[$key]);
                    $query_values[] = $json_data;
					$insert_arr[$key] = $json_data;
                }
                else{
					$value = $data[$key];
                    $query_values[] = $value;
					$insert_arr[$key] = $value;
                }

			}else{
				Log::warning("BASE_REPO_WARN : $key not INSERTABLE for $model");
				//print_r("$key not INSERTABLE for $model\n");
				continue;		
			}
			
		}
		//print_r($model::TOUCH);
		if($model::TOUCH){
			if(!in_array('created_at',$keys)) {
				$column = 'created_at';
				$value = datetime_db();

				$column_str .= "`$column`, ";
				$query_values[] = $value;
				$param_str  .= "?, ";

				$insert_arr[$column] = $value;
			}
			if(!in_array('created_by',$keys)) {
				$column = 'created_by';
				$value = session('user_id');

				$column_str .= "`$column`";
				$query_values[] = $value;
				$param_str  .= "?";

				$insert_arr[$column] = $value;
			}
		}//else{

			$column_str = trim_comma($column_str);
			$param_str  = trim_comma($param_str);
		
		//}

		$table_name = $model::TABLE;

		$query_str = "/*$this->api_req_id*/ INSERT INTO $table_name ($column_str) values ($param_str)";

		return DB::table($table_name)->insertGetId($insert_arr);

		// $result = DB::insert($query_str, $query_values);
	    // //Log::warning($result);

		// if($result){
		// 	return $this->get_last_id();
		// }
		// return $result;

	}

	public function get_json_field($id, $json_field){
		$record = $this->find($id, [$json_field]);
		return json_decode($record->$json_field, true);
		
	}

    public function get_json_field_by_code($field_value, $json_field){
        $record = $this->find_by_code($field_value, [$json_field]);
        return $record->$json_field;

    }
	
	public static function insert($data_arr){
		DB::table(static::TABLE)->insert($data_arr);
    }

    public function decode_json_fields(&$result)
    {
        if($this->has_json()){
            $json_fields = $this->json_fields();
            foreach($json_fields as $json_field){
                if(is_array($result)){
                    foreach($result as $result_item){
                        if(isset($result_item->$json_field)){
                            $result_item->$json_field = json_decode($result_item->$json_field);
                        }
                    }
                }
                elseif(is_object($result)){
                    if(isset($result->$json_field)){
                        $result->$json_field = json_decode($result->$json_field);
                    }
                }
            }
        }
    }

    private function has_json()
    {
        $model = $this->model();
        return defined($model . "::JSON_FIELDS");
    }

    private function is_json($key){
         $model = $this->model();
         return defined($model."::JSON_FIELDS") && in_array($key,$model::JSON_FIELDS);
    }

    private function json_fields(){
        $json_fields = [];
        if($this->has_json()){
            $json_fields = $this->model()::JSON_FIELDS;
        }
        return $json_fields;
    }

    private function get_json_update_sql($fields_arr)
    {
        $addl_sql_arr = [];
        foreach ($fields_arr as $key => $value) {
            if (Str::contains($key, '.')) {
                $keys = explode('.', $key);
                $json_arr = $this->construct_json($keys, $fields_arr[$key]);
                $addl_sql_arr = array_merge($json_arr, $addl_sql_arr);
            } else {
                $addl_sql_arr[$key] = $value;
            }

        }
        $addl_sql = json_encode($addl_sql_arr);
        return $addl_sql;
    }

	public function update_json_arr_by_code($json_col_name,$fields_arr,$field_value){
		
		$model = $this->model();
		$field_name =  $model::CODE_NAME;
		
		$table_name = $this->get_table_name();
        $addl_sql = $this->get_json_update_sql($fields_arr);
		
        return DB::update("update {$table_name} set $json_col_name = JSON_MERGE_PATCH(`$json_col_name`, '$addl_sql') where $field_name = ?", [$field_value]);
	}
}
