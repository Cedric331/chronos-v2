<?php

use App\Http\Controllers\LinkShareController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware('isCoordinateur')->group(function () {
    Route::get('/team/{name}', [TeamController::class, 'show'])->name('team.show');
    Route::post('/team/update/{team}', [TeamController::class, 'update'])->name('team.update');
    Route::delete('/team/logo/delete/{team}', [TeamController::class, 'deleteLogo'])->name('logo.delete');
    Route::patch('/switch/team/{team}', [TeamController::class, 'switch'])->name('team.switch');

    Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('user.delete');
    Route::post('/user', [UserController::class, 'store'])->name('user.store');

});

Route::middleware('auth')->group(function () {
    Route::get('/information', [TeamController::class, 'getInformation'])->name('information.index');
    Route::patch('/user/{user}', [UserController::class, 'update'])->name('user.update');
});
