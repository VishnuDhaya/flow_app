<?php
namespace App\Services\Schedule;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Repositories\SQL\MasterDataRepositorySQL;
use Carbon\Carbon;
use App\Mail\FlowCustomMail;
use App\Repositories\SQL\LoanTransactionRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use Illuminate\Support\Facades\Storage;
use File;


use Consts;
use Illuminate\Support\Facades\DB;


class SystemScheduleService{

    public function send_location_list_email(){
        
        $last_sunday = new Carbon("last sunday");
       
        $end_date = $last_sunday->format(Consts::DB_DATE_FORMAT);
        $start_date = $last_sunday->subDays(7)->format(Consts::DB_DATE_FORMAT);
        $country_code = session('country_code');

        $locations = DB::select("select md.data_code, md.data_value,p.first_name as rm_name from master_data md, app_users a, persons p where md.created_by = a.id and a.person_id = p.id  and ( md.data_key = 'location') and p.country_code = ? and date(md.created_at) >= ? and date(md.created_at) <= ?", [$country_code, $start_date, $end_date]);
        
        if($locations){
            $last_sunday = new Carbon("last sunday");
            $end_date = $last_sunday->format(Consts::UI_DATE_FORMAT);
            $start_date = $last_sunday->subDays(7)->format(Consts::UI_DATE_FORMAT);
            $mail_data = compact('locations', 'start_date', 'end_date', 'country_code');
            Mail::to([get_ops_admin_email(), get_l3_email(), config('app.app_support_email')])->queue((new FlowCustomMail('updated_locations', $mail_data))->onQueue('emails'));
        }
    }

    public function update_task_status(){
        $country_code = session('country_code');

        DB::update("update tasks set status = ? where status = ? and country_code = ? ",[Consts::TASK_REJECTED, Consts::TASK_REQUESTED, $country_code]);
    }
    public function update_repeat_queue_status(){
        $country_code = session('country_code');

        DB::update("update fa_repeat_queue set status = ? where status = ? and country_code = ? ",[Consts::REPEAT_FA_REJECTED, Consts::REPEAT_FA_REQUESTED, $country_code]);
    }

    public function manual_capture_txns(){

        $country_code = session('country_code');
        $end_date = (new Carbon("last sunday"))->format(Consts::DB_DATE_FORMAT);
		$start_date = (new Carbon("last sunday"))->subDays(7)->format(Consts::DB_DATE_FORMAT);

        $loan_txns = DB::select("select created_at,country_code,photo_transaction_proof, loan_doc_id, amount, txn_type, txn_id, txn_mode, txn_exec_by, txn_date, reason_for_skip from loan_txns where reason_for_skip is not null and date(created_at) >= '$start_date' and date(created_at) <= '$end_date' and country_code = '$country_code'");

        foreach ($loan_txns as $loan_txn){

            $loan = (new LoanRepositorySQL)->find_by_code($loan_txn->loan_doc_id, ['biz_name', 'cust_name', 'acc_number']);
            
            if($loan_txn->txn_exec_by){
                $full_name = (new PersonRepositorySQL)->full_name($loan_txn->txn_exec_by);
                if($full_name){
                    $loan_txn->txn_exec_by =  $full_name;
                }
            }

            $loan_txn->biz_name = $loan->biz_name;
            $loan_txn->cust_name = $loan->cust_name;
            $loan_txn->acc_number = $loan->acc_number;
            $loan_txn->currency_code = (new CommonRepositorySQL())->get_currency()->currency_code;

            $photo_proof_arr = json_decode($loan_txn->photo_transaction_proof, true);
            
            if($loan_txn->txn_type == 'payment'){
                $photo_type = "photo_payment_proof";
            }else if($loan_txn->txn_type == 'disbursal'){
                $photo_type = "photo_disbursal_proof";
            }else if($loan_txn->txn_type == 'excess_reversal'){
                $photo_type = "photo_reversal_proof";
            }
            
            $photo_proof = $photo_proof_arr[$photo_type];
            $photo_proof_path = get_file_path("loan_txns", $loan_txn->txn_id,  $photo_type); 
            $full_path = $photo_proof_path.'/'.$photo_proof;
                        
            if(File::exists(Storage::path($full_path))){
                $loan_txn->full_path = $full_path;
            }

        }

        $mail_data = compact('loan_txns', 'start_date', 'end_date', 'country_code');

        send_email('manual_capture_txns',[get_l3_email(),get_market_admin_email(), get_ops_admin_email()], $mail_data, true);
    }

}