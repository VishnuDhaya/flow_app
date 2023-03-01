<?php

namespace App\Scripts\php;
use DB;


class BeginTransactionScript{

    public function checkBeginCommit(){
        try
        {
            DB::beginTransaction();
            DB::update("update borrowers set tot_loans = tot_loans + 1 where id = 874 ");

            $this->checkNestedBignCommit();
            DB::update("update borrowers set late_loans = late_loans + 1 where id  = 874");
            //thrw("before commit");
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollback();
            
        }
    }

    private function checkNestedBignCommit(){
        try{
            DB::beginTransaction();
            //thrw("before commit");
            DB::update("update borrowers set perf_tot_loans = perf_tot_loans + 1 where id = 874 ");
            thrw("before commit");
            DB::commit();
        }
        catch(\Exception $e) {
            DB::rollback();
            
        }
    }
}