<?php

use App\Modules\Spa\Http\Controllers\SpaController;
use Illuminate\Support\Facades\Route;

// API-only + SPA frontend.
// Serve the Vue SPA for non-API routes, while leaving `/api/*`, `/sanctum/*`, and `/docs/*` untouched.
Route::get('/{any?}', [SpaController::class, 'index'])
    ->where('any', '^(?!api($|/)|sanctum($|/)|docs($|/)).*')
    ->name('spa');
