<?php

namespace App\Models;
use App\Models\Model;
use Illuminate\Support\Facades\Log;

class Person extends Model
{
     const TABLE = "persons";

     const UPDATABLE = ["addl_mob_num", "first_name", "middle_name", "last_name", "initials", "dob" ,"gender" ,"whatsapp","email_id", "mobile_num" , "phone_num", "designation","associated_entity_code","national_id","photo_pps","photo_national_id","photo_national_id_back", "photo_selfie", "nationality","alt_biz_mobile_num_1","alt_biz_mobile_num_2", "verified_mobile_num", "verified_alt_biz_mobile_num_1", "verified_alt_biz_mobile_num_2", "id_type"];

     const INSERTABLE = ["country_code","first_name", "middle_name", "last_name", "initials", "dob" ,"gender" ,"whatsapp","email_id", "mobile_num" , "phone_num", "designation", "associated_with","associated_entity_code","address_id","id_type","national_id","photo_pps","photo_national_id","photo_national_id_back","photo_selfie", "nationality","alt_biz_mobile_num_1","alt_biz_mobile_num_2","relation_with_owner","handling_biz_since",'national_id_exp_date'];

     const CODE_NAME = 'id';
     public function model(){
        return self::class;
    }
     public static function rules($json_key){
        $required = Model::is_required($json_key);
        $default_rules = [
               
                'first_name' => "$required|min:3|max:20|regex:/^[\'\pL\s\-]+$/u",
                'middle_name' => "nullable|min:3|max:20|regex:/^[\'\pL\s\-]+$/u",
                'last_name' => "$required|max:20|regex:/^[\'\pL\s\-]+$/u",
                'initials' => 'max:5',
                'dob' => "date_format:Y-m-d|before:today|before:today", 
                //'whatsapp' => "regex:/^[^0][0-9]{8}$/",
                'mobile_num' => "regex:/^[^0][0-9]{8}$/|$required|digits:9",
                'alt_biz_mobile_num_1' => "regex:/^[^0][0-9]{8}$/|$required|digits:9",
                'phone_num' => "regex:/^[0-9]+$/",
                //'national_id' => "$required|regex:/^[a-zA-Z0-9]*([a-zA-Z][0-9]|[0-9][a-zA-Z])[a-zA-Z0-9]+$/",
                'gender' => "$required",
                'email_id' => "email|nullable"

            ];
            if(session('country_code') == 'UGA') {
                $default_rules['national_id'] = "$required|regex:/^[a-zA-Z0-9]+$/|starts_with:C,P";
            }
            elseif(session('country_code') == 'RWA') {
                $default_rules['national_id'] = "$required|regex:/^[0-9]+$/|size:16";
            }
            if($json_key == "person_create_person"){
                // $default_rules['email_id'] = ["email", "required", "regex:/((.+)@flowglobal\.net)|((.+)@inbox\.flowglobal\.net)/i"];
                $default_rules['email_id'] = ["email", "required"];
                return $default_rules;
            }else if ($json_key == "owner_person_reg_cust" ){
                $default_rules['dob'] = "$required|date_format:Y-m-d|before:18 years ago";
                return $default_rules;
            } 
            if ($json_key == "cr_owner_person_reg_cust" || $json_key == "cr_owner_person_submit_kyc"){
                $default_rules['dob'] = "$required|date_format:Y-m-d|before:18 years ago";
                $default_rules['photo_national_id'] = "$required";
                $default_rules['photo_national_id_back'] = "$required";
                $default_rules['photo_selfie'] = "$required";
                $default_rules['photo_pps'] = "$required";
                unset($default_rules['mobile_num'],$default_rules['alt_biz_mobile_num_1']);
                return $default_rules;
            }
           

            if($json_key == "cr_addl_num_submit_kyc"){
               
                $default_rules = [];
                $default_rules['name'] = "$required|min:3|max:20|regex:/^[\'\pL\s\-]+$/u";
                $default_rules['mobile_num'] = "regex:/^[^0][0-9]{8}$/|$required|digits:9";
                $default_rules['serv_prvdr'] = "$required";
                $default_rules['relation'] = "$required";
                return $default_rules;


            }
            if($json_key == "contact_persons_reg_cust"){
                $default_rules['dob'] = "$required|date_format:Y-m-d|before:18 years ago"; 
                return $default_rules;
        
            }else if ($json_key =="relationship_manager_create"){
                $default_rules['dob'] = "$required|date_format:Y-m-d|before:today";
                $rules = array_merge($default_rules, 
                        ['email_id' => "$required|email|max:50"]);
                        //'designation' =>"$required|max:40"]);
                return $rules;
    	
            }  
            else if($json_key == 'owner_person_extract_text_details_from_card'){
                $default_rules['dob'] = "$required|date_format:Y-m-d|before:18 years ago";
                $default_rules['national_id'] =  "$required";
                unset($default_rules['mobile_num'],$default_rules['alt_biz_mobile_num_1']);
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
            'first_name.required' => 'First name is a required field',
            'first_name.max' => 'First name should contain maximum of 20 characters',
            'first_name.regex' => 'First name should not contain special characters',
            'last_name.required' => 'Last name is a required field',
            'last_name.max' => 'Last name should contain maximum of 20 characters',
            'last_name.regex' => 'Last name should not contain special characters',
            'dob.required' => 'Date of Birth is a required field',
            'dob.date_format' => 'Date of Birth is not valid',
            'whatsapp.max' => 'Whatsapp should contain maximum of 10 characters',
            'whatsapp.min' => 'Whatsapp should contain minimum of 10 characters',
            'email_id.required' => 'Email is a required field',
            'email_id.email' => 'Enter valid email address',
            'mobile_num.required' => 'Mobile number is a required field',
            'mobile_num.max' => 'Mobile number should contain maximum of 10 characters',
            'mobile_num.min' => 'Mobile number should contain minimum of 10 characters',
            'alt_biz_mobile_num_1.required' => 'Alternate biz mobile number 1 is a required field',
            'alt_biz_mobile_num_1.max' => 'Alternate biz mobile number 1 should contain maximum of 10 characters',
            'alt_biz_mobile_num_1.min' => 'Alternate biz mobile number 1 should contain minimum of 10 characters',
            'designation.required' => 'Designation is a required field',
            'phone_num.regex' => 'Landline format is invalid',
            'photo_national_id' => 'Please upload the National ID photo',
            'photo_national_id_back' => 'Please upload the backside of National ID photo ',
            'photo_selfie' => 'Please upload the selfie photo',
            'photo_pps' => 'please upload the passport size photo'

            
        ];
    }
}
