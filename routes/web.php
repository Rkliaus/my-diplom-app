<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataController;
// use App\Http\Controllers\VacancyController;

Route::get('/', function () {
    return view('home');
});

Route::get('/data', action: [DataController::class, 'index']);


// Route::get(uri: '/data/getdata', [DataController::class, 'getData']);

// Route::get('/vacancies', [DataController::class, 'index']);








// Route::get('/vacancies', [DataController::class, 'getVacancies']);
// // Route::get('/vacancies', [VacancyController::class, 'index']);
// Route::middleware('auth:sanctum')->group(function () {
//     Route::apiResource('vacancies', VacancyController::class);
// });
// Route::get('/vacancies', [VacancyController::class, 'index']);
// Route::post('/vacancies', [VacancyController::class, 'store']);
// Route::put('/vacancies/{id}', [VacancyController::class, 'update']);
// Route::delete('/vacancies/{id}', [VacancyController::class, 'destroy']);
