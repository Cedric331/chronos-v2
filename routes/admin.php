<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/chronos-admin', [AdminController::class, 'index'])->name('admin.index')->middleware('isAdminOrHasPermission');
    Route::post('/chronos-admin/team/store', [AdminController::class, 'createTeam'])->name('admin.team.store')->middleware('isAdminOrHasPermission');
    Route::put('/chronos-admin/team/typedays', [AdminController::class, 'refreshTypeDays'])->name('admin.team.typedays')->middleware('isAdmin');
});
