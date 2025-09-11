<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Http\Resources\ServiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Service::withCount(['appointments', 'staff']);

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $services = $query->orderBy('name')->get();

        return response()->json(ServiceResource::collection($services));
    }

    // Removed unused CRUD methods to keep only index() for FE
}
