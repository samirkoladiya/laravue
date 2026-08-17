<?php

use App\Http\Controllers\Admin\AdminAnalyticsController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminFaqController;
use App\Http\Controllers\Admin\AdminInquiryController;
use App\Http\Controllers\Admin\AdminPasswordController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminTeamController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AnalyticsTrackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index']);

Route::get('/about', function () {
    return Inertia::render('About');
});

Route::get('/services', function () {
    return Inertia::render('Services');
});

Route::get('/portfolio', function () {
    return Inertia::render('Portfolio');
});

Route::get('/team', [TeamController::class, 'index']);

Route::get('/pricing', function () {
    return Inertia::render('Pricing');
});

Route::get('/contact', function () {
    return Inertia::render('Contact');
});

Route::post('/contact', [InquiryController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

// CSRF-exempt (see bootstrap/app.php): the JS client is framework-agnostic
// and can't reliably attach an Inertia CSRF token, and unload-time beacon
// calls can't pre-flight one. Only writes low-value analytics rows, never
// affects another user's state - protected by the rate limiter + payload
// validation instead.
Route::post('/analytics/track', [AnalyticsTrackController::class, 'store'])
    ->middleware('throttle:analytics')
    ->name('analytics.track');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.index');
    Route::post('/admin/login', [AdminAuthController::class, 'loginPost'])
        ->middleware('throttle:6,1')
        ->name('admin.login.post');

    Route::get('/admin/signup', [AdminAuthController::class, 'signup'])->name('admin.signup.index');
    Route::post('/admin/signup', [AdminAuthController::class, 'signupPost'])
        ->middleware('throttle:6,1')
        ->name('admin.signup.post');

    Route::get('/admin/forgot-password', [AdminAuthController::class, 'forgorpassword'])->name('admin.forgotpassword.index');
    Route::post('/admin/forgot-password', [AdminAuthController::class, 'forgorPasswordPost'])
        ->middleware('throttle:6,1')
        ->name('admin.forgotpassword.post');

    Route::get('/admin/verify-otp', [AdminAuthController::class, 'verifyotp'])->name('admin.verifyotp.index');
    Route::post('/admin/verify-otp', [AdminAuthController::class, 'verifyotpPost'])
        ->middleware('throttle:10,1')
        ->name('admin.verifyotp.post');
    Route::post('/admin/verify-otp/resend', [AdminAuthController::class, 'verifyotpResend'])
        ->middleware('throttle:3,1')
        ->name('admin.verifyotp.resend');

    Route::get('/admin/reset-password', [AdminAuthController::class, 'resetpassword'])->name('admin.resetpassword.index');
    Route::post('/admin/reset-password', [AdminAuthController::class, 'resetPasswordPost'])
        ->middleware('throttle:6,1')
        ->name('admin.resetpassword.post');
});

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::get('/admin/inquiry', [AdminInquiryController::class, 'index'])->name('admin.inquiry.index');

    Route::get('/admin/team', [AdminTeamController::class, 'index'])->name('admin.team.index');
    Route::get('/admin/team/create', [AdminTeamController::class, 'create'])->name('admin.team.create');
    Route::post('/admin/team', [AdminTeamController::class, 'store'])->name('admin.team.store');
    Route::get('/admin/team/{team}/edit', [AdminTeamController::class, 'edit'])->name('admin.team.edit');
    // File uploads on PUT/PATCH aren't reliably parsed as multipart across
    // server stacks, so the update route uses POST like the store route.
    Route::post('/admin/team/{team}', [AdminTeamController::class, 'update'])->name('admin.team.update');
    Route::delete('/admin/team/{team}', [AdminTeamController::class, 'destroy'])->name('admin.team.destroy');

    Route::get('/admin/faq', [AdminFaqController::class, 'index'])->name('admin.faq.index');
    Route::get('/admin/faq/create', [AdminFaqController::class, 'create'])->name('admin.faq.create');
    Route::post('/admin/faq', [AdminFaqController::class, 'store'])->name('admin.faq.store');
    Route::get('/admin/faq/{faq}/edit', [AdminFaqController::class, 'edit'])->name('admin.faq.edit');
    Route::post('/admin/faq/{faq}', [AdminFaqController::class, 'update'])->name('admin.faq.update');
    Route::delete('/admin/faq/{faq}', [AdminFaqController::class, 'destroy'])->name('admin.faq.destroy');

    Route::get('/admin/analytics', [AdminAnalyticsController::class, 'index'])->name('admin.analytics.index');
    Route::get('/admin/analytics/realtime', [AdminAnalyticsController::class, 'realtime'])->name('admin.analytics.realtime');
    Route::get('/admin/analytics/export', [AdminAnalyticsController::class, 'export'])->name('admin.analytics.export');

    Route::get('/admin/profile', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::post('/admin/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
    Route::get('/admin/profile/password', [AdminPasswordController::class, 'edit'])->name('admin.password.edit');
    Route::post('/admin/profile/password', [AdminPasswordController::class, 'update'])->name('admin.password.update');
});
