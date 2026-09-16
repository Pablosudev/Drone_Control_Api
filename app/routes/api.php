<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DroneController;
use App\Http\Controllers\Api\DroneTelemetryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



//DRONE ROUTES
Route::get('/drones', [DroneController::class, 'index']);
Route::post('/drones', [DroneController::class, 'store']);
Route::get('/drones/{drone}', [DroneController::class, 'show']);
Route::patch('/drones/{drone}', [DroneController::class, 'update']);
//DRONE TELEMETRY
Route::post('/drones/{drone}/telemetry', [DroneTelemetryController::class, 'store']);
Route::get('/drones/{drone}/telemetry', [DroneTelemetryController::class, 'index']);