<?php
 namespace App\Repositories\SQL;

use App\Repositories\SQL\BorrowerRepositorySQL;
use Illuminate\Support\Facades\DB;
use App\Models\Person;
use App\Models\FlowApp\AppUser;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use Log;

 class PersonRepositorySQL extends BaseRepositorySQL {

 	 public function __construct()
    {
      parent::__construct();

    }

 	public function model(){
			return Person::class;
	}

 	public function create(array $person,$lead_id = null, $person_type = null){
 		$person_id = parent::insert_model($person);
    if($lead_id && ($person_type == 'owner_person' || $person_type == 'contact_person' )){
      $photo_national_id_path = move_entity_file("leads","persons", $person, $person_id, 'photo_national_id',$lead_id);
	    $photo_national_id_back_path =  move_entity_file("leads","persons", $person, $person_id, 'photo_national_id_back',$lead_id);
      $photo_pps_path = move_entity_file("leads","persons", $person, $person_id, 'photo_pps',$lead_id);
      $photo_selfie_path = move_entity_file("leads","persons", $person, $person_id, 'photo_selfie',$lead_id);
      $result =  ["person_id"=> $person_id ,"photo_national_id_path" => $photo_national_id_path, "photo_national_id_back_path" => $photo_national_id_back_path,
      "photo_pps_path" => $photo_pps_path , "photo_selfie_path" => $photo_selfie_path ];
    }else if($person_type == 'third_party_owner'){
        $photo_national_id_path = move_entity_file("leads","persons", $person, $person_id, 'photo_national_id',$lead_id);
	      $photo_national_id_back_path =  move_entity_file("leads","persons", $person, $person_id, 'photo_national_id_back',$lead_id);
        $result = compact('person_id', 'photo_national_id_path', 'photo_national_id_back_path') ;
    }else{
      mount_entity_file("persons", $person, $person_id, 'photo_national_id');
      mount_entity_file("persons", $person, $person_id, 'photo_national_id_back');
      mount_entity_file("persons", $person, $person_id, 'photo_pps');
      mount_entity_file("persons", $person, $person_id, 'photo_selfie');
      $result = $person_id;
    }


 		return $result;

 	}


     public function update_person_kyc(array $person, $lead_id)
     {
         parent::update_model($person);
         $person_id = $person['id'];
         $photo_keys = ['photo_national_id', 'photo_national_id_back', 'photo_pps', 'photo_selfie'];
         foreach($photo_keys as $photo_key){
             move_entity_file("leads","persons", $person, $person_id, $photo_key,$lead_id);
         }

     }


  public function full_name_by_user_id($user_id){
    $person_id = $this->get_person_id($user_id);
    $person =  parent::find($person_id, ['first_name','middle_name','last_name']);
    return full_name($person);
	}

 	public function full_name($person_id){
     $person =  parent::find($person_id, ['first_name','middle_name','last_name']);
 		return full_name($person);
  }

  public function get_full_name($person_id){
    $person =  parent::find($person_id, ['first_name','middle_name','last_name']);
    return $person;
  }

  public function get_gender($person_id){
    $person = parent::find($person_id,['gender'])->gender;
    return $person;
  }

  public function get_first_name($person_id){
    $person = parent::find($person_id,['first_name'])->first_name;
    return $person;
  }

  public function full_name_by_cust_id($cust_id){
    $person =  $this->get_person_by_cust_id($cust_id);
    return full_name($person);
 }

  public function get_person_by_cust_id($cust_id){
    $borr_repo = new BorrowerRepositorySQL();
    $borrower = $borr_repo->get_record_by('cust_id',$cust_id,['owner_person_id',]);
    if($borrower){
      $person = $this->get_person_name($borrower->owner_person_id);
    }
    return $person;
  }

	public function get_mobile_num($flow_rel_mgr_id){
    $flow_rel_mgr_num=DB::selectOne("/*$this->api_req_id*/ select mobile_num from persons where id = ? and country_code = ? limit 1",[$flow_rel_mgr_id, $this->country_code]);
    return $flow_rel_mgr_num-> mobile_num;
	}



    public function get_person_contacts($person_id)
    {
         return DB::selectOne("/*$this->api_req_id*/ select email_id,mobile_num from persons where id = ?",[$person_id],$this->country_code);
    }

     public function get_contact_rel_mgr($person_id)
    {
         return DB::selectOne("/*$this->api_req_id*/ select email_id,mobile_num from persons where id = ?",[$person_id]);
    }
    /*public function assign_list($person_id)
    {
           $persons = parent::get_record_by('id',$person_id,['email_id','mobile_num'],$this->country_code);
           return $persons;
    }*/
    public function get_person_id($user_id)
    {
        // return DB::select("/*$this->api_req_id*/ select person_id from app_users where id = ? and country_code = ?",[$user_id, $this->country_code]);
       return DB::table('app_users')
                        ->select('person_id')
                        ->where('id',$user_id)
                        ->whereIn('country_code',[$this->country_code, "*"])
                        ->first()->person_id;
    }

    public function get_person_by_user_id($id){
      return DB::selectOne("/*$this->api_req_id*/ select id, first_name, last_name, mobile_num from persons where id in (select person_id from app_users where id = ?)",[$id]);
    }

    public function get_user_mobile_num($dp_code = 'XXX'){
        $person = $this->get_person_by_user_id(session('user_id'));

        if($person && $person->mobile_num){
            return $person->mobile_num;
        }else{
            return config('app.customer_success_mobile')[$dp_code];
        }

    }


    public function get_person_name($person_id,$fields = ['first_name','middle_name','last_name','mobile_num'])
    {
        $fields = implode(", ",$fields);
      return DB::selectOne("/*$this->api_req_id*/ select {$fields} from persons where id = ? and country_code =? limit 1",[$person_id, $this->country_code]);
    }

    public function get_person_id_by_mobile_num($mob_num){
      $persons =  DB::select("select id from persons where mobile_num = ?",[$mob_num]);
      $person_id = (empty($persons) || sizeof($persons) > 1) ? null : $persons[0]->id;
      return $person_id;
    }

    public function get_person_id_by_alt_mobile_num($mobile_num){
        $persons = DB::select("select id from persons where alt_biz_mobile_num_1 = '$mobile_num' or alt_biz_mobile_num_2 = '$mobile_num'");
        $person_id = (empty($persons) || sizeof($persons) > 1) ? null : $persons[0]->id;
        return $person_id;
    }


    public function get_op_mgr($cust_id){ #TODO get op mgr by customer id later
      return config('app.operations_manager')[session('acc_prvdr_code')];
      #return config('app.operations_manager')['CCA'];
    }

    public function is_new_user($person_id){
      $app_user =  DB::selectOne("select is_new_user from app_users where person_id = ?",[$person_id]);
      return $app_user->is_new_user;
    }
    public function get_handlers($field_value,$fields_arr){
      $field_names = ['associated_with','associated_entity_code'];
      $field_values = ['borrower' ,$field_value];
      return parent::get_records_by_many($field_names,$field_values,$fields_arr);
    }


     public function list($data, $fields_arr = ['*'])
     {
         return parent::get_records_by_many(array_keys($data), array_values($data), $fields_arr);
     }

     public function delete_contact_people($cust_id)
     {
         $model = $this->model();
		 $table_name = $model::TABLE;
         return DB::table($table_name)->where(['associated_with' => 'borrower', 'associated_entity_code' => $cust_id, 'country_code' => session('country_code')])->delete();
     }
     
     public function full_name_by_sql($person_id){
      $person =  DB::selectOne("/*$this->api_req_id*/ select first_name,middle_name,last_name,mobile_num from persons where id = ? ",[$person_id]);
      return full_name($person);
     }

     public function get_rm_by_contact($mobile_num){

        $person_details = DB::selectOne("select p.id,p.first_name,p.last_name,p.middle_name,a.country_code from persons p ,app_users a where  p.id = a.person_id and  a.role_codes = 'relationship_manager'and a.status = 'enabled' and p.mobile_num = ? and p.country_code = ?",[$mobile_num,session('country_code')]);

        return  $person_details;
     }

    public function get_email_n_msgr_token($person_id){
			$app_user = AppUser::where('person_id', $person_id)->get(['messenger_token', 'email']);
			$messenger_token = $app_user->pluck("messenger_token")[0];
			$email = $app_user->pluck("email")[0];
			return [$email, $messenger_token];
		}

 }
