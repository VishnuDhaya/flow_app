<?php

namespace App\Scripts\php;
use Illuminate\Support\Facades\DB;

use App\Services\BorrowerService;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\ProbationPeriodRepositorySQL;
use Log;

class ProbationBackTrackScript{

    public function insert_probation_period(){

        session()->put('country_code','UGA');
        $prob_fas_count = config('app.default_prob_fas');

        // $customers = DB::select("select l.cust_id, count(*) count
        // from loans l join loan_products p 
        // on l.product_id =p.id where p.product_type ='probation' 
        // and l.disbursal_date is not null and l.paid_date is not null and l.cust_id not in ('CCA-624132')
        // group by l.cust_id");

        $loans = DB::select("select cust_id, count(*) count from loans 
        where product_type ='probation' and
        disbursal_date is not null and paid_date is not null and 
        cust_id not in ('CCA-624132') group by cust_id");

        $borr_repo = new BorrowerRepositorySQL();
        
        foreach($loans as $loan){
            $borrower = $borr_repo->get_record_by('cust_id', $loan->cust_id, ['prob_fas']);

            if($loan->count >= 0 && $loan->count <= 4){
                if($borrower->prob_fas > 0){
                    $start_date = $this->get_disbursal_date($loan->cust_id, 1);
                    $prob_per_id = $this->startProbation($loan->cust_id, 'probation', $start_date);
                }
                else if($borrower->prob_fas == 0){
                    Log::warning("\nCouldn't allow this customer {$loan->cust_id} \nProbation FA count : {$loan->count}\n prob_fas : {$borrower->prob_fas}\n");
             }

            }
            else if($loan->count == 5){

                $this->create_initial_probation($loan->cust_id);

                if($borrower->prob_fas > 0){
                    $cond_customer = DB::select("select id from probation_period_live where type='condonation' and status = 'active' and cust_id = ?",[$loan->cust_id]);
                    Log::warning($cond_customer);
                    if(!$cond_customer){
                        $start_date = $this->get_paid_date($loan->cust_id, 5);
                        $this->create_condonation($loan->cust_id, $start_date, null, 1);
                    }
                    
                    #$prob_per_id = $this->startProbation($loan->cust_id, 'condonation', $start_date);
                     
                }
            }
            else if($loan->count >=6 && $loan->count <=9){

                $this->create_initial_probation($loan->cust_id);

                if($borrower->prob_fas > 0){
                    $start_date = $this->get_disbursal_date($loan->cust_id, 6);
                    $this->create_condonation($loan->cust_id, $start_date, null, 1);

                  /*$prob_per_id = $this->startProbation($loan->cust_id, 'condonation', $start_date);

                    $this->update_borrower($loan->cust_id, $start_date, '1');*/
                }
            }
            else if($loan->count == 10){

                if($borrower->prob_fas == 0){
                    $this->create_initial_probation($loan->cust_id);

                    $start_date = $this->get_disbursal_date($loan->cust_id, 6);
                    $end_date = $this->get_disbursal_date($loan->cust_id, 10);
                    $this->create_condonation($loan->cust_id, $start_date, $end_date, 1);

                }
                else if($borrower->prob_fas > 0){
                    Log::warning("\nCouldn't allow this customer {$loan->cust_id} \nProbation FA count : {$loan->count}\n prob_fas : {$borrower->prob_fas}\n");
                }    
                    
            }
            else if($loan->count >= 11 && $loan->count <= 14){
                $this->create_initial_probation($loan->cust_id);
                $prev_prob_start_date = $this->get_disbursal_date($loan->cust_id, 6);
                $end_date = $this->get_disbursal_date($loan->cust_id, 10);

                $this->create_condonation($loan->cust_id, $prev_prob_start_date, $end_date, 1);

                if($borrower->prob_fas > 0){
                    $start_date = $this->get_disbursal_date($loan->cust_id, 11);
                    
                    $this->create_condonation($loan->cust_id, $start_date, null, 2, $prev_prob_start_date);
                }
            }
            else if($loan->count == 15){
                if($borrower->prob_fas == 0){
                    $this->create_initial_probation($loan->cust_id);

                    $prev_prob_start_date = $this->get_disbursal_date($loan->cust_id, 6);
                    $end_date = $this->get_disbursal_date($loan->cust_id, 10);
                    $this->create_condonation($loan->cust_id, $prev_prob_start_date, $end_date, 1);

                   
                    $start_date = $this->get_disbursal_date($loan->cust_id, 11);
                    $end_date = $this->get_disbursal_date($loan->cust_id, 15);
                    $this->create_condonation($loan->cust_id, $start_date, $end_date, 2, $prev_prob_start_date);

                }
                else if($borrower->prob_fas > 0){
                    Log::warning("\nCouldn't allow this customer {$loan->cust_id} \nProbation FA count : {$loan->count}\n prob_fas : {$borrower->prob_fas}\n");
                }
            }
            else if($loan->count >= 16){

                $this->create_initial_probation($loan->cust_id);

                $prev_prob_start_date = $this->get_disbursal_date($loan->cust_id, 6);
                $end_date = $this->get_disbursal_date($loan->cust_id, 10);
                $this->create_condonation($loan->cust_id, $prev_prob_start_date, $end_date, 1);

                
                $start_date = $this->get_disbursal_date($loan->cust_id, 11);
                $end_date = $this->get_disbursal_date($loan->cust_id, $loan->count);
                $this->create_condonation($loan->cust_id, $start_date, $end_date, 2, $prev_prob_start_date);

                Log::error("###########3 Customer {$loan->cust_id} EXHAUSTED ALL CONDONATIONS");

            }
        }
    }

    private function create_initial_probation($cust_id){

        $start_date = $this->get_disbursal_date($cust_id, 1);
        $prob_per_id = $this->startProbation($cust_id, 'probation', $start_date);
        $end_date = $this->get_disbursal_date($cust_id, 5);
        $this->completeProbation($prob_per_id, $end_date);
    }

    private function create_condonation($cust_id, $start_date, $end_date, $cond_count, $prev_prob_start_date = null){

        $perf_data = $this->get_perf_data($cust_id, $start_date, $prev_prob_start_date);

        Log::warning($perf_data);
        $prob_per_id = $this->startProbation($cust_id, 'condonation',$start_date, $perf_data);
        if($end_date){
            $this->completeProbation($prob_per_id,$end_date);
        }
        $this->update_borrower($cust_id, $start_date, $cond_count);   
        
    }
    
    private function get_perf_data($cust_id, $eff_date, $prev_prob_efft_date){

        if($prev_prob_efft_date != null){
            $sql_for_loan  = " date(disbursal_date) >= date('$prev_prob_efft_date') and date(disbursal_date) < date('$eff_date')";
            $sql_for_loan_appl  = " date(loan_appl_date) >= date('$prev_prob_efft_date') and date(loan_appl_date) < date('$eff_date')";
        }
        else {
            $sql_for_loan = " date(disbursal_date) < date('$eff_date')";
            $sql_for_loan_appl = " date(loan_appl_date) < date('$eff_date')";
        }


        $tot_loans = collect(DB::select("select count(id) as count from loans where cust_id = ? and $sql_for_loan ",[$cust_id]))->pluck('count');

        $tot_applns = collect(DB::select("select count(id) as count from loan_applications where cust_id = ? and 
        $sql_for_loan_appl ",[$cust_id]))->pluck('count');
        
        $late_loans = collect(DB::select("select count(id) as count from loans where cust_id = ? and $sql_for_loan 
        and datediff(paid_date,due_date) > 0",[$cust_id]))->pluck('count');

        $perf_late_1_day_loans = collect(DB::select("select count(id) as count from loans where cust_id = ? and $sql_for_loan 
        and datediff(paid_date,due_date) = 1", [$cust_id]))->pluck('count');

        $perf_late_2_day_loans = collect(DB::select("select count(id) as count from loans where cust_id = ? and $sql_for_loan 
        and datediff(paid_date,due_date) = 2", [$cust_id]))->pluck('count');
        
        $perf_late_3_day_loans = collect(DB::select("select count(id) as count from loans where cust_id = ? and $sql_for_loan 
        and datediff(paid_date,due_date) = 3", [$cust_id]))->pluck('count');
        
        $perf_late_3_day_plus_loans = collect(DB::select("select count(id) as count from loans where cust_id = ? and $sql_for_loan
         and datediff(paid_date,due_date) > 3", [$cust_id]))->pluck('count');
        
        Log::warning($tot_loans);
        $data = ['perf_tot_loans' => $tot_loans[0], 'perf_tot_loan_appls' => $tot_applns[0], 'perf_late_loans' => $late_loans[0],
        'perf_late_1_day_loans' => $perf_late_1_day_loans[0], 'perf_late_2_day_loans' => $perf_late_2_day_loans[0],
        'perf_late_3_day_loans' => $perf_late_3_day_loans[0], 'perf_late_3_day_plus_loans' => $perf_late_3_day_plus_loans[0] ];

        return $data;
    }

    private function update_borrower($cust_id, $disb_date, $cond_count){

        $tot_loans = collect(DB::select("select count(id) as count from loans where cust_id = ? and 
        date(disbursal_date) >= date(?)",[$cust_id, $disb_date]))->pluck('count');

        $tot_applns = collect(DB::select("select count(id) as count from loan_applications where cust_id = ? and 
        date(loan_appl_date) >= date(?)",[$cust_id, $disb_date]))->pluck('count');

        $late_loans = collect(DB::select("select count(id) as count from loans where cust_id = ? and 
        date(disbursal_date) >= date(?) and datediff(paid_date,due_date) > 0", [$cust_id, $disb_date]))->pluck('count');

        $perf_late_1_day_loans = collect(DB::select("select count(id) as count from loans where cust_id = ? and 
        date(disbursal_date) >= date(?) and datediff(paid_date,due_date) = 1", [$cust_id, $disb_date]))->pluck('count');

        $perf_late_2_day_loans = collect(DB::select("select count(id) as count from loans where cust_id = ? and 
        date(disbursal_date) >= date(?) and datediff(paid_date,due_date) = 2", [$cust_id, $disb_date]))->pluck('count');
        
        $perf_late_3_day_loans = collect(DB::select("select count(id) as count from loans where cust_id = ? and 
        date(disbursal_date) >= date(?) and datediff(paid_date,due_date) = 3", [$cust_id, $disb_date]))->pluck('count');
        
        $perf_late_3_day_plus_loans = collect(DB::select("select count(id) as count from loans where cust_id = ? and 
        date(disbursal_date) >= date(?) and datediff(paid_date,due_date) > 3", [$cust_id, $disb_date]))->pluck('count');
        
        $data =[ 'cond_count' => $cond_count, 'cust_id' => $cust_id, 'perf_eff_date' => $disb_date,
                 'perf_tot_loan_appls' => $tot_applns[0], 'perf_tot_loans' => $tot_loans[0],
                 'perf_late_loans' => $late_loans[0], 'perf_late_1_day_loans' => $perf_late_1_day_loans[0],
                 'perf_late_2_day_loans' => $perf_late_2_day_loans[0], 'perf_late_3_day_loans' => $perf_late_3_day_loans[0],
                 'perf_late_3_day_plus_loans' => $perf_late_3_day_plus_loans[0]
                ];

        $borr_repo = new BorrowerRepositorySQL();
        $borr_repo->update_model_by_code($data);
    }

    private function get_disbursal_date($cust_id, $position){
        
        $disbursal_date = collect(DB::select("select disbursal_date from loans 
        where cust_id = ? and  product_type ='probation' and disbursal_date is not null 
        order by disbursal_date limit ?, 1",[$cust_id, $position-1]))->pluck('disbursal_date');

        return $disbursal_date[0];
    }

    private function get_paid_date($cust_id, $position){
        
        $paid_date = collect(DB::select("select paid_date from loans 
        where cust_id = ? and  product_type ='probation' and paid_date is not null 
        order by paid_date limit ?, 1",[$cust_id, $position-1]))->pluck('paid_date');
        
        return $paid_date[0];
    }

    private function startProbation($cust_id, $prob_type, $start_date, $perf_data = []){

        $prob_period_repo = new ProbationPeriodRepositorySQL();
        $prob_fas_count = config('app.default_prob_fas');
        $data = [
            'cust_id' => $cust_id,
            'country_code' => session('country_code'),
            'start_date' => $start_date,
            'type' => $prob_type,
            'fa_count' => $prob_fas_count,
            'status' => "active",
            
        ];
        $data = array_merge($data, $perf_data);
        
        $prob_period_id = $prob_period_repo->insert_model($data);

        return $prob_period_id;
    }
    
    private function completeProbation($prob_period_id, $end_date){

        $prob_period_repo = new ProbationPeriodRepositorySQL();
        $data = [
                    'id' => $prob_period_id,
                    'end_date' => $end_date,
                    'status' => "completed"
                ];
        $prob_period_repo->update_model($data);
    }
}