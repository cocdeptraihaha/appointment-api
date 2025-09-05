<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Http\Resources\AppointmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Appointment::with(['services']);

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $startTime = strtotime($request->start_date) * 1000;
            $endTime = strtotime($request->end_date) * 1000;
            $query->whereBetween('start_time', [$startTime, $endTime]);
        }

        // Filter by staff
        if ($request->has('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        // Filter by contact
        if ($request->has('contact_id')) {
            $query->where('contact_id', $request->contact_id);
        }

        // Filter by appointment type
        if ($request->has('type_id')) {
            $query->where('type_id', $request->type_id);
        }

        // Search by title
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $appointments = $query->orderBy('start_time')->get();

        return response()->json(AppointmentResource::collection($appointments));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Use start/end from React app, fallback to start_time/end_time
        $startTime = $request->start ?? $request->start_time;
        $endTime = $request->end ?? $request->end_time;

        $appointment = Appointment::create([
            'id' => Str::random(10),
            'title' => $request->title,
            'type_id' => $request->type_id,
            'contact_id' => $request->contact_id,
            'staff_id' => $request->staff_id,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        // Attach services if provided
        if ($request->has('service_ids')) {
            $appointment->services()->attach($request->service_ids);
        }

        $appointment->load(['services']);

        return response()->json(new AppointmentResource($appointment), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $appointment = Appointment::with(['services'])
            ->findOrFail($id);

        return response()->json(new AppointmentResource($appointment));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);

        // Map start/end from React to start_time/end_time if provided
        $payload = $request->only(['title', 'type_id', 'contact_id', 'staff_id', 'start_time', 'end_time']);
        if ($request->has('start')) {
            $payload['start_time'] = $request->start;
        }
        if ($request->has('end')) {
            $payload['end_time'] = $request->end;
        }

        $appointment->update($payload);

        // Update services if provided
        if ($request->has('service_ids')) {
            $appointment->services()->sync($request->service_ids);
        }

        $appointment->load(['services']);

        return response()->json(new AppointmentResource($appointment));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return response()->json([], 204);
    }
}
