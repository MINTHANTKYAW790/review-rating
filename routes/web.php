<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleLoginController;
use Illuminate\Support\Facades\Session;

Route::get('language/{locale}', function ($locale) {
    Session::put('locale', $locale);
    return redirect()->back();
})->name('language.switch');

Route::get('/auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);

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


Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->name('dashboard');

    Route::view('profile', 'profile')
        ->name('profile');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('store', StoreController::class)->names('store');
    Route::resource('staff', StaffController::class)->names('staff');
    Route::resource('review', ReviewController::class)->names('review');
    Route::get('/qr/download/{store}', [StoreController::class, 'downloadQr'])->name('qr.download');
    Route::get('/qr/download/{store}/{template}', [StoreController::class, 'downloadStyledQr'])->name('qr.download.template');
    Route::get('/qr/image/{store}', [StoreController::class, 'getQrImage'])->name('qr.image');
    Route::delete('/review/{review}/comment', [ReviewController::class, 'deleteComment'])->name('review.deleteComment');
    Route::get('/public/qr/qr-preview', [StoreController::class, 'showQr'])->name('public.qr-preview');
});
Route::get('/public/qr/{slug}', [StoreController::class, 'showStore'])->name('public.review');
Route::post('/public/qr/{slug}', [StoreController::class, 'submitReview'])->name('public.submit-review');



require __DIR__ . '/auth.php';
