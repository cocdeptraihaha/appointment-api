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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $service = Service::create([
            'id' => Str::random(10),
            'name' => $request->name,
        ]);

        return response()->json(new ServiceResource($service), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $service = Service::with(['appointments.contact', 'appointments.staff', 'staff'])
            ->findOrFail($id);

        return response()->json(new ServiceResource($service));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $service = Service::findOrFail($id);

        $service->update($request->only(['name']));

        return response()->json(new ServiceResource($service));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $service = Service::findOrFail($id);
        
        // Check if service has appointments or staff
        if ($service->appointments()->count() > 0 || $service->staff()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete service with existing appointments or staff assignments'
            ], 422);
        }

        $service->delete();

        return response()->json([], 204);
    }
}
