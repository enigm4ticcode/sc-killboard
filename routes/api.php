<?php

use App\Http\Controllers\Api\KillsApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('kills')->group(function () {
        Route::post('/', [KillsApiController::class, 'create'])
            ->middleware(['throttle:submit-kill', 'api-key'])
            ->name('api.kills.create');
    });
});
