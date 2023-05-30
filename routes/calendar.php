<?php

use App\Http\Controllers\CalendarController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')->group(function () {

    Route::get('/planning', [CalendarController::class, 'getPlanning'])->name('planning');

    Route::post('/planning', [CalendarController::class, 'getPlanningCustom'])->name('planning.custom');
});
