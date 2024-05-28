<?php

use RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/sync/invoices/unpaid', function(Request $request) {

    $rates = Cache::remember('currency_rates', now()->addHours(12), function() {
        $response = Http::withOptions([
        'verify' => 'C:/certificates/cacert.pem',
        ])->get('https://api.currencyapi.com/v3/latest', [
            'apikey' => 'cur_live_BaUSTWrL9z6vWPRCt1YK0xGKG665sIhPBq2dqwKm',
            'currencies' => 'EUR,USD,CAD'
        ]);
        if($response->ok()) {
            $rates =  $response->json()['data'];
            return $rates;
        }else{
            throw new RuntimeException('Currency Api Error');
        }

    });
    

    return [
        ['id' => 1, 'client_id' => 3, 'currency' => 'CAD', 'amount' => round($rates['CAD']['value'] * 100,2)],
        ['id' => 4, 'client_id' => 6, 'currency' => 'EUR', 'amount' => round($rates['EUR']['value'] * 200,2)],
        ['id' => 5, 'client_id' => 2, 'currency' => 'USD', 'amount' => round($rates['EUR']['value'] * 150,2)],
    ];
});
