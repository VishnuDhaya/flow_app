<?php

namespace App\Scripts\php;
use DB;
use Log;
use Illuminate\Support\Facades\DB as FacadesDB;

class UpdateCustAddressScript{

    public function update_address(){
        $records = DB::select("select field_8,field_10,id from address_info where field_8 is not null or field_10 is not null");
        foreach($records as $record){
            $id = $record->id;
            $gps = $record->field_10;
            $location = $record->field_8;
            $gps = $gps ? "'$gps'" : 'null';
            $location = (!$location || $location == 'NA') ? 'null' : "'$location'";                        
            DB::update("update borrowers set gps = $gps,location = $location where biz_address_id = $id");
        }

    }
}