<?php

namespace App\Providers;

use App\Services\StarCitizenWikiService;
use Illuminate\Support\ServiceProvider;

class StarCitizenWikiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(StarCitizenWikiService::class, function ($app) {
            return new StarCitizenWikiService(config('wiki'));
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
