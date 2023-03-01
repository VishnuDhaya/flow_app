<?php

namespace App\Scripts\php;
use Illuminate\Support\Facades\DB;
use App\Repositories\SQL\BorrowerRepositorySQL;
use Log;

class FaCountBackTrackScript{

    public function updateFACount(){

        $borr_repo = new BorrowerRepositorySQL();
        $borrowers = DB::select("select id, cust_id, category, prob_fas from borrowers where prob_fas > 0");
        $old_prob_fa_count = 5;
        $old_cond_fa_count = 5;
        $diff_prob_fa = config("app.default_prob_fas") - $old_prob_fa_count;
        $diff_cond_fa = config("app.default_cond_fas") - $old_cond_fa_count;
        foreach ($borrowers as $borrower){
            
            if($borrower->category == 'Probation'){
                $fas_count = $borrower->prob_fas + $diff_prob_fa;
            }else if($borrower->category == 'Condonation'){
                $fas_count = $borrower->prob_fas + $diff_cond_fa;
            }

            if($fas_count < 0){
                $fas_count = 0;
            }
            
            $borr_repo->update_model(['id' => $borrower->id, 'prob_fas' => $fas_count]);
        }
        
    }
}
