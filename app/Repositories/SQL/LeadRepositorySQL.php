<?php
 
namespace App\Repositories\SQL;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Support\Facades\Log;
use App\Models\Lead;
use App\Consts;
use Exception;
use Carbon\Carbon;

class LeadRepositorySQL extends BaseRepositorySQL{
	public function __construct()
    {
      parent::__construct();
      $this->class = Lead::class;
    }
	
	public function model(){
			
			return $this->class;
	}


	public function get_cust_reg_arr($lead_id){
        $lead_data = $this->find($lead_id,['cust_reg_json']); 
       	return json_decode($lead_data->cust_reg_json,true);
	}

    public function get_file_json($lead_id){
        $lead_data = $this->find($lead_id,['file_json']); 
       	return json_decode($lead_data->file_json,true);
	}

	public function update_cust_reg_json($cust_reg_json,$lead_id,$addl_fields_arr =null){
		$cust_reg_json = json_encode($cust_reg_json);
		$update_arr = ['cust_reg_json' => $cust_reg_json,"id" => $lead_id];
		if($addl_fields_arr){
			$update_arr = array_merge($addl_fields_arr,$update_arr);
		}
		return $this->update_model($update_arr);
	}
	public function list(array $data){
	    throw new BadMethodCallException();
		
	}

    public function searchterm($term){
        $result = db::select("select id,biz_name,created_at,tf_status,account_num, profile_status from leads where acc_prvdr_code = ? and JSON_CONTAINS(acc_purpose, JSON_ARRAY('terminal_financing')) and (biz_name like '{$term}' or account_num like '{$term}') ", [session('acc_prvdr_code')]);
        return $result;
    }

    public function tf_update_dup_check($data,$crnt_id){
        $check = db::select("select id from leads where json_contains(update_data_json,'{\"transfer\" : {\"txn_id\" : \"$data\"}}')");
        Log::warning("checkingg");
        Log::warning($check);
        if($check) {
            if ($crnt_id == $check[0]->id) {
                return null;
            }
        }
        else{
            return $check;
        }
    }


	public function delete($id){
//		throw new BadMethodCallException();
        db::delete("delete from leads where id = {$id}");
	}


    public function update_lead_json($checklist_json, $lead_id){
		$biz_name = $checklist_json['biz_name'];
		$ap_code = $checklist_json['acc_prvdr_code'];
		$account_num = $checklist_json['acc_number'];
		return DB::update("update leads set lead_json = JSON_MERGE_PATCH(`lead_json`, '{\"biz_name\" : \"$biz_name\", \"acc_prvdr_code\" : \"$ap_code\", \"account_num\" : \"$account_num\" }') where id = ?", [$lead_id]);
	}
	public function get_lead_id_from_mobile_num($mobile_num){  
		$lead_data = DB::select("select id from leads where json_contains(cust_reg_json->'$.biz_identity.*.value','\"{$mobile_num}\"') and profile_status != 'closed'");                       
		return empty($lead_data ) ? null : (sizeof($lead_data ) > 1 ? $lead_data  : $lead_data[0]->id);
	}

    public function get_rekyc_lead_cust_id($lead_id){
        $lead = $this->find($lead_id, ['type', 'cust_id']);
        if($lead && $lead->type == 're_kyc') {
            return $lead->cust_id;
        }
    }

    public function get_cust_id_from_lead($lead_id){
        $lead = $this->find($lead_id, ['cust_id']);
        if($lead){
            return $lead->cust_id;
        }
    }

    public function get_file_data_consent($lead_id){
        $result =   DB::selectOne("select substring_index(substring_index(consent_json,'/signed_consent/',-1),'\"',1) file_data_consent from leads where id = ?", [$lead_id]);
        return $result->file_data_consent;
    }
	
} 
