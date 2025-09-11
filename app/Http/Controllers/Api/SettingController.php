<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // Build settings JSON directly from current data (no DB settings read)
        $visibleStaffIds = Staff::where('visible', true)
            ->orderBy('name')
            ->pluck('id')
            ->values();

        // Keep FE expected shape: an object of settings
        return response()->json([
            'visibleStaffs' => $visibleStaffIds,
        ]);
    }

    // Removed unused CRUD and key-based endpoints; FE only uses index + bulkUpdate

    /**
     * Bulk update settings for React compatibility
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        // Do not persist settings; derive from live data and return in expected shape
        $visibleStaffIds = Staff::where('visible', true)
            ->orderBy('name')
            ->pluck('id')
            ->values();

        return response()->json([
            'visibleStaffs' => $visibleStaffIds,
        ]);
    }
}