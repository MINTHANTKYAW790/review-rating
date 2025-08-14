<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');


// Route::middleware(['auth'])->group(function () {
// Route::get('/store/create', [StoreController::class, 'create'])->name('store.create');
// Route::post('/store', [StoreController::class, 'store'])->name('store.store');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('store', StoreController::class)->names('store');
Route::resource('staff', StaffController::class)->names('staff');
Route::get('/public/qr/qr-preview', [StoreController::class, 'showQr'])->name('public.qr-preview');
Route::get('/public/qr/{slug}', [StoreController::class, 'showStore'])->name('public.review');
Route::post('/public/qr/{slug}', [StoreController::class, 'submitReview'])->name('public.submit-review');
Route::resource('review', ReviewController::class)->names('review');

// });



require __DIR__ . '/auth.php';
