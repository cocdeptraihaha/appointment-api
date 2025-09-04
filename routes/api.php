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

// Contact Routes
Route::apiResource('contacts', ContactController::class);

// Service Routes
Route::apiResource('services', ServiceController::class);

// Staff Routes
Route::apiResource('staff', StaffController::class);

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

// Setting Routes
Route::apiResource('settings', SettingController::class);
Route::get('settings/key/{key}', [SettingController::class, 'getByKey']);
Route::put('settings/key/{key}', [SettingController::class, 'updateByKey']);

// Bulk update settings for React compatibility
Route::put('settings', [SettingController::class, 'bulkUpdate']);

// Combined data endpoint for React app
Route::get('data', function () {
    $appointments = \App\Models\Appointment::with(['services'])->orderBy('start_time')->get();
    $appointmentTypes = \App\Models\AppointmentType::whereNull('deleted_at')->get();
    $contacts = \App\Models\Contact::all();
    $staff = \App\Models\Staff::with(['services'])->get();
    $services = \App\Models\Service::all();
    $settings = \App\Models\Setting::all()->mapWithKeys(function ($setting) {
        $value = $setting->setting_value;
        
        // Try to parse JSON strings
        if (is_string($value) && (str_starts_with($value, '[') || str_starts_with($value, '{'))) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            }
        }
        
        return [$setting->setting_key => $value];
    });

    return response()->json([
        'appointments' => \App\Http\Resources\AppointmentResource::collection($appointments),
        'appointment_types' => \App\Http\Resources\AppointmentTypeResource::collection($appointmentTypes),
        'contacts' => \App\Http\Resources\ContactResource::collection($contacts),
        'staff' => \App\Http\Resources\StaffResource::collection($staff),
        'services' => \App\Http\Resources\ServiceResource::collection($services),
        'settings' => $settings,
    ]);
});

