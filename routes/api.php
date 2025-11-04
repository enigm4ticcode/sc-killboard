<?php

use App\Http\Controllers\Api\KillsApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('kills')->group(function () {
        Route::get('/', [KillsApiController::class, 'index'])
            ->middleware(['throttle:api', 'cache.response:60'])
            ->name('api.kills.index');

        Route::post('/', [KillsApiController::class, 'create'])
            ->middleware(['throttle:submit-kill', 'api-key'])
            ->name('api.kills.create');
    });
});
