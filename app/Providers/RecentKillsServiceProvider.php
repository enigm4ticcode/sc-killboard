<?php

namespace App\Providers;

use App\Services\RecentKillsService;
use Illuminate\Support\ServiceProvider;

class RecentKillsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(RecentKillsService::class, function ($app) {
            $cacheKey = config('killboard.home_page.cache_key');
            $recentKillsDays = config('killboard.home_page.most_recent_kills_days');
            $itemsPerPage = config('killboard.pagination.kills_per_page');

            return new RecentKillsService($cacheKey, $recentKillsDays, $itemsPerPage);
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
