<?php
 namespace App\Repositories\SQL;
use Illuminate\Support\Facades\DB;
use App\Models\Person;
use App\Repositories\SQL\AddressRepositorySQL;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use Log;
 class RelationshipManagerRepositorySQL extends BaseRepositorySQL implements BaseRepositoryInterface{
 	
 	public function __construct()
  	{
      parent::__construct();

    }
    
 	public function model(){
			return Person::class;
	}
 	public function create(array $person){
 		$person_id = parent::insert_model($person);
 		return $person_id;
 	}

 	public function view($relationship_manager_id, $country_code){
 		$relationship_manager = parent::find($relationship_manager_id);
		#$relationship_manager->file_rel_path = file_rel_path();
		return $relationship_manager;
 	}
 	public function update(array $relationship_manager){
 		return parent::update_model($relationship_manager);
 	}

	public function list($id){
		throw new BadMethodCallException();	
	}

	public function delete($id){
		throw new BadMethodCallException();	
	}
	 public function show_list(array $data){
	return parent::get_records_by_many(array_keys($data), array_values($data));
    }

     public function show_name_list(array $req){
        $sql = "/*$this->api_req_id*/ select id, first_name,middle_name,last_name from persons where (country_code=? and status=?) and (associated_with = 'FG' or (associated_with = ? and associated_entity_code is null) or ";
        $person_list = null;
    	 	if(isset($req["associated_entity_code"]))
    	 	{
    	 		 $person_list = DB::select(" $sql (associated_with=? and associated_entity_code = ?))" ,[session('country_code'), $req['status'], $req['associated_with'], $req['associated_with'], $req['associated_entity_code']]);
    	 	}else if(isset($req["associated_with"])){
            $person_list =  DB::select(" $sql associated_with = ?) ",[session('country_code'), $req['status'], $req['associated_with'], $req['associated_with']]);
        }
        return $this->get_as_id_full_name($person_list);
    }

     public function get_flow_rel_name($country_code,$associated_with)
     {
   
	  $persons  = DB::select("/*$this->api_req_id*/ select p.id, first_name,middle_name,last_name from persons p, app_users a where a.role_codes = 'relationship_manager' and a.person_id = p.id and p.associated_with=? and p.status ='enabled' and p.country_code=?" ,[$associated_with,$country_code]);
      return $this->get_as_id_full_name($persons);
         
     }


     private function get_as_id_full_name($persons){
         $person_list = array();
         foreach ($persons as $person)
          {
           $val['id'] = $person->id;
           $val['name'] = full_name($person);
           $person_list[] = $val;
         }
         return $person_list;
     }
 }
