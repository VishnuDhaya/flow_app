<?php

use App\Consts;
use App\Models\Loan;
use App\Repositories\SQL\LoanRepositorySQL;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateOverdueAndPenalatyDaysValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $countries = DB::table('countries')
            ->select('countries.country','countries.country_code')
            ->join('markets','markets.country_code','=','countries.country_code')
            ->get();
        foreach ($countries as $country){
            session()->put('country_code', $country->country_code);
            $this->update_overdue_days();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

    private function update_overdue_days(){
        $status = Consts::LOAN_SETTLED;
        $loans = (new Loan())->get_records_by("status",$status,['loan_doc_id','due_date','paid_date']);
        foreach ($loans as $loan) {
            $penalty_days = getPenaltyDate($loan->due_date,$loan->paid_date);
            $overdue_days = Carbon::parse($loan->due_date)->startOfDay()->diffInDays(Carbon::today());
            if($penalty_days > 0){
                (new LoanRepositorySQL())->update_model_by_code(['loan_doc_id' => $loan->loan_doc_id,'overdue_days' => $overdue_days, 'penalty_days' => $penalty_days]);
            }
        }
    }

}
