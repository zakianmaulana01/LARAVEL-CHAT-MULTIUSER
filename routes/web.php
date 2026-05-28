<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Redirect root to blade login if not authenticated
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('blade.home');
    }
    return redirect()->route('blade.login');
});
