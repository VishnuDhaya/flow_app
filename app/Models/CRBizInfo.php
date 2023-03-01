<?php

namespace App\Models;
use App\Models\Model;
//use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Rules\CCACustIDRule;
use App\Rules\UEZMCustIDRule;

class CRBizInfo extends Model
{
    


    public static function rules($json_key)
    {
        $required = parent::is_required($json_key);
        
        $default_rules = [
                'dp_rel_mgr_id' => "$required",
                'biz_name' => "$required",
                'biz_addr_prop_type' => "$required",
                'ownership' => "$required",
                'business_distance' => "$required",
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
            'biz_type.$required' => 'Business type $required!',
            'ownership' => 'Ownership is $required!',
        ];
        

        }
    
}
