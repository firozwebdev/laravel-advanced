<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CurrencyConverter\CurrencyApiService;
use App\Services\CurrencyConverter\CurrencyApiInterface;

class ThirdPartyServicesProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CurrencyApiInterface::class, CurrencyApiService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
