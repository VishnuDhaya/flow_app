<?php

namespace App\Models;
use App\Models\Model;
use Log;
use Illuminate\Support\Facades\DB;


class CSModelWeightages extends Model
{
	
	const INSERTABLE = ["country_code","cs_model_code","csf_type","new_cust_weightage","repeat_cust_weightage"];
	const TABLE = "cs_model_weightages";


	 public static function rules($json_key)
    {
        $required = Model::is_required($json_key);

        $default_rules = [
                'cs_model_code' => "$required",
                'csf_type' => "$required"
                
                  ];


    	if ($json_key == "cs_model_weightage"){       	     
            return $default_rules; 
        }
        else{                       
    		return $default_rules;   		
    	}
    }
     /**
     * Custom message for validation
     *
     * @return array
     */
    public static function messages($json_key)
    {
        return [
           
        ];
    }

    public function model(){
        return CSModelWeightages::class;
    }

    /*public function list_cs_weightages(array $data){
    	
    	return $this->get_records_by("cs_model_code", $data['cs_model_code'],['csf_type', 'new_cust_weightage', 'repeat_cust_weightage'], $data['country_code']);
    }

    public function update_cs_weightages(array $cs_weightages){
    	
    	
    	foreach ($cs_weightages as $key => $value) {
    		$split_key = explode("_", $key);
    		$last_str = end($split_key);
    		$remove_last_el = array_pop($split_key);	
    		$field_str = implode("_", $split_key);
    		
    		$field_name = "csf_type";
    		$field_value = $field_str;
    		$update_col_val = $value;

    		if($last_str == "new"){
    			$update_col_name = "new_cust_weightage";
    			
    		}else if($last_str == "repeat"){
    			$update_col_name = "repeat_cust_weightage";
    		}   		

    		DB::table('cs_model_weightages')
    					->where($field_name , $field_value)
    					->update([$update_col_name => $update_col_val]);

    	
    	}

    }

    public function create(array $data){
    	if(array_key_exists('csf_type', $data) && $data['csf_type'] != null){
    		return $this->insert_model($data);
    	}else{
    		thrw("Factor is a required field");
    	}
    	
    }*/
}