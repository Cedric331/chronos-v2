<?php

use App\Http\Controllers\TeamScheduleController;
use Illuminate\Support\Facades\Route;

Route::middleware('isCoordinateur')->group(function () {
    Route::post('/team/schedule', [TeamScheduleController::class, 'store'])->name('team.schedule.store');
    Route::get('/team/check/schedule', [TeamScheduleController::class, 'checkHoraire'])->name('team.schedule.check');
    Route::patch('/mark-all-read', [TeamScheduleController::class, 'read'])->name('team.schedule.read');
    Route::patch('/mark-read/{alert}', [TeamScheduleController::class, 'readOne'])->name('team.schedule.readOne');

});
