<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DiagnosisApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/consultations/active', [DiagnosisApiController::class, 'getActiveConsultations']);
    Route::post('/consultations/{id}/diagnosis', [DiagnosisApiController::class, 'storeDiagnosis']);
});
