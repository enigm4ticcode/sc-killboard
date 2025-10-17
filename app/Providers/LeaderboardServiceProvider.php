<?php

namespace App\Providers;

use App\Services\LeaderboardService;
use Illuminate\Support\ServiceProvider;

class LeaderboardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(LeaderboardService::class, function ($app) {
            return new LeaderboardService(
                config('killboard.cache.leaderboards-cache-key'),
                config('killboard.leaderboards.number-of-positions'),
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
