<?php

namespace App\Providers;

use App\Services\RsiAccountVerificationService;
use Illuminate\Support\ServiceProvider;

class RsiAccountVerificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(RsiAccountVerificationService::class, function ($app) {
            return new RsiAccountVerificationService(config('killboard.rsi_verification'));
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
