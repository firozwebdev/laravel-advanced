<?php

namespace App\Services\CurrencyConverter;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;


Class CurrencyApiService implements CurrencyApiInterface
{
    public function convert(string $from, string $to, float $amount)
    {
        $currencyApiKey = config('app.currency_api.key');

        $rates = Cache::remember('currency_rate', now()->addHours(12), function() use ($currencyApiKey) {
            $response = Http::withOptions([
                'verify' => 'C:/certificates/cacert.pem',
            ])->get('https://api.currencyapi.com/v3/latest', [
                'apikey' => $currencyApiKey,
                'currencies' => 'EUR,USD,CAD'
            ]);

            if ($response->ok()) {
                $jsonResponse = $response->json();

                if (isset($jsonResponse['data'])) {
                    return $jsonResponse['data'];
                } else {
                    \Log::error('Currency API response does not contain data key.', [
                        'response' => $jsonResponse
                    ]);
                    throw new \RuntimeException('Currency API response format error');
                }
            } else {
                \Log::error('Currency API request failed.', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \RuntimeException('Currency API request error');
            }
        });

        if (!isset($rates[$to])) {
            \Log::error("Currency rate for '{$to}' not found.", [
                'rates' => $rates
            ]);
            throw new \RuntimeException("Currency rate for '{$to}' not found");
        }

        return round($rates[$to]['value'] * $amount, 2);
    }

}