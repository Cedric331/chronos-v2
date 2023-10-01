<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

    Route::post('/login', [AuthenticatedSessionController::class, 'loginApi']);
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

//Route::group([
//    'middleware' => ['auth:sanctum'],
//    'prefix' => 'api'
//], function ($router) {
//
//    Route::get('/user', function (Request $request) {
//        return $request->user();
//    });
//    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
//    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
//
//});

