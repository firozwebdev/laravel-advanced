<?php

namespace App\Services\CurrencyConverter;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;


Class CurrencyApiService implements CurrencyApiInterface
{
    public function convert( string $from, string $to, float $amount): float
    {
        $currencyApiKey = config('currency_api.key');

        $rates = Cache::remember('currency_rates', now()->addHours(12), function() use ($currencyApiKey) {
            $response = Http::withOptions([
            'verify' => 'C:/certificates/cacert.pem',
            ])->get('https://api.currencyapi.com/v3/latest', [
                'apikey' => $currencyApiKey,
                'currencies' => 'EUR,USD,CAD'
            ]);
            if($response->ok()) {
                $rates =  $response->json()['data'];
                return $rates;
            }else{
                throw new \RuntimeException('Currency Api Error');
            }

        });

        return round($rates[$to]['value'] * $amount);
    
    }
}