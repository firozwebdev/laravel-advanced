<?php


use App\Services\CurrencyApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Services\CurrencyConverter\CurrencyApiInterface;

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

Route::get('/sync/invoices/unpaid', function(Request $request, CurrencyApiInterface $converter) {

    return [
        ['id' => 1, 'client_id' => 3, 'currency' => 'CAD', 'amount' => $converter->convert('USD','CAD',400)],
        ['id' => 4, 'client_id' => 6, 'currency' => 'EUR', 'amount' => $converter->convert('USD','EUR',200)],
        ['id' => 5, 'client_id' => 2, 'currency' => 'USD', 'amount' => $converter->convert('USD','EUR',300)],
    ];
});
