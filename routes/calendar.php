<?php

use App\Http\Controllers\GoogleController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')->group(function () {

    Route::get('/redirect/google', [GoogleController::class, 'redirectToGoogle'])->name('socialite.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
});
