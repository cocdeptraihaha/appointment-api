<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppointmentType;
use App\Http\Resources\AppointmentTypeResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class AppointmentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = AppointmentType::withCount('appointments');

        // Filter by deleted status
        if ($request->has('include_deleted') && $request->include_deleted) {
            $query->withTrashed();
        } else {
            $query->whereNull('deleted_at');
        }

        // Search by label
        if ($request->has('search')) {
            $query->where('label', 'like', '%' . $request->search . '%');
        }

        $appointmentTypes = $query->orderBy('label')->get();

        return response()->json(AppointmentTypeResource::collection($appointmentTypes));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $appointmentType = AppointmentType::create([
            'id' => Str::random(10),
            'label' => $request->label,
            'color' => $request->color,
        ]);

        return response()->json(new AppointmentTypeResource($appointmentType), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $appointmentType = AppointmentType::withTrashed()
            ->with(['appointments.contact', 'appointments.staff'])
            ->findOrFail($id);

        return response()->json(new AppointmentTypeResource($appointmentType));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $appointmentType = AppointmentType::withTrashed()->findOrFail($id);

        // Handle soft delete via PUT method for React compatibility
        if ($request->has('deleted_at') && $request->deleted_at) {
            $appointmentType->delete(); // Use soft delete
        } else {
            $appointmentType->update($request->only(['label', 'color']));
        }

        return response()->json(new AppointmentTypeResource($appointmentType));
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(string $id): JsonResponse
    {
        $appointmentType = AppointmentType::findOrFail($id);

        $appointmentType->delete(); // Soft delete

        return response()->json([], 204);
    }

    /**
     * Restore a soft deleted appointment type.
     */
    public function restore(string $id): JsonResponse
    {
        $appointmentType = AppointmentType::withTrashed()->findOrFail($id);
        $appointmentType->restore();

        return response()->json(new AppointmentTypeResource($appointmentType));
    }
}
