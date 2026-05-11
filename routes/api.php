<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeaveRequestController;

// Auth Konvensional
Route::post('/login', [AuthController::class, 'login']);

// Auth OAuth (Google)
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::middleware('auth:sanctum')->group(function () {
   Route::post('/leave-requests', [LeaveRequestController::class, 'store']);
    
    Route::middleware('admin')->group(function () {
        Route::patch('/leave-requests/{id}/status', [LeaveRequestController::class, 'updateStatus']);
    });
    
});