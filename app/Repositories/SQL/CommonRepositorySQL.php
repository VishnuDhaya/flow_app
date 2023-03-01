<?php
namespace App\Repositories\SQL;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Models\MasterData;
use App\Models\FlowApp\AppUser;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Repositories\SQL\PersonRepositorySQL;
use Carbon\Carbon;
use Log;

class CommonRepositorySQL  extends BaseRepositorySQL 
{
	public function __construct($class = null)
    {
      parent::__construct();
      $this->class = $class;

    }

   

	public function model(){
		return $this->class;
	}

	public function get_entity_id_from_mobile_num($mobile_num){
		$brwr_repo = new BorrowerRepositorySQL;
		$entity_id = $brwr_repo->get_cust_id_from_mobile_num($mobile_num, true);
		$entity = 'customer';
		if(!$entity_id){
			$lead_repo = new LeadRepositorySQL;
			$entity_id = $lead_repo->get_lead_id_from_mobile_num($mobile_num);
			$entity = 'lead';
		}
		return [$entity,$entity_id];
	}

	public function get_master_data($master_data, $include_global = false, $fields = ["data_key", "data_code", "data_value", "parent_data_code", "status"]){
		$this->class = MasterData::class;
		if(array_key_exists('country_code', $master_data)){
			$country_code = $master_data['country_code'];	
			unset($master_data['country_code']);
		}else{
			$country_code = '*';
		}
		
		
		$field_names = array_keys($master_data);
		$field_values = array_values($master_data);
		if($include_global && $country_code!='*'){
			$field_values[] = "*";	
			$field_values[] = $country_code;
			$addl_sql_condition = " and country_code in (?, ?) order by data_value";
		}else{
			$field_values[] = $country_code;
			$addl_sql_condition = " and country_code = ? order by data_value";
		}
	    
	    
		return parent::get_records_by_many($field_names, $field_values, $fields , " and ", $addl_sql_condition, false);
	}

	public function get_country_name_list(){
		return DB::select("/*$this->api_req_id*/ select country_code as id,country as name,currency_code from countries where status='enabled'");
	}


	public function get_country_list(){
		return DB::select("/*$this->api_req_id*/ select country_code ,country ,currency_code, time_zone from countries where status='enabled'");
	}


	public function get_currency_list(){
		return DB::select("select distinct currency_code as id,currency as name from countries where currency_code != '' and status='enabled'");
	}
	public function get_currency_code($country_code){

		return DB::selectOne("/*$this->api_req_id*/ select currency_code from countries where country_code = ?" , [$country_code]);
	}

	public function get_currency(){

		return DB::selectOne("/*$this->api_req_id*/ select currency_code from markets where country_code = ?" , [$this->country_code]);
	}

	public function get_new_flow_id($country_code, $id_type){

		$flow_id_result = DB::selectOne("/*$this->api_req_id*/ select * from flow_ids where id_type = ? and country_code = ? limit 1" , [$id_type,$country_code]);
		$id = $flow_id_result->id;
		DB::delete("/*$this->api_req_id*/ delete from flow_ids where id = ?" , [$id]);
		return $flow_id_result->id_value;
	}

	public function get_markets(){
		return DB::select("/*$this->api_req_id*/ select country_code, time_zone from markets where status=?" , ["enabled"]);
	}
    

   public function get_persons_by_role($role){
        return DB::select("/*$this->api_req_id*/ select person_id, email from app_users where role_codes=? and country_code=?",[$role, $this->country_code]);
    }
   public function get_users_by_role($role, $status){
      $users =  DB::table('app_users')
		                    ->select('person_id','email', 'status')
		                    ->where('role_codes',$role)
		                    ->where('status',$status)
		                    ->whereIn('country_code',[$this->country_code, "*"])
		                    ->orderBy('status', 'desc')
		                    ->orderBy('email', 'asc')
		                    ->get()->toArray(); 
      return $users;
    }

    public function get_role_codes($priv_code, $status){
		//return parent::get_record_by('priv_code', $priv_code, ['role_code'], $this->country_code);
		return DB::select ("/*$this->api_req_id*/ select role_code from app_role_privileges where priv_code=? and status = ?", [$priv_code, $status]);
		}

		public function get_users_by_role_codes($role_codes,$status){
			$users =  DB::table('app_users')
		                    ->select('person_id','email', 'status')
		                    ->whereIn('role_codes',$role_codes)
		                    ->where('status',$status)
		                    ->whereIn('country_code',[$this->country_code, "*"])
		                    ->orderBy('status', 'desc')
		                    ->orderBy('email', 'asc')
		                    ->get()->toArray(); 
      			return $users;


		}
	public function get_users_by_priv($priv_code,$country_code,$status){
		$role_codes = $this->get_role_codes($priv_code,$status);
		$persons_list =  array();
		foreach($role_codes as $role_code){
			$role = $role_code->role_code;
			$persons = $this->get_users_by_role($role,$status);
			$persons_list = array_merge($persons,$persons_list);
		}
		return $persons_list;
	}

	public function get_all_users(){
		$this->class = AppUser::class;
		//return parent::get_records_by_country_code(['id', 'person_id', 'email']);
	return DB::select("/*$this->api_req_id*/ select id,person_id,email from app_users where country_code=? and status=? ", [$this->country_code,'enabled']);
	}

	public function get_person_full_names($persons, $default_person_id = null){
		$person_repo = new PersonRepositorySQL();
		$persons_list =  array();

		foreach($persons as $person){
				$person_name = $person_repo->full_name_by_sql($person->person_id);
				$selected = false;
				if($default_person_id == $person->person_id){
					
					$selected = true;
				}
				$persons_list[] = ['id' => $person->person_id, 'name' => "$person_name ($person->status)", 'email' => $person->email,'selected' => $selected, 'name_email' => "$person_name ($person->email)"];
				
				
			}
		return $persons_list;
	}
	public function get_time_zone($country_code){
		$result = DB::selectOne("/*$this->api_req_id*/ select time_zone from countries where country_code=?", [$country_code]);
		return $result->time_zone;
	}
	public function update_status(array $data){
 		$table_name = key($data);		
 		$status = $data[$table_name]['status'];			
 		return DB::table(DB::raw("$table_name /*$this->api_req_id*/ "))->where('id', $data[$table_name]['id'])->update(['status' => $status]);
	}
 	public function remove_file_frm_table($id, $table_name, $column_name){
 		return DB::update("/*$this->api_req_id*/ update $table_name set $column_name = NULL where id = ?",[$id]);
 	}

 	public function check_if_file_exists($id, $table_name, $column_name){

 		$resp = DB::table(DB::raw("$table_name /*$this->api_req_id*/ "))->whereNotNull($column_name)->whereRaw('id = ?',[$id])->pluck($column_name);

 		if(sizeof($resp) > 0){
 			return $resp[0];
 		}
 		return null;		
 	}

 	public function create_customer_register($data){ 		

 		$resp = DB::insert('insert into agents(name,acc_prvdr_code,mob_num,acc_number,password,verified_by_dp,consent)
 			values(?,?,?,?,?,?,?)',array_values($data)); 		 
 		     return $resp;		
		 } 
	
	
	
 	   
  }

