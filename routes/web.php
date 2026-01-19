<?php

use App\Http\Controllers\Admin\Account\ProfileController;
use App\Http\Controllers\Admin\Account\SecurityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware('auth', 'verified')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 用户管理
    Route::resource('user', UserController::class);

    // 通知
    Route::get('/account/notification', [NotificationController::class, 'index'])->name('notification.index');
    Route::post('/account/notification/{id}/read', [NotificationController::class, 'read'])->name('notification.read');
    Route::post('/account/notification/read-all', [NotificationController::class, 'readAll'])->name('notification.read-all');
    Route::delete('/account/notification/{id}', [NotificationController::class, 'destroy'])->name('notification.destroy');

    // Profile
    Route::get('/account/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/account/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/account/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Security
    Route::get('/account/security', [SecurityController::class, 'show'])->name('security.show');
});

require __DIR__.'/auth.php';
