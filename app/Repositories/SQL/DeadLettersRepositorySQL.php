<?php
namespace App\Repositories\SQL;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Log;
use DB;

class DeadLettersRepositorySQL 
{
    public function model(){
        return DeadLetters::class;
    }

    public function insert(array $dead_letter){
        Log::warning($dead_letter);
        return DB::insert('insert into dead_letters(acc_pvdr_code,country_code,notify_json)
        values(?,?,?)',array_values($dead_letter)); 
    }
}