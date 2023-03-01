<?php

namespace App\Scripts\php;


use Illuminate\Support\Facades\DB;

class BalanceFundScript{
    public static function run(){
        try{
            DB::beginTransaction();
            DB::update("set @runtot:=0, @runfee:=0;");
            $loans = DB::select("select id, (@runfee:=@runfee+flow_fee) cum_fee, (@runtot:= @runtot + loan_principal) as sum from loans where fund_code='VC-MAY21-USD' and status='settled' and date(disbursal_date) < '2021-10-01' and (@runtot + loan_principal) <= 138250000");
            $ids = csv(collect($loans)->pluck('id')->toArray());
            echo $ids;
            DB::update("update loans set fund_code = 'VC-MAY21-EUR' where id in ({$ids})");
            $size = sizeof($loans);
            $tot_fee = $loans[$size-1]->cum_fee;
            DB::update("update capital_funds set tot_disb_amount=tot_disb_amount+138250000, earned_fee = earned_fee + {$tot_fee} where fund_code = 'VC-MAY21-EUR' ");
            DB::update("update capital_funds set tot_disb_amount=tot_disb_amount-138250000, earned_fee = earned_fee - {$tot_fee} where fund_code = 'VC-MAY21-USD' ");
            DB::commit();

        }catch(\Exception $e){
            DB::rollBack();
            thrw($e);
        }
    }
}
