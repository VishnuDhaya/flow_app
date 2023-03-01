<?php
namespace App\Services\Schedule;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Repositories\SQL\BorrowerRepositorySQL;

class ActivityStatusScheduleService {

    public function update_activity_status(){

        $borr_repo = new BorrowerRepositorySQL();
        $borrowers = $borr_repo->get_records_by('country_code', session('country_code'), ['id', 'cust_id', 'last_loan_date', 'is_og_loan_overdue']);
        
        $last_30_date = Carbon::now()->subDays(30);
        $last_90_date = Carbon::now()->subDays(90);
        
        foreach($borrowers as $borrower){
            $activity_status = null;
            
            if($borrower->last_loan_date >= $last_30_date || $borrower->is_og_loan_overdue){
                $activity_status = 'active';
            }
            else if($borrower->last_loan_date <= $last_90_date){
                $activity_status = 'dormant';
            }
            else{
                $activity_status = 'passive';
            }
            $borr_repo->update_model([
                'id' => $borrower->id,
                'activity_status' => $activity_status
            ]);
            // Log::warning("$borrower->id -->  $borrower->cust_id --> $borrower->last_loan_date --> $borrower->is_og_loan_overdue --> $activity_status");
        }
    }
}
