<?php

namespace App\Providers;

use App\Services\StarCitizenWikiService;
use App\Services\VehicleService;
use Illuminate\Support\ServiceProvider;

class VehicleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(VehicleService::class, function ($app) {
            return new VehicleService(
                app(StarCitizenWikiService::class),
                config('killboard.cache.vehicles-cache-key'),
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
