<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\AppointmentTypeController;
use App\Http\Controllers\Api\SettingController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Appointment Routves
Route::apiResource('appointments', AppointmentController::class);

// Contact Routes: full CRUD operations
Route::get('contacts/paginated', [ContactController::class, 'paginated']);
Route::apiResource('contacts', ContactController::class);

// Service Routes (FE only needs GET list)
Route::get('services', [ServiceController::class, 'index']);

// Staff Routes (FE only needs GET list)
Route::get('staff', [StaffController::class, 'index']);
Route::put('staff/{id}', [\App\Http\Controllers\Api\StaffController::class, 'update']);
// Appointment Type Routes
Route::apiResource('appointment-types', AppointmentTypeController::class);
Route::post('appointment-types/{id}/restore', [AppointmentTypeController::class, 'restore']);

// Alternative routes with underscores for React compatibility
Route::get('appointment_types', [AppointmentTypeController::class, 'index']);
Route::post('appointment_types', [AppointmentTypeController::class, 'store']);
Route::get('appointment_types/{id}', [AppointmentTypeController::class, 'show']);
Route::put('appointment_types/{id}', [AppointmentTypeController::class, 'update']);
Route::delete('appointment_types/{id}', [AppointmentTypeController::class, 'destroy']);
Route::post('appointment_types/{id}/restore', [AppointmentTypeController::class, 'restore']);

// Settings: only the endpoints used by FE
Route::get('settings', [SettingController::class, 'index']);
Route::put('settings', [SettingController::class, 'bulkUpdate']);
