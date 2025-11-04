<?php

namespace App\Providers;

use App\Services\ManufacturerService;
use App\Services\StarCitizenWikiService;
use Illuminate\Support\ServiceProvider;

class ManufacturerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ManufacturerService::class, function ($app) {
            return new ManufacturerService(
                app(StarCitizenWikiService::class),
                config('gamelog'),
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
