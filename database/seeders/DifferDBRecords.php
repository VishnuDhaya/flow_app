<?php

use Illuminate\Database\Seeder;

class DifferDBRecords extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$this->left_db = "flow_api_dev";
        //$this->right_db = "flow_api_test";

        $db1_tables_name = DB::select("select TABLE_NAME from information_schema.TABLES where TABLE_SCHEMA=?",[$this->left_db]);

		foreach($db1_tables_name as $db1_table_name){
       		$table_repo = new CommonRepositorySQL();

        	$db1_table = $db1_table_name->TABLE_NAME;
        	$column_names = DB::select("select COLUMN_NAME from information_schema.COLUMNS where TABLE_SCHEMA = ? and table_name = ? and COLUMN_NAME not in('created_at','created_by','updated_at','updated_by')",[$db_name1,$db1_table]);
        	$column_names = pluck($column_names, "COLUMN_NAME")	;

        	$this->compare_table($db1_table_name, $column_names);
        }
	}


 	public function compare_table($table_name, $column_names = null){
 		$this->left_db = "flow_api_test_nov_1";
        $this->right_db = "flow_api_test_nov_5";
 		$db1_table = $table_name;
    	$column_names = DB::select("select COLUMN_NAME from information_schema.COLUMNS where TABLE_SCHEMA = ? and table_name = ? and COLUMN_NAME not like '%_id' and COLUMN_NAME not in('created_at','created_by','updated_at','updated_by', 'id')",[$this->left_db,$db1_table]);
    	$column_names = pluck($column_names, "COLUMN_NAME");


 		
 		$this->full_table_name = "{$this->right_db}.$table_name";
 		$column_names_str = implode(", ", $column_names);
 		$all_records = DB::select("select id, $column_names_str from {$this->left_db}.$table_name");
 		foreach ($all_records as $record) {
 			$record = (array)$record;
 			
 			$this->id = $record['id'];
 			unset($record['id']);
 			//var_dump($column_names);
 			//var_dump(array_values($record));
 			$result = $this->get_record_by_many($column_names,array_values($record), $column_names);
 			if($result == null){
 				var_dump("$$$ NOT EXIST {$this->left_db}.{$table_name}.{$this->id}");
 			}else{
 				//var_dump("{$this->left_db}.{$table_name}.{$id} exists as {$this->right_db}.{$table_name}.{$result->id}");
 			}
 		}
	}
 	private function unset_columns(&$column_names){
		$to_unset = ['id', 'create'];
		foreach ($to_unset as  $value) {
			if(array_key_exists($value, $column_names)){
				unset($column_names[$value]);
			}
		}
	}
 		
    public function get_record_by_many($field_names,  $field_values, $fields_arr = ["*"]){
		$result = $this->get_records_by_many($field_names,  $field_values, $fields_arr);

		if(sizeof($result) == 1){
			return $result[0];
 		}
		if(sizeof($result) == 0){
			return null;
		}else{
			//thrw("ERROR : More than one {$this->model()} returned");
			//return null;
			/*var_dump("DUP-START");
			foreach ($result as $item) {
				var_dump("DUPLICATE {$this->full_table_name}.{$item->id}");	
			}
			var_dump("DUP-END");*/
			return $result[0];;
		}
	}


	public function get_records_by_many(array $field_names,  array $field_values ,array $fields_arr = ["*"], $condition = "and", $addl_sql_condition = "", $add_country = false){
		//$model = $this->model();
		$table_name = $this->full_table_name;
		$fields_str = $this->get_field_str($fields_arr);
		
		$where_str = "";
		$op = '=';
		$this->remove_null_status($field_names, $field_values);
		
		for ($i = 0; $i < sizeof($field_names); $i++) {
			$field_name = $field_names[$i];
			if($field_values[$i] === null){
				unset($field_values[$i]);
				$where_str .= " $field_name is null";
			}else{
				$where_str .= " $field_name $op ?";	
			}	
			
			if ($i < sizeof($field_names) - 1){ // Append and for all but last
				$where_str .= " $condition ";
			}
		}	
		if ($where_str != "" && $addl_sql_condition!=""){
			$addl_sql_condition = " $addl_sql_condition";
		}
		if($add_country){
			if($where_str != ""){
				$where_str = "($where_str) and country_code = ?";
			}else{
				$where_str = "country_code = ?";
				if($addl_sql_condition){
					$where_str .= " and ";
				}
			}
			$field_values[] = $this->country_code;
		}

		$sql = "/*{$this->id}*/select $fields_str from $table_name where $where_str $addl_sql_condition";
		//var_dump($field_values);

		$field_values = array_values($field_values);
		//var_dump($field_values);
		return DB::select($sql, $field_values);
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
	private function get_field_str($fields_arr){
		$fields_str = implode(", ",$fields_arr);
		if ($fields_str != "*" && !str_contains($fields_str, "id,")){
			$fields_str = "`id`, " .  $fields_str;
		}
		return $fields_str;
	}
}
