<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KillsApiController;

Route::prefix('api')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::prefix('kills')->group(function () {
            Route::post('/', [KillsApiController::class, 'create'])
                ->middleware(['api-key'])
                ->name('api.kills.create');
        });
    });
});
