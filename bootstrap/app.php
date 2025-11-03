<?php

use App\Console\Commands\GetAllVehiclesCommand;
use App\Http\Middleware\ApiKey;
use App\Http\Middleware\RsiNotVerified;
use App\Http\Middleware\RsiVerified;
use App\Http\Middleware\SetLocale;
use App\Jobs\CheckRsiStatusJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetLocale::class,
        ]);

        $middleware->alias([
            'rsi-verified' => RsiVerified::class,
            'rsi-not-verified' => RsiNotVerified::class,
            'api-key' => ApiKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->withSchedule(function (Schedule $schedule): void {
        $schedule->command(GetAllVehiclesCommand::class)->weekly()->thursdays()->at('23:00');
        $schedule->command('sail artisan scout:queue-import "App\Models\Player"')->daily();
        $schedule->command('sail artisan scout:queue-import "App\Models\Organizations"')->daily();
        $schedule->job(CheckRsiStatusJob::class)->everyFiveMinutes();
    })->create();
