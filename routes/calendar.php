<?php

use App\Http\Controllers\CalendarController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [CalendarController::class, 'showGeneratedDaysWithHolidays'])->middleware(['auth', 'verified'])->name('dashboard');

});
