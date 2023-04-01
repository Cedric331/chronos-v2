<?php

use App\Http\Controllers\CalendarController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [CalendarController::class, 'generateDaysWithHolidays'])->middleware(['auth', 'verified'])->name('dashboard');

});
