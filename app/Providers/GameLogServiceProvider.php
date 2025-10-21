<?php

namespace App\Providers;

use App\Services\GameLogService;
use App\Services\VehicleService;
use Illuminate\Support\ServiceProvider;

class GameLogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(GameLogService::class, function () {
            return new GameLogService(
                app(VehicleService::class),
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
