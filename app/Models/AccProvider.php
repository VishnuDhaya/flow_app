<?php

namespace App\Models;
use App\Models\Model;
//use Illuminate\Database\Eloquent\Model;

class AccProvider extends Model
{
    const CODE_NAME = "acc_prvdr_code";
    const TABLE = "acc_providers";
    const INSERTABLE = ["name", "country_code", "acc_prvdr_code", "acc_provider_logo", "org_id"];
    const UPDATABLE =  ["name", "acc_provider_logo"];

    const JSON_FIELDS = ["mobile_cred_format"];

     public static function rules($json_key)
    {
        $required = parent::is_required($json_key);
        $default_rules = [
        'name' => "$required",
        "acc_prvdr_code" => "$required"
        ];
        return $default_rules;
}
}