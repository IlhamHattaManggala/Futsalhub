<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TacticController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\SuperadminController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\SitemapController;


// Route::get('/debug-ini', function() {
//     return [
//         'loaded_ini' => php_ini_loaded_file(),
//         'curl.cainfo' => ini_get('curl.cainfo'),
//         'openssl.cafile' => ini_get('openssl.cafile'),
//         'php_version' => PHP_VERSION,
//     ];
// });

// Public & Auth
Route::get('/', function () {
    $landingStats = app(\App\Http\Controllers\SuperadminController::class)->getLandingApi()->getData(true);
    return view('welcome', compact('landingStats'));
})->name('landing');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('/manifest.json', function () {
    $logo = \App\Models\Setting::get('web_logo', 'images/logo.png');
    $favicon = \App\Models\Setting::get('web_favicon', 'favicon.ico');

    $logoExt = pathinfo($logo, PATHINFO_EXTENSION);
    $favExt = pathinfo($favicon, PATHINFO_EXTENSION);

    $logoMime = $logoExt === 'webp' ? 'image/webp' : ($logoExt === 'png' ? 'image/png' : 'image/x-icon');
    $favMime = $favExt === 'webp' ? 'image/webp' : ($favExt === 'png' ? 'image/png' : 'image/x-icon');

    return response()->json([
        "name" => \App\Models\Setting::get('web_name', 'FutsalHub'),
        "short_name" => \App\Models\Setting::get('web_name', 'FutsalHub'),
        "description" => \App\Models\Setting::get('web_description', 'Sistem informasi manajemen tim futsal modern terintegrasi.'),
        "start_url" => "/",
        "background_color" => "#0f172a",
        "theme_color" => "#10b981",
        "display" => "standalone",
        "orientation" => "portrait",
        "icons" => [
            [
                "src" => asset($favicon),
                "sizes" => "192x192",
                "type" => $favMime
            ],
            [
                "src" => asset($logo),
                "sizes" => "512x512",
                "type" => $logoMime
            ]
        ]
    ])->header('Content-Type', 'application/json');
});

Route::get('/privacy-policy', function () {
    return view('privacy');
})->name('privacy');

Route::get('/terms-and-conditions', function () {
    return view('terms');
})->name('terms');

// Dynamic image proxy for Google Drive cloud storage
Route::get('/images/{directory}/{filename}', function ($directory, $filename) {
    // Only proxy specific folders to prevent unauthorized access
    $allowedDirectories = ['Task Proof', 'Receipts', 'QRIS', 'Logos', 'Avatars'];

    if (in_array($directory, $allowedDirectories)) {
        $path = $directory . '/' . $filename;

        if (\Illuminate\Support\Facades\Storage::disk('google')->exists($path)) {
            $file = \Illuminate\Support\Facades\Storage::disk('google')->get($path);
            $mimeType = \Illuminate\Support\Facades\Storage::disk('google')->mimeType($path) ?? 'image/jpeg';

            return response($file, 200)->header('Content-Type', $mimeType);
        }
    }

    // Fallback: If not found on Google Drive, check the local public storage
    $localPath = public_path('images/' . $directory . '/' . $filename);
    if (file_exists($localPath)) {
        $mimeType = mime_content_type($localPath) ?: 'image/jpeg';
        return response()->file($localPath, ['Content-Type' => $mimeType]);
    }

    abort(404);
})->where('directory', '[a-zA-Z0-9_ -]+')->where('filename', '[a-zA-Z0-9_.-]+');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Email Verification Routes
Route::get('/email/verify', function () {
    if (Auth::user()->hasVerifiedEmail()) {
        $slug = Auth::user()->isSuperAdmin() ? 'superadmin' : (Auth::user()->slug ?? 'user');
        return redirect()->route('dashboard', ['slug' => $slug]);
    }
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    $request->fulfill();

    $user = Auth::user();
    $slug = $user->isSuperAdmin() ? 'superadmin' : ($user->slug ?? 'user');
    return redirect()->route('dashboard', ['slug' => $slug])->with('success', 'Email Anda berhasil diverifikasi!');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Link verifikasi baru telah dikirim ke alamat email Anda.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Account Reactivation Routes
Route::post('/account/reactivate/send', [AuthController::class, 'sendReactivationEmail'])->name('account.reactivate.send');
Route::get('/account/reactivate/{id}', [AuthController::class, 'reactivateAccount'])->name('account.reactivate')->middleware('signed');

// Google OAuth routes
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
Route::get('/register/google/complete', [GoogleAuthController::class, 'showCompleteRegistration'])->name('register.google.complete');
Route::post('/register/google/complete', [GoogleAuthController::class, 'completeRegistration'])->name('register.google.complete.post');

// Push Subscriptions
Route::middleware(['auth'])->group(function () {
    Route::post('/v1/push-subscriptions', [PushSubscriptionController::class, 'store'])->name('push-subscriptions.store');
    Route::delete('/v1/push-subscriptions', [PushSubscriptionController::class, 'destroy'])->name('push-subscriptions.destroy');
});

// Superadmin Global Panel
Route::middleware(['auth', 'role:superadmin'])->prefix('v1/superadmin')->group(function () {
    Route::get('/dashboard', [SuperadminController::class, 'index'])->name('superadmin.dashboard');

    Route::get('/teams', [SuperadminController::class, 'teams'])->name('superadmin.teams');
    Route::post('/teams', [SuperadminController::class, 'storeTeam'])->name('superadmin.teams.store');
    Route::get('/teams/{id}', [SuperadminController::class, 'showTeam'])->name('superadmin.teams.show');
    Route::put('/teams/{id}', [SuperadminController::class, 'updateTeam'])->name('superadmin.teams.update');
    Route::delete('/teams/{id}', [SuperadminController::class, 'destroyTeam'])->name('superadmin.teams.destroy');

    Route::get('/users', [SuperadminController::class, 'users'])->name('superadmin.users');
    Route::post('/users', [SuperadminController::class, 'storeUser'])->name('superadmin.users.store');
    Route::get('/users/{id}', [SuperadminController::class, 'showUser'])->name('superadmin.users.show');
    Route::put('/users/{id}', [SuperadminController::class, 'updateUser'])->name('superadmin.users.update');
    Route::delete('/users/{id}', [SuperadminController::class, 'destroyUser'])->name('superadmin.users.destroy');
    Route::post('/users/{id}/toggle-lock', [SuperadminController::class, 'toggleUserLock'])->name('superadmin.users.toggle-lock');

    // Premium Verifications
    Route::get('/payments', [\App\Http\Controllers\SubscriptionController::class, 'adminIndex'])->name('superadmin.payments.index');
    Route::get('/payments/{id}', [\App\Http\Controllers\SubscriptionController::class, 'showPaymentAdmin'])->name('superadmin.payments.show');

    // Settings
    Route::get('/settings/profile', [SuperadminController::class, 'editProfile'])->name('superadmin.settings.profile');
    Route::put('/settings/profile', [SuperadminController::class, 'updateProfile'])->name('superadmin.settings.profile.update');
    Route::get('/settings/website', [SuperadminController::class, 'editWebsite'])->name('superadmin.settings.website');
    Route::put('/settings/website', [SuperadminController::class, 'updateWebsite'])->name('superadmin.settings.website.update');
    Route::get('/settings/landing', [SuperadminController::class, 'editLanding'])->name('superadmin.settings.landing');
    Route::put('/settings/landing', [SuperadminController::class, 'updateLanding'])->name('superadmin.settings.landing.update');
});

// Multi-Tenant Team Area
Route::middleware(['auth', 'verified', 'tenant'])->prefix('v1/{slug}')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Tactical Board (Only Coach & No CRUD)
    Route::middleware(['role:coach'])->group(function () {
        Route::get('/tactical-board', [TacticController::class, 'index'])->name('tactics.index');
        Route::post('/tactical-board/save', [TacticController::class, 'save'])->name('tactics.save');
    });

    // Schedules & Attendances
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
    Route::get('/schedules/{id}/attendance', [ScheduleController::class, 'attendance'])->name('schedules.attendance');
    Route::post('/schedules/{id}/attendance', [ScheduleController::class, 'saveAttendance'])->name('schedules.attendance.save');
    Route::get('/schedules/{id}/scan', [ScheduleController::class, 'scanAttendance'])->name('schedules.scan');
    Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
    Route::get('/schedules/{id}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
    Route::put('/schedules/{id}', [ScheduleController::class, 'update'])->name('schedules.update');

    // Player Tasks
    Route::get('/tasks', [\App\Http\Controllers\TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [\App\Http\Controllers\TaskController::class, 'store'])->name('tasks.store');
    Route::delete('/tasks/{id}', [\App\Http\Controllers\TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/tasks/{id}/start', [\App\Http\Controllers\TaskController::class, 'start'])->name('tasks.start');
    Route::post('/tasks/{id}/complete', [\App\Http\Controllers\TaskController::class, 'complete'])->name('tasks.complete');

    // Player & Coach Management (Management role only)
    Route::middleware(['role:management'])->group(function () {
        Route::get('/players', [PlayerController::class, 'index'])->name('players.index');
        Route::post('/players', [PlayerController::class, 'store'])->name('players.store');
        Route::delete('/players/{id}', [PlayerController::class, 'destroy'])->name('players.destroy');
        Route::post('/players/{id}/toggle-status', [PlayerController::class, 'toggleStatus'])->name('players.toggle-status');

        Route::get('/coaches', [\App\Http\Controllers\CoachController::class, 'index'])->name('coaches.index');
        Route::post('/coaches', [\App\Http\Controllers\CoachController::class, 'store'])->name('coaches.store');
        Route::delete('/coaches/{id}', [\App\Http\Controllers\CoachController::class, 'destroy'])->name('coaches.destroy');
        Route::post('/coaches/{id}/toggle-status', [\App\Http\Controllers\CoachController::class, 'toggleStatus'])->name('coaches.toggle-status');

        // QRIS Tim
        Route::post('/finances/qris', [FinanceController::class, 'updateQris'])->name('finances.qris.update');
    });

    // Player Show Route (Accessible to all authenticated tenant users)
    Route::get('/players/{id}', [PlayerController::class, 'show'])->name('players.show');

    // Matches & Statistics
    Route::get('/matches', [MatchController::class, 'index'])->name('matches.index');
    Route::post('/matches', [MatchController::class, 'store'])->name('matches.store');
    Route::get('/matches/{id}/stats', [MatchController::class, 'stats'])->name('matches.stats');
    Route::post('/matches/{id}/stats', [MatchController::class, 'saveStats'])->name('matches.stats.save');

    // Finances (Kas Keuangan)
    Route::middleware(['role:management,coach'])->group(function () {
        Route::get('/finances', [FinanceController::class, 'index'])->name('finances.index');
        Route::post('/finances', [FinanceController::class, 'store'])->name('finances.store');
        Route::get('/finances/export', [FinanceController::class, 'export'])->name('finances.export');
    });

    // Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');

    // Player Only Routes
    Route::middleware(['role:player'])->group(function () {
        Route::post('/schedules/{id}/receipt', [ScheduleController::class, 'uploadReceipt'])->name('schedules.attendance.receipt');
    });

    // Subscription Upgrade
    Route::get('/upgrade', [\App\Http\Controllers\SubscriptionController::class, 'showUpgrade'])->name('subscription.upgrade');
    Route::post('/upgrade', [\App\Http\Controllers\SubscriptionController::class, 'submitUpgrade'])->name('subscription.submit');
    Route::get('/upgrade/payment/{merchantRef}', [\App\Http\Controllers\SubscriptionController::class, 'showPayment'])->name('subscription.payment');

    // Settings
    Route::get('/settings/profile', [DashboardController::class, 'editProfile'])->name('settings.profile');
    Route::put('/settings/profile', [DashboardController::class, 'updateProfile'])->name('settings.profile.update');
    Route::post('/settings/profile/close', [DashboardController::class, 'closeAccount'])->name('settings.profile.close');

    Route::middleware(['role:management'])->group(function () {
        Route::get('/settings/team', [DashboardController::class, 'editTeam'])->name('settings.team');
        Route::put('/settings/team', [DashboardController::class, 'updateTeam'])->name('settings.team.update');
    });
});

// TriPay Webhook Callback (Public, no auth/tenant middleware)
Route::post('/tripay/callback', [\App\Http\Controllers\TripayCallbackController::class, 'handleCallback'])->name('tripay.callback');

// API Settings for AJAX Real-time Landing Page Updates
Route::get('/api/settings/landing', [SuperadminController::class, 'getLandingApi'])->name('api.settings.landing');
