<?php

namespace App\Services\Schedule;
use Illuminate\Support\Facades\Log;
use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\RiskCategoryRepositorySQL;
use Carbon\Carbon;
use DB;

class RiskCategoryScheduleService{

    public function update_risk_type(){
        $borr_repo = new BorrowerRepositorySQL();
        $risk_cate_repo = new RiskCategoryRepositorySQL();

        $borrowers = $borr_repo->get_records_by('country_code', session('country_code'), ['cust_id', 'acc_prvdr_code', 'ongoing_loan_doc_id', 'last_loan_doc_id', 'is_og_loan_overdue']);
        foreach ($borrowers as $borrower){
            
            $cust_state = 'no_current_overdue'; //due, ongoing, settled(last loan)
            $loan_doc_id = '';

            if($borrower->ongoing_loan_doc_id){ // due, ongoing, overdue
                $loan_doc_id = $borrower->ongoing_loan_doc_id;
                
                if($borrower->is_og_loan_overdue){
                    $cust_state = 'current_overdue'; // overdue
                }
            }else{
                $loan_doc_id = $borrower->last_loan_doc_id; // settled last loan
            } 
            
            $field_names = ['country_code', 'acc_prvdr_code', 'cust_state'];
            $field_values = [session('country_code'), $borrower->acc_prvdr_code, $cust_state];
            $fields_arr = ["exposure_from", "exposure_upto", "late_days_from", "late_days_to","fas_from","fas_to","risk_category"];

            $risk_category = $risk_cate_repo->get_records_by_many($field_names, $field_values, $fields_arr);
            
            $loan = DB::selectOne("select loan_principal, DATEDIFF(CURDATE(),due_date) as overdue_days from loans where loan_doc_id = ?", [$loan_doc_id]);  //current date differnece
            
            $risk_type = null;
            $risk_category_array = ['0_low_risk'];
            $late_days = 0;
            
            foreach($risk_category as $category){
                $max_late_days_arr = [0];
                if($loan && $loan->loan_principal >= $category->exposure_from && $loan->loan_principal <= $category->exposure_upto){

                    if($cust_state == 'current_overdue'){
                        if($loan->overdue_days >= $category->late_days_from && $loan->overdue_days <= $category->late_days_to){
                            $risk_type = $category->risk_category;
                        }
                    }
                     
                    else if($cust_state == 'no_current_overdue'){
                        
                        $last_n_fas = DB::select("select DATEDIFF(paid_date, due_date) as late_days from loans where cust_id = ? order by id desc limit ?, ? ", [$borrower->cust_id, $category->fas_from - 1, $category->fas_to - $category->fas_from + 1]);
                        
                        foreach($last_n_fas as $record){
                            array_push($max_late_days_arr, $record->late_days);
                        }
                        $max_late_days = max($max_late_days_arr);
                        
                        if($max_late_days < 0){
                             $max_late_days = 0;
                        }
                        if($max_late_days >= $category->late_days_from && $max_late_days <=  $category->late_days_to){
                            array_push($risk_category_array, $category->risk_category);
                        } 
                    }
                }
            }
            
            if($loan && $cust_state == 'no_current_overdue'){
               $risk_type = max($risk_category_array);
            }
            
            $borr_repo->update_model_by_code(['cust_id' => $borrower->cust_id,
                                            'risk_category' => $risk_type]);
        }
        
    }
}