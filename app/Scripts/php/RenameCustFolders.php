<?php
namespace App\Scripts\php;
use Illuminate\Support\Facades\DB;
use File;

class RenameCustFolders{

    public static function rename(){
        $custs = DB::select("select cust_id, old_cust_id from borrowers where cust_id != old_cust_id");
        $path = env('FLOW_STORAGE_PATH').'/files/UGA/borrowers/';
        foreach($custs as $cust){
            if(File::exists($path.$cust->old_cust_id)){
                rename($path.$cust->old_cust_id, $path.$cust->cust_id);
            }
        }
    }



}

