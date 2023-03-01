<?php
namespace App\Services\InvApp;
use App\Repositories\SQL\MarketRepositorySQL;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use phpDocumentor\Reflection\Types\Object_;


class InvAppService{
  public function __construct()
  {
  }

    public function get_home_data()
    {
        $inv_person_id = session('inv_person_id');
        $month = session('month');
        $report_date = session('report_date')->format('d M');

        $total_invested_amount = $this->get_investment($inv_person_id);
        $total_earnings = $this->get_total_earnings($month, $inv_person_id);
        $social_returns = $this->get_social_return_data($month, $inv_person_id);
        $total_annualized_returns = $this->calculate_total_annualized_returns($inv_person_id, $month, $total_invested_amount);
        $bonds = $this->get_invested_bonds_details($inv_person_id, $month);

        $funds = DB::select("select distinct fund_type as bond_type, profit_rate * 100 as profit_percent, floor_rate * 100 as floor_rate,duration from capital_funds where fund_type not in ('internal') order by duration DESC");
        return ["crnt_bonds" => (object)$funds, "total_earnings" => $total_earnings, "total_inv_amt" => $total_invested_amount, "report_date" => $report_date, 'social_returns' => $social_returns, 'total_annualized_returns' => $total_annualized_returns, 'bonds' => $bonds];
    }


    public function get_transactions($fund_code){
        $inv_person_id = session('inv_person_id');
        $bonds = $this->get_investment_transactions($inv_person_id,$fund_code);
        $color_code = $this->get_color_code();
        $dist_bonds = $this->get_dist_bonds();
        return ['bonds' => $bonds, 'color_code' => $color_code, 'dist_bonds' => $dist_bonds, 'selected_fund' => $fund_code];
    }

    public function get_bank_acc(){
        $inv_person_id = session('inv_person_id');
        $bank_data = DB::selectOne("select * from investor_bank_accounts where person_id = {$inv_person_id}");
        $currency = DB::selectOne("select currency_code from investment_txns where person_id = {$inv_person_id} and txn_type = 'investment'")->currency_code;
        if($bank_data == null)
        {
            $bank_data = (object)$bank_data;
            $keys = ['country','first_name','last_name','usd_ach_routing_num','usd_account_num','eur_bic','eur_iban','institution','address_line_1','address_line_2','city','postcode'];
            foreach ($keys as $key){
                $bank_data->$key = null;
            }
        }
        return ['bank_data' => $bank_data, 'currency' => $currency ];

    }

    public function add_bank_acc($data){
        $inv_person_id = session('inv_person_id');
        $currency = DB::selectOne("select currency_code from investment_txns where person_id = {$inv_person_id} and txn_type = 'investment'")->currency_code;
        $data['person_id'] = $inv_person_id;
        $data['currency'] = $currency;
        $exist = db::table('investor_bank_accounts')->where('person_id',$inv_person_id)->first('id');
        if($exist){
            DB::table('investor_bank_accounts')->where('person_id',$inv_person_id)->limit(1)->update($data);
        }
        else{
            DB::table('investor_bank_accounts')->where('person_id',$inv_person_id)->insert($data);
        }
        return true;
    }


    public function get_bond_details($fund_code)
    {
        $date = Db::table('capital_funds')->where('fund_code',$fund_code)->pluck('alloc_date');
        $alloc_date = Carbon::parse($date[0]);
        $summary_date = Carbon::now()->subMonth()->startOfDay();
        $allocation = true;
        $inv_person_id = session('inv_person_id');
        $month = session('month');
        $report_date = session('report_date')->format('d M');
        $my_bonds = $this->get_invested_bonds($inv_person_id);
        if($alloc_date >= $summary_date){
            $allocation = false;
            $bond_details = new Object_();
            $bond_details->fund_code = $fund_code;
            return ["allocation" => $allocation, "report_date" => $report_date, 'bond_details' => $bond_details, 'my_bonds' => $my_bonds];
        }
        $bond_details = $this->get_bond_summary($month, $fund_code, $inv_person_id);
        $all_bond_details = $this->get_all_bonds_summary($month, $inv_person_id);
        $bonds = $this->get_capital_fund_info();


        return ['bond_details' => $bond_details, 'bonds' => $bonds, 'my_bonds' => $my_bonds,
            "report_date" => $report_date, "all_bond_details" => $all_bond_details, "allocation" => $allocation];
    }


    private function get_investment($person_id = null, $fund_code = null, $untill = null)
    {
        $addl = "";
        if ($untill != null){
            $addl = " and alloc_date < '{$untill}' ";
        }
        $fund_codes = "'null'";
        $person_condn = $fund_condn = "";
        if($fund_code){
            $fund_codes = "'$fund_code'";
        }else{
            $funds = DB::select("select fund_code from capital_funds where date_add(alloc_date, interval duration month) >= curdate()
                                      and fund_type != 'internal' {$addl}");
            if(sizeof($funds) > 0){
                $funds = collect($funds)->pluck('fund_code')->toArray();
                $fund_codes = csv($funds);
            }
        }

        if($person_id){
            $person_condn = " person_id = {$person_id}";
            $fund_condn = " and fund_code in ($fund_codes)";
        }
        else{
            $fund_condn = "fund_code in ($fund_codes)";
        }

        $investments = DB::select("select amount, currency_code from  investment_txns 
                                            where {$person_condn} {$fund_condn} and txn_type = 'investment' {$addl}");
        $inv_amount = 0;
        foreach($investments as $investment){
            $forex = $this->get_forex_rate($investment->currency_code, session('currency_code'));
            $inv_amount += $investment->amount * $forex;
        }
        return $inv_amount;
    }

    private function get_investment_transactions($person_id,$fund_code = null)
    {
        $addl_sql = "";
        if($fund_code){
            $addl_sql = " and fund_code = '{$fund_code}'";
        }
        $inv_txns = DB::select("select fund_code, txn_date, realisation_date, currency_code, amount, txn_type from investment_txns where person_id = {$person_id} {$addl_sql} order by txn_date desc");
        foreach($inv_txns as $txn){
            $fund_details = DB::selectOne("select fund_type, duration from capital_funds where fund_code = '{$txn->fund_code}'");
            $txn->fund_type = $fund_details->fund_type;
            $txn->duration = $fund_details->duration;
        }
        return $inv_txns;
    }

    function get_color_code(){
        $colors = ['#FFF9DC', '#D6E5FD', '#DDFFDC', '#FFDBE2', '#FFE9E1', '#E1D9FF', '#FED2FF'];
        $bonds = $this->get_dist_bonds(true);
        $color_code = array_combine($bonds,array_slice($colors,0,count($bonds)));
        return $color_code;
    }
    function get_dist_bonds($toArray = false){
        $inv_person_id = session('inv_person_id');
        $bonds = DB::table('investment_txns')->distinct('fund_code')->where('person_id',$inv_person_id)->pluck('fund_code');
        if($toArray){
            $bonds = $bonds->toArray();
        }

        return $bonds;
    }

    private function get_invested_bonds_details($person_id, $month)
    {
        $funds = $this->get_capital_fund_info($person_id,null,true);

        foreach($funds as $fund){
            $fund->amount = $this->get_investment($person_id, $fund->fund_code);
            $fund->earning = $this->get_total_earnings($month, $person_id, $fund->fund_code);
        }
        usort($funds, function($a, $b) {return $b->earning > $a->earning;});

        return $funds;
    }

    public function get_invested_bonds($inv_person_id,$until = null)
    {
        $addl = "";
        if($until != null){
            $addl = " and alloc_date < '{$until}'";
        }
        $funds = DB::select("select c.fund_code from capital_funds c, investment_txns i
                                       where c.fund_code = i.fund_code and person_id = $inv_person_id and txn_type = 'investment' 
                                       and date_add(alloc_date, interval duration month) >= curdate() {$addl} ");
        $fund_codes = collect($funds)->pluck('fund_code')->toArray();
        return $fund_codes;
    }


    public function best_bond($inv_person_id){
        $report_date = session('report_date');
        $bond = DB::selectOne("select fund_code from investment_txns where person_id = '{$inv_person_id}' and txn_type = 'investment' and date(realisation_date) < '{$report_date}' order by amount DESC");
        return $bond;
    }

    private function get_total_earnings($month, $person_id = null, $fund_code = null){
        $funds = $this->get_capital_fund_info($person_id, $fund_code);
        $earnings = 0;
        foreach($funds as $fund){
            $fund->amount = $this->get_investment($person_id, $fund->fund_code);
            $net_returns = (DB::connection('report')->selectOne("select sum(net_returns) as net_returns from bonds_monthly where fund_code = '{$fund->fund_code}' and month <= {$month}"))->net_returns;
            Log::warning("rtnn");
            Log::warning($net_returns);
            Log::warning($fund->alloc_amount_fc);
            $earning = ($net_returns ) * $fund->profit_rate  *($fund->amount/$fund->alloc_amount_fc);
            $earnings += $this->compare_with_floor_returns(['earnings' => $earning, 'inv_amount' => $fund->amount,
                'floor_rate' => $fund->floor_rate, 'alloc_date' => $fund->alloc_date]);
        }
        return $earnings;
    }

    private function calculate_total_annualized_returns($inv_person_id, $month, $total_invested_amount){
        $funds = $this->get_capital_fund_info($inv_person_id);
        $annual_earnings = 0;
        $total_amount = 0;
        foreach($funds as $fund){
            $fund->amount = $this->get_investment($inv_person_id, $fund->fund_code);
            $earnings = $this->get_total_earnings($month, $inv_person_id, $fund->fund_code);
            if($fund->fund_type == "fixed_coupon"){
                $annual_pc = $fund->profit_rate * 100;
            }else{
                $annual_pc = floatval(str_replace(',', '', get_annualized_returns($fund->amount, $earnings, $fund->alloc_date)));
            }
            $annual_earnings += ($fund->amount * $annual_pc);
            $total_amount += $fund->amount;
        }
        $overall_annual_pc = $annual_earnings / $total_amount;
        Log::warning($overall_annual_pc);
        return $overall_annual_pc;
    }

    public function get_bond_summary($month, $fund_code, $inv_person_id, $until = null){
        $bond = DB::selectOne("select fund_code, fund_type, country_code, alloc_date,duration, forex, alloc_date, profit_rate, floor_rate, license_rate, fe_currency_code, current_alloc_cust, (alloc_amount_fc) allocated_amount   from capital_funds  where fund_code = '{$fund_code}'");
        $invested_amount = $this->get_investment($inv_person_id, $fund_code,$until);
        $inv_perc = ($invested_amount/$bond->allocated_amount);
        $bond->current_alloc_cust *= $inv_perc;
        $bond->allocated_amount *= $inv_perc;

        $market_repo = new MarketRepositorySQL();
        $market_currency = $market_repo->get_market_info($bond->country_code)->currency_code;
        $forex = $bond->forex;
        $bond_report = DB::connection('report')->selectOne("select  sum((tot_fee_rcvd * ({$inv_perc} / {$forex}))) fee_earned, sum((tot_principal_os * ({$inv_perc} / {$forex}))) principal_os, sum((tot_disbursed * ({$inv_perc} / {$forex}))) total_disbursed, sum((commission * ({$inv_perc} / {$forex}))) comm, sum((bad_debts * ({$inv_perc} / {$forex}))) bad_debts, sum((bad_debts_recovered * ({$inv_perc} / {$forex}))) bad_debts_recovered, sum((net_returns * {$inv_perc})) net_returns  from bonds_monthly where fund_code='{$fund_code}' and month <={$month}");
        $bond_details = array_merge((array)$bond, (array)$bond_report);
        $bond_details['earnings'] = $this->get_total_earnings($month, $inv_person_id, $fund_code);
        $bond_details['invested_amount'] = $invested_amount;

        $social_returns = $this->get_social_return_data($month, $inv_person_id, $fund_code);
        $bond_details = (object) array_merge($bond_details, (array)$social_returns);
        return $bond_details;
    }

    private function get_all_bonds_summary($month, $inv_person_id)
    {
        $all_bonds = $this->get_capital_fund_info();
        $principal_os = $total_disbursed = $net_returns = $allocated_amount = $current_alloc_cust = 0;
        foreach($all_bonds as $bond){
            $forex = $bond->forex;
            $bond_details = DB::connection('report')->selectOne("select (tot_principal_os / {$forex}) principal_os, (tot_disbursed / {$forex}) total_disbursed, (net_returns / {$forex}) net_returns from bonds_monthly b where month = {$month}  and fund_code = '{$bond->fund_code}'");
            $principal_os += $bond_details->principal_os;
            $total_disbursed += $bond_details->total_disbursed;
            $net_returns += $bond_details->net_returns;
            $current_alloc_cust += $bond->current_alloc_cust;
        }
        $all_bond_details = [];
        $all_bond_details['principal_os'] = $principal_os;
        $all_bond_details['total_disbursed'] = $total_disbursed;
        $all_bond_details['net_returns'] = $net_returns;
        $all_bond_details['current_alloc_cust'] = $current_alloc_cust;

        $all_bond_details['earnings'] = $this->get_total_earnings($month);
        $all_bond_details['invested_amount'] = $this->get_investment();
        $all_bond_details['social_returns'] = $this->get_social_return_data($month);
        $all_bond_details['annualized_returns'] = $this->calculate_total_annualized_returns(null, $month, $all_bond_details['invested_amount']);
        return (object) $all_bond_details;

    }

    private function get_capital_fund_info($person_id = null, $fund_code = null, $all_bonds = false){
        $report_date = session('report_date')->format('Y-m-d');
        $person_condition = "";
        if($person_id){
            if($fund_code == null){
                $fund_codes_str = "'null'";
                $fund_codes = $this->get_invested_bonds($person_id);
                if(sizeof($fund_codes) > 0) {
                    $fund_codes_str = csv($fund_codes);
                }
            }else{
                $fund_codes_str = "'{$fund_code}'";
            }
            $person_condition = " and fund_code in ($fund_codes_str)";
        }elseif($fund_code){
            $person_condition = " and fund_code in ('$fund_code')";
        }
        $include_all = " and date(alloc_date) < '{$report_date}' ";
        if($all_bonds){
            $include_all = "";
        }
        $funds = DB::select("select fund_code, alloc_amount_fc, forex , fe_currency_code, profit_rate, alloc_date, floor_rate, fund_type, duration,
                                                current_alloc_cust, country_code
                                                from capital_funds
                                                where fund_type != 'internal'
                                                and date_add(alloc_date, interval duration month) >= curdate() {$include_all}
                                                {$person_condition}");

        return $funds;
    }

    private function get_invest_percent($person_id, $fund_code, $tot_alloc_amount){
        $inv_amount = (DB::selectOne("select amount from investment_txns where person_id = {$person_id} and fund_code = '$fund_code'  and txn_type = 'investment'"))->amount;
        $inv_perc = ($inv_amount / $tot_alloc_amount);
        return $inv_perc;
    }

    private function get_social_return_data($month, $inv_person_id = null, $fund_code = null)
    {
        $capital_fund_info = $this->get_capital_fund_info($inv_person_id, $fund_code);
        $cust_revenue = $tot_retail_txn_value = $people_benefited = $small_business =  $male_cust = $num_of_txn = 0;

        foreach($capital_fund_info as $fund_info){
            $fund_code = $fund_info->fund_code;
            $ip = 1;
            if($inv_person_id) {
                $ip = $this->get_invest_percent($inv_person_id, $fund_code, $fund_info->alloc_amount_fc);
            }
            $forex = $fund_info->forex;
            $social_data =  DB::connection('report')->selectOne(
                "select (cust_revenue * $ip) / $forex as cust_revenue, (tot_retail_txn_value * $ip) / $forex as tot_retail_txn_value,
                                         people_benefited * $ip as people_benefited, male_perc, current_alloc_cust as small_business, num_of_txn  
                                    from bonds_monthly where month = {$month} and fund_code = '{$fund_code}' ");

            $cust_revenue += $social_data->cust_revenue;
            $tot_retail_txn_value += $social_data->tot_retail_txn_value;
            $num_of_txn += $social_data->num_of_txn;
            $people_benefited += $social_data->people_benefited;
            $small_business += $social_data->small_business;
            $male_cust += $social_data->male_perc * $fund_info->current_alloc_cust;
        }
        $male_perc = 0;
        $female_perc = 0;
        if($small_business != 0) {
            $male_perc = $male_cust / $small_business;
            $female_perc = 1 - $male_perc;
            $small_business *= $ip;
        }

        return (object)['cust_revenue' => $cust_revenue, 'tot_retail_txn_value' => $tot_retail_txn_value, 'num_of_txn' => $num_of_txn, 'people_benefited' => $people_benefited,
            'small_business' => $small_business, 'male_perc' => $male_perc, 'female_perc' => $female_perc];
    }

    private function compare_with_floor_returns($data)
    {
        $report_date = session('report_date');
        $days = $report_date->diffInDays(Carbon::parse($data['alloc_date']));
        $years = to_years($days);
        $floor_returns = $data['inv_amount'] * $years * $data['floor_rate'];
        $earnings = ($data['earnings'] < $floor_returns) ? $floor_returns : $data['earnings'];
        return $earnings;
    }

    private function get_forex_rate($base, $quote){
      $str = "{$base}_{$quote}";

      $this->$str = get_forex_by_date($base, $quote, session('report_date')->toDateString());
      return $this->$str;
    }

    public function get_alloc_date($fund_code)
    {
        $fund = DB::selectOne("select alloc_date from capital_funds where fund_code = '{$fund_code}'");
        return $fund->alloc_date;
    }


}
