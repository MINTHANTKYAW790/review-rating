<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StoreNoteController;
use App\Http\Controllers\SuperAdmin\BillingSettingController;
use App\Http\Controllers\SuperAdmin\PaymentController;
use App\Http\Controllers\SuperAdmin\PlanController;
use App\Http\Controllers\SuperAdmin\SubscriptionController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Session;

Route::get('language/{locale}', function ($locale) {
    Session::put('locale', $locale);

    return redirect()->back();
})->name('language.switch');

Route::get('/auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::view('profile', 'profile')->name('profile');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::resource('roles', RoleController::class)->names('roles');
    Route::resource('users', UserController::class)->names('users');
    Route::resource('store', StoreController::class)->names('store');
    Route::resource('staff', StaffController::class)->names('staff');
    Route::resource('review', ReviewController::class)->only(['index', 'show', 'destroy'])->names('review');
    Route::patch('/review/{review}/triage', [ReviewController::class, 'updateTriage'])->name('review.triage');
    Route::post('/review/{review}/star', [ReviewController::class, 'toggleStar'])->name('review.star');
    Route::post('/review/{review}/done', [ReviewController::class, 'markDone'])->name('review.done');
    Route::post('/review/{review}/reopen', [ReviewController::class, 'reopen'])->name('review.reopen');
    Route::post('/review/{review}/mark-read', [ReviewController::class, 'markRead'])->name('review.mark-read');
    Route::delete('/review/{review}/comment', [ReviewController::class, 'deleteComment'])->name('review.deleteComment');
    Route::resource('notes', StoreNoteController::class);

    Route::get('/qr/download/{store}', [StoreController::class, 'downloadQr'])->name('qr.download');
    Route::get('/qr/download/{store}/{template}', [StoreController::class, 'downloadStyledQr'])->name('qr.download.template');
    Route::get('/qr/image/{store}', [StoreController::class, 'getQrImage'])->name('qr.image');
    Route::get('/public/qr/qr-preview', [StoreController::class, 'showQr'])->name('public.qr-preview');
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');

    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::get('/billing/add-store', [BillingController::class, 'addStore'])->name('billing.add-store');
    Route::get('/billing/extend', [BillingController::class, 'extend'])->name('billing.extend');
    Route::get('/billing/payments', [BillingController::class, 'payments'])->name('billing.payments');
    Route::post('/billing', [BillingController::class, 'store'])->name('billing.store');
    Route::get('/billing/{payment}', [BillingController::class, 'show'])->name('billing.show');

    Route::middleware('superadmin')->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [SuperAdminController::class, 'users'])->name('users.index');
        Route::get('/users/{user}', [SuperAdminController::class, 'showUser'])->name('users.show');
        Route::patch('/users/{user}/active', [SuperAdminController::class, 'toggleUser'])->name('users.active');
        Route::get('/stores', [SuperAdminController::class, 'stores'])->name('stores.index');
        Route::get('/stores/{store}', [SuperAdminController::class, 'showStore'])->name('stores.show');
        Route::patch('/stores/{store}/active', [SuperAdminController::class, 'toggleStore'])->name('stores.active');
        Route::resource('plans', PlanController::class)->except(['show']);
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('/billing-settings', [BillingSettingController::class, 'edit'])->name('billing-settings.edit');
        Route::put('/billing-settings', [BillingSettingController::class, 'update'])->name('billing-settings.update');
    });
});

Route::get('/public/qr/{slug}', [StoreController::class, 'showStore'])->name('public.review');
Route::post('/public/qr/{slug}', [StoreController::class, 'submitReview'])->name('public.submit-review');

require __DIR__.'/auth.php';
