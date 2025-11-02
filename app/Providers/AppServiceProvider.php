<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('testing')) {
            config()->set('cache.default', 'array');
            config()->set('cache.limiter', 'array');
            config()->set('cache.stores.redis.driver', 'array');

            // Ensure nothing tries to use Redis during tests
            config()->set('database.redis', []);
            config()->set('queue.default', 'sync');
            config()->set('session.driver', 'array');
            config()->set('scout.connection', null);
            config()->set('scout.queue.connection', 'sync');
            config()->set('scout.queue.queue', 'sync');
            // Disable Scout syncing entirely in tests
            config()->set('scout.driver', 'null');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('submit-kill', function (Request $request) {
            return Limit::perMinute(20)->by($request->user()?->id ?: $request->ip());
        });
    }
}
