<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\HomePage;
use App\Livewire\Service\UploadLog;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------H------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', HomePage::class)->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::group(['prefix' => 'services'], function () {
        Route::get('upload-log', UploadLog::class)->name('service.upload-log');
    });
});
