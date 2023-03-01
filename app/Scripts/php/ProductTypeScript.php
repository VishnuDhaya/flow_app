<?php

namespace App\Scripts\php;

use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Repositories\SQL\ProbationPeriodRepositorySQL;
use App\Repositories\SQL\LoanRepositorySQL;
use App\Services\LoanService;
use DB;
use Log;

class ProductTypeScript{

    public function updateProductType(){
        $borr_repo = new BorrowerRepositorySQL();
        // $borrowers = $borr_repo->get_all_customers();
        $borrowers = collect(DB::select("select cust_id from borrowers"))->pluck('cust_id');

        foreach($borrowers as $borrower){
            DB::update("update loans set product_type='probation' where cust_id = ? order by id limit 5",[$borrower]);
            DB::update("update loan_applications set product_type='probation' where cust_id = ? order by id limit 5",[$borrower]);
            
        }
        
        DB::update("update loans l, loan_products p set l.product_type = 'probation' where l.product_id =p.id and p.product_type = 'probation'");
    
        $this->updateProbFas();
    }

    public function updateProbFas(){
        $loans = DB::select("select l.cust_id, count(*) count from loans l join borrowers b 
        on l.cust_id = b.cust_id where b.prob_fas is null and l.product_type ='probation' 
        and l.disbursal_date is not null and l.paid_date is not null 
        group by l.cust_id having count(*) < 5");

        $prob_fas_count = config('app.default_prob_fas');

        foreach($loans as $loan){
            $prob_fas = $prob_fas_count - $loan->count;
            DB::update("update borrowers set category = 'Probation', prob_fas = ? where cust_id = ?",[$prob_fas, $loan->cust_id]);
        }
    }
    public function complete_prob_cond(){
        $probations = DB::select("select id, cust_id from probation_period where cust_id in ('CCA-159769','CCA-183599','CCA-523207','UEZM-251438','UEZM-640296') and status = 'active'");
        
        $prob_period_repo = new ProbationPeriodRepositorySQL();
        foreach($probations as $probation){
            $prob_per_live_cust = DB::select("select cust_id, start_date from probation_period_live where cust_id = ? and status = 'active' and type ='condonation'",[$probation->cust_id]);
            
            $prob_period_repo->complete_probation($probation->id, $prob_per_live_cust[0]->start_date);
        }

        //$customers = DB::select("select id, cust_id from probation_period where cust_id in ('CCA-469622','CCA-557057','CCA-563872','CCA-674573','UEZM-666502','UEZM-872501','UEZM-936496','UEZM-980222')");
        
    }

    public function updateBorrowerCategory(){
        $borrowers = DB::select("select cust_id, cond_count, prob_fas, tot_loans from borrowers");

        $loan_serv = new LoanService();
        $borr_repo = new BorrowerRepositorySQL();
        foreach($borrowers as $borrower){
           
            $category = $loan_serv->get_cust_category($borrower);
            $borr_repo->update_model_by_code(['cust_id' => $borrower->cust_id, 'category' => $category]);
        }
    }
}