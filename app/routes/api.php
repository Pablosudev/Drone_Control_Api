<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DroneController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



//DRONE ROUTES

Route::get('/drones', [DroneController::class, 'index']);
Route::post('/drones', [DroneController::class, 'store']);
Route::get('/drones/{drone}', [DroneController::class, 'show']);
Route::patch('/drones/{drone}', [DroneController::class, 'update']);