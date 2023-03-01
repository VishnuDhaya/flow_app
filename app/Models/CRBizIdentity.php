<?php

namespace App\Models;
use App\Models\Model;

class CRBizIdentity extends Model
{
     
     public function model(){
        return self::class;
    }
     public static function rules($json_key){
        $required = Model::is_required($json_key);
        $default_rules = [
                'gps' => "$required",
                'mobile_num' => "regex:/^[^0][0-9]{8}$/|$required|digits:9",
                'alt_biz_mobile_num_1' => "regex:/^[^0][0-9]{8}$/|$required|digits:9",
                'photo_shop' => "$required",
                'photo_biz_lic' => "$required",
            ];


            
    		  return $default_rules;
    	    
    }
     /**
     * Custom message for validation
     *
     * @return array
     */
    public static function messages($json_key)
    {
        return [
            'photo_shop.required' =>  'Please upload the Establishment photo',
            'photo_biz_lic.required' =>  'Please upload the Bussiness license photo',
            'gps.required' => 'GPS is a required field',
            'mobile_num.required' => 'Mobile number is a required field',
            'mobile_num.max' => 'Mobile number should contain maximum of 10 characters',
            'mobile_num.min' => 'Mobile number should contain minimum of 10 characters',
            'alt_biz_mobile_num_1.required' => 'Alternate biz mobile number 1 is a required field',
            'alt_biz_mobile_num_1.max' => 'Alternate biz mobile number 1 should contain maximum of 10 characters',
            'alt_biz_mobile_num_1.min' => 'Alternate biz mobile number 1 should contain minimum of 10 characters',

            
        ];
    }
}
