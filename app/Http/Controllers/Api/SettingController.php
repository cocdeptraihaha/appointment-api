<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Http\Resources\SettingResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Setting::query();

        // Search by setting key
        if ($request->has('search')) {
            $query->where('setting_key', 'like', '%' . $request->search . '%');
        }

        $settings = $query->orderBy('setting_key')->get();

        // Return as key-value object for React compatibility
        $settingsObject = $settings->mapWithKeys(function ($setting) {
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
        
        return response()->json($settingsObject);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $setting = Setting::create([
            'setting_key' => $request->setting_key,
            'setting_value' => $request->setting_value,
        ]);

        return response()->json(new SettingResource($setting), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $setting = Setting::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $setting
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $setting = Setting::findOrFail($id);

        $setting->update($request->only(['setting_key', 'setting_value']));

        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully',
            'data' => $setting
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $setting = Setting::findOrFail($id);
        $setting->delete();

        return response()->json([], 204);
    }

    /**
     * Get setting by key.
     */
    public function getByKey(string $key): JsonResponse
    {
        $setting = Setting::where('setting_key', $key)->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $setting
        ]);
    }

    /**
     * Update setting by key.
     */
    public function updateByKey(Request $request, string $key): JsonResponse
    {
        $setting = Setting::where('setting_key', $key)->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }

        $setting->update(['setting_value' => $request->setting_value]);

        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully',
            'data' => $setting
        ]);
    }

    /**
     * Bulk update settings for React compatibility
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $updatedSettings = [];

        // Update visibleContacts if provided
        if ($request->has('visibleContacts')) {
            $visibleContactsSetting = Setting::where('setting_key', 'visibleContacts')->first();
            
            if ($visibleContactsSetting) {
                $visibleContactsSetting->update([
                    'setting_value' => json_encode($request->visibleContacts)
                ]);
                $updatedSettings['visibleContacts'] = $request->visibleContacts;
            } else {
                // Create new setting if it doesn't exist
                Setting::create([
                    'setting_key' => 'visibleContacts',
                    'setting_value' => json_encode($request->visibleContacts)
                ]);
                $updatedSettings['visibleContacts'] = $request->visibleContacts;
            }
        }

        // Add more settings here as needed
        // if ($request->has('otherSetting')) { ... }

        return response()->json($updatedSettings);
    }
}
