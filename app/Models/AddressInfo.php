<?php

namespace App\Models;
use App\Models\Model;

class AddressInfo extends Model
{
    const TABLE = "address_info";
    const INSERTABLE = ["country_code", "field_1", "field_2", "field_3", "field_4","field_5","field_6","field_7","field_8", "field_9","field_10"];
    const UPDATABLE = ["field_1", "field_2", "field_3", "field_4","field_5","field_6","field_7","field_8", "field_9","field_10"];
    public static function rules($json_key, $country_code)
    
    {

       $required = parent::is_required($json_key);
       if($country_code == "UGA"){
       $default_rules = [
                'country_code' => "$required",
                "region" => "$required",
                "district" => "$required",
                'county' => "$required",
                'sub_county' => "$required|min:3|max:32",
                'location' => "$required|min:3|max:32",
                'landmark' => "$required|min:3|max:126",
                'village' => "max:32",
                'parish' => "max:32"

            ];
        }else if($country_code == "RWA"){
            $default_rules = [
                'country_code' => "$required",
                "province" => "$required",
                "sector" => "$required",
                "village" => "max:32",
                "cell" => "max:32"
                
            ];
        } else {
            $default_rules = [];
        }
    
            return $default_rules;
    }    
}

