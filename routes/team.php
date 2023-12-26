<?php

use App\Http\Controllers\LinkTeamController;
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
    Route::post('/user/invitation', [UserController::class, 'sendInvitation'])->name('user.invitation');
    Route::get('/teams/activities', [TeamController::class, 'paginateActivities'])->name('team.activities');

});

Route::middleware('auth')->group(function () {
    Route::get('/information', [TeamController::class, 'getInformation'])->name('information.index');
    Route::post('/links', [LinkTeamController::class, 'store'])->name('link.store');
    Route::patch('/links/{link}', [LinkTeamController::class, 'update'])->name('link.patch');
    Route::delete('/links/{link}', [LinkTeamController::class, 'destroy'])->name('link.destroy');
    Route::patch('/user/{user}', [UserController::class, 'update'])->name('user.update');
});
