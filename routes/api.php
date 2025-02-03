<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\TeamController;

Route::get("/test-me", function () {
    return 'Hello from Laravel!';
});

Route::get('/events', [EventController::class, 'index']); // Pro získání všech kvízů
Route::get('/events/{id}', [EventController::class, 'show']); // Pro zobrazení konkrétního kvízu
Route::get('/teams', [TeamController::class, 'index']); // Pro získání všech týmů s počtem účastí
Route::get('/events/{id}/teams', [TeamController::class, 'showTeamsByEvent']); // Pro získání týmů, které se zúčastnily daného kvízu
Route::get('/event-results/{id?}', [ResultController::class, 'showResultsByEvent']); // Pro získání výsledků daného kvízu
