<?php

namespace App\Scripts\php;

use Illuminate\Http\Request;
use DB;
use Excel;
use Illuminate\Support\Facades\Log;

class AddressCoordinateScript{


    public function run(){
        $file_path = base_path()."/storage/data/Flow Uganda - Customer Locations.csv";
        $data = Excel::toArray([],$file_path);
        $records = $data[0];
        array_shift($records);
        try {
            DB::beginTransaction();
            foreach ($records as $record) {
                $dp_ids = explode(',', $record[4]);
                foreach ($dp_ids as $dp_id) {
                    $addr_id = $this->check_for_match($dp_id, $record[2]);
                    if ($addr_id) {$this->set_gps($addr_id, $dp_id, [$record[1], $record[0]]);}
                }
            }
            DB::commit();
        }
        catch(\Exception $e){
            DB::rollback();
            thrw($e);
        }
    }

    private function check_for_match($dp_id, $biz_name)
    {

        $addr_id = null;
        if($dp_id == 0){
            return null;
        }
        $borrowers = DB::select("select biz_address_id from borrowers where data_prvdr_cust_id = '$dp_id'");
        if(sizeof($borrowers) == 1){
            $addr_id = $borrowers[0]->biz_address_id;
        }
        elseif(sizeof($borrowers) == 0){
            Log::warning("No matching record found for ".$biz_name);
        }
        elseif(sizeof($borrowers) > 1){
            Log::warning("Multiple records found for ".$biz_name);
        }
        return $addr_id;
    }

    private function set_gps($addr_id, $dp_id, $coordinates_arr)
    {
        $coordinates_str = implode(",",$coordinates_arr);
        DB::update("update address_info set field_10 = '$coordinates_str' where id = $addr_id");
        DB::update("update borrowers set gps = '$coordinates_str' where data_prvdr_cust_id = '$dp_id'");
    }

}