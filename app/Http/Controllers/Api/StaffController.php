<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Http\Resources\StaffResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Staff::with(['services']);

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $staff = $query->orderBy('name')->get();

        return response()->json(StaffResource::collection($staff));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'avatar' => ['nullable', 'string'],
            'visible' => ['sometimes', 'in:0,1'],
            'service_ids' => ['sometimes', 'array'],
            'service_ids.*' => ['string', 'size:10', 'exists:services,id'],
        ]);

        $staff = Staff::create([
            'id' => Str::random(10),
            'name' => $validated['name'],
            'avatar' => $validated['avatar'] ?? null,
            'visible' => array_key_exists('visible', $validated) ? (int) $validated['visible'] : 1,
        ]);

        // Attach services if provided
        if (array_key_exists('service_ids', $validated)) {
            $staff->services()->attach($validated['service_ids']);
        }

        $staff->load('services');

        return response()->json(new StaffResource($staff), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $staff = Staff::with(['services'])
            ->findOrFail($id);

        return response()->json(new StaffResource($staff));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $staff = Staff::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:100'],
            'avatar' => ['sometimes', 'nullable', 'string'],
            'visible' => ['sometimes', 'in:0,1'],
            'service_ids' => ['sometimes', 'array'],
            'service_ids.*' => ['string', 'size:10', 'exists:services,id'],
        ]);

        if (array_key_exists('visible', $validated)) {
            $validated['visible'] = (int) $validated['visible'];
        }

        $staff->update($validated);

        // Update services if provided
        if (array_key_exists('service_ids', $validated)) {
            $staff->services()->sync($validated['service_ids']);
        }

        $staff->load('services');

        return response()->json(new StaffResource($staff));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $staff = Staff::findOrFail($id);
        
        // Check if staff has appointments
        if ($staff->appointments()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete staff with existing appointments'
            ], 422);
        }

        $staff->delete();

        return response()->json([], 204);
    }
}
