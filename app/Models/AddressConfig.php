<?php

namespace App\Models;
use App\Models\Model;

class AddressConfig extends Model
{
    const TABLE = "addr_config";

    const CODE_NAME = "id";

    public function model(){
        return self::class;
    }
}