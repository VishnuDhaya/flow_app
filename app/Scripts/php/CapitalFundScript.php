<?php
namespace App\Scripts\php;
use Illuminate\Support\Facades\DB;

class CapitalFundScript{
    
    static function split($fund_code, $percentage, $final = false){
        if($final){
            $new_code_a = "$fund_code-A";
            $new_code_b = "$fund_code-B";
        }
        else{
            $new_code_a = "$fund_code-USD";
            $new_code_b = "$fund_code-EUR";
        }
        $custs = DB::select("select cust_id from borrowers where fund_code = '$fund_code'");
        $cust_count = count($custs);
        $cust_ids = [];
        $cust_a_count=round($cust_count*$percentage);
        foreach ($custs as $cust) {
            $cust_ids[] = $cust->cust_id;
        }
        $cust_a_str = implode("','", array_slice($cust_ids,0,$cust_a_count));
        $cust_b_str = implode("','", array_slice($cust_ids,$cust_a_count));


        DB::update("update borrowers set fund_code = '$new_code_a' where cust_id in ('$cust_a_str')");
        DB::update("update borrowers set fund_code = '$new_code_b' where cust_id in ('$cust_b_str')");
        DB::update("update loans set fund_code = '$new_code_a' where fund_code = '$fund_code' and cust_id in ('$cust_a_str')");
        DB::update("update loans set fund_code = '$new_code_b' where fund_code = '$fund_code' and cust_id in ('$cust_b_str')");

        return [count(array_slice($cust_ids,0,$cust_a_count)),count(array_slice($cust_ids,$cust_a_count))];

    }


    static function splitCapitalFunds(){
  try {
        DB::beginTransaction();
        $funds = DB::select("select fund_code, alloc_amount_usd, alloc_amount_eur  from capital_funds where fund_type not in ('internal')");
        $eur_forex = 0.845;
        foreach ($funds as $fund) {
            $alloc_usd = $fund->alloc_amount_usd;
            $alloc_eur = $fund->alloc_amount_eur / 0.845;
            $eur_percentage = $alloc_eur / ($alloc_usd + $alloc_eur);
            $usd_percentage = 1 - $eur_percentage;
            print_r($eur_percentage.'\t'.$usd_percentage.'\n');
                $counts = self::split($fund->fund_code, $usd_percentage);
                self::fetchFundInfo($fund->fund_code, $usd_percentage, $counts);

            }

        $counts = self::split('FC-JUN21-EUR', 0.6, true);
        $fund = DB::selectOne("select * from capital_funds where fund_code = 'FC-JUN21-EUR'");
        DB::table('capital_funds')->insert([['country_code' => "$fund->country_code", 'fund_code' => "$fund->fund_code-A", 'fund_name' => "$fund->fund_name - A", 'lender_code' => "$fund->lender_code", 'fund_type' => "$fund->fund_type",  'is_lender_default' => $fund->is_lender_default, 'alloc_date' => $fund->alloc_date , 'fe_currency_code' => 'EUR', 'alloc_amount_fc' => 0.6 * $fund->alloc_amount_fc, 'forex' => 4310, 'alloc_amount' => null,'os_amount' => 0.6 * $fund->os_amount,'earned_fee' => 0.6 * $fund->earned_fee,'total_alloc_cust' => round(0.6 * $fund->total_alloc_cust), 'current_alloc_cust' => $counts[0], 'status' => "$fund->status", 'tot_disb_amount' => 0.6 * $fund->tot_disb_amount],
                                            ['country_code' => "$fund->country_code", 'fund_code' => "$fund->fund_code-B", 'fund_name' => "$fund->fund_name - B", 'lender_code' => "$fund->lender_code", 'fund_type' => "$fund->fund_type",  'is_lender_default' => $fund->is_lender_default, 'alloc_date' => $fund->alloc_date , 'fe_currency_code' => 'EUR', 'alloc_amount_fc' => 0.4 * $fund->alloc_amount_fc, 'forex' => 4310, 'alloc_amount' => null,'os_amount' => 0.4 * $fund->os_amount,'earned_fee' => 0.4 * $fund->earned_fee,'total_alloc_cust' => round(0.4 * $fund->total_alloc_cust), 'current_alloc_cust' => $counts[1], 'status' => "$fund->status", 'tot_disb_amount' => 0.4 * $fund->tot_disb_amount]]);
        DB::delete("delete from capital_funds where fund_code = 'FC-JUN21-EUR'");
        DB::delete("delete from capital_funds where alloc_amount_fc = 0 and fund_type not in ('internal')");

        DB::commit();
        }
        catch(\Exception $e){
            DB::rollBack();
            thrw($e);
        }
    }



    static function fetchFundInfo($fund_code, $usd_percentage, $counts){
        $tot_repay_usd = DB::select("select sum(lt.amount) as tot_repay from loans l,loan_txns lt where fund_code='$fund_code-USD' and lt.txn_type='payment' and l.loan_doc_id=lt.loan_doc_id");
        $tot_disb_usd = DB::select("select sum(loan_principal)  as tot_disb from loans where fund_code ='$fund_code-USD'");
        $tot_repay_eur = DB::select("select sum(lt.amount) as tot_repay from loans l,loan_txns lt where fund_code='$fund_code-EUR' and txn_type='payment' and l.loan_doc_id=lt.loan_doc_id");
        $tot_disb_eur = DB::select("select sum(loan_principal) as tot_disb from loans where fund_code ='$fund_code-EUR' ");
        $fee_earned_usd = DB::select("select sum(flow_fee) as fee from loans where fund_code='$fund_code-USD' and paid_date is not null");
        $fee_earned_eur = DB::select("select sum(flow_fee) as fee from loans where fund_code='$fund_code-EUR' and paid_date is not null");
        $tot_os_usd = $tot_disb_usd[0]->tot_disb - $tot_repay_usd[0]->tot_repay;
        $tot_os_eur = $tot_disb_eur[0]->tot_disb - $tot_repay_eur[0]->tot_repay;


        $fund = DB::selectOne("select * from capital_funds where fund_code = '$fund_code'");

        DB::table('capital_funds')->insert([['country_code' => "$fund->country_code", 'fund_code' => "$fund_code-EUR", 'fund_name' => "$fund->fund_name-EUR", 'lender_code' => "$fund->lender_code", 'fund_type' => "$fund->fund_type",  'is_lender_default' => $fund->is_lender_default, 'alloc_date' => $fund->alloc_date , 'fe_currency_code' => 'EUR', 'alloc_amount_fc' => $fund->alloc_amount_eur, 'forex' => 4310, 'alloc_amount' => null,'os_amount' => $tot_os_eur,'earned_fee' => $fee_earned_eur[0]->fee,'total_alloc_cust' => round((1-$usd_percentage) * $fund->total_alloc_cust), 'current_alloc_cust' => $counts[1], 'status' => "$fund->status", 'tot_disb_amount' => $tot_disb_eur[0]->tot_disb],
                                            ['country_code' => "$fund->country_code", 'fund_code' => "$fund_code-USD", 'fund_name' => "$fund->fund_name-USD", 'lender_code' => "$fund->lender_code", 'fund_type' => "$fund->fund_type",  'is_lender_default' => $fund->is_lender_default, 'alloc_date' => $fund->alloc_date , 'fe_currency_code' => 'USD', 'alloc_amount_fc' => $fund->alloc_amount_usd, 'forex' => 4310, 'alloc_amount' => null,'os_amount' => $tot_os_usd,'earned_fee' => $fee_earned_usd[0]->fee,'total_alloc_cust' => round(($usd_percentage) * $fund->total_alloc_cust), 'current_alloc_cust' => $counts[0], 'status' => "$fund->status", 'tot_disb_amount' => $tot_disb_usd[0]->tot_disb]]);

        DB::delete("delete from capital_funds where fund_code = '$fund_code'");

    }
}