<?php

use App\Http\Controllers\InvoicePrintController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/invoices/{invoice}/print', [InvoicePrintController::class, 'show'])
    ->name('invoices.print')
    ->middleware(['web','auth']);
require __DIR__.'/auth.php';
