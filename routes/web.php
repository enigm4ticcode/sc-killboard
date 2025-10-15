<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\HomePage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Application web routes.
|
*/

Route::get('/', HomePage::class);
