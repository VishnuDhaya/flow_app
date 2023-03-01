<?php
namespace App\Repositories\SQL;

use Illuminate\Support\Facades\DB;
use App\Models\AccountStmts;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class AccountStmtRepositorySQL  extends BaseRepositorySQL 
{

	public function __construct(){
            parent::__construct();
            $this->class = AccountStmts::class;

    }
        
    public function model(){        
        return $this->class;
    }

    public function get_acc_stmt_txns($data)
    {
        $addl_sql = "";

        if(array_key_exists('recon_status', $data)){
          if($data['recon_status'] == 'recon_not_done'){
            $addl_sql = " and recon_status != '80_recon_done'";
            unset($data['recon_status']);
          }else if($data['recon_status'] == "cant_recon"){
            $addl_sql = " and recon_status not in ('10_capture_payment_pending','80_recon_done') ";
            unset($data['recon_status']);
          }else if($data['recon_status'] == 'review_pending_payments'){
            $recon_start_date = config('app.recon_scr_strt_date');
            $addl_sql = " and date(stmt_txn_date) >= '$recon_start_date' and recon_status = '10_capture_payment_pending' and review_reason is not null";
            unset($data['recon_status']);
          }else if($data['recon_status'] == 'unmatched_credits' && isset($data['period']) == 'before_july'){
            $recon_start_date = config('app.recon_scr_strt_date'); #'2022-06-01';
            $addl_sql = " and date(stmt_txn_date) >= '$recon_start_date' and date(stmt_txn_date) <= '2022-07-31' and recon_status not in ('10_capture_payment_pending', '80_recon_done', '60_non_fa_credit')";
            unset($data['recon_status']);
            unset($data['period']);

          }else if($data['recon_status'] == 'unmatched_debits' && isset($data['period'])== 'before_july'){
            $recon_start_date = config('app.recon_scr_strt_date'); #'2022-06-01';
            $addl_sql = " and date(stmt_txn_date) >= '$recon_start_date' and date(stmt_txn_date) <= '2022-07-31' and recon_status != '80_recon_done'";
            unset($data['recon_status']); 
            unset($data['period']);    
          }else if($data['recon_status'] == 'unmatched_credits'){
            $addl_sql = " and recon_status not in ('10_capture_payment_pending', '80_recon_done')";
            unset($data['recon_status']);
          }else if($data['recon_status'] == 'unmatched_debits'){
            $addl_sql = " and recon_status != '80_recon_done'";
            unset($data['recon_status']);
          }
         
        }
        $addl_sql = $addl_sql." order by stmt_txn_date desc";
        $results = $this->get_records_by_many(array_keys($data), array_values($data), ['account_id', 'network_prvdr_code', 'acc_prvdr_code', 'cust_id', 'acc_number', 'updated_at', 'stmt_txn_type', 'cr_amt', 'dr_amt', 'balance', 'stmt_txn_id', 'descr', 'stmt_txn_date', 'recon_desc', 'loan_doc_id', 'recon_status', 'review_reason', 'country_code'],  " and ", $addl_sql);

        #unset($data['start_date']);
        #unset($data['end_date']);
        return $results;
             
  	}

    
}


