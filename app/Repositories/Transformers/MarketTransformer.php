<?php
namespace App\Repositories\Transformers;

class MarketTransformer extends Transformer{
    public function transform($market){
        return [
            'market_name' => $market->name,
            'market_id' => $market->id,
            'country_code' => $market->country_code,
            'country_name' => $market->country_code
        ];
    }
}
