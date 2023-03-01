<?php

namespace App\Scripts\php;

use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Services\BorrowerService;
use App\Services\LoanApplicationService;
use App\Models\CSModelWeightages;
use App\Models\ScoreModel;
use Log;
use App\Consts;
use Carbon\Carbon;

class ScoreEligibilityScript{

    public static function calcScoreModel($score_type){

        $country_code = 'UGA';
        session()->put('country_code',$country_code);

        $loan_repo = new LoanRepositorySQL();
        $borrower_repo = new BorrowerRepositorySQL();
        $borrowers = $borrower_repo->get_all_customers();
       
        $cust_report = [];

        foreach($borrowers as $borrower){
            
            $loan_appl_serv = new LoanApplicationService();
           
            #$borrower_info->prob_fas = 0;
            $csf_values_return = $loan_appl_serv->get_cust_csf_values($borrower);
            
            if($score_type == 'by_models'){
              
                if($csf_values_return['perf_eff_result'] != [[],[]] ){
                    $cust_report[] = (new static)->calc_score_by_models($csf_values_return, $borrower);
                }
                else{
                    
                    $cust_report[] =   (new static)->calc_score_by_models_for_probs_customers($borrower);
                        
                   
                }
            }
            elseif($score_type == 'by_products'){
               $cust_report = (new static)->calc_score_by_products($csf_values_return[0], $borrower);
            }
        }
        //Log::warning($cust_report);
        return collect($cust_report);
    }
    
    public static function calc_score_by_models($csf_values_return, $borrower){
        //$score_models = ['top_ups','loyalty_products','welcome_products','existing_customers','float_vending','probation'];
        $person_repo = new PersonRepositorySQL();
        $score_model = new ScoreModel();
        $loan_repo = new LoanRepositorySQL();
        
        $score_models = $score_model->get_records_by_country_code(['model_code', 'model_name']);
        $cust_report = [];

		$csf_values_return_from_eff_date = 	$csf_values_return['perf_eff_result'];
		
        $csf_values_return_from_true_eff_date = $csf_values_return['true_perf_eff_result'];
        
        //$csf_values_arr = $csf_values_return_from_true_eff_date[0];
        $csf_values_arr = $csf_values_return_from_eff_date[0];
        $field_name = ['csf_type','repeat_cust_weightage as weightage'];
        foreach ($score_models as $score_model) {
            if($score_model->model_code =='loyalty_products'){

                $csf_values_arr = $csf_values_return_from_true_eff_date[0];
            }else{
                $csf_values_arr = $csf_values_return_from_eff_date[0];
            }
            [$is_eligible, $cust_score, $result_code] = (new static)->check_model_eligibility($score_model->model_code, $csf_values_arr, $field_name);
            
            $cust_report['score_for_'.$score_model->model_code] = $cust_score;
            $cust_report['result_for_'.$score_model->model_code] = $result_code;
        }
        
        $cust_report['cust_id'] = $borrower->cust_id;
        $cust_report['dp_cust_id'] = $borrower->data_prvdr_cust_id;
        $cust_report['cust_name'] = $person_repo->full_name($borrower->owner_person_id);
        $cust_report['prob_fas'] = $borrower->prob_fas;
        $cust_report['tot_loans'] = $borrower->tot_loans;
        $cust_report['late_loans'] = $borrower->late_loans;
        $cust_report['first_loan_date'] = $borrower->first_loan_date;
        $cust_report['last_loan_date'] = $borrower->last_loan_date;
        $cust_report['late_1_day_loans'] = $borrower->late_1_day_loans;
        $cust_report['late_2_day_loans'] = $borrower->late_2_day_loans;
        $cust_report['late_3_day_loans'] = $borrower->late_3_day_loans;
        $cust_report['late_3_day_plus_loans'] = $borrower->late_3_day_plus_loans;
        $cust_report['gross_ontime_loans_pc'] = $csf_values_arr['gross_ontime_loans_pc'];
        $cust_report['gross_repaid_after_3_days_pc'] = $csf_values_arr['gross_repaid_after_3_days_pc'];
        $cust_report['gross_number_of_advances_till_now'] = $csf_values_arr['gross_number_of_advances_till_now'];
        $cust_report['gross_number_of_advances_per_quarter'] = $csf_values_arr['gross_number_of_advances_per_quarter'];
        $cust_report['gross_repaid_after_10_days_pc'] = $csf_values_arr['gross_repaid_after_10_days_pc'];
        $cust_report['gross_repaid_after_30_days_pc'] = $csf_values_arr['gross_repaid_after_30_days_pc'];
        
        if($borrower->ongoing_loan_doc_id){
            $person_loan =  $loan_repo->get_outstanding_loan($borrower->ongoing_loan_doc_id);
            $cust_report['ongoing_fa'] = $borrower->ongoing_loan_doc_id; 
            $cust_report['due_date'] = $person_loan->due_date;
        }
        // Log::warning($cust_report);
           
        return $cust_report;
        
    }
    private static function calc_score_by_models_for_probs_customers($borrower)
    {
        $loan_repo = new LoanRepositorySQL();
        $person_repo =  new PersonRepositorySQL();
        $person_loan =  $loan_repo->get_outstanding_loan($borrower->ongoing_loan_doc_id);

        $cust_report['cust_id'] = $borrower->cust_id;
        $cust_report['dp_cust_id'] = $borrower->data_prvdr_cust_id;
        $cust_report['cust_name'] = $person_repo->full_name($borrower->owner_person_id);
        $cust_report['prob_fas'] = $borrower->prob_fas;
        $cust_report['tot_loans'] = $borrower->tot_loans;
        $cust_report['late_loans'] = $borrower->late_loans;
        $cust_report['first_loan_date'] = $borrower->first_loan_date;
        $cust_report['last_loan_date'] = $borrower->last_loan_date;
        $cust_report['late_1_day_loans'] = $borrower->late_1_day_loans;
        $cust_report['late_2_day_loans'] = $borrower->late_2_day_loans;
        $cust_report['late_3_day_loans'] = $borrower->late_3_day_loans;
        $cust_report['late_3_day_plus_loans'] = $borrower->late_3_day_plus_loans;
        if($borrower->ongoing_loan_doc_id){
            $person_loan =  $loan_repo->get_outstanding_loan($borrower->ongoing_loan_doc_id);
            $cust_report['ongoing_fa'] = $borrower->ongoing_loan_doc_id; 
            $cust_report['due_date'] = $person_loan->due_date;
        }

        // $cust_report[] = ['ongoing_fa' => $ongoing_loan_doc_id, 
        //                 'due_date' => $person_loan->due_date];

        return $cust_report;
    }
    public static function calc_score_by_products($csf_values_return, $borrower, $report_data, $final){

        $loan_prod_repo = new LoanProductRepositorySQL();
        $loan_appl_serv = new LoanApplicationService();

        $loan_products = $loan_prod_repo->get_products_by($borrower->lender_code, $borrower->data_prvdr_code, $borrower->agrmt_for,'enabled');
				
        $agrmt = $loan_appl_serv->get_active_agreement($borrower->current_aggr_doc_id);
            
        $score = $loan_appl_serv->add_product_attributes($loan_products, $csf_values_return, null, $borrower, false);
        //$remove_dupli = [];
        $i =1;
        foreach($loan_products as $product => $item){
            
            $report_data[] = [ $item->cs_model_code." ". $i => $item->product_name,
                                'Score for '.$item->cs_model_code." ".$i  => $item->cust_score,
                                'Result for '.$item->cs_model_code." ".$i => $item->result_code
                            ];

            $i++;
        }
        Log::warning($report_data);
            
        $new_report =[];
        foreach($report_data as $key => $val)
        {
            $new_report = array_merge($val, $new_report);
        }
        $final[] = $new_report;

        Log::warning($final);
        return $final;
        // foreach($result_array as $key => $value){
        //     if ( !in_array($value['cs_model_code'], $remove_dupli) ) { 
        //         $remove_dupli[] = $value['cs_model_code']; 
        //         $newArray[$key] = $value; 
        //     } 
        // }
        // Log::warning($newArray);

    }
    
    public static function check_model_eligibility($cs_model_code, $csf_values_arr, $field_name){

        // $field_name = ['csf_type','repeat_cust_weightage as weightage'];
        $is_eligible = false;
        $cust_score = 0;
        $loan_appl_serv = new LoanApplicationService();
        [$no_data, $result_code, $result_configs, 
			$prod_csf_values, $cust_score, $is_eligible] = $loan_appl_serv->calc_csf_value($cs_model_code, $csf_values_arr, $field_name);

        return [$is_eligible, $cust_score, $result_code];
    }

} 