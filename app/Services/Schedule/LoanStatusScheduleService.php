<?php

namespace App\Services\Schedule;

use App\Services\LoanService;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\LoanTransactionRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Repositories\SQL\ProbationPeriodRepositorySQL;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Services\BorrowerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Consts;
use Carbon\CarbonPeriod;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use App\Exceptions\FlowCustomException;

class LoanStatusScheduleService {
    public function __construct($country_code, $time_zone){
       
        $this->country_code = $country_code;
        $this->time_zone = $time_zone;
        
        
    }

    public function __invoke(){
        print_r("calling..");
        session()->put('country_code', $this->country_code);
        setPHPTimeZone($this->time_zone);
        session()->put('user_id', 0);
       
        
        $this->process_ongoing_loans();
        $this->process_due_loans();
    }

public function process_ongoing_loans()
{

    $loan_repo = new LoanRepositorySQL();
    // $loan_event_repo = new LoanEventRepositorySQL();
    $result="";
    Log::warning("Executing process_ongoing_loans...");
    try
    {
        DB::beginTransaction();
        $today = Carbon::now()->endOfDay();//->format('Y-m-d'); //." "."23:59"
        //$today = Carbon::now()->toDateString();
        $field_names = ["due_date","status"];
        $field_values = [$today, Consts::LOAN_ONGOING];

        $loans = $loan_repo->get_records_by_many($field_names,$field_values, ['id','loan_doc_id']);
        
        foreach ($loans as $loan) {
            $loan_id = $loan->id;
            $loan_doc_id = $loan->loan_doc_id;
            $update_loan = $loan_repo->update_record_status(Consts::LOAN_DUE,$loan_id);
            // $result = $loan_event_repo->create_event($loan_doc_id, Consts::LOAN_DUE);

        }
        DB::commit();
    }

    catch (\Exception $e) {

        DB::rollback();
        if ($e instanceof QueryException){
                    throw $e;
                }else{
                thrw($e->getMessage());
                }
    }
    return $result;
    }

 
public function process_due_loans()
{

    $loan_repo = new LoanRepositorySQL();
    // $loan_event_repo = new LoanEventRepositorySQL();
    $loan_txn_repo = new LoanTransactionRepositorySQL();
    $loan_product_repo = new LoanProductRepositorySQL();
    $brwr_repo = new BorrowerRepositorySQL();
    $result="";

    try
    {
        DB::beginTransaction();
        $yesterday = Carbon::now()->yesterday()->endOfDay()->format(Consts::DB_DATETIME_FORMAT); //." "."23:59"

        $field_names = ["country_code"];
        $field_values = [session('country_code')];
        $due = Consts::LOAN_DUE;
        $overdue = Consts::LOAN_OVERDUE;
        $ongoing = Consts::LOAN_ONGOING;
        $loans = $loan_repo->get_records_by_many($field_names,$field_values, 
            ['id', 'current_os_amount','provisional_penalty', 'product_id', 'loan_doc_id', 'cust_id', 'due_date', 'status'], "" , " and due_date <= '$yesterday' and status in ('$due' , '$overdue', '$ongoing')");

        foreach ($loans as $loan) {
            $loan_id = $loan->id;
            $loan_doc_id = $loan->loan_doc_id;
            $penalty_days = getPenaltyDate($loan->due_date);
            $overdue_days = Carbon::parse($loan->due_date)->startOfDay()->diffInDays(Carbon::today());
            if($overdue_days > 0){
                $loan->overdue_days = $overdue_days;
                $loan->penalty_days = $penalty_days;
            }

            if($loan->status != Consts::LOAN_OVERDUE){

                if($overdue_days > 0){
                    $loan_serv = new LoanService();
                    $loan_serv->process_overdue($loan);
                }
            }
            $provisional_penalty = $loan_product_repo->get_penalty_amount($loan->product_id);
            
            //$penalty_amount = $penalty_amount->penalty_amount;
            if($provisional_penalty > 0){
                
                $loan_updt_data = $this->get_loan_upd_data($loan, $provisional_penalty);
                $loan_repo->update_model_by_code($loan_updt_data);
                
                
                /*$today = Carbon::now()->toDateString();
                $period = CarbonPeriod::create($loan->due_date, $today);
                $dates = $period->toArray();
                array_shift($dates); # To remove the due date
                
                foreach ($dates as $date){
                    Log::warning("Checking $date");
                    if(!isHoliday($date)){
                        Log::warning("Adding $date");
                        $added_penalty = DB::select("select id, created_at from loan_events where loan_doc_id = ? and event_type = ? and date(created_at) = ?", [$loan_doc_id, "provisional_penalty", $date]);
                        if(!$added_penalty){
                            $loan_txn = $this->get_prov_penalty_txn_data($provisional_penalty, $loan_doc_id, $date);

                            $loan_txn_repo->create($loan_txn, true , null, "{\"provisional_penalty\": $provisional_penalty}");
                        }    
                    }
                }*/

            }
        }
        DB::commit();
    }

    catch (\Exception $e) {

        DB::rollback();
        if ($e instanceof QueryException){
            throw $e;
        }else{
            thrw($e->getMessage());
        }

    }
    return $result;
    }

    private function get_loan_upd_data($loan, $provisional_penalty){
        //  $data = ['current_os_amount' => $loan->current_os_amount + $penalty_amount,
         //           'penalty_amount' =>  $loan->penalty_amount + $penalty_amount,
         //           'loan_doc_id' => $loan->loan_doc_id
         //           ];
       return ['provisional_penalty' => $provisional_penalty,
                     'loan_doc_id' => $loan->loan_doc_id,
                    'overdue_days' => $loan->overdue_days,
                    'penalty_days' => $loan->penalty_days];
          
    }
    private function get_prov_penalty_txn_data($penalty_amount, $loan_doc_id, $date){
        return  [

                            "txn_type" => "provisional_penalty",
                            "amount" => $penalty_amount,
                            "txn_exec_by" => "system",
                            "txn_date" => $date,
                            "loan_doc_id" => $loan_doc_id
                        ];


    }

    public function process_long_overdue_loans(){
        
       // DB::update("update borrowers set category = 'Probation', prob_fas = 5 where cust_id in (select cust_id from loans where DATEDIFF(CURDATE(), due_date) > 30 and status = 'overdue') and (prob_fas = 0 or prob_fas is null)");
       $condonation_overdue_days = config('app.auto_condonation_overdue_days');
       $borrowers = DB::select("select cust_id from loans where DATEDIFF(CURDATE(), due_date) > ? and status = 'overdue'",[$condonation_overdue_days]);

       foreach($borrowers as $borrower){

            $prob_period_repo = new ProbationPeriodRepositorySQL();
            $cust_prob = $prob_period_repo->get_record_by_many(['cust_id', 'status','type'],[$loan->cust_id, 'active','condonation'], ['id']);
            if(!$cust_prob){
                try{
                    $borr_serv = new BorrowerService();
                    $borr_serv->allow_condonation($loan->cust_id, true); 
                }
                catch (App\Exceptions\FlowCustomException $e) {
                    
                }
            }
            
       }
    }
}
