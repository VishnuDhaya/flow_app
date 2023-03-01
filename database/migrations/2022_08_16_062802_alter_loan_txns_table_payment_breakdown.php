<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Services\RepaymentService;

class AlterLoanTxnsTablePaymentBreakdown extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_txns', function (Blueprint $table){
            $table->double("principal")->nullable();
            $table->double("fee")->nullable();
            $table->double("penalty")->nullable();
            $table->double("excess")->nullable();
        });


        try{
            DB::beginTransaction();

            //EXcess not accounted in payment cases
            DB::update("UPDATE loans l, loan_txns t set amount = amount + paid_excess where l.loan_doc_id = t.loan_doc_id and  l.loan_doc_id in ('UFLW-787119874-44320','UFLW-781012320-31460','UFLW-777159832-40289','UFLW-772746910-32990','UFLW-771699181-42075','UFLW-770318886-84092','UFLW-755219838-12961','UFLW-754812611-71443') and txn_type = 'payment'");
            DB::update("UPDATE loan_txns set amount = 500 where id = 161049");
            DB::update("UPDATE loan_txns set amount = amount + 200 where id = 170820");
            
            
            
            //Change Penalty Payment txns to payment
            //==========================================================================================================================================
            $txns = DB::select("select txn_id from loan_txns where txn_id in (select txn_id from loan_txns where txn_type = 'penalty_payment') group by txn_id having count(*) = 2");
            
            foreach($txns as $txn){
                $txn_id = $txn->txn_id;
                $pen = DB::selectOne("select amount, id from loan_txns where txn_id = ? and txn_type = 'penalty_payment'", [$txn_id]);
                
                DB::update("update loan_txns set amount = amount + ?, penalty = ? where txn_id = ? and txn_type = 'payment'", [$pen->amount, $pen->amount, $txn_id]);
                
                DB::update("UPDATE account_stmts SET recon_status = null, loan_doc_id = null WHERE stmt_txn_id = ? and date(stmt_txn_date) >= ?", [$txn_id, config('app.recon_scr_strt_date')]);

                DB::delete("delete from loan_txns where id = ?", [$pen->id]);
            }
            
            
            DB::update("update loan_txns t inner join (select txn_id from loan_txns where txn_id in (select txn_id from loan_txns where txn_type = 'penalty_payment') group by txn_id having count(*) = 1) a on t.txn_id = a.txn_id set txn_type = 'payment', penalty = amount ");
            
            DB::update("update loan_txns set txn_id = null, txn_type = 'payment', penalty = amount where id in (69282, 69301, 70194)");
            //==========================================================================================================================================
            
            
            //Update payment breakdown fields
            //==========================================================================================================================================
            DB::update("update loan_txns t, loans l, (select l.loan_doc_id from loan_txns x, loans l where l.loan_doc_id = x.loan_doc_id and txn_type = 'payment' AND loan_purpose = 'float_advance' group by loan_doc_id having count(*) =  1) b set principal = IFNULL(paid_principal,0), fee = IFNULL(paid_fee,0), penalty = IFNULL(penalty_collected,0), excess = IFNULL(paid_excess,0) where l.loan_doc_id = t.loan_doc_id  and l.loan_doc_id = b.loan_doc_id AND txn_type = 'payment';
            ");
            
            
            $loans = DB::select("select loan_doc_id, 0 paid_principal, 0 paid_fee, 0 penalty_collected, 0 paid_excess, 0 penalty_waived, penalty_collected tot_prov_penalty, loan_principal, flow_fee from loans where status not in ('voided', 'hold', 'pending_disbursal', 'pending_mnl_disbursal')  and loan_doc_id in (select distinct loan_doc_id from loan_txns where txn_type = 'payment' and  principal is null and excess is null and fee is null)");
            $i = 0;
            foreach($loans as $loan){
                // print(++$i);
                $txns = DB::select("select id, amount from loan_txns where loan_doc_id = ? and txn_type = 'payment' order by txn_date", [$loan->loan_doc_id]);
                foreach($txns as $txn){
                    $capture_amounts = $this->get_amounts_to_capture($loan, $txn->amount, $loan->tot_prov_penalty);
                    $loan->paid_principal += $capture_amounts['principal'];
                    $loan->paid_fee += $capture_amounts['fee'];
                    $loan->penalty_collected += $capture_amounts['penalty'];
                    $loan->paid_excess += $capture_amounts['excess'];
                    DB::update("Update loan_txns set principal = ?, fee = ?, penalty = ?, excess = ? where id = ? ", [$capture_amounts['principal'],$capture_amounts['fee'],$capture_amounts['penalty'],$capture_amounts['excess'], $txn->id]);
                    
                    
                }
            }
            //==========================================================================================================================================
            
            
            
            
            
            //Set txn id of all loan txns with duplicate txn_ids to NULL (before 2022)
            DB::update("update loan_txns t, (select txn_id, count(*) from loan_txns where year(txn_date) < 2022  group by txn_id having count(*) > 1) tt set t.txn_id = null where t.txn_id = tt.txn_id");

            //Remove penalty_payments with NULL loan_doc_id and txn_id created by UpdatePenaltyCorrectionScript.php
            DB::delete("delete from loan_txns where txn_type = 'penalty_payment' and loan_doc_id = txn_id is null");

            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            thrw($e);
        }
        
        
    }
    
        /**
         * Reverse the migrations.
         *
     * @return void
     */
    
    
    public function get_amounts_to_capture(&$loan, $txn_amount, $tot_prov_penalty){
        $rpmt_serv = new RepaymentService;
        $principal = $fee = $penalty = $excess = 0;
        $principal = $rpmt_serv->capture($txn_amount, $loan->paid_principal, $loan->loan_principal);
        $remaining = $txn_amount - $principal;
        
        
        $fee = $rpmt_serv->capture($remaining, $loan->paid_fee, $loan->flow_fee);
        $remaining = $remaining - $fee;


        $penalty = $rpmt_serv->capture($remaining, $loan->penalty_collected + $loan->penalty_waived, $tot_prov_penalty);

        $excess = $remaining - $penalty;

        return [
                'principal' => $principal,
                'fee' => $fee,
                'penalty' => $penalty,
                'excess' => $excess,
                'tot_prov_penalty' => $tot_prov_penalty,

            ]; 

    }
    public function down()
    {
        //
    }
}
