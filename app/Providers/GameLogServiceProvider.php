<?php

namespace App\Providers;

use App\Services\GameLogService;
use Illuminate\Support\ServiceProvider;

class GameLogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(GameLogService::class, function ($app) {
            return new GameLogService(config('gamelog'));
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
