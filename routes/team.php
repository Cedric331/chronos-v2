<?php

use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;


Route::middleware('isCoordinateur')->group(function () {
    Route::get('/team/{name}', [TeamController::class, 'show'])->name('team.show');
    Route::post('/team/update/{team}', [TeamController::class, 'update'])->name('team.update');
    Route::delete('/team/logo/delete/{team}', [TeamController::class, 'deleteLogo'])->name('logo.delete');
});
