<?php

namespace App\Models;
use App\Models\Model;
//use Illuminate\Database\Eloquent\Model;


class Org extends Model
{
    
    const UPDATABLE = ['name','inc_name' ,'inc_number', 'inc_date','tax_id'];
    const INSERTABLE = ['name','inc_name' ,'inc_number', 'inc_date','tax_id', 'reg_address_id','country_code'];
    const TABLE = "orgs";
     public static function rules($json_key)
	
    {

        $required = parent::is_required($json_key);

        $default_rules =  [

                'name' => "$required|regex:/^[0-9\.,\pL\s]+$/u|max:50",
                'inc_name' => "nullable|regex:/^[0-9\.,\pL\s]+$/u|max:50",
                'inc_number' => "nullable|regex:/^[0-9\pL\s]+$/u|max:20",
                'inc_date' => "nullable|before:today",
                'tax_id' => "nullable|regex:/^[0-9\pL\s]+$/u|max:20"
                //'name' => 'required|max:100|regex:/^[\pL\s\-]+$/u',
                //'inc_name' => 'max:100|regex:/^[ A-Za-z,.]*$/',
                //'inc_date' => 'date_format:Y-m-d|before:today',
             
            ];

    	if ($json_key =="org_update"){

	       return $default_rules;
    	
        }elseif($json_key == "org_create"){
    	   
           
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
           /* 'name.required' => 'Name is a required field',
            'name.max' => 'Name should contain maximum of 100 characters',
            'name.regex' => 'Name should not contain special characters',
            'inc_name.max' => 'Inc name should contain maximum of 100 characters',
            'inc_name.regex' => 'Inc name should not contain special characters',*/
            //'inc_date.date_format' => 'The inc date must be a date before today' 
        ];
    }
}
