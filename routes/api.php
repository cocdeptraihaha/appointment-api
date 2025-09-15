<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\AppointmentTypeController;
use App\Http\Controllers\Api\SettingController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Appointment Routves
Route::apiResource('appointments', AppointmentController::class);

Route::apiResource('appointment_types', AppointmentTypeController::class);

// Contact routes - specific routes must come before apiResource
Route::get('contacts/paginated', [ContactController::class, 'paginated']);
Route::apiResource('contacts', ContactController::class);

Route::get('services', [ServiceController::class, 'index']);

Route::get('staff', [StaffController::class, 'index']);

Route::get('settings', [SettingController::class, 'index']);

