<?php

namespace App\Models;
use App\Models\Model;

class Vendor extends Model
{

    const INSERTABLE = ['country_code','type', 'vendor_code', 'status', 'credentials', 'balance', 'created_at', 'created_by'];
    const UPDATABLE = ['updated_at', 'updated_by', 'balance'];

    const JSON_FIELDS = ["credentials"];
    const TABLE = "vendors";

    public function model(){
        return Vendor::class;
	}
}

?>