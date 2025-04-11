<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\TeamEventsController;
use App\Http\Controllers\BirthdayController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    Route::get('/planning', [CalendarController::class, 'getPlanning']);
    Route::get('/auth/check', function () {
        return response()->json(['authenticated' => Auth::check()]);
    });

    // Routes pour le module d'échange de planning
    Route::middleware(['auth', 'exchange.enabled'])->group(function () {
        Route::get('/users/{user}/plannings', [PlanningController::class, 'getUserPlannings']);
        Route::get('/users/{user}/exchange-dates', [PlanningController::class, 'getExchangeDates']);
    });

    // Routes pour les widgets
    Route::middleware(['auth'])->group(function () {
        // Route pour récupérer les actualités
        Route::post('/fetch-news', [NewsController::class, 'fetchNews']);

        // Route pour récupérer les événements d'équipe et anniversaires
        Route::post('/team-events', [TeamEventsController::class, 'getTeamEvents']);

        // Route pour récupérer uniquement les anniversaires
        Route::post('/birthdays', [BirthdayController::class, 'getBirthdays']);
    });

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

