<?php

use App\Http\Controllers\Auth\VueAuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::prefix('vue')->name('vue.')->group(function () {
    // Auth API
    Route::middleware('guest')->group(function () {
        Route::post('login', [VueAuthController::class, 'login'])->name('login');
        Route::post('register', [VueAuthController::class, 'register'])->name('register');
    });

    // Authenticated API
    Route::middleware(['auth', 'not.banned'])->group(function () {
        Route::post('logout', [VueAuthController::class, 'logout'])->name('logout');
        Route::get('user', [VueAuthController::class, 'user'])->name('user');

        // Chat API
        Route::get('conversations', [ChatController::class, 'index'])->name('conversations');
        Route::get('conversations/{conversation}', [ChatController::class, 'show'])->name('conversations.show');
        Route::post('messages', [ChatController::class, 'store'])->name('messages.store');
        Route::patch('messages/{message}', [ChatController::class, 'update'])->name('messages.update');
        Route::delete('messages/{message}', [ChatController::class, 'destroy'])->name('messages.destroy');
        Route::post('conversations/{conversation}/read', [ChatController::class, 'markRead'])->name('messages.read');
        Route::post('conversations/start', [ChatController::class, 'startConversation'])->name('conversations.start');
        Route::post('typing', [ChatController::class, 'typing'])->name('typing');
        Route::get('users/search', [ChatController::class, 'searchUsers'])->name('users.search');

        // Admin API
        Route::middleware('superadmin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
            Route::get('users', [AdminController::class, 'users'])->name('users');
            Route::delete('users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
            Route::post('users/{user}/ban', [AdminController::class, 'banUser'])->name('users.ban');
            Route::get('messages', [AdminController::class, 'messages'])->name('messages');
            Route::delete('messages/{message}', [AdminController::class, 'destroyMessage'])->name('messages.destroy');
        });
    });

    // Vue SPA catch-all (harus di paling bawah)
    Route::get('/{any?}', function () {
        return view('vue.app');
    })->where('any', '.*')->name('spa');
});
