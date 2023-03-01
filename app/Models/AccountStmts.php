<?php

namespace App\Models;
use App\Models\Model;
//use Illuminate\Database\Eloquent\Model;

class AccountStmts extends Model
{
     //$updatable = ["name","lender_type","status"];
    const TABLE = "account_stmts";

    const CODE_NAME = "stmt_txn_id";

    
    const INSERTABLE = ['photo_statement_proof', 'reason_for_add_txn','account_id', 'acc_prvdr_code', 'network_prvdr_code', 'ref_account_num', 'acc_number', 'stmt_txn_date', 'stmt_txn_type', 'descr', 'amount', 'cr_amt', 'dr_amt', 'balance', 'stmt_txn_id', 'country_code', 'import_id', 'created_at', 'created_by', 'sms_log_id', 'sms_import_status', 'sms_content', 'source', 'recon_status'];

    const UPDATABLE =  ['acc_txn_type', 'loan_doc_id','recon_status','stmt_txn_id', 'recon_desc', 'review_reason', 'recon_status', 'sms_import_status', 'source'];

    const JSON_FIELDS = ['sms_content'];

    public function model(){        
            return AccountStmts::class;
    }

    
}
