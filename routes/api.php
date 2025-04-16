<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

// GET
Route::get('/events', [EventController::class, 'index']); // Pro získání všech kvízů
Route::get('/upcoming-events', [EventController::class, 'upcoming']);
Route::get('/events/next', [EventController::class, 'next']);
Route::get('/events/{id}', [EventController::class, 'show']); // Pro zobrazení konkrétního kvízu
Route::get('/events/{id}/teams', [TeamController::class, 'showTeamsByEvent']); // Pro získání týmů, které se zúčastnily daného kvízu
Route::get('/event-results/{id?}', [ResultController::class, 'showResultsByEvent']); // Pro získání výsledků daného kvízu

Route::get('/events/{id?}/registrations', [RegistrationController::class, 'showRegistrations']); // Pro získání výsledků daného kvízu


Route::get('/teams', [TeamController::class, 'index']); // Pro získání všech týmů s počtem účastí
Route::get('/teams/{id}', [TeamController::class, 'show']); // Tým podle ID

// POST
Route::post('/events/{id}/register', [RegistrationController::class, 'register']);