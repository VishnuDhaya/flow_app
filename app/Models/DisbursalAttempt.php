<?php

namespace App\Models;
use App\Models\Model;

class DisbursalAttempt extends Model
{

    const UPDATABLE = ['status','flow_request','flow_response','partner_request','partner_response','partner_combined_response'];
    const INSERTABLE = ['loan_doc_id', 'status', 'flow_request', 'flow_response', 'partner_request', 'partner_response','partner_combined_response', 'country_code', 'cust_id'];
    const TABLE = "disbursal_attempts";
}
