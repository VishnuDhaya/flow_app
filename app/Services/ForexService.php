<?php

namespace App\Services;

use Carbon\Carbon;
use \GuzzleHttp\Client;
use DB;

class ForexService
{


    private $LATEST_FOREX_ENDPOINT = "https://api.currencyapi.com/v3/latest";
    private $HISTORIC_FOREX_ENDPOINT = "https://api.currencyapi.com/v3/historical";


    /**
     * Get all market currencies along with USD and EUR
     */
    private function get_currencies(){
        $market_currencies = DB::table('markets')->pluck('currency_code')->toArray();
        return array_merge(['USD', 'EUR'], $market_currencies);
    }

    /**
     * Get the forex rates at the time of execution (give or take 30 seconds)
     */
    public function fetch_current_forex_rates(){

        $currencies = $this->get_currencies();
        $api_key = env('FREE_CURRENCY_API_KEY');
        $url = $this->LATEST_FOREX_ENDPOINT."?apikey={$api_key}";
        $date = Carbon::now()->toDateString();
        $this->fetch_forex_rates($currencies, $url, $date);
    }

    /**
     * Get the forex rates on a specific date
     */


    public function generate_forex(){
        $start_date=carbon::now()->startOfYear()->endOfMonth();
        $end_date=carbon::now()->subMonth()->endOfMonth();
        while($start_date != $end_date){
            echo ($start_date);
            $this->fetch_forex_rates_for_date($start_date->format('Y-m-d'));
            $start_date->addMonthNoOverflow()->endOfMonth();
        }
    }


    public function fetch_forex_rates_for_date($date_str){
        $currencies = $this->get_currencies();
        $api_key = env('FREE_CURRENCY_API_KEY');
        $url = $this->HISTORIC_FOREX_ENDPOINT."?apikey={$api_key}";
        $url .= "&date={$date_str}";
        $this->fetch_forex_rates($currencies, $url, $date_str);
    }


    /**
     * Makes api call to fetch the forex rates for the supplied currencies and date and inserts into forex_rates table
     */
    private function fetch_forex_rates($currencies, $url, $date){
        $client = new \GuzzleHttp\Client();
        foreach ($currencies as $base){
            $final_url = $url."&base_currency=$base";
            $request = new \GuzzleHttp\Psr7\Request('GET', $final_url);
            $response = $client->sendRequest($request);
            $body = json_decode(trim($response->getBody()));
            foreach ($currencies as $quote){
                if ($base != $quote){
                    $forex_rate = $body->data->$quote->value;
                    DB::table('forex_rates')->insert(['base' => $base, 'quote' => $quote, 'forex_rate' => $forex_rate, 'forex_date' => $date]);
                }
            }
        }
    }

}
