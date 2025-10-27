<?php

namespace App\Providers;

use App\Services\RsiStatusService;
use Illuminate\Support\ServiceProvider;

class RsiStatusServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(RsiStatusService::class, function ($app) {
            return new RsiStatusService(
                config('killboard.rsi_status.base_url'),
                config('killboard.rsi_status.index'),
                config('killboard.rsi_status.cache_key'),
                config('killboard.rsi_status.ttl')
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
