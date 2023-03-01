<?php

namespace App\Models;
//use \Illuminate\Database\Eloquent\Model as EloqModel;
use App\Repositories\SQL\BaseRepositorySQL;
use Illuminate\Support\Str;

class Model extends BaseRepositorySQL {
	
	const TOUCH = true;
	const UPDATABLE = [];
	const INSERTABLE = [];
	const AUDITABLE = false;
    
    public static function  is_required($json_key){
		
        if(str_contains($json_key, "create")){
            return "required";
        }
        if(str_contains($json_key, "update")){
        	return "nullable";
        }
        
        return "required";
    }    

    /*public function table(){
        return Str::plural(Str::snake((new \ReflectionClass($this))->getShortName()));
    }  */ 
    public static function messages($json_key)
    {

        return[];

    }
}