<?php

use App\Http\Controllers\Auth\BladeAuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::prefix('blade')->name('blade.')->group(function () {
    // Auth (guest only)
    Route::middleware('guest')->group(function () {
        Route::get('login', [BladeAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [BladeAuthController::class, 'login']);
        Route::get('register', [BladeAuthController::class, 'showRegister'])->name('register');
        Route::post('register', [BladeAuthController::class, 'register']);
    });

    // Authenticated routes
    Route::middleware(['auth', 'not.banned'])->group(function () {
        Route::post('logout', [BladeAuthController::class, 'logout'])->name('logout');

        // Chat
        Route::get('/', [ChatController::class, 'index'])->name('home');
        Route::get('conversations', [ChatController::class, 'index'])->name('conversations');
        Route::get('conversations/{conversation}', [ChatController::class, 'show'])->name('conversations.show');
        Route::post('messages', [ChatController::class, 'store'])->name('messages.store');
        Route::post('conversations/{conversation}/read', [ChatController::class, 'markRead'])->name('messages.read');
        Route::post('conversations/start', [ChatController::class, 'startConversation'])->name('conversations.start');
        Route::post('typing', [ChatController::class, 'typing'])->name('typing');
        Route::get('users/search', [ChatController::class, 'searchUsers'])->name('users.search');

        // Superadmin
        Route::middleware('superadmin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
            Route::get('users', [AdminController::class, 'users'])->name('users');
            Route::delete('users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
            Route::post('users/{user}/ban', [AdminController::class, 'banUser'])->name('users.ban');
            Route::get('messages', [AdminController::class, 'messages'])->name('messages');
            Route::delete('messages/{message}', [AdminController::class, 'destroyMessage'])->name('messages.destroy');
        });
    });
});
