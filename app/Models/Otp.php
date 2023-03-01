<?php

namespace App\Models;
use App\Models\Model;

class Otp extends Model
{

    const UPDATABLE = ['otp', 'otp_type', 'entity', 'entity_id', 'status','rcvd_msg'];
    const INSERTABLE = ['otp','lead_id', 'otp_type', 'country_code', 'entity', 'entity_id', 'mob_num', 'generate_time', 'expiry_time','status','cust_id', 'entity_verify_col', 'entity_update_value'];
    const TABLE = "otps";
}
